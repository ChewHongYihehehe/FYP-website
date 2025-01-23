<?php
include 'connect.php';
session_start();

// Fetch admins
$admins = [];
$stmt = $conn->prepare("SELECT * FROM admin WHERE role = 'admin'"); // Fetch only admins
$stmt->execute();
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle adding a new admin
if (isset($_POST['add_admin'])) {
    $admin_name = $_POST['admin_name'];
    $admin_email = $_POST['admin_email'];
    $admin_phone = $_POST['admin_phone'];
    $admin_password = password_hash('marcus', PASSWORD_DEFAULT); // Set a default password
    $admin_role = 'admin';

    $stmt = $conn->prepare("INSERT INTO admin (admin_password, admin_name, admin_email, admin_phone, admin_status) 
                             VALUES (:admin_password, :admin_name, :admin_email, :admin_phone, 'Active')");
    $stmt->bindParam(':admin_password', $admin_password);
    $stmt->bindParam(':admin_name', $admin_name);
    $stmt->bindParam(':admin_email', $admin_email);
    $stmt->bindParam(':admin_phone', $admin_phone);

    if ($stmt->execute()) {
        header("Location: admin_list_s.php");
        exit();
    } else {
        $error_message = "Error adding admin.";
    }
}

// Handle terminating or reactivating an admin
if (isset($_GET['id']) && isset($_GET['status'])) {
    $admin_id = $_GET['id'];
    $status = $_GET['status'];
    if ($status === 'terminate') {
        // Terminate admin
        $stmt = $conn->prepare("
                UPDATE admin
                SET 
                    admin_status = 'terminated'
                WHERE id = :id
            ");
    } else {
        // Reactivate admin
        $stmt = $conn->prepare("
                UPDATE admin
                SET 
                    admin_status = 'active'
                WHERE id = :id
            ");
    }
    $stmt->bindParam(':id', $admin_id, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: admin_list.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admins</title>
    <link rel="stylesheet" type="text/css" href="assets/css/admin_categories.css"> <!-- Use the same CSS file -->
</head>

<body>

    <?php include 'sidebar.php'; ?>
    <div class="container">

        <div class="product-display">
            <h1>Manage Admins</h1>

            <div class="btn-container page-top">
                <button class="btn-add-new" id="addAdminBtn" style="margin-bottom:20px;" class="btn">Add Admin</button>
            </div>

            <table class="product-display-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $admin): ?>
                        <tr>
                            <td><?= htmlspecialchars($admin['id']); ?></td>
                            <td><?= htmlspecialchars($admin['admin_name']); ?></td>
                            <td><?= htmlspecialchars($admin['admin_email']); ?></td>
                            <td><?= htmlspecialchars($admin['admin_phone']); ?></td>
                            <td>
                                <span class="<?= strtolower($admin['admin_status']) == 'active' ? 'status-active' : 'status-inactive'; ?>">
                                    <?= htmlspecialchars($admin['admin_status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if (strtolower($admin['admin_status']) == 'terminated'): ?>
                                    <a href="?id=<?= $admin['id']; ?>&status=active"
                                        class="btn btn-reactivate"
                                        onclick="return confirm('Are you sure you want to reactivate this admin?');">
                                        Reactivate
                                    </a>
                                <?php else: ?>
                                    <a href="?id=<?= $admin['id']; ?>&status=terminate"
                                        class="btn btn-terminate"
                                        onclick="return confirm('Are you sure you want to terminate this admin?');">
                                        Terminate
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Add Admin Form -->
    <div id="addAdmin_Modal" class="modal">
        <div class="modal-content">
            <span class="close-button" id="closeAddAdminModal">&times;</span>
            <form method="post">
                <div class="account-header">
                    <h1 class="account-title">Add Admin</h1>
                </div>
                <div class="account-edit">
                    <div class="input-container">
                        <label>Name</label>
                        <input type="text" placeholder="Enter admin name" name="admin_name" required>
                    </div>
                </div>
                <div class="account-edit">
                    <div class="input-container">
                        <label>Email</label>
                        <input type="email" placeholder="Enter email" name="admin_email" required>
                    </div>
                </div>
                <div class="account-edit">
                    <div class="input-container">
                        <label>Phone</label>
                        <input type="text" placeholder="Enter phone number" name="admin_phone" required>
                    </div>
                </div>
                <div class="btn-container">
                    <button type="submit" class="btn-save" name="add_admin">Add Admin</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add Admin Modal
            const addAdmin_Modal = document.getElementById('addAdmin_Modal');
            const closeAddAdminModal = document.getElementById('closeAddAdminModal');
            const addAdminButton = document.getElementById('addAdminBtn');

            // Show Add Admin Modal
            addAdminButton.addEventListener('click', function() {
                addAdmin_Modal.style.display = 'block'; // Show the modal
            });

            // Close Add Admin Modal
            closeAddAdminModal.addEventListener('click', function() {
                addAdmin_Modal.style.display = 'none';
            });

            // Close modals when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target === addAdmin_Modal) {
                    addAdmin_Modal.style.display = 'none';
                }
            });
        });
    </script>

</body>

</html>