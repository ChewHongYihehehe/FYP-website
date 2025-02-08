<?php
include 'connect.php';
include 'header.php';

$stmt = $conn->prepare("SELECT * FROM contact_page_content WHERE id = 1");
$stmt->execute();
$contact_content = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$name = $_POST['name'] ?? '';
	$email = $_POST['email'] ?? '';
	$phone = $_POST['phone'] ?? '';
	$message = $_POST['message'] ?? '';
	$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

	$stmt = $conn->prepare("INSERT INTO messages (user_id, name, email, number, message)
								VALUES (:user_id, :name, :email, :number, :message)");
	$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
	$stmt->bindParam(':name', $name);
	$stmt->bindParam(':email', $email);
	$stmt->bindParam(':number', $phone);
	$stmt->bindParam(':message', $message);

	if ($stmt->execute()) {
		$success_message = "Message sent successfullly!";
	} else {
		$error_message = "Failed to send message. Please try again.";
	}
}

?>

<head>
	<link rel="stylesheet" type="text/css" href="assets/css/contact_styles.css">
</head>

<body>

	<div class="super_container">
		<div class="container contact_container">
			<div class="row">
				<div class="col">

					<!-- Breadcrumbs -->

					<div class="breadcrumbs d-flex flex-row align-items-center">
						<ul>
							<li><a href="home.php">Home</a></li>
							<li class="active"><a href="#"><i class="fa fa-angle-right" aria-hidden="true"></i>Contact</a></li>
						</ul>
					</div>

				</div>
			</div>
			<!-- Contact Us -->

			<div class="row">

				<div class="col-lg-6 contact_col">
					<div class="contact_contents">
						<h1><?= htmlspecialchars($contact_content['title']); ?></h1>
						<p><?= htmlspecialchars($contact_content['description']); ?></p>
						<div>
							<p><?= htmlspecialchars($contact_content['phone']); ?></p>
							<p><?= htmlspecialchars($contact_content['email']); ?></p>
						</div>
						<div>
							<p>Open hours: <?= htmlspecialchars($contact_content['open_hours']); ?></p> <!-- Opening hours -->
							<p>Closed: <?= htmlspecialchars($contact_content['closed_info']); ?></p> <!-- Closed information -->
						</div>
					</div>


				</div>

				<div class="col-lg-6 get_in_touch_col">
					<div class="get_in_touch_contents">
						<h1><?= htmlspecialchars($contact_content['get_in_touch_title']); ?></h1>
						<p><?= htmlspecialchars($contact_content['form_description']); ?></p>

						<?php
						if (isset($success_message)) {
							echo "<div class='alert alert-success'>" . htmlspecialchars($success_message) . "</div>";
						}
						if (isset($error_message)) {
							echo "<div class='alert alert-danger'>" . htmlspecialchars($error_message) . "</div>";
						}
						?>


						<form method="POST" action="">
							<div>
								<input id="input_name" class="form_input input_name input_ph" type="text" name="name" placeholder="Name" required="required" data-error="Name is required.">
								<input id="input_email" class="form_input input_email input_ph" type="email" name="email" placeholder="Email" required="required" data-error="Valid email is required.">
								<input id="input_phone" class="form_input input_phone input_ph" type="tel" name="phone" placeholder="Phone Number" required="required" data-error="Phone number is required.">
								<textarea id="input_message" class="input_ph input_message" name="message" placeholder="Message" rows="3" required data-error="Please, write us a message."></textarea>
							</div>
							<div>
								<button id="review_submit" type="submit" class="red_button message_submit_btn trans_300" value="Submit">send message</button>
							</div>
						</form>
					</div>
				</div>

			</div>
		</div>

		<!-- Newsletter -->

		<div class="newsletter">
			<div class="container">
				<div class="row">
					<div class="col-lg-6">
						<div class="newsletter_text d-flex flex-column justify-content-center align-items-lg-start align-items-md-center text-center">
							<h4>Newsletter</h4>
							<p>Subscribe to our newsletter and get 20% off your first purchase</p>
						</div>
					</div>
					<div class="col-lg-6">
						<form action="post">
							<div class="newsletter_form d-flex flex-md-row flex-column flex-xs-column align-items-center justify-content-lg-end justify-content-center">
								<input id="newsletter_email" type="email" placeholder="Your email" required="required" data-error="Valid email is required.">
								<button id="newsletter_submit" type="submit" class="newsletter_submit_btn trans_300" value="Submit">subscribe</button>
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
						<div class="footer_nav_container d-flex flex-sm-row flex-column align-items-center justify-content-lg-start justify-content-center text-center">
							<ul class="footer_nav">
								<li><a href="#">Blog</a></li>
								<li><a href="#">FAQs</a></li>
								<li><a href="contact.html">Contact us</a></li>
							</ul>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="footer_social d-flex flex-row align-items-center justify-content-lg-end justify-content-center">
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
							<div class="cr">©2018 All Rights Reserverd. Template by <a href="#">Colorlib</a> &amp; distributed by <a href="https://themewagon.com">ThemeWagon</a></div>
						</div>
					</div>
				</div>
			</div>
		</footer>

	</div>


	<script src="styles/bootstrap4/bootstrap.min.js"></script>
</body>

</html>