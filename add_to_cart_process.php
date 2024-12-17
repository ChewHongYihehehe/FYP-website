<?php
include 'connect.php';
session_start();

// Silently exit if parameters are not fully set
if (!isset($_SESSION['user_id'])) {
    exit(); // Just exit without any output
}

$user_id = $_SESSION['user_id'];

// Function to get current cart count
function getCartCount($conn, $user_id)
{
    $cart_count_query = "SELECT COUNT(*) as count FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($cart_count_query);
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'];
}

// Function to get total price of items in the cart
function getTotalPrice($conn, $user_id)
{
    $total_price_query = "SELECT SUM(price * quantity) as total FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($total_price_query);
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'] ? $result['total'] : 0; // Return 0 if no items
}

// Handle item removal
if (isset($_POST['cart_id']) && !isset($_POST['quantity'])) {
    $cart_id = $_POST['cart_id'];

    try {
        // Prepare and execute delete query
        $delete_query = "DELETE FROM cart WHERE id = :cart_id AND user_id = :user_id";
        $stmt = $conn->prepare($delete_query);
        $stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // Check if any row was deleted
        if ($stmt->rowCount() > 0) {
            // Get updated cart count and total price
            $cart_count = getCartCount($conn, $user_id);
            $total_price = getTotalPrice($conn, $user_id);
            echo json_encode(['success' => true, 'cart_count' => $cart_count, 'total_price' => $total_price]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Could not remove item']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit();
}

// Handle quantity update
if (isset($_POST['cart_id']) && isset($_POST['quantity'])) {
    $cart_id = $_POST['cart_id'];
    $quantity = $_POST['quantity'];

    try {
        // Update the quantity in the cart
        $update_query = "UPDATE cart SET quantity = :quantity WHERE id = :cart_id AND user_id = :user_id";
        $stmt = $conn->prepare($update_query);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // Get updated cart count and total price
        $cart_count = getCartCount($conn, $user_id);
        $total_price = getTotalPrice($conn, $user_id);

        echo json_encode(['success' => true, 'cart_count' => $cart_count, 'total_price' => $total_price]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit();
}

// Handle adding to cart (only proceed if all required parameters are set)
if (!isset($_POST['product_id']) || !isset($_POST['size']) || !isset($_POST['color'])) {
    echo json_encode(['success' => false, 'message' => 'Incomplete product information']);
    exit();
}

$product_id = $_POST['product_id'];
$size = $_POST['size'];
$color = $_POST['color'];

// First, check if the product variant exists and has stock
$check_variant_query = "SELECT * FROM product_variants 
                        WHERE product_id = :product_id 
                        AND size = :size 
                        AND (color = :color OR :color = 'Unknown')
                        AND stock > 0";
$stmt = $conn->prepare($check_variant_query);
$stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
$stmt->bindParam(':size', $size, PDO::PARAM_INT);
$stmt->bindParam(':color', $color, PDO::PARAM_STR);
$stmt->execute();
$variant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$variant) {
    exit(); // Silently exit if no variant found
}

// Get product name
$product_query = "SELECT name FROM products WHERE id = :product_id";
$stmt = $conn->prepare($product_query);
$stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if product is already in cart
$check_cart_query = "SELECT * FROM cart WHERE user_id = :user_id 
                     AND pid = :pid 
                     AND size = :size 
                     AND color = :color";
$stmt = $conn->prepare($check_cart_query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindParam(':pid', $product_id, PDO::PARAM_INT);
$stmt->bindParam(':size', $size, PDO::PARAM_INT);
$stmt->bindParam(':color', $color, PDO::PARAM_STR);
$stmt->execute();
$existing_cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing_cart_item) {
    // Update quantity if product already in cart
    $update_query = "UPDATE cart 
                     SET quantity = quantity + 1, 
                         price = :price 
                     WHERE id = :cart_id";
    $stmt = $conn->prepare($update_query);
    $stmt->bindParam(':price', $variant['price'], PDO::PARAM_STR);
    $stmt->bindParam(':cart_id', $existing_cart_item['id'], PDO::PARAM_INT);
    $stmt->execute();
} else {
    // Insert new item into cart
    $insert_query = "INSERT INTO cart (user_id, pid, size, color, quantity, price, name) 
                     VALUES (:user_id, :pid, :size, :color, 1, :price, :name)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':pid', $product_id, PDO::PARAM_INT);
    $stmt->bindParam(':size', $size, PDO::PARAM_INT);
    $stmt->bindParam(':color', $color, PDO::PARAM_STR);
    $stmt->bindParam(':price', $variant['price'], PDO::PARAM_STR);
    $stmt->bindParam(':name', $product['name'], PDO::PARAM_STR);
    $stmt->execute();
}

// Get updated cart count and total price
$cart_count = getCartCount($conn, $user_id);
$total_price = getTotalPrice($conn, $user_id); // Get total price
echo json_encode(['success' => true, 'cart_count' => $cart_count, 'total_price' => $total_price]);

// Silently exit
exit();
