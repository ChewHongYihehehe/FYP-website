<?php
include 'connect.php';
session_start();

if (isset($_SESSION['user_id']) && isset($_POST['address_id'])) {
    $user_id = $_SESSION['user_id'];
    $new_shipping_address_id = $_POST['address_id'];

    // Reset previous shipping address
    $stmt = $conn->prepare("UPDATE user_addresses SET is_shipping_address = FALSE WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // Set new shipping address
    $stmt = $conn->prepare("UPDATE user_addresses SET is_shipping_address = TRUE WHERE id = :address_id");
    $stmt->bindParam(':address_id', $new_shipping_address_id, PDO::PARAM_INT);
    $stmt->execute();

    // Redirect back to checkout page
    header("Location: checkout.php");
    exit();
} else {
    // Handle error
    echo "Invalid request.";
}
