<?php

include 'connect.php';
session_start();


$error_messages = '';


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

//Fetch user information
$stmt = $conn->prepare("SELECT id,fullname, email, phone FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

$user_fullname = $user['fullname'] ?? '';
$email = $user['email'] ?? '';
$phone = $user['phone'] ?? '';




// Fetch user orders
$stmt = $conn->prepare("SELECT id, user_id, name, number, email, method, total_products, total_price, placed_on, payment_status, shipping_fullname, shipping_address_line, shipping_city, shipping_post_code, shipping_state, order_number FROM orders WHERE user_id = :user_id ORDER BY placed_on DESC");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$current_page = basename($_SERVER['PHP_SELF']);


?>


<link rel="stylesheet" type="text/css" href="assets/css/profiles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<body>

    <div class="container product_section_container">
        <div class="row">
            <div class="col product_section clearfix">


                <!-- Breadcrumbs -->
                <div class="breadcrumbs d-flex flex-row align-items-center">
                    <ul>
                        <li><a href="home.php">Home</a></li>
                        <li><a href="profiles.php"><i class="fa fa-angle-right" aria-hidden="true"></i>Profile</a></li>
                        <li><a href="order_history.php"><i class="fa fa-angle-right" aria-hidden="true"></i>Order History</a></li>
                    </ul>
                </div>


                <!-- Sidebar -->

                <div class="sidebar">
                    <div class="sidebar_section">
                        <div class="sidebar_title">
                            <h5>My Account</h5>
                        </div>
                    </div>


                    <div class="section">
                        <div class="profile">
                            <div class="profile-header">
                                <div class="profile-text-container">
                                    <h1 class="profile-title"><?php echo htmlspecialchars($user_fullname); ?></h1>
                                    <p class="profile-email"><?php echo htmlspecialchars($email); ?></p>
                                </div>
                            </div>

                            <div class="menu">
                                <a href="profiles.php" class="menu-link <?php echo ($current_page == 'profiles.php') ? 'active' : ''; ?>">
                                    <i class="fa-solid fa-circle-user menu-icon"></i>Account</a>
                                <a href="user_address.php" class="menu-link <?php echo ($current_page == 'user_address.php') ? 'active' : ''; ?>">
                                    <i class="fa-solid fa-bell menu-icon"></i>Shipping Address
                                </a>
                                <a href="order_history.php" class="menu-link <?php echo ($current_page == 'order_history.php') ? 'active' : ''; ?>">
                                    <i class="fa-solid fa-gear menu-icon"></i>Order history
                                </a>
                                <a href="logout.php" onclick="return confirm('Are you sure you want to log out?');" class="menu-link"><i class="fas fa-sign-out-alt"></i>Logout</a>
                            </div>
                        </div>

                        <div class="account">
                            <div class="account-header">
                                <h1 class="account-title">My Orders</h1>
                            </div>

                            <div class="order_section">
                                <?php if (empty($orders)): ?>
                                    <p>No orders found.</p>
                                <?php else: ?>
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Order Number</th>
                                                <th>Order Date</th>
                                                <th>Payment Method</th>
                                                <th>Payment Status</th>
                                                <th>Total Payment</th>
                                                <th>Order Details</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($orders as $order): ?>
                                                <tr class="<?php echo ($order['payment_status'] === 'Completed') ? 'completed-order' : ''; ?>">
                                                    <td><?php echo htmlspecialchars($order['order_number']); ?></td>
                                                    <td><?php echo htmlspecialchars(date('Y-m-d H:i:s', strtotime($order['placed_on']))); ?></td>
                                                    <td><?php echo htmlspecialchars(strtoupper($order['method'])); ?></td>
                                                    <td>
                                                        <?php if ($order['payment_status'] === 'Completed'): ?>
                                                            Completed
                                                            <i class="fas fa-check-circle"></i>
                                                        <?php else: ?>
                                                            <?php echo htmlspecialchars($order['payment_status']); ?>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>RM <?php echo htmlspecialchars(number_format($order['total_price'], 2)); ?></td>
                                                    <td>
                                                        <a href="receipt.php?order_id=<?php echo $order['id']; ?>&order_number=<?php echo htmlspecialchars($order['order_number']); ?>"
                                                            class="btn-view-receipt">
                                                            View receipt
                                                            <i class="fas fa-receipt"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/profiles.js"></script>

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