<?php
include 'connect.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

include 'header.php';

// Fetch wishlist items for the logged-in user
$wishlist_query = "SELECT DISTINCT w.id AS wishlist_id, p.id AS product_id, p.name, p.category, p.brand, pv.color, pv.price, pv.image1_display 
                   FROM wishlist w 
                   JOIN products p ON w.product_id = p.id 
                   JOIN product_variants pv ON w.product_id = pv.product_id 
                   WHERE w.user_id = ?";
$stmt = $conn->prepare($wishlist_query);
$stmt->execute([$user_id]);
$wishlist_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle removal of an item from the wishlist
if (isset($_POST['remove_from_wishlist'])) {
    $wishlist_id = $_POST['wishlist_id'];
    $remove_query = "DELETE FROM wishlist WHERE id = ?";
    $remove_stmt = $conn->prepare($remove_query);
    $remove_stmt->execute([$wishlist_id]);
    header("Location: wishlist.php");
    exit();
}

function getAvailableSizes($conn, $productId)
{
    $sizes_query = "SELECT DISTINCT size FROM product_variants WHERE product_id = ? AND stock > 0";
    $stmt = $conn->prepare($sizes_query);
    $stmt->execute([$productId]);
    $sizes = $stmt->fetchAll(PDO::FETCH_COLUMN);
    return $sizes;
}
?>

<div class="super_container">
    <div class="wishlist">
        <div class="container">
            <div class="row">
                <div class="col text-center">
                    <div class="section_title wishlist_title">
                        <h2>My Wishlist</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <!-- Product Grid -->
                    <div class="product-grid" data-isotope='{ "itemSelector": ".product-item", "layoutMode": "fitRows" }'>
                        <?php foreach ($wishlist_items as $item): ?>
                            <div class="product-item" data-product-id="<?php echo htmlspecialchars($item['product_id']); ?>" data-available-sizes='<?php echo json_encode(getAvailableSizes($conn, $item['product_id'])); ?>'>
                                <div class="product product_filter">
                                    <div class="product_image">
                                        <a href="product.php?product_id=<?php echo htmlspecialchars($item['product_id']); ?>" class="main-product-link">
                                            <img src="<?php echo htmlspecialchars($item['image1_display']); ?>" alt="" class="main-product-image">
                                        </a>
                                    </div>
                                    <div class="favorite">
                                        <i class="far fa-heart"></i>
                                    </div>
                                    <div class="product_info">
                                        <h6 class="product_name">
                                            <a href="product.php?product_id=<?php echo htmlspecialchars($item['product_id']); ?>">
                                                <?php echo htmlspecialchars($item['name']); ?>
                                            </a>
                                        </h6>
                                        <div class="product_price">
                                            $<?php echo htmlspecialchars($item['price']); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="red_button add_to_cart_button quick-add-button">
                                    <a href="#">Quick Add <i class="fa fa-plus quick-add-icon"></i></a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
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