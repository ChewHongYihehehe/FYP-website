<?php

include 'connect.php';

$dsn = 'mysql:host=localhost;dbname=shoes_db;charset=utf8mb4';
$username = 'root'; 
$password = ''; 

// Handle status change
if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];

    // Update the user's status
    $query = "UPDATE users SET status = :status WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        echo "<script>alert('User status updated successfully.');</script>";
    } else {
        echo "<script>alert('Failed to update user status.');</script>";
    }
}

// Fetch all users from the database
$query = "SELECT id, fullname, email, phone, age, gender, status FROM users";
$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Users</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f7f7f7;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px auto;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: rgb(116, 161, 219);
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .action-link {
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
        }

        .action-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Manage Users</h1>
    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Display each user in a table row
            foreach ($result as $row) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['fullname']) . "</td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                echo "<td>" . htmlspecialchars($row['age']) . "</td>";
                echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                echo "<td>
                        <a class='action-link' href='?id=" . $row['id'] . "&status=active'>Activate</a> | 
                        <a class='action-link' href='?id=" . $row['id'] . "&status=terminate'>Terminate</a>
                      </td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
