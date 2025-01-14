<?php

include 'connect.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

include 'header.php';


$stmt = $conn->prepare("SELECT fullname, email FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $fullname = $user['fullname'];
    $email = $user['email'];
} else {
    $fullname = 'Guest';
    $email = 'Not logged in';
}


// Check if there is a current shipping address
$stmt = $conn->prepare("SELECT * FROM user_addresses WHERE user_id = :user_id AND is_shipping_address = 1");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();


$current_shipping_address = $stmt->fetch(PDO::FETCH_ASSOC);

//Prepare shipping address variables for display
if ($current_shipping_address) {
    //If a current shipping address exists, use it
    $shipping_fullname = $current_shipping_address['fullname'] ?? 'N/A';
    $shipping_address_line1 = $current_shipping_address['address_line'] ?? 'N/A';
    $shipping_city = $current_shipping_address['city'] ?? 'N/A';
    $shipping_zip_code = $current_shipping_address['postcode'] ?? 'N/A';
    $shipping_state = $current_shipping_address['state'] ?? 'N/A';
    $shipping_full_address = trim($shipping_address_line1);
} else {
    //If no shipping address is set, use the default address
    $stmt = $conn->prepare("SELECT * FROM user_addresses WHERE user_id = :user_id AND is_default = 1");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $default_address = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($default_address) {
        //Set the default address as the shipping address in the database
        $stmt = $conn->prepare("UPDATE user_addresses SET is_shipping_address = 1 WHERE id = :address_id");
        $stmt->bindParam(':address_id', $default_address['id'], PDO::PARAM_INT);
        $stmt->execute();

        $current_shipping_address = $default_address;

        //Prepare shipping address variables for display
        $shipping_fullname = $current_shipping_address['fullname'] ?? 'N/A';
        $shipping_address_line1 = $current_shipping_address['address_line'] ?? 'N/A';
        $shipping_city = $current_shipping_address['city'] ?? 'N/A';
        $shipping_zip_code = $current_shipping_address['postcode'] ?? 'N/A';
        $shipping_state = $current_shipping_address['state'] ?? 'N/A';
        $shipping_full_address = trim($shipping_address_line1);
    } else {
        // If no address is set, set default values
        $shipping_fullname = 'N/A';
        $shipping_address_line1 = 'N/A';
        $shipping_city = 'N/A';
        $shipping_zip_code = 'N/A';
        $shipping_state = 'N/A';
        $shipping_full_address = 'N/A';
    }
}

