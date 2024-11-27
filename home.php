<?php

include 'connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
	$user_id = $_SESSION['user_id'];
} else {
	$user_id = '';
};

function groupProductsByNameandColor($products)
{
	$groupedProducts = [];
	foreach ($products as $product) {
		//Use a unique key combining name and color
		$key = $product['name'] . '_' . $product['color'];

		if (!isset($groupedProducts[$product['name']])) {
			$groupedProducts[$product['name']] = [];
		}

		$groupedProducts[$product['name']][] = $product;
	}
	return $groupedProducts;
}

//Modify the New Arrivals section query
$brand_query = "SELECT * FROM products WHERE id >= 1 ORDER BY id ASC LIMIT 16";
$stmt = $conn->prepare($brand_query);
$stmt->execute();
$brand_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$grouped_products = groupProductsByNameAndColor($brand_result);

//Group products by name
$grouped_products = [];
$unique_products = [];
foreach ($brand_result  as $product) {
	if (!isset($grouped_products[$product['name']])) {
		$grouped_products[$product['name']] = [];
		$unique_products[] = $product;
	}
	$grouped_products[$product['name']][] = $product;
}


try {
	// Fetch categories
	$categories_query = "SELECT * FROM categories";
	$stmt = $conn->prepare($categories_query);
	$stmt->execute();
	$categories_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
	echo "Error: " . $e->getMessage();
}

$brands_query = "SELECT * FROM brand"; // Fetch all brands
$stmt = $conn->prepare($brands_query);
$stmt->execute();
$brands_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

//New Arrivals Query
$new_arrivals_query = "SELECT * FROM products ORDER BY id DESC LIMIT 12";
$stmt = $conn->prepare($new_arrivals_query);
$stmt->execute();
$new_arrivals_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

