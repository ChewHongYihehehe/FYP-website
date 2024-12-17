<?php
include 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];
$color = $_POST['color'];
$action = $_POST['action'];

try {
    // Validate the product exists
    $product_check = $conn->prepare("SELECT COUNT(*) FROM products WHERE id = :product_id");
    $product_check->bindParam(':product_id', $product_id);
    $product_check->execute();

    if ($product_check->fetchColumn() == 0) {
        echo json_encode(['success' => false, 'message' => 'Product does not exist']);
        exit;
    }

    // Get all color variants for this product
    $color_variants_stmt = $conn->prepare("SELECT color FROM product_variants WHERE product_id = :product_id");
    $color_variants_stmt->bindParam(':product_id', $product_id);
    $color_variants_stmt->execute();
    $color_variants = $color_variants_stmt->fetchAll(PDO::FETCH_COLUMN);

    // If no color variants exist, use a default color
    if (empty($color_variants)) {
        $matchedColor = 'Default';
    } else {
        // Try to find the most similar color
        $matchedColor = null;
        foreach ($color_variants as $variant) {
            // Exact match
            if (strcasecmp($variant, $color) === 0) {
                $matchedColor = $variant;
                break;
            }
        }

        // If no exact match, try partial match
        if ($matchedColor === null) {
            foreach ($color_variants as $variant) {
                if (stripos($variant, $color) !== false) {
                    $matchedColor = $variant;
                    break;
                }
            }
        }

        // If still no match, use the first available color
        if ($matchedColor === null) {
            $matchedColor = $color_variants[0];
        }
    }

    if ($action === 'add') {
        // Check if already in wishlist
        $check_stmt = $conn->prepare("SELECT COUNT(*) FROM wishlist 
                                      WHERE user_id = :user_id 
                                      AND product_id = :product_id 
                                      AND color = :color");
        $check_stmt->bindParam(':user_id', $user_id);
        $check_stmt->bindParam(':product_id', $product_id);
        $check_stmt->bindParam(':color', $matchedColor);
        $check_stmt->execute();

        if ($check_stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Product already in wishlist']);
            exit;
        }

        // Insert into wishlist
        $stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id, color) 
                                VALUES (:user_id, :product_id, :color)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':color', $matchedColor);
        $stmt->execute();
        echo json_encode(['success' => true, 'color' => $matchedColor]);
    } elseif ($action === 'remove') {
        // Remove from wishlist
        $stmt = $conn->prepare("DELETE FROM wishlist 
                                WHERE user_id = :user_id 
                                AND product_id = :product_id 
                                AND color = :color");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':color', $matchedColor);
        $stmt->execute();
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn = null;
