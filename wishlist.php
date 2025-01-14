<?php

include 'connect.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

include 'header.php';

$wishlist_query = "
    SELECT
        w.id as wishlist_entry_id,
        w.product_id,
        w.color,
        p.name,
        p.category,
        p.brand,
        pv.price,
        pv.image1_display,
        pv.size
    FROM wishlist w
    JOIN products p ON w.product_id = p.id
    JOIN product_variants pv ON w.product_id = pv.product_id AND w.color = pv.color
    WHERE w.user_id = :user_id
    ORDER BY w.id DESC";

$stmt = $conn->prepare($wishlist_query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

$all_wishlist_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

//Prepare unique products
$unique_products = [];
$seen_combinations = [];

foreach ($all_wishlist_products as $product) {
    $unique_key = $product['product_id'] . '_' . $product['color'];

    if (!isset($seen_combinations[$unique_key])) {
        $unique_products[] = $product;
        $seen_combinations[$unique_key] = true;
    }
}

$products_per_page = 6;
$total_products = count($unique_products);
$total_pages = ceil($total_products / $products_per_page);

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, min($page, $total_pages));

$offset = ($page - 1) * $products_per_page;

//Slice products for current page
$current_page_products = array_slice($unique_products, $offset, $products_per_page);

function getAvailableSizes($conn, $productId)
{
    try {
        $sizes_query = "SELECT DISTINCT size FROM product_variants WHERE product_id = ? AND stock > 0";
        $stmt = $conn->prepare($sizes_query);
        $stmt->execute([$productId]);
        $sizes = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $sizes;
    } catch (PDOException $e) {
        error_log("Error fetching sizes: " . $e->getMessage());
        return [];
    }
}


?>


