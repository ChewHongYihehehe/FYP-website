<?php
include 'connect.php';
session_start();

if (isset($_GET['id'])) {
    $restore_id = $_GET['id'];

    // Fetch the deleted color details
    $stmt = $conn->prepare("SELECT * FROM deleted_colors WHERE id = :id");
    $stmt->bindParam(':id', $restore_id, PDO::PARAM_INT);
    $stmt->execute();
    $deleted_color = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($deleted_color) {
        // Insert the color back into the colors table
        $stmt = $conn->prepare("INSERT INTO color (color_name) VALUES (:color_name)");
        $stmt->bindParam(':color_name', $deleted_color['color_name']);
        $stmt->execute();

        // Now delete the color from the deleted_colors table
        $stmt = $conn->prepare("DELETE FROM deleted_colors WHERE id = :id");
        $stmt->bindParam(':id', $restore_id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: admin_restore_colors.php"); // Redirect back to the restore colors page
        exit();
    }
}
