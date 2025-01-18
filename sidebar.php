<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>sidebar</title>
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>
    <section id="sidebar">
        <a href="#" class="brand">
            <i class='bx bx-smile'></i>
            <span class="text">Admin Dashboard</span>
        </a>
        <ul class="side-menu top">
            <li class="active">
                <a href="#">
                    <i class="bx bxs-shopping-bag-alt"></i>
                    <span class="text">My Store</span>
                </a>
            </li>
            <li>
                <a href="admin_product.php">
                    <i class='bx bxs-data'></i>
                    <span class="text">Product</span>
                </a>
            </li>
            <li class="has-dropdown">
                <a href="javascript:void(0)" onclick="toggleDropdown(this)">
                    <i class='bx bx-category'></i>
                    <span class="text">Category</span>
                </a>
                <div class="dropdown">
                    <a href="admin_category.php">Category</a>
                    <a href="admin_brand.html">Brand</a>
                    <a href="admin_color.html">Color</a>
                    <a href="admin_size.html">Size</a>
                </div>
            </li>
            <li>
                <a href="admin_order.html">
                    <i class="bx bxs-doughnut-chart"></i>

                    <a href="admin_order.php">
                        <i class="bx bxs-doughnut-chart"></i>
                        <span class="text">Order</span>
                    </a>
            </li>
            <li>
                <a href="admin_total_sale.php">
                    <i class='bx bx-dollar'></i>
                    <span class="text">Total Sale</span>
                </a>
            </li>
            <li>
                <a href="admin_payment.php">
                    <i class="bx bxs-wallet"></i>
                    <span class="text">Payment</span>
                </a>
            </li>
            <li>
                <a href="admin_message.php">
                    <i class="bx bxs-message-dots"></i>
                    <span class="text">Message</span>
                </a>
            </li>
            <li>
                <a href="admin_user.php">
                    <i class="bx bxs-user"></i>
                    <span class="text">User</span>
                </a>
            </li>
            <li>
                <a href="admin_list.php">
                    <i class='bx bxs-group'></i>
                    <span class="text">Admin</span>
                </a>
            </li>
            <li>
                <a href="admin_profile.php">
                    <i class='bx bx-user-circle'></i>
                    <span class="text">My Profile</span>
                </a>
            </li>
            <li>
                <a href="#" class="logout">
                    <i class='bx bx-log-out'></i>
                    <span class="text">Log Out</span>
                </a>
            </li>
        </ul>
    </section>
    <script src="path/to/sidebar.js"></script>
    <script>
        // JavaScript for dropdown toggle
        function toggleDropdown(element) {
            const parentLi = element.parentElement;
            parentLi.classList.toggle('active');
        }
    </script>

</body>

</html>