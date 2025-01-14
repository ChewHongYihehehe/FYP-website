<?php
session_start();
include 'connect.php'; // Ensure this file establishes a PDO connection and assigns it to $pdo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_id = $_POST['admin_id'];
    $admin_password = $_POST['admin_password'];

    try {
        // Query to check user credentials
        $query = "SELECT * FROM admin WHERE admin_id = :admin_id AND admin_password = :admin_password";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_STR);
        $stmt->bindParam(':admin_password', $admin_password, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $_SESSION['admin_id'] = $admin['id']; // Adjust the column name if necessary
            $_SESSION['role'] = $admin['role'];

            // Redirect based on role
            if ($admin['role'] === 'super_admin') {
                header("Location: super_admin.php");
            } else {
                header("Location: admin.php");
            }
            exit;
        } else {
            echo "Invalid admin ID or password!";
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
    <title>Admin Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(120deg, #2980b9, #8e44ad);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #fff;
        }

        .form-container {
            background: #fff;
            color: #333;
            padding: 20px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-container .inputbox {
            position: relative;
            margin-bottom: 20px;
        }

        .form-container .inputbox input {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
        }

        .form-container .inputbox label {
            position: absolute;
            top: -8px;
            left: 10px;
            background: #fff;
            padding: 0 5px;
            font-size: 14px;
            color: #333;
        }

        .form-container .button-container input {
            width: 100%;
            background: #2980b9;
            border: none;
            padding: 10px;
            color: #fff;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .form-container .button-container input:hover {
            background: #21618c;
        }

        .form-container .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }

        .form-container .show-password {
            font-size: 14px;
            color: #555;
        }
    </style>
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
