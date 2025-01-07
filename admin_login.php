/* got some problem and will make correction soon

<?php

include 'connect.php'; // Ensure your database connection is correct
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_id = $_POST["admin_id"];
    $admin_password = $_POST["admin_password"];

    try {
        // Prepare SQL query
        $stmt = $conn->prepare("SELECT * FROM admin WHERE admin_id = :admin_id");
        $stmt->bindParam(':admin_id', $admin_id);
        $stmt->execute();

        // Fetch the result
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $stored_password = $row['admin_password'];

            // Verify the password
            if (password_verify($admin_password, $stored_password) || $stored_password === $admin_password) {
                // If password matches
                $_SESSION['admin_id'] = $row['admin_id'];

                // Redirect to profile page
                header('location:admin_profile.php');
                exit();
            } else {
                echo "<script>alert('Incorrect admin ID or password!');</script>";
            }
        } else {
            echo "<script>alert('Incorrect admin ID or password!');</script>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Login Page</title>
    <link rel="stylesheet" href="assets/css/admin_login.css">
</head>
<body>
    <div class="form-box">
        <h2>Login</h2>
        <form method="post" action="login.php">
            <div class="inputbox">
                <i class="fa fa-user"></i>
                <input type="text" name="admin_id" required>
                <label for="admin_id">Admin ID</label>
            </div>
            <div class="inputbox">
                <i class="fa fa-lock"></i>
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
