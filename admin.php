<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ALL SHOES ONLINE SHOP ADMIN PANEL</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="asadminpanel.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <section id="content">
        <nav>
            <i class='bx bx-menu-alt-left'></i>
            <a href="#" class="nav-link">Categories</a>
            <form action="#">
                <div class="form-input">
                    <input type="search" placeholder="Search...">
                    <button type="submit" class="search-btn">
                        <i class='bx bx-search'></i>
                    </button>
                </div>
            </form>
        </nav>

        <store>
            <div class="head-title">
                <div class="left">
                    <h1>MY STORE</h1>
                </div>
            </div>

            <ul class="box-info">
                <li>
                    <a href="admin_order.html">
                        <i class="bx bxs-doughnut-chart"></i>
                        <span class="totalorder">
                            <p>Total Order</p>
                        </span>
                    </a>
                </li>
                <!-- Add other sections here -->
            </ul>
        </store>
    </section>

    <script src="asadminpanel.js"></script>
</body>
</html>
