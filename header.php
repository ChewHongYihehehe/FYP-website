<?php
// Ensure session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


// Fetch existing navbar items
$stmt = $conn->prepare("SELECT * FROM navbar_menu ORDER BY position ASC");
$stmt->execute();
$navbar_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize cart count to 0
$cart_count = 0;

// Check if user is logged in before querying cart
if (isset($_SESSION['user_id'])) {
    try {
        $user_id = $_SESSION['user_id'];
        $cart_count_query = "SELECT COUNT(*) as cart_count FROM cart WHERE user_id = ?";
        $stmt = $conn->prepare($cart_count_query);
        $stmt->execute([$user_id]);
        $cart_count_result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Safely assign cart count
        $cart_count = $cart_count_result['cart_count'] ?? 0;
    } catch (PDOException $e) {
        // Log the error or handle it appropriately
        error_log("Error fetching cart count: " . $e->getMessage());
        $cart_count = 0;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Colo Shop</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Colo Shop Template">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="stylesheet" type="text/css" href="assets/css/owl.carousel.css">
    <link rel="stylesheet" type="text/css" href="assets/css/owl.theme.default.css">
    <link rel="stylesheet" type="text/css" href="assets/css/main_styles.css">
    <link rel="stylesheet" type="text/css" href="assets/css/responsive.css">
    <link rel="stylesheet" type="text/css" href="assets/css/wishlist.css">
</head>

<body>

    <div class="super_container">

        <!-- Header -->

        <header class="header trans_300">

            <!-- Top Navigation -->

            <div class="top_nav">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="top_nav_left">free shipping on all u.s orders over $50</div>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="top_nav_right">
                                <ul class="top_nav_menu">

                                    <!-- Currency / Language / My Account -->

                                    <li class="account">
                                        <a href="#">
                                            My Account
                                            <i class="fa fa-angle-down"></i>
                                        </a>
                                        <ul class="account_selection">
                                            <li><a href="login.php"><i class="fa fa-sign-in" aria-hidden="true"></i>Sign In</a>
                                            </li>
                                            <li><a href="register.php"><i class="fa fa-user-plus"
                                                        aria-hidden="true"></i>Register</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Navigation -->

            <div class="main_nav_container">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 text-right">
                            <div class="logo_container">
                                <a href="home.php">St<span>ep</span></a>
                            </div>
                            <nav class="navbar">
                                <ul class="navbar_menu">
                                    <?php foreach ($navbar_items as $item): ?>
                                        <li><a href="<?= htmlspecialchars($item['link']); ?>"><?= htmlspecialchars($item['title']); ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                                <ul class="navbar_user">
                                    <li><a href="wishlist.php"><i class="fas fa-heart" aria-hidden="true"></i></a></li>
                                    <li><a href="profiles.php"><i class="fa fa-user" aria-hidden="true"></i></a></li>
                                    <li class="checkout">
                                        <a href="add_to_cart.php" id="cart-icon">
                                            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                            <span class="cart-count"><?php echo $cart_count; ?></span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

        </header>