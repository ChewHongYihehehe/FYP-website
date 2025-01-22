<?php
include 'connect.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User  not logged in']);
    exit();
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
    $quantity = max(1, intval($_POST['quantity'])); // Ensure minimum quantity of 1

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
if (!isset($_POST['product_id']) || !isset($_POST['size'])) {
    echo json_encode(['success' => false, 'message' => 'Incomplete product information']);
    exit();
}

$product_id = $_POST['product_id'];
$size = $_POST['size'];
$color = $_POST['color'] ?? null; // Make color optional

// Fetch the most appropriate product variant
$variant_query = "SELECT pv.*, p.name as product_name 
                  FROM product_variants pv
                  JOIN products p ON pv.product_id = p.id
                  WHERE pv.product_id = :product_id 
                  AND pv.size = :size
                  AND pv.stock > 0
                  LIMIT 1";
$stmt = $conn->prepare($variant_query);
$stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
$stmt->bindParam(':size', $size, PDO::PARAM_STR);
$stmt->execute();
$variant = $stmt->fetch(PDO::FETCH_ASSOC);

// If no variant found, try to fetch any variant for this product
if (!$variant) {
    $fallback_query = "SELECT pv.*, p.name as product_name 
                       FROM product_variants pv
                       JOIN products p ON pv.product_id = p.id
                       WHERE pv.product_id = :product_id 
                       AND pv.stock > 0
                       LIMIT 1";
    $stmt = $conn->prepare($fallback_query);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $variant = $stmt->fetch(PDO::FETCH_ASSOC);
}

// If still no variant found, handle the case
if (!$variant) {
    // Optionally, you can fetch the product details without variants
    $product_query = "SELECT name, price FROM products WHERE id = :product_id";
    $stmt = $conn->prepare($product_query);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // Insert without color variant
        $insert_query = "INSERT INTO cart (user_id, pid, name, price, quantity, size, color, image) 
                         VALUES (:user_id, :pid, :name, :price, 1, :size, :color, :image)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':pid', $product_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $product['name'], PDO::PARAM_STR);
        $stmt->bindParam(':price', $product['price'], PDO::PARAM_STR);
        $stmt->bindParam(':size', $size, PDO::PARAM_STR);
        $stmt->bindValue(':color', 'N/A', PDO::PARAM_STR); // Set a default value for color
        $stmt->bindValue(':image', 'default_image.png', PDO::PARAM_STR); // Set a default image
        $stmt->execute();
    }
} else {
    // Check if product is already in cart
    $check_cart_query = "SELECT * FROM cart WHERE user_id = :user_id AND pid = :pid AND size = :size AND color = :color";
    $stmt = $conn->prepare($check_cart_query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':pid', $product_id, PDO::PARAM_INT);
    $stmt->bindParam(':size', $size, PDO::PARAM_STR);
    $stmt->bindParam(':color', $variant['color'], PDO::PARAM_STR); // Use the color from the variant
    $stmt->execute();
    $existing_cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing_cart_item) {
        // Update quantity if product already in cart
        $update_query = "UPDATE cart SET quantity = quantity + 1 WHERE id = :cart_id";
        $stmt = $conn->prepare($update_query);
        $stmt->bindParam(':cart_id', $existing_cart_item['id'], PDO::PARAM_INT);
        $stmt->execute();
    } else {
        // Insert new item into cart
        $insert_query = "INSERT INTO cart (user_id, pid, name, price, quantity, size, color, image) 
                         VALUES (:user_id, :pid, :name, :price, 1, :size, :color, :image)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':pid', $product_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $variant['product_name'], PDO::PARAM_STR);
        $stmt->bindParam(':price', $variant['price'], PDO::PARAM_STR);
        $stmt->bindParam(':size', $size, PDO::PARAM_STR);
        $stmt->bindParam(':color', $variant['color'], PDO::PARAM_STR); // Use the color from the variant
        $stmt->bindParam(':image', $variant['image1_display'], PDO::PARAM_STR);
        $stmt->execute();
    }
}

// Get updated cart count and total price
$cart_count = getCartCount($conn, $user_id);
$total_price = getTotalPrice($conn, $user_id);
echo json_encode(['success' => true, 'cart_count' => $cart_count, 'total_price' => $total_price]);

// Handle quantity update
if (isset($_POST['cart_id']) && isset($_POST['quantity'])) {
    $cart_id = $_POST['cart_id'];
    $quantity = max(1, intval($_POST['quantity'])); // Ensure minimum quantity of 1

    try {
        // Update the quantity in the cart
        $update_query = "UPDATE cart SET quantity = :quantity WHERE id = :cart_id AND user_id = :user_id";
        $stmt = $conn->prepare($update_query);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // Get updated total price
        $total_price_query = "SELECT COALESCE(SUM(price * quantity), 0) as total FROM cart WHERE user_id ```php
= ?";
        $stmt = $conn->prepare($total_price_query);
        $stmt->execute([$user_id]);
        $total_price = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        echo json_encode([
            'success' => true,
            'total_price' => number_format($total_price, 2),
            'current_quantity' => $quantity
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage(),
            'current_quantity' => $quantity
        ]);
    }
    exit();
}

// Silently exit
exit();
