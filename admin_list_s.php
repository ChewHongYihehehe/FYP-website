<?php
include 'connect.php';
session_start();

// Fetch admins
$admins = [];
$stmt = $conn->prepare("SELECT * FROM admin"); // Assuming your admin table is named 'admins'
$stmt->execute();
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
// Handle editing an admin
if (isset($_POST['edit_admin'])) {
    $admin_id = $_POST['admin_id'];
    $admin_name = $_POST['admin_name'];
    $admin_email = $_POST['admin_email'];
    $admin_phone = $_POST['admin_phone'];

    $stmt = $conn->prepare("UPDATE admin SET admin_name = :admin_name, admin_email = :admin_email, admin_phone = :admin_phone WHERE id = :id");
    $stmt->bindParam(':admin_name', $admin_name);
    $stmt->bindParam(':admin_email', $admin_email);
    $stmt->bindParam(':admin_phone', $admin_phone);
    $stmt->bindParam(':id', $admin_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header("Location: admin_list.php");
        exit();
    } else {
        $error_message = "Error updating admin.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admins</title>
    <link rel="stylesheet" type="text/css" href="assets/css/admin_user.css"> <!-- Use the same CSS file -->
</head>

<body>

    <?php include 'sidebar.php'; ?>
    <div class="container">

        <div class="product-display">
            <table class="product-display-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Role</th>
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
                                <span class="<?= $admin['admin_status'] == 'Active' ? 'status-active' : 'status-inactive'; ?>">
                                    <?= htmlspecialchars($admin['admin_status']); ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($admin['role']); ?></td>
                            <td>
                                <?php if (strtolower($admin['admin_status']) == 'terminated'): ?>
                                    <a href="?id=<?= $admin['id']; ?>&status=active"
                                        class="btn btn-reactivate"
                                        onclick="return confirm('Are you sure you want to reactivate this admin?');">
                                        Reactivate
                                    </a>
                                <?php else: ?>
                                    <button class="btn edit-admin-btn"
                                        data-id="<?= htmlspecialchars($admin['id']); ?>"
                                        data-name="<?= htmlspecialchars($admin['admin_name']); ?>"
                                        data-email="<?= htmlspecialchars($admin['admin_email']); ?>"
                                        data-phone="<?= htmlspecialchars($admin['admin_phone']); ?>">
                                        Edit
                                    </button>
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

    <!-- Modal for Edit Admin Form -->
    <div id="editAdmin_Modal" class="modal">
        <div class="modal-content">
            <span class="close-button" id="closeEditAdminModal">&times;</span>
            <form method="post">
                <input type="hidden" name="admin_id" id="editAdminId">
                <div class="account-header">
                    <h1 class="account-title">Edit Admin</h1>
                </div>
                <div class="account-edit">
                    <div class="input-container">
                        <label>Name</label>
                        <input type="text " placeholder="Enter admin name" name="admin_name" id="editAdmin_Name" required>
                    </div>
                </div>
                <div class="account-edit">
                    <div class="input-container">
                        <label>Email</label>
                        <input type="email" placeholder="Enter email" name="admin_email" id="editAdmin_Email" required>
                    </div>
                </div>
                <div class="account-edit">
                    <div class="input-container">
                        <label>Phone</label>
                        <input type="text" placeholder="Enter phone number" name="admin_phone" id="editAdmin_Phone" required>
                    </div>
                </div>
                <div class="btn-container">
                    <button type="submit" class="btn-save" name="edit_admin">Save</button>
                </div>
            </form>
        </div>
    </div>

</body>

<script src="assets/js/admin_user.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Edit Admin Modal
        const editAdmin_Modal = document.getElementById('editAdmin_Modal');
        const closeEditAdminModal = document.getElementById('closeEditAdminModal');
        const editAdminButtons = document.querySelectorAll('.edit-admin-btn');
        const editAdminIdInput = document.getElementById('editAdminId');
        const editAdminNameInput = document.getElementById('editAdmin_Name');
        const editAdminEmailInput = document.getElementById('editAdmin_Email');
        const editAdminPhoneInput = document.getElementById('editAdmin_Phone');

        // Show Edit Admin Modal
        editAdminButtons.forEach(button => {
            button.addEventListener('click', function() {
                const adminId = this.getAttribute('data-id');
                const adminName = this.getAttribute('data-name');
                const adminEmail = this.getAttribute('data-email');
                const adminPhone = this.getAttribute('data-phone');

                // Set the values in the edit modal
                editAdminIdInput.value = adminId;
                editAdminNameInput.value = adminName;
                editAdminEmailInput.value = adminEmail;
                editAdminPhoneInput.value = adminPhone;

                editAdmin_Modal.style.display = 'block'; // Show the modal
            });
        });

        // Close Edit Admin Modal
        closeEditAdminModal.addEventListener('click', function() {
            editAdmin_Modal.style.display = 'none';
        });

        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === editAdmin_Modal) {
                editAdmin_Modal.style.display = 'none';
            }
        });
    });
</script>

</html>