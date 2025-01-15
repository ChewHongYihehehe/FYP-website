<?php
session_start();
include 'connect.php'; // Ensure this file sets up the $conn variable

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_id = $_POST['admin_id'];
    $admin_password = $_POST['admin_password'];

    // Prepare a statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT admin_password, role FROM admin WHERE admin_id = :admin_id");
    $stmt->bindParam(':admin_id', $admin_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $result = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the result as an associative array
        $hashed_password = $result['admin_password'];
        $role = $result['role']; // Fetch the role

        // Verify the password
        if (password_verify($admin_password, $hashed_password)) {
            $_SESSION['admin_id'] = $admin_id;
            $_SESSION['role'] = $role;

            // Redirect based on role
            if ($role === 'super_admin') {
                header("Location: super_admin.php");
                exit();
            } else {
                header("Location: admin.php");
                exit();
            }
        } else {
            $error = "Invalid Admin ID or Password.";
        }
    } else {
        $error = "Invalid Admin ID or Password.";
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
</head>

<body>
    <div class="form-container">
        <h2>Admin Login</h2>
        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" action="admin_login.php">
            <div class="inputbox">
                <input type="text" name="admin_id" required>
                <label for="admin_id">Admin ID</label>
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
    </script>
</body>

</html>