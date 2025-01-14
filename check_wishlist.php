<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User  not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];
$color = isset($_POST['color']) ? $_POST['color'] : null; // Get the color if provided

try {
    // Prepare the SQL statement to check if the product is in the wishlist
    $stmt = $conn->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = :user_id AND product_id = :product_id" . ($color ? " AND color = :color" : ""));
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':product_id', $product_id);

    if ($color) {
        $stmt->bindParam(':color', $color);
    }

    $stmt->execute();
    $is_favorited = $stmt->fetchColumn() > 0;

    echo json_encode(['success' => true, 'is_favorited' => $is_favorited]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
