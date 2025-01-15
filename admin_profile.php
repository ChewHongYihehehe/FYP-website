<?php
session_start();
include 'connect.php'; // Ensure this file sets up the $conn variable

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id']; // Get the admin ID from the session

// Fetch the admin's profile data
try {
    $query = "SELECT admin_id, admin_name, admin_email, admin_phone, admin_status, age, gender, role FROM admin WHERE admin_id = :admin_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_STR);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        die("No admin found with ID $admin_id.");
    }
} catch (PDOException $e) {
    die("Error fetching admin data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-container {
            width: 50%;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .profile-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-details {
            font-size: 16px;
            line-height: 1.8;
        }

        .profile-details strong {
            display: inline-block;
            width: 120px;
            text-align: right;
            margin-right: 10px;
        }

        .form-container {
            margin-top: 20px;
        }

        .form-container label {
            font-size: 14px;
            font-weight: bold;
        }

        .form-container input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-container button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-container button:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
    <h1>Admin Profile</h1>
    <div class="profile-container">
        <h2>Welcome, <?= htmlspecialchars($admin['admin_name']); ?></h2>
        <div class="profile-details">
            <p><strong>Admin ID:</strong> <?= htmlspecialchars($admin['admin_id']); ?></p>
            <p><strong>Name:</strong> <?= htmlspecialchars($admin['admin_name']); ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($admin['admin_email']); ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($admin['admin_phone']); ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($admin['admin_status']); ?></p>
            <p><strong>Age:</strong> <?= htmlspecialchars($admin['age']); ?></p>
            <p><strong>Gender:</strong> <?= htmlspecialchars($admin['gender']); ?></p>
        </div>

        <div class="form-container">
            <form method="POST">
                <label for="new_password">Change Password</label>
                <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
                <button type="submit">Update Password</button>
            </form>
        </div>
    </div>
</body>

</html>