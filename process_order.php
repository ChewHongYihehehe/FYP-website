<?php

include 'connect.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

$stmt = $conn->prepare("SELECT fullname, email FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

//Fetch cart items
$stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

// Fetch shipping address
$shipping_address = [];
$stmt = $conn->prepare("SELECT * FROM user_addresses WHERE user_id = :user_id AND is_shipping_address = 1");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$shipping_address = $stmt->fetch(PDO::FETCH_ASSOC);

// Generate the order number
$order_number = generateOrderNumber($conn);

$paymentMethod = $_POST['method'] ?? null; // Use null coalescing to avoid undefined index notice

// Insert the order into the database
$stmt = $conn->prepare("INSERT INTO orders (user_id, order_number, total_price, placed_on, payment_status, method, shipping_fullname, shipping_address_line, shipping_city, shipping_post_code, shipping_state) VALUES (:user_id, :order_number, :total_price, NOW(), 'Completed', :method, :shipping_fullname, :shipping_address_line, :shipping_city, :shipping_post_code, :shipping_state)");

// Bind parameters
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':order_number', $order_number);
$stmt->bindParam(':total_price', $total_price);
$stmt->bindParam(':method', $paymentMethod);
$stmt->bindParam(':shipping_fullname', $shipping_address['fullname']);
$stmt->bindParam(':shipping_address_line', $shipping_address['address_line']);
$stmt->bindParam(':shipping_city', $shipping_address['city']);
$stmt->bindParam(':shipping_post_code', $shipping_address['postcode']);
$stmt->bindParam(':shipping_state', $shipping_address['state']);
$stmt->execute();

// Get the last inserted order ID
$order_id = $conn->lastInsertId();

// Insert order items into the order_items table
foreach ($cart_items as $item) {

    $stmt = $conn->prepare("SELECT pv.image1_display 
                            FROM products p
                            JOIN product_variants pv ON p.id = pv.product_id
                            WHERE p.name = :name 
                            AND pv.size = :size 
                            AND pv.color = :color");
    $stmt->bindParam(':name', $item['name']);
    $stmt->bindParam(':size', $item['size']);
    $stmt->bindParam(':color', $item['color']);
    $stmt->execute();
    $product_image = $stmt->fetchColumn();

    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, name, size, color, quantity, price, image) VALUES (:order_id, :product_id, :name, :size, :color, :quantity, :price, :image)");
    $stmt->bindParam(':order_id', $order_id);
    $stmt->bindParam(':product_id', $item['pid']); // Assuming you have product_id in the cart
    $stmt->bindParam(':name', $item['name']); // Assuming you have name in the cart
    $stmt->bindParam(':size', $item['size']); // Assuming you have size in the cart
    $stmt->bindParam(':color', $item['color']); // Assuming you have color in the cart
    $stmt->bindParam(':quantity', $item['quantity']);
    $stmt->bindParam(':price', $item['price']);
    $stmt->bindParam(':image', $product_image);
    $stmt->execute();
}

function generateOrderNumber($conn)
{
    $date = date('Ymd');
    $random_number = rand(1000, 9999);
    $order_number = "ORD-" . $date . "-" . $random_number;

    $stmt = $conn->prepare("SELECT COUNT(*) FROM orders WHERE order_number = :order_number");
    $stmt->bindParam(':order_number', $order_number);
    $stmt->execute();

    if ($stmt->fetchColumn() > 0) {
        return generateOrderNumber($conn); // Recursively generate a new order number if it already exists
    }

    return $order_number;
}


// Clear the cart after the order is placed
$stmt = $conn->prepare("DELETE FROM cart WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

// Redirect to the receipt page
header("Location: receipt.php?order_id=" . $order_id . "&order_number=" . $order_number);
exit();
