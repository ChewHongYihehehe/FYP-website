<?php
include 'connect.php';
session_start();

if (isset($_GET['id'])) {
    $restore_id = $_GET['id'];

    // Fetch the deleted size details
    $stmt = $conn->prepare("SELECT * FROM deleted_sizes WHERE id = :id");
    $stmt->bindParam(':id', $restore_id, PDO::PARAM_INT);
    $stmt->execute();
    $deleted_size = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($deleted_size) {
        // Insert the size back into the sizes table
        $stmt = $conn->prepare("INSERT INTO sizes (size) VALUES (:size)");
        $stmt->bindParam(':size', $deleted_size['size']);
        $stmt->execute();

        // Now delete the size from the deleted_sizes table
        $stmt = $conn->prepare("DELETE FROM deleted_sizes WHERE id = :id");
        $stmt->bindParam(':id', $restore_id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: admin_restore_sizes.php"); // Redirect back to the restore sizes page
        exit();
    }
}
