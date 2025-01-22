<?php
include 'connect.php';
session_start();

//Fetch messages
$messages = [];
$stmt = $conn->prepare("SELECT * FROM messages");
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

//Handle deletion of a messages
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    //Delete the message from the message table
    $stmt = $conn->prepare("DELETE FROM messages WHERE id = :id");
    $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
    $stmt->execute();

    header("Location:admin_contact.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage User Messages</title>
    <link rel="stylesheet" type="text/css" href="assets/css/admin_categories.css">
</head>

<body>

    <?php include 'sidebar.php'; ?>
    <div class="container">
        <h1>Manage User Messages</h1>

        <div class="product-display">
            <table class="product-display-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Number</th>
                        <th>Message</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $message): ?>
                        <tr>
                            <td><?= htmlspecialchars($message['id']); ?></td>
                            <td><?= htmlspecialchars($message['name']); ?></td>
                            <td><?= htmlspecialchars($message['email']); ?></td>
                            <td><?= htmlspecialchars($message['number']); ?></td>
                            <td><?= htmlspecialchars($message['message']); ?></td>
                            <td>
                                <button class="btn edit-message-btn"
                                    data-id="<?= htmlspecialchars($message['id']); ?>"
                                    data-message="<?= htmlspecialchars($message['message']); ?>">Edit</button>
                                <a href="?delete_id=<?= htmlspecialchars($message['id']); ?>" class="btn">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>


    <script src="assets/js/admin_contact.js"></script>
</body>

</html>