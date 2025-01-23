<?php
include 'user_status.php';
include 'connect.php';

if (isset($_SESSION['user_id'])) {
	$user_id = $_SESSION['user_id'];
} else {
	$user_id = '';
}

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
//Modify the queries to include product_id
$brand_query = "SELECT DISTINCT p.id, p.name, p.category, p.brand, pv.product_id, pv.color, pv.size, pv.price, pv.image1_display 
                FROM products p 
                JOIN product_variants pv ON p.id = pv.product_id 
                WHERE p.id >= 1 
                ORDER BY p.id ASC 
                LIMIT 50";
$stmt = $conn->prepare($brand_query);
$stmt->execute();
$brand_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$grouped_products = groupProductsByNameAndColor($brand_result);

// Fetch categories
try {
	$categories_query = "SELECT * FROM categories";
	$stmt = $conn->prepare($categories_query);
	$stmt->execute();
	$categories_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
	echo "Error: " . $e->getMessage();
}

// Fetch all brands
$brands_query = "SELECT * FROM brand";
$stmt = $conn->prepare($brands_query);
$stmt->execute();
$brands_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// New Arrivals Query

$new_arrivals_query = "SELECT DISTINCT p.id, p.name, p.category, p.brand, pv.product_id, pv.color, pv.size, pv.price, pv.image1_display 
                       FROM products p 
                       JOIN product_variants pv ON p.id = pv.product_id 
                       ORDER BY p.id DESC 
                       LIMIT 36";
$stmt = $conn->prepare($new_arrivals_query);
$stmt->execute();
$new_arrivals_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group new arrivals by name and color
$new_arrivals_grouped = groupProductsByNameAndColor($new_arrivals_result);
$unique_new_arrivals = [];
foreach ($new_arrivals_result as $product) {
	$key = $product['name'] . '_' . $product['color'];
	if (!isset($new_arrivals_grouped[$key])) {
		$new_arrivals_grouped[$key] = [];
		$unique_new_arrivals[] = $product;
	}
	$new_arrivals_grouped[$key][] = $product;
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




?>

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
				<!-- Product Grid -->
				<div class="product-grid" data-isotope='{ "itemSelector": ".product-item", "layoutMode": "fitRows" }'>
					<?php foreach ($grouped_products as $product_key => $product_data):
						$first_variant = $product_data['main_product'] ?? null;
						$color_variants = $product_data['color_variants'] ?? [];

						if (!$first_variant) continue;

						$brand_class = strtolower(str_replace(' ', '-', htmlspecialchars($first_variant['brand'] ?? '')));
						$availableSizesForProduct = json_encode(getAvailableSizes($conn, $first_variant['id'])); // Get available sizes for the main product
					?>
						<div class="product-item <?php echo $brand_class; ?>" data-product-id="<?php echo htmlspecialchars($first_variant['id'] ?? ''); ?>" data-color="<?php echo htmlspecialchars($first_variant['color']); ?>" data-available-sizes='<?php echo $availableSizesForProduct; ?>'>
							<div class="product product_filter">
								<div class="product_image">
									<a href="product.php?product_id=<?php echo htmlspecialchars($first_variant['id'] ?? ''); ?>" class="main-product-link">
										<img src="<?php echo htmlspecialchars($first_variant['image1_display'] ?? ''); ?>" alt="" class="main-product-image">
									</a>
								</div>
								<div class="favorite">
									<i class="far fa-heart" data-product-id="<?php echo htmlspecialchars($first_variant['id']); ?>" data-color="<?php echo htmlspecialchars($first_variant['color']); ?>"></i>
								</div>
								<div class="product_info">
									<h6 class="product_name">
										<a href="product.php?product_id=<?php echo htmlspecialchars($first_variant['id'] ?? ''); ?>">
											<?php echo htmlspecialchars($first_variant['name'] ?? 'Unknown Product'); ?>
										</a>
									</h6>
									<div class="product_price">
										RM<?php echo htmlspecialchars($first_variant['price'] ?? '0.00'); ?>
									</div>

									<!-- Color Variants -->
									<?php if (!empty($color_variants) && count($color_variants) > 1): ?>
										<div class="color-variants">
											<?php foreach ($color_variants as $index => $variant): ?>
												<span
													class="color-circle <?php echo $index === 0 ? 'color-active' : ''; ?>"
													style="background-color: <?php echo htmlspecialchars($variant['color']); ?>;"
													data-product-id="<?php echo htmlspecialchars($variant['product_id']); ?>"
													data-product-image="<?php echo htmlspecialchars($variant['image1_display']); ?>"
													data-product-price="<?php echo htmlspecialchars($variant['price']); ?>"
													data-available-sizes='<?php
																			// Fetch sizes specifically for this product variant
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
						<?php foreach ($new_arrivals_grouped as $product_name => $product_data):
							$first_variant = $product_data['main_product'];
							$color_variants = $product_data['color_variants'];
							$brand_class = strtolower(str_replace(' ', '-', htmlspecialchars($first_variant['brand'])));
							$availableSizesForProduct = json_encode(getAvailableSizes($conn, $first_variant['id']));
						?>
							<div class="owl-item product_slider_item">
								<div class="product-item <?php echo $brand_class; ?>"
									data-product-id="<?php echo htmlspecialchars($first_variant['id'] ?? ''); ?>"
									data-available-sizes='<?php echo $availableSizesForProduct; ?>'>
									<div class="product">
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
										<div class="product_bubble product_bubble_left product_bubble_green d-flex flex-column align-items-center">
											<span>new</span>
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
						<i class="fa fs-chevron-left" aria-hidden="true"></i>
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