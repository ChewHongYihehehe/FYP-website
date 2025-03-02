<?php
session_start();
include 'connect.php'; // Ensure this file sets up the $conn variable

$error = '';
$terminated = false;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_email = trim($_POST['admin_email']);
    $admin_password = $_POST['admin_password'];


    $stmt = $conn->prepare("SELECT id, admin_password, role, admin_status FROM admin WHERE admin_email = :admin_email");
    $stmt->bindParam(':admin_email', $admin_email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $hashed_password = $result['admin_password'];
        $role = $result['role'];
        $admin_id = $result['id'];
        $status = $result['admin_status'];

        // Check if the admin is terminated
        if (strcasecmp($status, 'terminated') === 0) { // Use strcasecmp for case-insensitive comparison
            $terminated = true;
        } elseif (password_verify($admin_password, $hashed_password)) {
            $_SESSION['admin_id'] = $admin_id;
            $_SESSION['admin_email'] = $admin_email;
            $_SESSION['role'] = $role;

            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Invalid Admin Email or Password.";
        }
    } else {
        $error = "Invalid Admin Email or Password.";
    }
}
$conn = null; // Close the database connection
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" type="text/css" href="assets/css/admin_login.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>
    <div class="form-container">
        <h2>Admin Login</h2>
        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" action="admin_login.php">
            <div class="inputbox">
                <input type="text" name="admin_email" required>
                <label for="admin_email">Admin Email</label>
            </div>
            <div class="inputbox">
                <input type="password" name="admin_password" id="password-field" required>
                <label for="admin_password">Password</label>
            </div>
            <div class="show-password">
                <input type="checkbox" id="toggle-password" onclick="togglePasswordVisibility()"> Show Password
            </div>
            <div class="button-container">
                <input type="submit" value="Sign In">
            </div>
        </form>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordField = document.getElementById('password-field');
            passwordField.type = passwordField.type === 'password' ? 'text' : 'password';
        }

        <?php if ($terminated): ?>
            Swal.fire({
                icon: 'error',
                title: 'Access Denied',
                text: 'Your account has been terminated. Please contact support.',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>
    </script>
</body>

</html>