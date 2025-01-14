<?php

include 'connect.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cardholderName = $_POST['cardholder-name'];
    $cardNumber = $_POST['card-number'];
    $paymentMethod = $_POST['payment_method'];

    $cardType = '';
    if (preg_match('/^4/', $cardNumber)) {
        $cardType = 'visa';
    } elseif (preg_match('/^5/', $cardNumber)) {
        $cardType = 'mastercard';
    } else {
        // Handle invalid card type
        die('Invalid card number.');
    }

    // Mask the card number for display
    $maskedCardNumber = '**** **** **** ' . substr($cardNumber, -4); // Show last 4 digits
}

// Fetch user details
$stmt = $conn->prepare("SELECT fullname, email FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch cart items
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
    <title>Payment Confirmation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/cimb_approve.css">
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo-container">
                <?php if ($cardType === 'visa'): ?>
                    <img class="visa-logo" src="assets/image/visa.png" alt="Visa Logo">
                <?php elseif ($cardType === 'mastercard'): ?>
                    <img class="mastercard-logo" src="assets/image/mastercard.png" alt="Mastercard Logo">
                <?php else: ?>
                    <h2>No Card Selected</h2>
                <?php endif; ?>
            </div>
        </div>
        <div class="title-container">
            <div class="title">Debit Card Payment</div>
            <div class="transaction-details">
                <p class="transaction-title">Transaction Amount: MYR <?php echo number_format($total_price, 2); ?></p>
                <p><strong>Fee Amount:</strong> MYR 0.00</p>
            </div>
        </div>
        <div class="section">
            <div class="section-title">Review Payment Details</div>
            <div class="details">
                <p><span id="payment-date"></span></p>
                <p><strong>Merchant Name:</strong> STEP SDN BHD</ p>
            </div>
        </div>
        <div class="section">
            <div class="section-title">From</div>
            <div class="details">
                <p><strong>Debit Card No:</strong> <?php echo $maskedCardNumber; ?></p>
            </div>
        </div>
        <div class="section">
            <div class="section-title">Total</div>
            <div class="transaction-amount">
                <p class="amount">MYR <?php echo number_format($total_price, 2); ?></p>
            </div>
        </div>
        <form action="process_order.php" method="POST">
            <input type="hidden" name="method" value="<?php echo htmlspecialchars($paymentMethod); ?>">
            <div class="actions">
                <button type="button" class="cancel-btn" onclick="window.history.back();">Cancel</button>
                <button type="submit" class="proceed-btn">Proceed</button>
            </div>
        </form>
    </div>

    <script src="assets/js/cimb_approve.js"></script>
</body>

</html>