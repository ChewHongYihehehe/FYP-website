<?php
include 'connect.php'; // Ensure this file sets up the $conn variable

// Handle status change
if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];

    // Update the user's status
    $query = "UPDATE users SET status = :status WHERE id = :id";
    $statement = $conn->prepare($query); // Renamed from $stmt to $statement
    $statement->bindParam(':status', $status);
    $statement->bindParam(':id', $id);

    if ($statement->execute()) {
        echo "<script>alert('User  status updated successfully.');</script>";
    } else {
        echo "<script>alert('Failed to update user status.');</script>";
    }
}

// Fetch all users from the database
$query = "SELECT id, fullname, email, phone, status FROM users";
$statement = $conn->prepare($query); // Renamed from $stmt to $statement
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Users</title>
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