<?php

include 'connect.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}


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
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Colo Shop Template">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="stylesheet" type="text/css" href="assets/css/main_styles.css">
    <link rel="stylesheet" href="assets/css/receipt.css">
</head>

<body>



    </div>


    <div class="container">
        <div class="brand-logo-container">
            <a href="#">St<span>ep</span></a>
        </div>
        <a href="admin_order.php" class="btn btn-secondary" style="margin-bottom: 20px;">Back</a>
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
</body>

</html>