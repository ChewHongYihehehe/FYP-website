<?php
include 'connect.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "error_not_logged_in";
    exit();
}

// Check if product ID is provided
if (!isset($_POST['product_id'])) {
    echo "error_no_product";
    exit();
}


// Prepare and execute delete query
$delete_query = "DELETE FROM wishlist WHERE user_id = :user_id AND product_id = :product_id";
$stmt = $conn->prepare($delete_query);
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->bindParam(':product_id', $_POST['product_id'], PDO::PARAM_INT);
$result = $stmt->execute();

if ($result) {
    echo "success"; // Simple success response
} else {
    echo "error_delete_failed";
}
