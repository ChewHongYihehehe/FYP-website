<?php
include 'connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
	$user_id = $_SESSION['user_id'];
} else {
	$user_id = '';
}

$products_per_page = 12;

// Get the current page number from the URL, default to 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $products_per_page;

// Capture price filter values explicitly
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : null;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : null;

// Fetch categories from the category table
$sql = "SELECT name FROM categories";
$result = $conn->query($sql);

// Fetch products based on filters to get the actual min and max prices
$price_sql = "SELECT MIN(pv.price) AS min_price, MAX(pv.price) AS max_price 
              FROM products p 
              JOIN product_variants pv ON p.id = pv.product_id 
              WHERE 1=1"; // Add your filters here if needed
$price_result = $conn->query($price_sql);
$price_row = $price_result->fetch(PDO::FETCH_ASSOC);

// For the color section
$color_sql = "SELECT color_name FROM color";
$color_result = $conn->query($color_sql);

// For the brand section
$brand_sql = "SELECT name FROM brand";
$brand_result = $conn->query($brand_sql);

// For the product section
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$color_filter = isset($_GET['color']) ? $_GET['color'] : '';
$brand_filter = isset($_GET['brand']) ? $_GET['brand'] : '';

// Modify your product query to ensure you get multiple variants
$product_sql = "SELECT DISTINCT p.*, pv.product_id, pv.color, pv.size, pv.price, pv.image1_display 
                FROM products p 
                JOIN product_variants pv ON p.id = pv.product_id 
                LEFT JOIN color c ON pv.color = c.color_name
                WHERE 1=1";

// Append conditions based on available filters
if ($category_filter) {
	$product_sql .= " AND category = :category";
}

if ($color_filter) {
	$product_sql .= " AND color_name = :color";
}

if ($brand_filter) {
	$product_sql .= " AND brand = :brand";
}

// For the filter price section
if ($min_price !== null && $max_price !== null) {
	$product_sql .= " AND pv.price BETWEEN :min_price AND :max_price";
}

// Group and order to get multiple variants
$product_sql .= " GROUP BY p.name, pv.color";

// Add pagination
$product_sql .= " LIMIT :limit OFFSET :offset";
$product_stmt = $conn->prepare($product_sql);

// Bind values only if they are set
if ($category_filter) {
	$product_stmt->bindValue(':category', $category_filter);
}
if ($color_filter) {
	$product_stmt->bindValue(':color', $color_filter);
}
if ($brand_filter) {
	$product_stmt->bindValue(':brand', $brand_filter);
}

// Bind price values if set
if ($min_price !== null && $max_price !== null) {
	$product_stmt->bindValue(':min_price', $min_price);
	$product_stmt->bindValue(':max_price', $max_price);
}

$product_stmt->bindValue(':limit', $products_per_page, PDO::PARAM_INT);
$product_stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$product_stmt->execute();
$products = $product_stmt->fetchAll(PDO::FETCH_ASSOC);

$total_products = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_pages = ceil($total_products / $products_per_page);

// Ensure the price range is displayed correctly
$min_price_display = ($price_row['min_price'] !== null) ? number_format($price_row['min_price'], 2) : '0.00';
$max_price_display = ($price_row['max_price'] !== null) ? number_format($price_row['max_price'], 2) : '0.00';

$min_price = ($min_price !== null) ? $min_price : ($price_row['min_price'] !== null ? (float)$price_row['min_price'] : 0);
$max_price = ($max_price !== null) ? $max_price : ($price_row['max_price'] !== null ? (float)$price_row['max_price'] : 0);

// Modify your existing code to check for filter_applied
$filter_applied = isset($_GET['filter_applied']) && $_GET['filter_applied'] == '1';

// When checking price filter conditions
if ($filter_applied && $min_price !== null && $max_price !== null) {
	$product_sql .= " AND price BETWEEN :min_price AND :max_price";
}

// Group products
$grouped_products = groupProductsByNameAndColor($products);

