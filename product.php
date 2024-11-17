<?php
include 'connect.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}



// Fetch products data from the database
$select_products = $conn->prepare("SELECT * FROM `products`");
$select_products->execute();
$all_products = $select_products->fetchAll(PDO::FETCH_ASSOC);

// Group products by name
$products = [];
foreach ($all_products as $product) {
    $products[$product['name']][] = [
        'id' => $product['id'],
        'category' => $product['category'],
        'brand' => $product['brand'],
        'name' => $product['name'], // Add name field
        'color' => $product['color'],
        'price' => $product['price'],
        'images' => [
            $product['image1_display'],
            $product['image2_display'],
            $product['image3_display'],
            $product['image4_display']
        ],
        'thumbnails' => [
            $product['image1_thumb'],
            $product['image2_thumb'],
            $product['image3_thumb'],
            $product['image4_thumb']
        ]
    ];
}

// Flatten the array for easier access
$products = array_values($products);
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
                <span class="categoryText">MENS</span>
                <h1 class="title">
                    <span class="titleText">
                        Red Nike Jordan Max Aura 3
                    </span>
                    <span class="titleOverlay"></span>
                </h1>
                <p class="description">
                    Lorem ipsum dolor sit amet
                </p>
                <div class="thumbs">
                    <img src="img/showcase/thumbs/shoe1-1/img1.jpg" class="thumb-active" />
                    <img src="img/showcase/thumbs/shoe1-1/img2.jpg" />
                    <img src="img/showcase/thumbs/shoe1-1/img3.jpg" />
                    <img src="img/showcase/thumbs/shoe1-1/img4.jpg" />
                </div>
            </div>
            <div class="showcase">
                <div>
                    <img src="img/showcase/shoe1-1/img1.png" />
                    <div class="shadow"></div>
                </div>
            </div>
            <div class="options">
                <h4>Size</h4>
                <ul class="sizes">
                    <li class="size-active">37</li>
                    <li>38</li>
                    <li>39</li>
                    <li>40</li>
                    <li>41</li>
                </ul>
                <div class="revies">
                    <h4>Reviews</h4>
                    <ul class="stars">
                        <li>
                            <span class="material-icons-outlined">
                                star
                            </span>
                        </li>
                        <li>
                            <span class="material-icons-outlined">
                                star
                            </span>
                        </li>
                        <li>
                            <span class="material-icons-outlined">
                                star
                            </span>
                        </li>
                        <li>
                            <span class="material-icons-outlined">
                                star
                            </span>
                        </li>
                        <li>
                            <span class="material-icons-outlined">
                                star
                            </span>
                        </li>
                        <li>
                            <span class="material-icons-outlined">
                                star_outline
                            </span>
                        </li>
                    </ul>
                </div>
                <div class="pricing">
                    <h4>Price</h4>
                    <h4 class="price">$150</h4>
                </div>
                <div class="colors">
                    <h4>Colors</h4>
                    <ul>
                        <li class="color color-active"></li>
                        <li class="color"></li>
                    </ul>
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
        const products = <?php echo json_encode($products); ?>;
    </script>

    <!-- custom js file link  -->
    <script src="assets/js/product.js"></script>



</body>

</html>