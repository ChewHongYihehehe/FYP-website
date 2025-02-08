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





// Fetch admins
$admins = [];
$stmt = $conn->prepare("SELECT * FROM admin WHERE role = 'admin'"); // Fetch only admins
$stmt->execute();
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Admins</title>
    <link rel="stylesheet" type="text/css" href="assets/css/admin_categories.css">
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
            <h1>View Admins</h1>

            <table class="product-display-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
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
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>