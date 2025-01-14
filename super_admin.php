<?php
session_start();
include 'connect.php';

// Check if user is logged in and has super_admin role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header("Location: login.php");
    exit;
}

// Fetch all admins
$query = "SELECT * FROM admin WHERE role = 'admin'";
$result = $conn->query($query);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle Add, Edit, Delete, or Update status
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $user_id = $_POST['user_id'] ?? null;

        if ($action === 'add') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $status = 'active';
            $add_query = "INSERT INTO users (username, password, role, status) VALUES ('$username', '$password', 'admin', '$status')";
            $conn->query($add_query);
        } elseif ($action === 'edit') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $edit_query = "UPDATE users SET username = '$username', password = '$password' WHERE id = $user_id";
            $conn->query($edit_query);
        } elseif ($action === 'delete') {
            $delete_query = "DELETE FROM users WHERE id = $user_id";
            $conn->query($delete_query);
        } elseif ($action === 'update_status') {
            $status = $_POST['status'];
            $status_query = "UPDATE users SET status = '$status' WHERE id = $user_id";
            $conn->query($status_query);
        }
    }
    header("Location: super_admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin</title>
</head>
<body>
    <h1>Super Admin Dashboard</h1>

    <h2>Manage Admins</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php while ($admin = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $admin['id']; ?></td>
            <td><?= $admin['username']; ?></td>
            <td><?= $admin['status']; ?></td>
            <td>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="user_id" value="<?= $admin['id']; ?>">
                    <input type="hidden" name="action" value="update_status">
                    <select name="status" onchange="this.form.submit();">
                        <option value="active" <?= $admin['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="terminated" <?= $admin['status'] === 'terminated' ? 'selected' : ''; ?>>Terminated</option>
                    </select>
                </form>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="user_id" value="<?= $admin['id']; ?>">
                    <input type="hidden" name="action" value="delete">
                    <button type="submit">Delete</button>
                </form>
            </td>
        </tr>
        <?php } ?>
    </table>

    <h2>Add Admin</h2>
    <form method="POST">
        <input type="hidden" name="action" value="add">
        <label for="username">Username:</label>
        <input type="text" name="username" required>
        <label for="password">Password:</label>
        <input type="password" name="password" required>
        <button type="submit">Add Admin</button>
    </form>
</body>
</html>