// Handle address selection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_address_id'])) {
    $selected_address_id = $_POST['selected_address_id'];

    // Reset the previous shipping address
    $stmt = $conn->prepare("UPDATE user_addresses SET is_shipping_address = 0 WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // Set the selected address as the shipping address
    $stmt = $conn->prepare("UPDATE user_addresses SET is_shipping_address = 1 WHERE id = :address_id");
    $stmt->bindParam(':address_id', $selected_address_id, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch the newly selected address for display
    $stmt = $conn->prepare("SELECT * FROM user_addresses WHERE id = :address_id");
    $stmt->bindParam(':address_id', $selected_address_id, PDO::PARAM_INT);
    $stmt->execute();
    $current_shipping_address = $stmt->fetch(PDO::FETCH_ASSOC);

    // Prepare shipping address variables for display
    $shipping_fullname = $current_shipping_address['fullname'] ?? 'N/A';
    $shipping_address_line1 = $current_shipping_address['address_line'] ?? 'N/A';
    $shipping_city = $current_shipping_address['city'] ?? 'N/A';
    $shipping_zip_code = $current_shipping_address['postcode'] ?? 'N/A';
    $shipping_state = $current_shipping_address['state'] ?? 'N/A';
    $shipping_full_address = trim($shipping_address_line1);
}


//Fetch cart items
$stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total_price = 0;




?>

<link rel="stylesheet" type="text/css" href="assets/css/checkout.css">


<div class="card-body">
    <div class="row">
        <div class="col-md-7">

            <div class="user-info border mb-3">
                <form name="userInfoForm">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Account</h4>
                            <div class="form-group">
                                <div class="account-details">
                                    <div class="account-email">
                                        <span><?php echo htmlspecialchars($email); ?></span>
                                        <hr class="email-underline">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="shipping-info border mb-3">
                <form name="shippingAddressForm" method="POST">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Shipping Address</h4>
                            <div class="form-group">
                                <div class="shipping-details">
                                    <div class="shipping-address">
                                        <span><?php echo htmlspecialchars($shipping_fullname); ?></span>
                                        <span><?php echo htmlspecialchars($shipping_full_address); ?></span>
                                        <span><?php echo htmlspecialchars($shipping_city . ', ' . $shipping_state . ' ' . $shipping_zip_code); ?></span>
                                        <hr class="address-underline">
                                    </div>
                                </div>
                            </div>
                            <div id="showOtherAddresses" class="btn btn-link">Show Other Addresses</div>
                            <div id="otherAddresses" style="display: none;">

                                <h4 style="margin-top:30px;">Other Addresses</h4>
                                <?php
                                // Fetch other addresses that are not the current shipping address
                                $stmt = $conn->prepare("SELECT * FROM user_addresses WHERE user_id = :user_id AND is_shipping_address = 0");
                                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                                $stmt->execute();
                                $other_addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($other_addresses as $other_address): ?>
                                    <div class="shipping-address">
                                        <div style="display: flex; align-items: center;">
                                            <input type="radio" name="selected_address_id" id="address-<?php echo $other_address['id']; ?>" value="<?php echo $other_address['id']; ?>" class="address-radio" onchange="this.form.submit();">
                                            <label for="address-<?php echo $other_address['id']; ?>" style="margin-left: 10px; flex-grow: 1;">
                                                <div><?php echo htmlspecialchars($other_address['fullname']); ?> - </div>
                                                <div><?php echo htmlspecialchars($other_address['address_line']); ?></div>
                                                <div><?php echo htmlspecialchars($other_address['city'] . ', ' . $other_address['state'] . ' ' . $other_address['postcode']); ?></div>
                                            </label>
                                        </div>
                                        <hr class="address-underline">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                </form>
            </div>
        </div>

        <div class="left border">
            <h4>Select Payment Method</h4>
            <form id="payment-method-form" name="paymentMethodForm">
                <div class="payment-methods">
                    <div class="onlineBankMethod" id="online-bank">
                        <span>Online Bank</span>
                        <div class="icons_">
                            <img src="assets/image/fpx.png" alt="Online Bank" />
                        </div>
                    </div>

                    <div class="debitCardMethod" id="debit-card">
                        <span>Debit Card</span>
                        <div class="icons_">
                            <img src="https://img.icons8.com/color/48/000000/visa.png" />
                            <img src="https://img.icons8.com/color/48/000000/mastercard-logo.png" />
                        </div>
                    </div>
                </div>
            </form>

            <div id="payment-form" style="display: none;">
                <h4>Payment</h4>
                <div class="row">
                    <div class="icons">
                        <img src="https://img.icons8.com/color/48/000000/visa.png" />
                        <img src="https://img.icons8.com/color/48/000000/mastercard-logo.png" />
                    </div>
                </div>
                <form id="PaymentForm" method="POST" action="debit_process.php">
                    <span>Cardholder's name:</span>
                    <input id="cardholder-name" name="cardholder-name" placeholder="Your Name" required>
                    <span>Card Number:</span>
                    <input id="card-number" name="card-number" type="text" placeholder="0000 0000 0000 0000" maxlength="19" pattern="\d*" required>
                    <div class="row">
                        <div class="col-4">
                            <span>Expiry date:</span>
                            <input id="expiry-date" name="expiry-date" placeholder="MM/YY" required>
                        </div>
                        <div class="col-4">
                            <span>CVV:</span>
                            <input id="cvv" name="cvv" required>
                        </div>
                    </div>
                    <input type="hidden" name="payment_method" value="Debit Card"> <!-- Ensure this line is present -->
                    <button type="submit" class="btn" id="proceed-btn">Place Order</button>
                </form>
            </div>

            <div id="bank-selection" style="display: none;">
                <h4>Select Your Bank</h4>
                <div class="bank-options">
                    <input type="radio" name="bank" id="cimb-bank" value="Cimb Bank" class="bank-radio">
                    <label for="cimb-bank" class="bankMethod">
                        <span>CIMB BANK</span>
                        <div class="icons_">
                            <img src="assets/image/cimb.png" alt="CIMB BANK" />
                        </div>
                    </label>
                </div>
                <a href="cimb_login.php" class="btn" id="place-order-btn">Place order</a>

            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="right border">
            <h4>Order Summary</h4>
            <p><?php echo count($cart_items); ?> items</p>
            <?php foreach ($cart_items as $item):
                $total_price += $item['price'] * $item['quantity']; ?>
                <div class="row item">
                    <div class="col-4 align-self-center"><img class="img-fluid" src="<?php echo htmlspecialchars($item['image']); ?>"></div>
                    <div class="col-8">
                        <div class="row"><b><?php echo number_format($item['price'], 2); ?></b></div>
                        <div class="row text-muted"><?php echo htmlspecialchars($item['name']); ?></div>
                        <div class="row">Qty: <?php echo htmlspecialchars($item['quantity']); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
            <hr>
            <div class="row lower">
                <div class="col text-left">Subtotal</div>
                <div class="col text-right">$ <?php echo number_format($total_price, 2); ?></div>
            </div>
            <div class="row lower">
                <div class="col text-left">Delivery</div>
                <div class="col text-right">Free</div>
            </div>
            <div class="row lower">
                <div class="col text-left"><b>Total to pay</b></div>
                <div class="col text-right"><b>$ <?php echo number_format($total_price, 2); ?></b></div>
            </div>
            <div class="row lower">
                <div class="col text-left"><a href="#"><u>Add promo code</u></a></div>
            </div>
            <p class="text-muted text-center">Complimentary Shipping & Returns</p>
        </div>
    </div>
</div>
</div>

<div>
</div>
</div>


<script src="assets/js/checkout.js"></script>

</body>

</html>