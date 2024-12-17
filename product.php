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


<body>


    <div class="container">

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
                <p class="description">
                    Lorem ipsum dolor sit amet
                </p>
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
                <h4>Size</h4>
                <ul class="sizes">
                    <?php foreach ($unique_sizes as $index => $size): ?>
                        <li class="<?php echo $index === 0 ? 'size-active' : ''; ?>"><?php echo htmlspecialchars($size); ?></li>
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
                    <h4>Price</h4>
                    <h4 class="price">$<?php echo number_format($product['price'], 2); ?></h4>
                </div>
            </div>
        </main>
        <section class="bar-bottom">
            <div>
                <a href="#">
                    <span class="material-icons-outlined">
                        play_arrow
                    </span>
                    <span>Play Video</span>
                </a>
            </div>
            <div class="controls">
                <div class="arrows">
                    <span class="material-icons-outlined 
                    arr-left">
                        arrow_right_alt
                    </span>
                    <span class="material-icons-outlined
                            arr-right">
                        arrow_right_alt
                    </span>
                </div>
                <div>
                    <small class="shoe-num">01</small>
                    <div class="pagination">
                        <span class="pag pag-active"></span>
                        <span class="pag"></span>
                        <span class="pag"></span>
                    </div>
                    <small class="shoe-total">03</small>
                </div>
            </div>
            <div class="cart">
                <button>Add To Cart</button>
                <span class="material-icons-outlined">
                    favorite_border
                </span>
            </div>
        </section>
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
</body>

</html>