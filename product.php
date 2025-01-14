<?php
include 'connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

// Get the product id from the url
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

if ($product_id > 0) {
    // Fetch product details with variants
    $stmt = $conn->prepare("
        SELECT p.*, 
               pv.color, 
               pv.size, 
               pv.price, 
               pv.image1_display, 
               pv.image2_display, 
               pv.image3_display, 
               pv.image4_display,
               pv.image1_thumb,
               pv.image2_thumb,
               pv.image3_thumb,
               pv.image4_thumb
        FROM products p
        JOIN product_variants pv ON p.id = pv.product_id
        WHERE p.id = :id
    ");
    $stmt->bindParam(':id', $product_id);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo "Product not found.";
        exit;
    }
} else {
    echo "Invalid product ID.";
    exit;
}

// Fetch all variants for this product name
$select_variants = $conn->prepare("
    SELECT p.*, 
           pv.color, 
           pv.size, 
           pv.price, 
           pv.image1_display, 
           pv.image2_display, 
           pv.image3_display, 
           pv.image4_display,
           pv.image1_thumb,
           pv.image2_thumb,
           pv.image3_thumb,
           pv.image4_thumb
    FROM products p
    JOIN product_variants pv ON p.id = pv.product_id
    WHERE p.name = :name
");
$select_variants->bindParam(':name', $product['name']);
$select_variants->execute();
$all_variants = $select_variants->fetchAll(PDO::FETCH_ASSOC);

// Group variants
$grouped_variants = [];
$unique_colors = [];
$unique_sizes = [];

foreach ($all_variants as $variant) {
    $key = $variant['name'] . '_' . $variant['color'];

    if (!isset($grouped_variants[$key])) {
        $grouped_variants[$key] = $variant;
    }

    // Collect unique colors and sizes
    if (!in_array($variant['color'], $unique_colors)) {
        $unique_colors[] = $variant['color'];
    }

    if (!in_array($variant['size'], $unique_sizes)) {
        $unique_sizes[] = $variant['size'];
    }
}

// Sort sizes
sort($unique_sizes);

// Fetch recommended products from the same category, excluding the current product
$recommended_query = "SELECT DISTINCT p.id, p.name, p.category, p.brand, pv.product_id, pv.color, pv.size, pv.price, pv.image1_display 
                      FROM products p 
                      JOIN product_variants pv ON p.id = pv.product_id 
                      WHERE p.category = :category 
                      AND p.id != :current_product_id 
                      ORDER BY RAND() 
                      LIMIT 10"; // Adjust the limit as needed

$stmt = $conn->prepare($recommended_query);
$stmt->bindParam(':category', $product['category']);
$stmt->bindParam(':current_product_id', $product_id);
$stmt->execute();
$recommended_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group recommended products
$recommended_grouped = groupProductsByNameAndColor($recommended_products);

include 'header.php';
function groupProductsByNameAndColor($products)
{
    $groupedProducts = [];
    $processedProductNames = [];
    $processedProductIds = [];

    foreach ($products as $product) {
        if (in_array($product['name'], $processedProductNames) || in_array($product['id'], $processedProductIds)) {
            continue;
        }

        $key = $product['name'] . '_' . $product['color'];
        $colorVariants = [];
        $seenColors = [];

        foreach ($products as $variant) {
            if ($variant['name'] === $product['name'] && !in_array($variant['color'], $seenColors)) {
                $colorVariants[] = $variant;
                $seenColors[] = $variant['color'];
            }
        }

        $groupedProducts[$key] = [
            'main_product' => $product,
            'color_variants' => $colorVariants
        ];

        $processedProductNames[] = $product['name'];
        $processedProductIds[] = $product['id'];

        if (count($groupedProducts) >= 16) {
            break;
        }
    }

    return $groupedProducts;
}

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
// Fetch all available sizes
$availableSizes = getAvailableSizes($conn, $product_id);



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>menu</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- Add this line in the <head> section of your HTML -->

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="assets/css/product.css">
</head>


<body class="product">


    <div class="product-container">


        <div class="breadcrumbs d-flex flex-row align-items-center">
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="product.php"><i class="fa fa-angle-right" aria-hidden="true"></i>Product</a></li>
            </ul>
        </div>


        <main>
            <div>
                <span class="categoryText"><?php echo htmlspecialchars($product['category']); ?></span>
                <h1 class="title">
                    <span class="titleText">
                        <span class="titleText"><?php echo htmlspecialchars($product['name']); ?></span>
                        <span class="titleOverlay"></span>
                    </span>
                    <span class="titleOverlay"></span>
                </h1>
                <div class="thumbs">
                    <?php
                    $thumbs = [
                        $product['image1_thumb'] ?? '',
                        $product['image2_thumb'] ?? '',
                        $product['image3_thumb'] ?? '',
                        $product['image4_thumb'] ?? ''
                    ];
                    foreach ($thumbs as $index => $thumb):
                        if (!empty($thumb)):
                    ?>
                            <img src="<?php echo htmlspecialchars($thumb); ?>"
                                class="<?php echo $index === 0 ? 'thumb-active' : ''; ?>" />
                    <?php
                        endif;
                    endforeach;
                    ?>
                </div>
            </div>
            <div class="showcase">
                <div>
                    <img src="<?php echo htmlspecialchars($product['image1_display'] ?? ''); ?>"
                        id="main-image"
                        data-default-image="<?php echo htmlspecialchars($product['image1_display'] ?? ''); ?>" />
                    <div class="shadow"></div>
                </div>
            </div>
            <div class="options">
                <h4>Size(UK)</h4>
                <ul class="sizes">
                    <?php foreach ($unique_sizes as $index => $size): ?>
                        <li class="<?php echo $index === 0 ? 'size-active' : ''; ?>" data-size="<?php echo htmlspecialchars($size); ?>"><?php echo htmlspecialchars($size); ?></li>
                    <?php endforeach; ?>
                </ul>

                <div class="colors">
                    <h4>Colors</h4>
                    <ul>
                        <?php foreach ($unique_colors as $index => $color): ?>
                            <li class="color <?php echo $index === 0 ? 'color-active' : ''; ?>"
                                style="background-color: <?php echo htmlspecialchars($color); ?>"
                                data-color="<?php echo htmlspecialchars($color); ?>">
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="pricing">
                    <h4>Price: </h4>
                    <h4 class="price"> RM<?php echo number_format($product['price'], 2); ?></h4>
                </div>
            </div>
        </main>
        <section class="bar-bottom">
            <div class="cart">
                <button id="add-to-cart-button"
                    data-product-id="<?php echo htmlspecialchars($product['id']); ?>"
                    data-size=""
                    data-color="">Add To Cart</button>
                <div class="favorite-product">
                    <i class="<?php echo $is_favorited ? 'fas' : 'far'; ?> fa-heart" data-product-id="<?php echo htmlspecialchars($product['id']); ?>"></i>
                </div>

            </div>
        </section>
    </div>

    <!-- Recommended Products Section -->
    <div class="recommended_products">
        <div class="container">
            <div class="row">
                <div class="col text-center">
                    <div class="section_title new_arrivals_title">
                        <h2>Recommended for You</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="product_slider_container">
                        <div class="owl-carousel owl-theme product_slider">
                            <?php foreach ($recommended_grouped as $product_name => $product_data):
                                $first_variant = $product_data['main_product'];
                                $color_variants = $product_data['color_variants'];
                                $brand_class = strtolower(str_replace(' ', '-', htmlspecialchars($first_variant['brand'])));
                                $availableSizesForProduct = json_encode(getAvailableSizes($conn, $first_variant['id']));
                            ?>
                                <div class="owl-item product_slider_item">
                                    <div class="product-item <?php echo $brand_class; ?>"
                                        data-product-id="<?php echo htmlspecialchars($first_variant['id'] ?? ''); ?>"
                                        data-available-sizes='<?php echo $availableSizesForProduct; ?>'>
                                        <div class="product product_filter">
                                            <div class="product_image">
                                                <a href="product.php?product_id=<?php echo htmlspecialchars($first_variant['id']); ?>" class="main-product-link">
                                                    <img src="<?php echo htmlspecialchars($first_variant['image1_display']); ?>" alt="" class="main-product-image">
                                                </a>
                                            </div>
                                            <div class="favorite">
                                                <i class="far fa-heart" data-product-id="<?php echo htmlspecialchars($first_variant['id']); ?>"></i>
                                            </div>
                                            <div class="product_info">
                                                <h6 class="product_name">
                                                    <a href="product.php?product_id=<?php echo htmlspecialchars($first_variant['id']); ?>">
                                                        <?php echo htmlspecialchars($first_variant['name']); ?>
                                                    </a>
                                                </h6>
                                                <div class="product_price">
                                                    RM<?php echo htmlspecialchars($first_variant['price']); ?>
                                                </div>

                                                <!-- Color Variants -->
                                                <?php if (count($color_variants) > 1): ?>
                                                    <div class="color-variants">
                                                        <?php foreach ($color_variants as $index => $variant): ?>
                                                            <span
                                                                class="color-circle <?php echo $index === 0 ? 'color-active' : ''; ?>"
                                                                style="background-color: <?php echo htmlspecialchars($variant['color']); ?>;"
                                                                data-product-id="<?php echo htmlspecialchars($variant['product_id']); ?>"
                                                                data-product-image="<?php echo htmlspecialchars($variant['image1_display']); ?>"
                                                                data-product-price="<?php echo htmlspecialchars($variant['price']); ?>"
                                                                data-available-sizes='<?php
                                                                                        $variantSizes = getAvailableSizes($conn, $variant['product_id']);
                                                                                        echo json_encode($variantSizes);
                                                                                        ?>'>
                                                            </span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="red_button add_to_cart_button quick-add-button">
                                            <a href="#">Quick Add <i class="fa fa-plus quick-add-icon"></i></a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Slider Navigation -->
                        <div class="product_slider_nav_left product_slider_nav d-flex align-items-center justify-content-center flex-column">
                            <i class="fa fa-chevron-left" aria-hidden="true"></i>
                        </div>
                        <div class="product_slider_nav_right product_slider_nav d-flex align-items-center justify-content-center flex-column">
                            <i class="fa fa-chevron-right" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <script>
        const products = <?php
                            // Group variants by color
                            $grouped_variants = [];
                            foreach ($all_variants as $variant) {
                                $key = $variant['color'];
                                $grouped_variants[$key][] = $variant;
                            }
                            echo json_encode(array_values($grouped_variants));
                            ?>;
    </script>


    <!-- custom js file link  -->
    <script src="assets/js/products.js"></script>
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/owl.carousel.js"></script>
    <script src="assets/js/custom.js"></script>
</body>

</html>