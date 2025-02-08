<?php
include 'connect.php';
session_start();

$error_message = '';

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    $error_message = 'You must be logged in to view this page.';
} else {
    // Fetch admin details
    $admin_id = $_SESSION['admin_id'];
    $stmt = $conn->prepare("SELECT admin_status FROM admin WHERE id = :admin_id");
    $stmt->bindParam(':admin_id', $admin_id);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the admin's status is terminated
    if ($admin && strtolower($admin['admin_status']) === 'terminated') {
        $error_message = 'Your account has been terminated. Please contact support.';
    }
}



if (isset($_GET['id']) && isset($_GET['status'])) {
    $user_id = $_GET['id'];
    $status = $_GET['status'];

    // Prepare the SQL statement to update the user status
    if ($status === 'terminate') {
        $stmt = $conn->prepare("UPDATE users SET status = 'terminated', termination_date = NOW() WHERE id = :id");
    } else {
        $stmt = $conn->prepare("UPDATE users SET status = 'active', termination_date = NULL WHERE id = :id");
    }
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // Redirect to the same page to see the updated status
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch users
$users = [];
$stmt = $conn->prepare("SELECT * FROM users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" type="text/css" href="assets/css/admin_user.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Check if there is an error message to display
        var errorMessage = <?= json_encode($error_message); ?>; // Convert PHP variable to JavaScript

        if (errorMessage) {
            window.onload = function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Access Denied',
                    text: errorMessage,
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'admin_login.php';
                });
            };
        }
    </script>
</head>

<body>

    <?php include 'sidebar.php'; ?>
    <div class="container">

        <div class="product-display">
            <table class="product-display-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Termination Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $row_count = 1;
                    foreach ($users as $user): ?>
                        <tr>
                            <td><?= $row_count++; ?></td>
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
                                    <!-- No termination date for active users -->
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
                                    <!-- Terminate option for active users -->
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

</body>

<script src="assets/js/admin_user.js"></script>

</html>