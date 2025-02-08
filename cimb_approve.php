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

$transaction_id = uniqid('FPX-', true);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FPX Payment</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/cimb_approve.css">
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo-container">
                <img class="cimb-logo" src="assets/image/cimb_logo.png" alt="CIMB Bank">
                <img class="fpx-logo" src="assets/image/fpx.png" alt="FPX">
            </div>
        </div>
        <div class="title-container">
            <div class="title">FPX Payment</div>
            <div class="transaction-details">
                <p class="transaction-title">Transaction Amount: MYR <?php echo number_format($total_price, 2); ?></p>
                <p><strong>Fee Amount:</strong> MYR 0.00</p>
            </div>
        </div>
        <div class="section">
            <div class="section-title">Review Payment Details</div>
            <div class="details">
                <p><span id="payment-date"></span></p>
                <p><strong>Merchant Name:</strong> STEP SDN BHD</p>
                <p><strong>FPX Transaction ID:</strong> <span id="transaction-id"><?php echo $transaction_id; ?></span></p>
            </div>
        </div>
        <div class="section">
            <div class="section-title">From</div>
            <div class="details">
                <p><strong>Account:</strong> BSA-i FEE - STMT.......</p>
            </div>
        </div>
        <div class="section">
            <div class="section-title">Total</div>
            <div class="transaction-amount">
                <p class="amount">MYR <?php echo number_format($total_price, 2); ?></p>
            </div>
        </div>
        <form action="process_order.php" method="POST">
            <input type="hidden" name="method" value="FPX">
            <div class="actions">
                <button class="cancel-btn">Cancel</button>
                <button class="proceed-btn">Proceed</button>
            </div>
        </form>
    </div>

    <script src="assets/js/cimb_approve.js"></script>
</body>

</html>