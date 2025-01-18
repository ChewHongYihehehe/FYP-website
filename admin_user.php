<?php
include 'connect.php';
session_start();

// Fetch users
$users = [];
$stmt = $conn->prepare("SELECT * FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle user status change
if (isset($_GET['id']) && isset($_GET['status'])) {
    $user_id = $_GET['id'];
    $status = $_GET['status'];
    if ($status === 'terminate') {
        // Terminate user
        $stmt = $conn->prepare("
                UPDATE users 
                SET 
                    status = 'terminated',
                    termination_date = NOW()
                WHERE id = :id
            ");
    } else {
        // Reactivate user
        $stmt = $conn->prepare("
                UPDATE users 
                SET 
                    status = 'active',
                    termination_date = NULL
                WHERE id = :id
            ");
    }
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: admin_user.php");
    exit();
}

// Handle editing a user (similar to categories)
if (isset($_POST['edit_user'])) {
    $user_id = $_POST['user_id'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("UPDATE users SET fullname = :fullname, email = :email, phone = :phone WHERE id = :id");
    $stmt->bindParam(':fullname', $fullname);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header("Location: admin_user.php");
        exit();
    } else {
        $error_message = "Error updating user.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" type="text/css" href="assets/css/admin_user.css">
</head>

<body>

    <?php include 'sidebar.php'; ?>
    <div class="container">

        <div class="product-display">
            <table class="product-display-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Termination Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']); ?></td>
                            <td><?= htmlspecialchars($user['fullname']); ?></td>
                            <td><?= htmlspecialchars($user['email']); ?></td>
                            <td><?= htmlspecialchars($user['phone']); ?></td>
                            <td>
                                <?php if ($user['status'] == 'terminated'): ?>
                                    <span class="status-terminated">Terminated</span>
                                <?php else: ?>
                                    <span class="status-active">Active</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($user['status'] == 'terminated'): ?>
                                    <?= htmlspecialchars($user['termination_date']); ?>
                                <?php else: ?>

                                <?php endif; ?>
                            </td>
                            <td>

                                <?php if ($user['status'] == 'terminated'): ?>
                                    <!-- Reactivate option for terminated users -->
                                    <a href="?id=<?= $user['id']; ?>&status=active"
                                        class="btn btn-reactivate"
                                        onclick="return confirm('Are you sure you want to reactivate this user?');">
                                        Reactivate
                                    </a>
                                <?php else: ?>
                                    <!-- Edit and Terminate options for active users -->
                                    <button class="btn edit-user-btn"
                                        data-id="<?= htmlspecialchars($user['id']); ?>"
                                        data-fullname="<?= htmlspecialchars($user['fullname']); ?>"
                                        data-email="<?= htmlspecialchars($user['email']); ?>"
                                        data-phone="<?= htmlspecialchars($user['phone']); ?>">
                                        Edit
                                    </button>
                                    <a href="?id=<?= $user['id']; ?>&status=terminate"
                                        class="btn btn-terminate"
                                        onclick="return confirm('Are you sure you want to terminate this user?');">
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

    <!-- Modal for Edit User Form -->
    <div id="editUser_Modal" class="modal">
        <div class="modal-content">
            <span class="close-button" id="closeEditModal">&times;</span>
            <form method="post">
                <input type="hidden" name="user_id" id="editUserId">
                <div class="account-header">
                    <h1 class="account-title">Edit User</h1>
                </div>
                <div class="account-edit">
                    <div class="input-container">
                        <label>Full Name</label>
                        <input type="text" placeholder="Enter full name" name="fullname" id="editUser_Fullname" required>
                    </div>
                </div>
                <div class="account-edit">
                    <div class="input-container">
                        <label>Email</label>
                        <input type="email" placeholder="Enter email" name="email" id="editUser_Email" required>
                    </div>
                </div>
                <div class="account-edit">
                    <div class="input-container">
                        <label>Phone</label>
                        <input type="text" placeholder="Enter phone number" name="phone" id="editUser_Phone" required>
                    </div>
                </div>
                <div class="btn-container">
                    <button type="submit" class="btn-save" name="edit_user">Save</button>
                </div>
            </form>
        </div>
    </div>

</body>

<script src="assets/js/admin_user.js"></script>

</html>