//Gorup new arrivals by name
$new_arrivals_grouped = [];
$unique_new_arrivals = [];
foreach ($new_arrivals_result as $product) {
	if (!isset($new_arrivals_grouped[$product['name']])) {
		$new_arrivals_grouped[$product['name']] = [];
		$unique_new_arrivals[] = $product;
	}
	$new_arrivals_grouped[$product['name']][] = $product;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
	<title>Colo Shop</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="Colo Shop Template">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

	<link rel="stylesheet" type="text/css" href="assets/css/owl.carousel.css">
	<link rel="stylesheet" type="text/css" href="assets/css/owl.theme.default.css">
	<link rel="stylesheet" type="text/css" href="assets/css/main_styles.css">
	<link rel="stylesheet" type="text/css" href="assets/css/responsive.css">


</head>

<body>

	<div class="super_container">

		<!-- Header -->

		<header class="header trans_300">

			<!-- Top Navigation -->

			<div class="top_nav">
				<div class="container">
					<div class="row">
						<div class="col-md-6">
							<div class="top_nav_left">free shipping on all u.s orders over $50</div>
						</div>
						<div class="col-md-6 text-right">
							<div class="top_nav_right">
								<ul class="top_nav_menu">

									<!-- Currency / Language / My Account -->

									<li class="currency">
										<a href="#">
											usd
											<i class="fa fa-angle-down"></i>
										</a>
										<ul class="currency_selection">
											<li><a href="#">cad</a></li>
											<li><a href="#">aud</a></li>
											<li><a href="#">eur</a></li>
											<li><a href="#">gbp</a></li>
										</ul>
									</li>
									<li class="language">
										<a href="#">
											English
											<i class="fa fa-angle-down"></i>
										</a>
										<ul class="language_selection">
											<li><a href="#">French</a></li>
											<li><a href="#">Italian</a></li>
											<li><a href="#">German</a></li>
											<li><a href="#">Spanish</a></li>
										</ul>
									</li>
									<li class="account">
										<a href="#">
											My Account
											<i class="fa fa-angle-down"></i>
										</a>
										<ul class="account_selection">
											<li><a href="#"><i class="fa fa-sign-in" aria-hidden="true"></i>Sign In</a>
											</li>
											<li><a href="#"><i class="fa fa-user-plus"
														aria-hidden="true"></i>Register</a></li>
										</ul>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Main Navigation -->

			<div class="main_nav_container">
				<div class="container">
					<div class="row">''
						<div class="col-lg-12 text-right">
							<div class="logo_container">
								<a href="#">St<span>ep</span></a>
							</div>
							<nav class="navbar">
								<ul class="navbar_menu">
									<li><a href="#">home</a></li>
									<li><a href="#">Brands</a></li>
									<li><a href="#">WOMEN</a></li>
									<li><a href="#">MEN</a></li>
									<li><a href="#">KIDS</a></li>
									<li><a href="contact.html">contact</a></li>
								</ul>
								<ul class="navbar_user">
									<li><a href="#"><i class="fa fa-search" aria-hidden="true"></i></a></li>
									<li><a href="#"><i class="fa fa-user" aria-hidden="true"></i></a></li>
									<li class="checkout">
										<a href="#">
											<i class="fa fa-shopping-cart" aria-hidden="true"></i>
											<span id="checkout_items" class="checkout_items">2</span>
										</a>
									</li>
								</ul>
								<div class="hamburger_container">
									<i class="fa fa-bars" aria-hidden="true"></i>
								</div>
							</nav>
						</div>
					</div>
				</div>
			</div>

		</header>

		<div class="fs_menu_overlay"></div>
		<div class="hamburger_menu">
			<div class="hamburger_close"><i class="fa fa-times" aria-hidden="true"></i></div>
			<div class="hamburger_menu_content text-right">
				<ul class="menu_top_nav">
					<li class="menu_item has-children">
						<a href="#">
							usd
							<i class="fa fa-angle-down"></i>
						</a>
						<ul class="menu_selection">
							<li><a href="#">cad</a></li>
							<li><a href="#">aud</a></li>
							<li><a href="#">eur</a></li>
							<li><a href="#">gbp</a></li>
						</ul>
					</li>
					<li class="menu_item has-children">
						<a href="#">
							English
							<i class="fa fa-angle-down"></i>
						</a>
						<ul class="menu_selection">
							<li><a href="#">French</a></li>
							<li><a href="#">Italian</a></li>
							<li><a href="#">German</a></li>
							<li><a href="#">Spanish</a></li>
						</ul>
					</li>
					<li class="menu_item has-children">
						<a href="#">
							My Account
							<i class="fa fa-angle-down"></i>
						</a>
						<ul class="menu_selection">
							<li><a href="#"><i class="fa fa-sign-in" aria-hidden="true"></i>Sign In</a></li>
							<li><a href="#"><i class="fa fa-user-plus" aria-hidden="true"></i>Register</a></li>
						</ul>
					</li>
					<li class="menu_item"><a href="#">home</a></li>
					<li class="menu_item"><a href="#">shop</a></li>
					<li class="menu_item"><a href="#">promotion</a></li>
					<li class="menu_item"><a href="#">pages</a></li>
					<li class="menu_item"><a href="#">blog</a></li>
					<li class="menu_item"><a href="#">contact</a></li>
				</ul>
			</div>
		</div>

		<!-- Slider -->

		<div class="main_slider" style="background-image:url(assets/image/post-item2.jpg)">
			<div class="container fill_height">
				<div class="row align-items-center fill_height">
					<div class="col">
						<div class="main_slider_content">
							<h6>Spring / Summer Collection 2017</h6>
							<h1>Get up to 30% Off New Arrivals</h1>
							<div class="red_button shop_now_button"><a href="#">shop now</a></div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Banner -->

		<div class="banner">
			<div class="container">
				<div class="row">
					<?php
					//Fetch categories for the banner
					$banner_query = "SELECT * FROM categories";
					$stmt = $conn->prepare($banner_query);
					$stmt->execute();
					$banner_categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

					foreach ($banner_categories as $category): ?>
						<div class="col-md-4">
							<div class="banner_item align-items-center" style="background-image:url(assets/image/<?php echo htmlspecialchars($category['image']); ?>)">
								<div class="banner_category">
									<a href="categories.php?category_id=<?php echo htmlspecialchars($category['id']); ?>"><?php echo htmlspecialchars($category['name']); ?></a>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<!-- New Arrivals -->

		<!-- New Arrivals -->
		<div class="new_arrivals">
			<div class="container">
				<div class="row">
					<div class="col text-center">
						<div class="section_title new_arrivals_title">
							<h2>Brands</h2>
						</div>
					</div>
				</div>
				<div class="row align-items-center">
					<div class="col text-center">
						<div class="new_arrivals_sorting">
							<ul class="arrivals_grid_sorting clearfix button-group filters-button-group">
								<li class="grid_sorting_button button d-flex flex-column justify-content-center align-items-center active is-checked" data-filter="*">all</li>
								<?php
								// Fetch brands from the database

								foreach ($brands_result as $brand):
									$brand_name = htmlspecialchars($brand['name']);
									$brand_class = strtolower(str_replace(' ', '-', $brand_name)); // Create a class name from the brand name
								?>
									<li class="grid_sorting_button button d-flex flex-column justify-content-center align-items-center" data-filter=".<?php echo $brand_class; ?>"><?php echo $brand_name; ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col">
						<div class="product-grid" data-isotope='{ "itemSelector": ".product-item", "layoutMode": "fitRows" }'>

							<?php foreach ($grouped_products as $product_name => $product_variants):

								// Use the first variant as the default
								$first_variant = $product_variants[0];
								$brand_class = strtolower(str_replace(' ', '-', htmlspecialchars($first_variant['brand'])));


							?>
								<div class="product-item <?php echo $brand_class; ?>">
									<div class="product product_filter">
										<div class="product_image">
											<a href="product.php?product_id=<?php echo htmlspecialchars($first_variant['id']); ?>" class="main-product-link">
												<img src="<?php echo htmlspecialchars($first_variant['image1_display']); ?>" alt="" class="main-product-image">
											</a>
										</div>
										<div class="favorite">
											<i class="far fa-heart"></i>
										</div>
										<div class="product_info">
											<h6 class="product_name">
												<a href="product.php?product_id=<?php echo htmlspecialchars($product['id']); ?>">
													<?php echo htmlspecialchars($product_name); ?>
												</a>
											</h6>
											<div class="product_price">
												$<?php echo htmlspecialchars($first_variant['price']); ?>
											</div>

											<!----Color Variants--->
											<?php if (count($product_variants) > 1): ?>
												<div class="color-variants">
													<?php foreach ($product_variants as $index => $variant): ?>
														<span
															class="color-circle <?php echo $index === 0 ? 'color-active' : ''; ?>"
															style="background-color: <?php echo htmlspecialchars($variant['color']); ?>;"
															data-product-id="<?php echo htmlspecialchars($variant['id']); ?>"
															data-product-image="<?php echo htmlspecialchars($variant['image1_display']); ?>"
															data-product-price="<?php echo htmlspecialchars($variant['price']); ?>"></span>
													<?php endforeach; ?>
												</div>
											<?php endif; ?>
										</div>
									</div>

								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Best Sellers -->
		<div class="best_sellers">
			<div class="container">
				<div class="row">
					<div class="col text-center">
						<div class="section_title new_arrivals_title">
							<h2>New Arrivals</h2>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col">
						<div class="product_slider_container">
							<div class="owl-carousel owl-theme product_slider">

								<?php foreach ($unique_new_arrivals as $product):
									// Create a class name for the product based on its brand
									$brand_class = strtolower(str_replace(' ', '-', htmlspecialchars($product['brand'])));

									// Get all variants of this product
									$product_variants = $new_arrivals_grouped[$product['name']];

									$first_variant = $product_variants[0];
								?>
									<div class="owl-item product_slider_item">
										<div class="product-item <?php echo $brand_class; ?>">
											<div class="product">
												<div class="product_image">
													<a href="product.php?product_id=<?php echo htmlspecialchars($first_variant['id']); ?>" class="main-product-link">
														<img src="<?php echo htmlspecialchars($product['image1_display']); ?>" alt="" class="main-product-image">
													</a>
												</div>
												<div class="favorite"></div>
												<div class="product_info">
													<h6 class="product_name"><a href="product.php?product_id=<?php echo htmlspecialchars($product['id']); ?>">
															<?php echo htmlspecialchars($product['name']); ?>
														</a>
													</h6>
													<div class="product_price">
														$<?php echo htmlspecialchars($product['price']); ?>
													</div>
													<!-- Color Variants -->
													<?php if (count($product_variants) > 1): ?>
														<div class="color-variants">
															<?php foreach ($product_variants as $index => $variant): ?>
																<span
																	class="color-circle <?php echo $index === 0 ? 'color-active' : ''; ?>"
																	style="background-color: <?php echo htmlspecialchars($variant['color']); ?>;"
																	data-product-id="<?php echo htmlspecialchars($variant['id']); ?>"
																	data-product-image="<?php echo htmlspecialchars($variant['image1_display']); ?>"
																	data-product-price="<?php echo htmlspecialchars($variant['price']); ?>"></span>
															<?php endforeach; ?>
														</div>
													<?php endif; ?>
												</div>
												<div class="product_bubble product_bubble_left product_bubble_green d-flex flex-column align-items-center">
													<span>new</span>
												</div>
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

		<!-- Benefit -->

		<div class="benefit">
			<div class="container">
				<div class="row benefit_row">
					<div class="col-lg-3 benefit_col">
						<div class="benefit_item d-flex flex-row align-items-center">
							<div class="benefit_icon"><i class="fa fa-truck" aria-hidden="true"></i></div>
							<div class="benefit_content">
								<h6>free shipping</h6>
								<p>Suffered Alteration in Some Form</p>
							</div>
						</div>
					</div>
					<div class="col-lg-3 benefit_col">
						<div class="benefit_item d-flex flex-row align-items-center">
							<div class="benefit_icon"><i class="fa fa-money" aria-hidden="true"></i></div>
							<div class="benefit_content">
								<h6>cach on delivery</h6>
								<p>The Internet Tend To Repeat</p>
							</div>
						</div>
					</div>
					<div class="col-lg-3 benefit_col">
						<div class="benefit_item d-flex flex-row align-items-center">
							<div class="benefit_icon"><i class="fa fa-undo" aria-hidden="true"></i></div>
							<div class="benefit_content">
								<h6>45 days return</h6>
								<p>Making it Look Like Readable</p>
							</div>
						</div>
					</div>
					<div class="col-lg-3 benefit_col">
						<div class="benefit_item d-flex flex-row align-items-center">
							<div class="benefit_icon"><i class="fa fa-clock-o" aria-hidden="true"></i></div>
							<div class="benefit_content">
								<h6>opening all week</h6>
								<p>8AM - 09PM</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Blogs -->

		<div class="blogs">
			<div class="container">
				<div class="row">
					<div class="col text-center">
						<div class="section_title">
							<h2>Latest Blogs</h2>
						</div>
					</div>
				</div>
				<div class="row blogs_container">
					<div class="col-lg-4 blog_item_col">
						<div class="blog_item">
							<div class="blog_background" style="background-image:url(images/blog_1.jpg)"></div>
							<div
								class="blog_content d-flex flex-column align-items-center justify-content-center text-center">
								<h4 class="blog_title">Here are the trends I see coming this fall</h4>
								<span class="blog_meta">by admin | dec 01, 2017</span>
								<a class="blog_more" href="#">Read more</a>
							</div>
						</div>
					</div>
					<div class="col-lg-4 blog_item_col">
						<div class="blog_item">
							<div class="blog_background" style="background-image:url(images/blog_2.jpg)"></div>
							<div
								class="blog_content d-flex flex-column align-items-center justify-content-center text-center">
								<h4 class="blog_title">Here are the trends I see coming this fall</h4>
								<span class="blog_meta">by admin | dec 01, 2017</span>
								<a class="blog_more" href="#">Read more</a>
							</div>
						</div>
					</div>
					<div class="col-lg-4 blog_item_col">
						<div class="blog_item">
							<div class="blog_background" style="background-image:url(images/blog_3.jpg)"></div>
							<div
								class="blog_content d-flex flex-column align-items-center justify-content-center text-center">
								<h4 class="blog_title">Here are the trends I see coming this fall</h4>
								<span class="blog_meta">by admin | dec 01, 2017</span>
								<a class="blog_more" href="#">Read more</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Newsletter -->

		<div class="newsletter">
			<div class="container">
				<div class="row">
					<div class="col-lg-6">
						<div
							class="newsletter_text d-flex flex-column justify-content-center align-items-lg-start align-items-md-center text-center">
							<h4>Newsletter</h4>
							<p>Subscribe to our newsletter and get 20% off your first purchase</p>
						</div>
					</div>
					<div class="col-lg-6">
						<form action="post">
							<div
								class="newsletter_form d-flex flex-md-row flex-column flex-xs-column align-items-center justify-content-lg-end justify-content-center">
								<input id="newsletter_email" type="email" placeholder="Your email" required="required"
									data-error="Valid email is required.">
								<button id="newsletter_submit" type="submit" class="newsletter_submit_btn trans_300"
									value="Submit">subscribe</button>
							</div>
						</form>
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
</body>

</html>