function groupProductsByNameAndColor($products)
{
	$groupedProducts = [];
	$processedProductNames = [];
	$processedProductIds = [];

	foreach ($products as $product) {
		// Check if this product name or ID has already been processed
		if (
			in_array($product['name'], $processedProductNames) ||
			in_array($product['id'], $processedProductIds)
		) {
			continue;
		}

		// Create a unique key for this product
		$key = $product['name'] . '_' . $product['color'];

		// Find all unique color variants for this product
		$colorVariants = [];
		$seenColors = [];

		foreach ($products as $variant) {
			// Check if this variant matches the current product and hasn't been seen before
			if ($variant['name'] === $product['name'] && !in_array($variant['color'], $seenColors)) {
				$colorVariants[] = $variant;
				$seenColors[] = $variant['color'];
			}
		}

		// Group the product
		$groupedProducts[$key] = [
			'main_product' => $product,
			'color_variants' => $colorVariants
		];

		// Mark this product ID as processed
		$processedProductNames[] = $product['name'];
		$processedProductIds[] = $product['id'];
	}

	return $groupedProducts;
}

?>





<!DOCTYPE html>
<html lang="en">

<head>
	<title>Colo Shop Categories</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="Colo Shop Template">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"
		type="text/css">
	<link rel="stylesheet" type="text/css" href="assets/plugins/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="assets/css/categories.css">
	<link rel="stylesheet" type="text/css" href="assets/css/categories_respond.css">

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
					<div class="row">
						<div class="col-lg-12 text-right">
							<div class="logo_container">
								<a href="#">colo<span>shop</span></a>
							</div>
							<nav class="navbar">
								<ul class="navbar_menu">
									<li><a href="#">home</a></li>
									<li><a href="#">shop</a></li>
									<li><a href="#">promotion</a></li>
									<li><a href="#">pages</a></li>
									<li><a href="#">blog</a></li>
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

		<div class="super_container">

			<!-- Main Navigation -->

			<div class="container product_section_container">
				<div class="row">
					<div class="col product_section clearfix">


						<!-- Breadcrumbs -->
						<div class="breadcrumbs d-flex flex-row align-items-center">
							<ul>
								<li><a href="index.php">Home</a></li>
								<li><a href="categories.php"><i class="fa fa-angle-right" aria-hidden="true"></i>Categories</a></li>

								<?php if ($category_filter): ?>
									<li class="active">
										<a href="?category=<?php echo urlencode($category_filter); ?>&color=<?php echo urlencode($color_filter); ?>&brand=<?php echo urlencode($brand_filter); ?>">
											<i class="fa fa-angle-right" aria-hidden="true"></i><?php echo htmlspecialchars($category_filter); ?>
											<i class="fa fa-times cancel-filter" data-filter="category" data-value="<?php echo htmlspecialchars($category_filter); ?>"></i>
										</a>
									</li>
								<?php endif; ?>

								<?php if ($color_filter): ?>
									<li class="active">
										<a href="?color=<?php echo urlencode($color_filter); ?>&category=<?php echo urlencode($category_filter); ?>&brand=<?php echo urlencode($brand_filter); ?>">
											<i class="fa fa-angle-right" aria-hidden="true"></i><?php echo htmlspecialchars($color_filter); ?>
											<i class="fa fa-times cancel-filter" data-filter="color" data-value="<?php echo htmlspecialchars($color_filter); ?>"></i>
										</a>
									</li>
								<?php endif; ?>

								<?php if ($brand_filter): ?>
									<li class="active">
										<a href="?brand=<?php echo urlencode($brand_filter); ?>&category=<?php echo urlencode($category_filter); ?>&color=<?php echo urlencode($color_filter); ?>">
											<i class="fa fa-angle-right" aria-hidden="true"></i><?php echo htmlspecialchars($brand_filter); ?>
											<i class="fa fa-times cancel-filter" data-filter="brand" data-value="<?php echo htmlspecialchars($brand_filter); ?>"></i>
										</a>
									</li>
								<?php endif; ?>

								<?php if ($min_price !== null && $max_price !== null): ?>
									<li class="active">
										<a href="?min_price=<?php echo urlencode($min_price); ?>&max_price=<?php echo urlencode($max_price); ?>&category=<?php echo urlencode($category_filter); ?>&color=<?php echo urlencode($color_filter); ?>&brand=<?php echo urlencode($brand_filter); ?>&filter_applied=1">
											<i class="fa fa-angle-right" aria-hidden="true"></i>Price: $<?php echo number_format($min_price, 2); ?> - $<?php echo number_format($max_price, 2); ?>
										</a>
									</li>
								<?php endif; ?>
							</ul>
						</div>

						<!-- Sidebar -->

						<div class="sidebar">
							<div class="sidebar_section">
								<div class="sidebar_title">
									<h5>Product Category</h5>
								</div>
								<ul class="sidebar_categories">
									<?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
										<li class="<?= ($category_filter === $row['name']) ? 'active' : '' ?>">
											<a href="?category=<?php echo urlencode($row['name']); ?>&color=<?php echo urlencode($color_filter); ?>&brand=<?php echo urlencode($brand_filter); ?>&min_price=<?php echo urlencode($min_price); ?>&max_price=<?php echo urlencode($max_price); ?>">
												<?php echo htmlspecialchars($row['name']); ?>
											</a>
										</li>
									<?php endwhile; ?>
								</ul>
							</div>


							<!-- Price Range Filtering -->
							<div class="sidebar_section">
								<div class="sidebar_title">
									<h5>Filter by Price</h5>
								</div>
								<form method="GET" action="">
									<p>
										<input type="text" id="amount" readonly
											style="border:0; color:#f6931f; font-weight:bold;"
											value="$<?php echo $min_price_display; ?> - $<?php echo $max_price_display; ?>">
									</p>
									<div id="slider-range" data-min="<?php echo $price_row['min_price']; ?>" data-max="<?php echo $price_row['max_price']; ?>"></div>
									<input type="hidden" name="min_price" id="min_price" value="<?php echo $min_price; ?>">
									<input type="hidden" name="max_price" id="max_price" value="<?php echo $max_price; ?>">
									<input type="hidden" name="filter_applied" id="filter_applied" value="0"> <!-- New hidden field -->

									<!-- Hidden fields for other filters -->
									<input type="hidden" name="category" id="category" value="<?php echo htmlspecialchars($category_filter); ?>">
									<input type="hidden" name="color" id="color" value="<?php echo htmlspecialchars($color_filter); ?>">
									<input type="hidden" name="brand" id="brand" value="<?php echo htmlspecialchars($brand_filter); ?>">

									<button type="button" class="filter_button" id="apply-filter"><span>Filter</span></button>
								</form>
							</div>

							<!-- Brand -->
							<div class="sidebar_section">
								<div class="sidebar_title">
									<h5>Brand</h5>
								</div>
								<ul class="sidebar_categories">
									<?php while ($brand_row = $brand_result->fetch(PDO::FETCH_ASSOC)): ?>
										<li>
											<a href="?brand=<?php echo urlencode($brand_row['name']); ?>&category=<?php echo urlencode($category_filter); ?>&color=<?php echo urlencode($color_filter); ?>&min_price=<?php echo urlencode($min_price); ?>&max_price=<?php echo urlencode($max_price); ?>">
												<?php echo htmlspecialchars($brand_row['name']); ?>
											</a>
										</li>
									<?php endwhile; ?>
								</ul>
							</div>


							<!-- Color -->
							<div class="sidebar_section">
								<div class="sidebar_title">
									<h5>Color</h5>
								</div>
								<ul class="checkboxes">
									<?php while ($color_row = $color_result->fetch(PDO::FETCH_ASSOC)): ?>
										<li>
											<a href="?color=<?php echo urlencode($color_row['color_name']); ?>&category=<?php echo urlencode($category_filter); ?>&brand=<?php echo urlencode($brand_filter); ?>&min_price=<?php echo urlencode($min_price); ?>&max_price=<?php echo urlencode($max_price); ?>">
												<i class="fa fa-square-o" aria-hidden="true"></i>
												<span><?php echo htmlspecialchars($color_row['color_name']); ?></span>
											</a>
										</li>
									<?php endwhile; ?>
								</ul>
								<div class="show_more">
									<span><span>+</span>Show More</span>
								</div>
							</div>



						</div>

						<!-- Main Content -->

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
											<!-- Product Grid -->
											<div class="product-grid" data-isotope='{ "itemSelector": ".product-item", "layoutMode": "fitRows" }'>
												<?php foreach ($grouped_products as $product_key => $product_data):
													// Ensure main_product and color_variants exist
													$first_variant = $product_data['main_product'] ?? null;
													$color_variants = $product_data['color_variants'] ?? [];

													// Skip if no product data
													if (!$first_variant) continue;

													$brand_class = strtolower(str_replace(' ', '-', htmlspecialchars($first_variant['brand'] ?? '')));
												?>
													<div class="product-item <?php echo $brand_class; ?>">
														<div class="product product_filter">
															<div class="product_image">
																<a href="product.php?product_id=<?php echo htmlspecialchars($first_variant['id'] ?? ''); ?>" class="main-product-link">
																	<img src="<?php echo htmlspecialchars($first_variant['image1_display'] ?? ''); ?>" alt="" class="main-product-image">
																</a>
															</div>
															<div class="favorite">
																<i class="far fa-heart"></i>
															</div>
															<div class="product_info">
																<h6 class="product_name">
																	<a href="product.php?product_id=<?php echo htmlspecialchars($first_variant['id'] ?? ''); ?>">
																		<?php echo htmlspecialchars($first_variant['name'] ?? 'Unknown Product'); ?>
																	</a>
																</h6>
																<div class="product_price">
																	$<?php echo htmlspecialchars($first_variant['price'] ?? '0.00'); ?>
																</div>

																<!-- Color Variants -->
																<?php if (!empty($color_variants) && count($color_variants) > 1): ?>
																	<div class="color-variants">
																		<?php foreach ($color_variants as $index => $variant): ?>
																			<span
																				class="color-circle <?php echo $index === 0 ? 'color-active' : ''; ?>"
																				style="background-color: <?php echo htmlspecialchars($variant['color'] ?? ''); ?>;"
																				data-product-id="<?php echo htmlspecialchars($variant['product_id'] ?? ''); ?>"
																				data-product-image="<?php echo htmlspecialchars($variant['image1_display'] ?? ''); ?>"
																				data-product-price="<?php echo htmlspecialchars($variant['price'] ?? ''); ?>"></span>
																		<?php endforeach; ?>
																	</div>
																<?php endif; ?>
															</div>
														</div>
														<div class="red_button add_to_cart_button"><a href="#">Quick Add</a></div>
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
														<span>1</span>
														<ul class="page_selection">
															<li><a href="#">1</a></li>
															<li><a href="#">2</a></li>
															<li><a href="#">3</a></li>
														</ul>
													</div>
													<div class="page_total"><span>of</span> 3</div>
													<div id="next_page_1" class="page_next"><a href="#"><i
																class="fa fa-long-arrow-right" aria-hidden="true"></i></a></div>
												</div>

											</div>

										</div>
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
								<div
									class="newsletter_form d-flex flex-md-row flex-column flex-xs-column align-items-center justify-content-lg-end justify-content-center">
									<input id="newsletter_email" type="email" placeholder="Your email" required="required"
										data-error="Valid email is required.">
									<button id="newsletter_submit" type="submit" class="newsletter_submit_btn trans_300"
										value="Submit">subscribe</button>
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
									<div class="cr">©2018 All Rights Reserverd. Template by <a href="#">Colorlib</a> &amp;
										distributed by <a href="https://themewagon.com">ThemeWagon</a></div>
								</div>
							</div>
						</div>
					</div>
				</footer>

			</div>


			<script src="assets/js/jquery-3.2.1.min.js"></script>
			<script src="assets/plugins/isotope.pkgd.min.js"></script>
			<script src="assets/plugins/jquery-ui.js"></script>
			<script src="assets/js/categories_custom.js"></script>

			<script>
				document.querySelectorAll('.cancel-filter').forEach(function(element) {
					element.addEventListener('click', function(event) {
						event.preventDefault();
						const filterType = this.getAttribute('data-filter');

						// Create a URL object to manipulate the current URL
						const url = new URL(window.location.href);

						// Remove the filter from the URL
						if (filterType === 'price') {
							url.searchParams.delete('min_price');
							url.searchParams.delete('max_price');
						} else {
							// Handle other filters
							url.searchParams.delete(filterType);
						}

						// Redirect to the updated URL
						window.location.href = url.toString();
					});
				});
			</script>

			<script>
				$(document).ready(function() {
					$('#apply-filter').on('click', function() {
						// Ensure filter_applied is set to 1
						$("#filter_applied").val("1");

						// Create a URL object to manipulate the current URL
						const url = new URL(window.location.href);

						// Append existing filters to the URL
						url.searchParams.set('min_price', $('#min_price').val());
						url.searchParams.set('max_price', $('#max_price').val());
						url.searchParams.set('filter_applied', '1'); // Ensure filter_applied is set

						// Append other filters
						if ($("#category").val()) {
							url.searchParams.set('category', $("#category").val());
						}
						if ($("#color").val()) {
							url.searchParams.set('color', $("#color").val());
						}
						if ($("#brand").val()) {
							url.searchParams.set('brand', $("#brand").val());
						}

						// Redirect to the updated URL
						window.location.href = url.toString();
					});
				});
			</script>




</body>

</html>