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





// Fetch total number of unique products
$stmt = $conn->prepare("SELECT COUNT(*) AS total_products FROM products");
$stmt->execute();
$total_products = $stmt->fetchColumn();

// Fetch total stock from product_variants
$stmt = $conn->prepare("SELECT SUM(stock) AS total_stock FROM product_variants");
$stmt->execute();
$total_stock = $stmt->fetchColumn();

// Fetch total number of orders
$stmt = $conn->prepare("SELECT COUNT(*) AS total_orders FROM orders");
$stmt->execute();
$total_orders = $stmt->fetchColumn();

// Fetch total number of messages
$stmt = $conn->prepare("SELECT COUNT(*) AS total_messages FROM messages");
$stmt->execute();
$total_messages = $stmt->fetchColumn();

//Fetch total number of customers(users)
$stmt = $conn->prepare("SELECT COUNT(*) AS total_users FROM users");
$stmt->execute();
$total_users = $stmt->fetchColumn();


$recent_orders = [];
$stmt = $conn->prepare("SELECT o.id, o.order_number, o.total_price, o.placed_on, o.payment_status 
                         FROM orders o 
                         ORDER BY o.placed_on DESC LIMIT 5");
$stmt->execute();
$recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch daily sales data for the last 30 days
$sales_data = [];
$stmt = $conn->prepare("SELECT DATE(placed_on) AS order_date, SUM(total_price) AS total_sales 
                         FROM orders 
                         WHERE placed_on >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                         GROUP BY order_date 
                         ORDER BY order_date");
$stmt->execute();
$sales_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($sales_data)) {
    echo "No sales data found for the last 30 days.";
}

// Prepare sales data for the chart
$sales_labels = [];
$sales_values = [];
foreach ($sales_data as $data) {
    $sales_labels[] = $data['order_date'];
    $sales_values[] = (float)$data['total_sales'];
}

// Fetch daily new users for the last 30 days
$user_growth_data = [];
$stmt = $conn->prepare("SELECT DATE(created_at) AS registration_date, COUNT(*) AS new_users 
                         FROM users 
                         WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                         GROUP BY registration_date 
                         ORDER BY registration_date");
$stmt->execute();
$user_growth_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare user growth data for the chart
$user_growth_labels = [];
$user_growth_values = [];
foreach ($user_growth_data as $data) {
    $user_growth_labels[] = $data['registration_date'];
    $user_growth_values[] = (int)$data['new_users'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="assets/css/admin_dashboard.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700,900" rel="stylesheet">
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
        <h1>Admin Dashboard</h1>
        <div class="dashboard-cards">
            <div class="card">
                <h2>Total Products</h2>
                <p><?= htmlspecialchars($total_products); ?></p>
            </div>
            <div class="card">
                <h2>Total Stock</h2>
                <p><?= htmlspecialchars($total_stock); ?></p>
            </div>
            <div class="card">
                <h2>Total Orders</h2>
                <p><?= htmlspecialchars($total_orders); ?></p>
            </div>
            <div class="card">
                <h2>Total Customers</h2>
                <p><?= htmlspecialchars($total_users); ?></p>
            </div>
            <div class="card">
                <h2>Total Messages</h2>
                <p><?= htmlspecialchars($total_messages); ?></p>
            </div>
        </div>

        <div class="charts">
            <div class="chart">
                <h2>Sales Overview</h2>
                <canvas id="salesChart"></canvas>
            </div>
            <div class="chart">
                <h2>User Growth</h2>
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>

        <div class="recent-orders">
            <h2>Recent Orders</h2>
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order Number</th>
                        <th>Total Payment</th>
                        <th>Payment Status</th>
                        <th>Order Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_orders as $recent_order): ?>
                        <tr>
                            <td><?= htmlspecialchars($recent_order['order_number']); ?></td>
                            <td>RM <?= htmlspecialchars(number_format($recent_order['total_price'], 2)); ?></td>
                            <td>
                                <span class="status <?= strtolower(str_replace(' ', '_', $recent_order['payment_status'])); ?>">
                                    <?= htmlspecialchars($recent_order['payment_status']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars(date('Y-m-d H:i:s', strtotime($recent_order['placed_on']))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Sales Overview Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($sales_labels); ?>, // Use PHP to output the labels
                datasets: [{
                    label: 'Sales',
                    data: <?= json_encode($sales_values); ?>, // Use PHP to output the sales data
                    borderColor: 'rgba(39, 174, 96, 1)',
                    backgroundColor: 'rgba(39, 174, 96, 0.2)',
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // User Growth Chart
        const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
        const userGrowthChart = new Chart(userGrowthCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($user_growth_labels); ?>, // Use PHP to output the labels
                datasets: [{
                    label: 'New Users',
                    data: <?= json_encode($user_growth_values); ?>, // Use PHP to output the user growth data
                    backgroundColor: 'rgba(254, 76, 80, 0.6)',
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>

</html>