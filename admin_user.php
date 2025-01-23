<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location:admin_login.php');
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