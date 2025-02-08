<?php

include 'connect.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $error_messages = 'You must be logged in to view this page.';
} else {
    // Fetch user details
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT status FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the user's status is terminated
    if ($user && strtolower($user['status']) === 'terminated') {
        $error_messages = 'Your account has been terminated. Please contact support.';
    }
}

include 'header.php';

if (!isset($_GET['order_id'])) {
    die("Order ID not specified.");
}

$order_id = $_GET['order_id'];

// Retrieve the order number from the URL
$order_number = isset($_GET['order_number']) ? $_GET['order_number'] : '';


$stmt = $conn->prepare("SELECT * FROM orders WHERE id = :order_id");
$stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
$stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
$stmt->execute();
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html>

<head>
    <title>Receipt</title>
    <link rel="stylesheet" href="assets/css/receipt.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <div class="breadcrumbs d-flex flex-row align-items-center">
        <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="profiles.php"><i class="fa fa-angle-right" aria-hidden="true"></i>Profile</a></li>
            <li><a href="order_history.php"><i class="fa fa-angle-right" aria-hidden="true"></i>Order History</a></li>
            <li><a href="receipt.php"><i class="fa fa-angle-right" aria-hidden="true"></i>Receipt</a></li>
        </ul>
    </div>
    <div class="brand-logo-container">
        <a href="#">St<span>ep</span></a>
    </div>
    <div class="receipt_container">
        <div class="left-section">
            <h2>SHIP TO</h2>
            <p><?php echo htmlspecialchars($order['shipping_fullname']); ?></p>
            <p><?php echo htmlspecialchars($order['shipping_address_line']); ?></p>
            <p><?php echo htmlspecialchars($order['shipping_city'] . ', ' . $order['shipping_state'] . ' ' . $order['shipping_post_code']); ?></p>
            <div class="placeholder"> </div>
            <h2>ORDER NUMBER</h2>
            <p><?php echo htmlspecialchars($order_number); ?></p>
            <h2>ORDER DATE</h2>
            <p><?php echo date('Y-m-d', strtotime($order['placed_on'])); ?></p>
            <h2>PAYMENT METHOD</h2>
            <p><?php echo htmlspecialchars(strtoupper($order['method'])); ?></p>
            <a href="order_history.php" class="order-status">ORDER STATUS ></a>
        </div>
        <div class="right-section">
            <?php if (!empty($order_items)): ?>
                <?php foreach ($order_items as $item): ?>
                    <div class="item-container">
                        <?php if ($item['image']): ?>
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="Product Image">
                        <?php else: ?>
                            <p>No image available</p>
                        <?php endif; ?>
                        <div class="item-info">
                            <h2>
                                <?php echo htmlspecialchars($item['name']); ?>
                            </h2>
                            <p>
                                SIZE: <?php echo htmlspecialchars($item['size']); ?>
                            </p>
                            <p>
                                COLOR: <?php echo htmlspecialchars($item['color']); ?>
                            </p>
                            <p>
                                QTY: <?php echo htmlspecialchars($item['quantity']); ?> * RM<?php echo number_format($item['price'], 2); ?>
                            </p>
                            <p>
                                RM<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                            </p>
                        </div>
                    </div>
                    <hr>
                <?php endforeach; ?>
                <table>
                    <tr>
                        <td>Subtotal</td>
                        <td>RM<?php echo htmlspecialchars($order['total_price']); ?></td>
                    </tr>
                    <tr>
                        <td>Delivery</td>
                        <td>Free</td>
                    </tr>
                    <tr>
                        <td>TOTAL</td>
                        <td>RM<?php echo htmlspecialchars($order['total_price']); ?></td>
                    </tr>
                </table>
            <?php endif; ?>
        </div>
    </div>
    <script>
        var errorMessages = <?= json_encode($error_messages); ?>;

        window.onload = function() {
            if (errorMessages)
                Swal.fire({
                    icon: 'error',
                    title: 'Access Denied',
                    text: errorMessages,
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'login.php';
                });
        };
    </script>
</body>

</html>