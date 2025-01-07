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

//Fetch user address
$stmt = $conn->prepare("SELECT * FROM user_addresses WHERE user_id = :user_id LIMIT 1");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

$address = $stmt->fetch(PDO::FETCH_ASSOC);

if ($address) {
    $first_name = $address['first_name'] ?? 'N/A';
    $last_name = $address['last_name'] ?? 'N/A';
    $address_line1 = $address['address_line1'] ?? 'N/A';
    $address_line2 = $address['address_line2'] ? ', ' . $address['address_line2'] : '';
    $city = $address['city'] ?? 'N/A';
    $zip_code = $address['zip_code'] ?? 'N/A';
    $state = $address['state'] ?? 'N/A';

    // Combine address lines
    $full_address = trim($address_line1 . $address_line2);
} else {
    $first_name = 'N/A';
    $last_name = 'N/A';
    $full_address = 'No address found';
    $city = 'N/A';
    $zip_code = 'N/A';
    $state = 'N/A';
}




?>

<link rel="stylesheet" type="text/css" href="assets/css/checkout.css">


<div class="card-body">
    <div class="row">
        <div class="col-md-7">

            <div class="user-info border mb-3">
                <form>
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
                <form>
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Shipping Address</h4>
                            <div class="form-group">
                                <div class="shipping-details">
                                    <div class="shipping-address">
                                        <span><?php echo htmlspecialchars($first_name . ' ' . $last_name); ?></span>
                                        <span><?php echo htmlspecialchars($full_address); ?></span>
                                        <span><?php echo htmlspecialchars($city . ', ' . $state . ' ' . $zip_code); ?></span>
                                        <hr class="address-underline">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="left border">
                <h4>Payment</h4>
                <div class="row">
                    <div class="icons">
                        <img src="https://img.icons8.com/color/48/000000/visa.png" />
                        <img src="https://img.icons8.com/color/48/000000/mastercard-logo.png" />
                    </div>
                </div>
                <form>
                    <span>Cardholder's name:</span>
                    <input placeholder="Your Name">
                    <span>Card Number:</span>
                    <input
                        id="card-number"
                        type="text"
                        placeholder="0000 0000 0000 0000"
                        maxlength="19"
                        pattern="\d*">
                    <div class="row">
                        <div class="col-4"><span>Expiry date:</span>
                            <input placeholder="MM/YY">
                        </div>
                        <div class="col-4"><span>CVV:</span>
                            <input id="cvv">
                        </div>
                    </div>
                    <input type="checkbox" id="save_card" class="align-left">
                    <label for="save_card">Save card details to wallet</label>
                </form>
            </div>
        </div>
        <div class="col-md-5">
            <div class="right border">
                <h4>Order Summary</h4>
                <p>2 items</p>
                <div class="row item">
                    <div class="col-4 align-self-center"><img class="img-fluid" src="https://i.imgur.com/79M6pU0.png"></div>
                    <div class="col-8">
                        <div class="row"><b>$ 26.99</b></div>
                        <div class="row text-muted">Be Legandary Lipstick-Nude rose</div>
                        <div class="row">Qty:1</div>
                    </div>
                </div>
                <div class="row item">
                    <div class="col-4 align-self-center"><img class="img-fluid" src="https://i.imgur.com/Ew8NzKr.jpg"></div>
                    <div class="col-8">
                        <div class="row"><b>$ 19.99</b></div>
                        <div class="row text-muted">Be Legandary Lipstick-Sheer Navy Cream</div>
                        <div class="row">Qty:1</div>
                    </div>
                </div>
                <hr>
                <div class="row lower">
                    <div class="col text-left">Subtotal</div>
                    <div class="col text-right">$ 46.98</div>
                </div>
                <div class="row lower">
                    <div class="col text-left">Delivery</div>
                    <div class="col text-right">Free</div>
                </div>
                <div class="row lower">
                    <div class="col text-left"><b>Total to pay</b></div>
                    <div class="col text-right"><b>$ 46.98</b></div>
                </div>
                <div class="row lower">
                    <div class="col text-left"><a href="#"><u>Add promo code</u></a></div>
                </div>
                <button class="btn">Place order</button>
                <p class="text-muted text-center">Complimentary Shipping & Returns</p>
            </div>
        </div>
    </div>
</div>

<div>
</div>
</div>

<script src="assets/js/jquery-3.2.1.min.js"></script>
<script src="assets/js/popper.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/isotope.pkgd.min.js"></script>
<script src="assets/js/owl.carousel.js"></script>
<script src="assets/js/easing.js"></script>
<script src="assets/js/custom.js"></script>
<script src="assets/js/checkout.js"></script>
</body>

</html>