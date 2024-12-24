<?php
// Define variables for the page content
$shopName = "Step Online Shoe Shop";
$tagline = "Your trusted partner in stepping into style and comfort.";
$mission = "To provide fashionable, durable, and affordable footwear that empowers every individual to walk confidently into their future.";
$features = [
    "Diverse Collection" => "We offer a wide range of styles for every occasion, from casual outings to formal events.",
    "Quality Assurance" => "Every product is handpicked to ensure the highest levels of comfort and durability.",
    "Affordable Prices" => "Enjoy top-quality footwear without stretching your budget.",
    "Customer Focused" => "Your satisfaction is our top priority. We are here to make your shopping experience seamless and enjoyable."
];

// Output the page
echo "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>About Us - $shopName</title>
    <link rel='stylesheet' href='assets/css/about.css'>
</head>
<body>
    <div class='header'>
        <h1>About $shopName</h1>
        <p>$tagline</p>
    </div>
    <div class='container'>
        <div class='about-section'>
            <h2>Who We Are</h2>
            <p>Welcome to $shopName, where style meets comfort. Our mission is to bring you the best footwear for all occasions, ensuring you look and feel your best with every step you take.</p>
            <p>With a diverse collection ranging from trendy sneakers to elegant formal shoes, we are dedicated to catering to your unique style and needs.</p>
        </div>
        <div class='mission'>
            <h3>Our Mission</h3>
            <p>$mission</p>
        </div>
        <div class='features'>";
foreach ($features as $title => $description) {
    echo "
            <div class='feature'>
                <h4>$title</h4>
                <p>$description</p>
            </div>";
}
echo "
        </div>
    </div>
    <footer>
        <p>© " . date("Y") . " $shopName. All rights reserved.</p>
    </footer>
</body>
</html>";
?>