<div class="super_container">

    <!-- Main Navigation -->

    <div class="container product_section_container">
        <div class="row">
            <div class="col product_section clearfix">


                <!-- Breadcrumbs -->
                <div class="breadcrumbs d-flex flex-row align-items-center">
                    <ul>
                        <li><a href="home.php">Home</a></li>
                        <li><a href="wishlist.php"><i class="fa fa-angle-right" aria-hidden="true"></i>Wishlist</a></li>
                    </ul>
                </div>


                <!-- Sidebar -->

                <div class="sidebar">
                    <div class="sidebar_section">
                        <div class="sidebar_title">
                            <h5>My Wishlist</h5>
                        </div>
                        <ul class="sidebar_categories">

                        </ul>
                    </div>


                    <div class="main_content">

                        <!-- Products -->

                        <div class="products_iso">
                            <div class="row">
                                <div class="col">

                                    <!-- Product Sorting -->

                                    <div class="product_sorting_container product_sorting_container_top">
                                        <ul class="product_sorting">
                                            <li>
                                                <span class="type_sorting_text">Default Sorting</span>
                                                <i class="fa fa-angle-down"></i>
                                                <ul class="sorting_type">
                                                    <li class="type_sorting_btn"
                                                        data-isotope-option='{ "sortBy": "original-order" }'>
                                                        <span>Default Sorting</span>
                                                    </li>
                                                    <li class="type_sorting_btn"
                                                        data-isotope-option='{ "sortBy": "price" }'><span>Price</span>
                                                    </li>
                                                    <li class="type_sorting_btn"
                                                        data-isotope-option='{ "sortBy": "name" }'><span>Product
                                                            Name</span></li>
                                                </ul>
                                            </li>
                                            <li>
                                                <span>Show</span>
                                                <span class="num_sorting_text">6</span>
                                                <i class="fa fa-angle-down"></i>
                                                <ul class="sorting_num">
                                                    <li class="num_sorting_btn"><span>6</span></li>
                                                    <li class="num_sorting_btn"><span>12</span></li>
                                                </ul>
                                            </li>
                                        </ul>
                                        <div class="pages d-flex flex-row align-items-center">
                                            <div class="page_current">
                                                <span><?php echo $page; ?></span>
                                                <ul class="page_selection">
                                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                        <li><a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                                                    <?php endfor; ?>
                                                </ul>
                                            </div>
                                            <div class="page_total"><span>of</span> <?php echo $total_pages; ?></div>
                                            <div id="next_page" class="page_next">
                                                <?php if ($page < $total_pages): ?>
                                                    <a href="?page=<?php echo $page + 1; ?>"><i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
                                                <?php endif; ?>
                                            </div>
                                            <div id="previous_page" class="page_previous">
                                                <?php if ($page > 1): ?>
                                                    <a href="?page=<?php echo $page - 1; ?>"><i class="fa fa-long-arrow-left" aria-hidden="true"></i></a>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Product Grid -->
                                        <div class="product-grid" data-isotope='{ "itemSelector": ".product-item", "layoutMode": "fitRows" }'>
                                            <?php foreach ($current_page_products as $product_details): ?>
                                                <?php
                                                //Get available sizes for this product
                                                $available_sizes = getAvailableSizes($conn, $product_details['product_id']);
                                                $available_sizes_json = json_encode($available_sizes);
                                                ?>
                                                <div class="product-item" data-product-id="<?php echo htmlspecialchars($product_details['product_id']); ?>" data-color="<?php echo htmlspecialchars($product_details['color']); ?>" data-available-sizes="<?php echo htmlspecialchars($available_sizes_json); ?>">
                                                    <div class="product product_filter">
                                                        <div class="product_image">
                                                            <a href="product.php?product_id=<?php echo htmlspecialchars($product_details['product_id']); ?>" class="main-product-link">
                                                                <img src="<?php echo htmlspecialchars($product_details['image1_display']); ?>" alt="" class="main-product-image">
                                                            </a>
                                                        </div>
                                                        <div class="cancel">
                                                            <i class="fas fa-trash cancel-icon" data-product-id="<?php echo htmlspecialchars($product_details['product_id']); ?>" data-color="<?php echo htmlspecialchars($product_details['color']); ?>"></i>
                                                        </div>
                                                        <div class="product_info">
                                                            <h6 class="product_name">
                                                                <a href="product.php?product_id=<?php echo htmlspecialchars($sproduct_details['product_id']); ?>">
                                                                    <?php echo htmlspecialchars($product_details['name']); ?>
                                                                </a>
                                                            </h6>
                                                            <div class="product_price">
                                                                RM<?php echo htmlspecialchars($product_details['price']); ?>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="red_button add_to_cart_button quick-add-button"><a href="#">Quick Add <i class="fa fa-plus quick-add-icon"></i></a></div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <!-- Product Sorting -->

                                        <div class="product_sorting_container product_sorting_container_bottom clearfix">
                                            <ul class="product_sorting">
                                                <li>
                                                    <span>Show:</span>
                                                    <span class="num_sorting_text">04</span>
                                                    <i class="fa fa-angle-down"></i>
                                                    <ul class="sorting_num">
                                                        <li class="num_sorting_btn"><span>01</span></li>
                                                        <li class="num_sorting_btn"><span>02</span></li>
                                                        <li class="num_sorting_btn"><span>03</span></li>
                                                        <li class="num_sorting_btn"><span>04</span></li>
                                                    </ul>
                                                </li>
                                            </ul>
                                            <span class="showing_results">
                                                Showing <?php echo ($offset + 1); ?>–<?php echo min($offset + $products_per_page, $total_products); ?> of <?php echo $total_products; ?> results
                                            </span>

                                            <div class="pages d-flex flex-row align-items-center">
                                                <div class="page_current">
                                                    <span><?php echo $page; ?></span>
                                                    <ul class="page_selection">
                                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                            <li><a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                                                        <?php endfor; ?>
                                                    </ul>
                                                </div>
                                                <div class="page_total"><span>of</span><?php echo $total_pages; ?></div>
                                                <div id="next_page" class="page_next">
                                                    <?php if ($page < $total_pages): ?>
                                                        <a href="?page=<?php echo $page + 1; ?>"><i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
                                                    <?php endif; ?>
                                                </div>
                                                <div id="previous_page" class="page_previous">
                                                    <?php if ($page > 1): ?>
                                                        <a href="?page=<?php echo $page - 1; ?>"><i class="fa fa-long-arrow-left" aria-hidden="true"></i></a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

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
                                            <div class="cr">©2018 All Rights Reserverd. Made with <i class="fas fa-heart-o" aria-hidden="true"></i>
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
                    <script src="assets/js/wishlist.js"></script>
                    </body>

                    </html>