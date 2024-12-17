<?php
include 'connect.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$cart_query = "SELECT c.*, p.id AS product_id, 
               (SELECT image1_display 
                FROM product_variants pv 
                WHERE pv.product_id = p.id 
                  AND pv.size = c.size 
                  AND (pv.color = c.color OR c.color = 'Unknown')) AS image1_display
               FROM cart c
               JOIN products p ON c.pid = p.id
               WHERE c.user_id = ?";
$stmt = $conn->prepare($cart_query);
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total
$total_price = 0;
$total_items = count($cart_items);

include 'header.php';
?>

<link rel="stylesheet" type="text/css" href="assets/css/add_to_cart.css">


<section class="h-100 gradient-custom">
    <div class="container py-5">
        <div class="row d-flex justify-content-center my-4">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header py-3">
                        <h5 class="mb-0" id="cart-items-count">Cart - <?php echo $total_items; ?> items</h5>
                    </div>
                    <div class="card-body" id="cart-items-container">
                        <?php foreach ($cart_items as $item):
                            $item_total = $item['price'] * $item['quantity'];
                            $total_price += $item_total;
                        ?>


                            <!-- Single item -->
                            <div class="row">
                                <div class="col-lg-3 col-md-12 mb-4 mb-lg-0">
                                    <!-- Image -->
                                    <div class="bg-image hover-overlay hover-zoom ripple rounded" data-mdb-ripple-color="light">
                                        <img src="<?php echo htmlspecialchars($item['image1_display']); ?>"
                                            class="w-100" alt="<?php echo htmlspecialchars($item['name']); ?>" />
                                        <a href="product.php?product_id=<?php echo $item['pid']; ?>">
                                            <div class="mask" style="background-color: rgba(251, 251, 251, 0.2)"></div>
                                        </a>
                                    </div>
                                    <!-- Image -->
                                </div>

                                <div class="col-lg-5 col-md-6 mb-4 mb-lg-0">
                                    <!-- Data -->
                                    <p><strong><?php echo htmlspecialchars($item['name']); ?></strong></p>
                                    <p>Color: <?php echo htmlspecialchars($item['color']); ?></p>
                                    <p>Size: <?php echo htmlspecialchars($item['size']); ?></p>


                                    <button type="button" class="btn btn-primary btn-sm me-1 mb-2 remove-item"
                                        data-cart-id="<?php echo $item['id']; ?>"
                                        title="Remove item">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm mb-2"
                                        title="Move to the wish list">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                    <!-- Data -->
                                </div>

                                <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                                    <!-- Quantity -->
                                    <div class="d-flex mb-4" style="max-width: 300px">
                                        <button data-mdb-button-init data-mdb-ripple-init class="btn btn-primary px-3 me-2"
                                            data-cart-id="<?php echo $item['id']; ?>">
                                            <i class="fas fa-minus"></i>
                                        </button>

                                        <div class="form-outline">
                                            <input type="number"
                                                class="form-control quantity-input"
                                                min="1"
                                                value="<?php echo $item['quantity']; ?>"
                                                data-cart-id="<?php echo $item['id']; ?>"
                                                data-price="<?php echo $item['price']; ?>" />
                                            <label class="form-label">Quantity</label>
                                        </div>

                                        <button class="btn btn-primary px-3 ms-2 increase-quantity"
                                            data-cart-id="<?php echo $item['id']; ?>">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <!-- Quantity -->

                                    <!-- Price -->
                                    <p class="text-start text-md-center item-total-price">
                                        <strong>RM<?php echo number_format($item_total, 2); ?></strong>
                                    </p>
                                    <!-- Price -->
                                </div>
                            </div>
                            <!-- Single item -->

                            <hr class="my-4" />
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header py-3">
                        <h5 class="mb-0">Summary</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li
                                class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 pb-0">
                                Products
                                <span id="subtotal">RM<?php echo number_format($total_price, 2); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                Shipping
                                <span>Gratis</span>
                            </li>
                            <li
                                class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 mb-3">
                                <div>
                                    <strong>Total amount</strong>
                                    <strong>
                                        <p class="mb-0">(including VAT)</p>
                                    </strong>
                                </div>
                                <span id="total-amount"><strong>RM<?php echo number_format($total_price, 2); ?></strong></span>
                            </li>
                        </ul>

                        <button type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-lg btn-block">
                            Go to checkout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->

<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div
                    class="footer_nav_container d-flex flex-sm-row flex-column align-items-center justify-content-lg-start justify-content-center text-center">
                    <ul class="footer_nav">
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">FAQs</a></li>
                        <li><a href="contact.html">Contact us</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-6">
                <div
                    class="footer_social d-flex flex-row align-items-center justify-content-lg-end justify-content-center">
                    <ul>
                        <li><a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                        <li><a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                        <li><a href="#"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                        <li><a href="#"><i class="fa fa-skype" aria-hidden="true"></i></a></li>
                        <li><a href="#"><i class="fa fa-pinterest" aria-hidden="true"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="footer_nav_container">
                    <div class="cr">©2018 All Rights Reserverd. Made with <i class="fas fa-heart-o"
                            aria-hidden="true"></i>
                        aria-hidden="true"></i> by <a href="#">Colorlib</a> &amp; distributed by <a
                            href="https://themewagon.com">ThemeWagon</a></div>
                </div>
            </div>
        </div>
    </div>

</footer>

</div>

<script src="assets/js/jquery-3.2.1.min.js"></script>
<script src="assets/js/popper.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/isotope.pkgd.min.js"></script>
<script src="assets/js/owl.carousel.js"></script>
<script src="assets/js/easing.js"></script>
<script src="assets/js/custom.js"></script>
<script src="assets/js/add_to_cart.js"></script>
</body>

</html>