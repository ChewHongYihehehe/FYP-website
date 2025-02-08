<?php
include 'connect.php';
session_start();

if (!isset($_POST['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Product ID is required']);
    exit();
}

$product_id = $_POST['product_id'];

$query = "SELECT color FROM product_variants WHERE product_id = ? LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->execute([$product_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    echo json_encode(['success' => true, 'color' => $result['color']]);
} else {
    echo json_encode(['success' => false, 'message' => 'No variants found']);
}
