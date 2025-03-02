<?php
include 'connect.php';
session_start();

$error_message = '';

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    $error_message = 'You must be logged in to view this page.';
} else {
    // Fetch admin details
    $admin_id = $_SESSION['admin_id'];
    $stmt = $conn->prepare("SELECT admin_status FROM admin WHERE id = :admin_id");
    $stmt->bindParam(':admin_id', $admin_id);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the admin's status is terminated
    if ($admin && strtolower($admin['admin_status']) === 'terminated') {
        $error_message = 'Your account has been terminated. Please contact support.';
    }
}




$orders = [];
$stmt = $conn->prepare("SELECT o.id, o.user_id, o.order_number, o.method, o.total_price, o.placed_on, o.payment_status, 
                                u.fullname AS payer_fullname, u.email AS payer_email, u.phone AS payer_phone, 
                                o.shipping_fullname AS receiver_fullname,
                                o.shipping_address_line, o.shipping_city, 
                                o.shipping_post_code, o.shipping_state 
                         FROM orders o 
                         JOIN users u ON o.user_id = u.id 
                         ORDER BY o.placed_on DESC");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

//Handle updating order status
if (isset($_POST['update_order'])) {
    $order_id = $_POST['order_id'];
    $payment_status = $_POST['payment_status'];

    //Update the order status in the database
    $stmt = $conn->prepare("UPDATE orders SET payment_status = :payment_status WHERE id = :order_id");
    $stmt->bindParam(':payment_status', $payment_status);
    $stmt->bindParam(':order_id', $order_id);
    $stmt->execute();

    header("Location: admin_order.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link rel="stylesheet" type="text/css" href="assets/css/admin_order.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Check if there is an error message to display
        var errorMessage = <?= json_encode($error_message); ?>; // Convert PHP variable to JavaScript

        if (errorMessage) {
            window.onload = function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Access Denied',
                    text: errorMessage,
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'admin_login.php';
                });
            };
        }
    </script>
</head>

<body>


    <?php include 'sidebar.php'; ?>

    <div class="container">

        <div class="product-display">
            <table class="product-display-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Order Number</th>
                        <th>Order Date</th>
                        <th>Payment Method</th>
                        <th>Payment Status</th>
                        <th>Total Payment</th>
                        <th>Payer Info</th>
                        <th>Receive Info</th>
                        <th>Shipping Address</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $row_count = 1;
                    foreach ($orders as $order): ?>
                        <tr>
                            <td><?= $row_count++; ?></td>
                            <td><?= htmlspecialchars($order['order_number']); ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d H:i:s', strtotime($order['placed_on']))); ?></td>
                            <td><?= htmlspecialchars(strtoupper($order['method'])); ?></td>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
                                    <select name="payment_status" onchange="this.form.submit()">
                                        <option value="To Receive" <?= ($order['payment_status'] === 'To Receive') ? 'selected' : ''; ?>>To Receive</option>
                                        <option value="Completed" <?= ($order['payment_status'] === 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                    </select>
                                    <input type="hidden" name="update_order" value="1">
                                </form>
                            </td>
                            <td>RM <?= htmlspecialchars(number_format($order['total_price'], 2)); ?></td>
                            <td>
                                <p><?= htmlspecialchars($order['payer_fullname']); ?></p>
                                <p><?= htmlspecialchars($order['payer_email']); ?></p>
                                <p><?= htmlspecialchars($order['payer_phone']); ?></p>
                            </td>
                            <td>
                                <p><?= htmlspecialchars($order['receiver_fullname']); ?></p>
                            </td>
                            <td>
                                <p><?= htmlspecialchars($order['shipping_address_line']); ?></p>
                                <p><?= htmlspecialchars($order['shipping_city'] . ', ' . $order['shipping_post_code'] . ', ' . $order['shipping_state']); ?></p>
                            </td>
                            <td>
                                <a href="admin_receipt.php?order_id=<?= $order['id']; ?>&order_number=<?= htmlspecialchars($order['order_number']); ?>" class="btn">
                                    View Receipt
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="assets/js/admin_category.js"></script>
</body>

</html>