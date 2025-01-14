<?php
// Start the session
session_start();

// Database connection using PDO
try {
    $db = new PDO('sqlite:shoes_db'); // Ensure 'shoes_db' is the correct file path
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    die("Access denied. Please log in first.");
}

// Get the logged-in admin's ID from the session
$admin_id = $_SESSION['admin_id'];

// Fetch the admin's profile data
try {
    $query = "SELECT admin_id, admin_name, admin_email, admin_phone, admin_status FROM admin WHERE admin_id = :admin_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
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
        </div>
    </div>
</body>
</html>
