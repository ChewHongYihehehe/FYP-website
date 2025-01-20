<?php
include 'connect.php';
session_start();

if (isset($_GET['id'])) {
    $restore_id = $_GET['id'];

    // Fetch the deleted category details
    $stmt = $conn->prepare("SELECT * FROM deleted_categories WHERE id = :id");
    $stmt->bindParam(':id', $restore_id, PDO::PARAM_INT);
    $stmt->execute();
    $deleted_category = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($deleted_category) {
        // Insert the category back into the categories table
        $stmt = $conn->prepare("INSERT INTO categories (name, image) VALUES (:name, :image)");
        $stmt->bindParam(':name', $deleted_category['name']);
        $stmt->bindParam(':image', $deleted_category['image']);
        $stmt->execute();

        // Now delete the category from the deleted_categories table
        $stmt = $conn->prepare("DELETE FROM deleted_categories WHERE id = :id");
        $stmt->bindParam(':id', $restore_id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: admin_restore_categories.php"); // Redirect back to the restore categories page
        exit();
    }
}
