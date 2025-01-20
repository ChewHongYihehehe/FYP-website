<?php
include 'connect.php';
session_start();

if (isset($_GET['id'])) {
    $restore_id = $_GET['id'];

    // Fetch the deleted brand details
    $stmt = $conn->prepare("SELECT * FROM deleted_brands WHERE id = :id");
    $stmt->bindParam(':id', $restore_id, PDO::PARAM_INT);
    $stmt->execute();
    $deleted_brand = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($deleted_brand) {
        // Insert the brand back into the brands table
        $stmt = $conn->prepare("INSERT INTO brand (name) VALUES (:name)");
        $stmt->bindParam(':name', $deleted_brand['name']);
        $stmt->execute();

        // Now delete the brand from the deleted_brands table
        $stmt = $conn->prepare("DELETE FROM deleted_brands WHERE id = :id");
        $stmt->bindParam(':id', $restore_id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: admin_restore_brands.php"); // Redirect back to the restore brands page
        exit();
    }
}
