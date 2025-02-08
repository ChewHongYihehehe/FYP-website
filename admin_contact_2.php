<?php
include 'connect.php';
session_start();



$error_message = '';

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    $error_message = 'You must be logged in to view this page.';
} else {
    // Fetch admin details
    $admin_id = $_SESSION['admin_id'];
    $stmt = $conn->prepare("SELECT admin_status FROM admin WHERE id = :admin_id");
    $stmt->bindParam(':admin_id', $admin_id);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the admin's status is terminated
    if ($admin && strtolower($admin['admin_status']) === 'terminated') {
        $error_message = 'Your account has been terminated. Please contact support.';
    }
}


// Fetch existing contact page content
$stmt = $conn->prepare("SELECT * FROM contact_page_content WHERE id = 1");
$stmt->execute();
$contact_content = $stmt->fetch(PDO::FETCH_ASSOC);

// Initialize messages
$success_message = "";
$error_message = "";

// Handle form submission
if (isset($_POST['update_contact_content'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $open_hours = $_POST['open_hours'];
    $closed_info = $_POST['closed_info'];
    $get_in_touch_title = $_POST['get_in_touch_title'];
    $form_description = $_POST['form_description'];


    // Update the contact page content
    $update_query = "UPDATE contact_page_content SET title = :title, description = :description, phone = :phone, email = :email, open_hours = :open_hours, closed_info = :closed_info, get_in_touch_title = :get_in_touch_title, form_description = :form_description WHERE id = 1";
    $stmt = $conn->prepare($update_query);
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':description', $description, PDO::PARAM_STR);
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':open_hours', $open_hours, PDO::PARAM_STR);
    $stmt->bindParam(':closed_info', $closed_info, PDO::PARAM_STR);
    $stmt->bindParam(':get_in_touch_title', $get_in_touch_title, PDO::PARAM_STR);
    $stmt->bindParam(':form_description', $form_description, PDO::PARAM_STR);

    // Execute the statement
    if ($stmt->execute()) {
        $success_message = "Contact page content updated successfully!";
    } else {
        $error_message = "Error updating contact page content.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Contact Us Content</title>
    <link rel="stylesheet" type="text/css" href="assets/css/admin_header.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Check if there is an error message to display
        var errorMessage = <?= json_encode($error_message); ?>; // Convert PHP variable to JavaScript

        if (errorMessage) {
            window.onload = function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Access Denied',
                    text: errorMessage,
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'admin_login.php';
                });
            };
        }
    </script>
</head>

<body>

    <?php include 'sidebar.php'; ?>
    <div class="container">
        <h1>Manage Contact Us Content</h1>

        <form method="post">
            <div class="input-container">
                <label>Title</label>
                <input type="text" name="title" value="<?= htmlspecialchars($contact_content['title']); ?>" required>
            </div>
            <div class="input-container">
                <label>Description</label>
                <textarea name="description" required><?= htmlspecialchars($contact_content['description']); ?></textarea>
            </div>
            <div class="input-container">
                <label>Phone Number</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($contact_content['phone']); ?>" required>
            </div>
            <div class="input-container">
                <label>Email Address</label>
                <input type="email" name="email" value="<?= htmlspecialchars($contact_content['email']); ?>" required>
            </div>
            <div class="input-container">
                <label>Open Hours</label>
                <input type="text" name="open_hours" value="<?= htmlspecialchars($contact_content['open_hours']); ?>" required>
            </div>
            <div class="input-container">
                <label>Closed Information</label>
                <input type="text" name="closed_info" value="<?= htmlspecialchars($contact_content['closed_info']); ?>" required>
            </div>
            <div class="input-container">
                <label>Get In Touch Title</label>
                <input type="text" name="get_in_touch_title" value="<?= htmlspecialchars($contact_content['get_in_touch_title']); ?>" required>
            </div>
            <div class="input-container">
                <label>Form Description</label>
                <textarea name="form_description" required><?= htmlspecialchars($contact_content['form_description']); ?></textarea>
            </div>
            <div class="btn-container">
                <button type="submit" name="update_contact_content">Update Contact Content</button>
            </div>
        </form>
    </div>

    <script>
        // Check if the success message is set
        <?php if (!empty($success_message)): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?= $success_message; ?>',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'admin_contact_2.php'; // Redirect to admin_contact_2.php
                }
            });
        <?php endif; ?>

        // Check if the error message is set
        <?php if (!empty($error_message)): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '<?= $error_message; ?>',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'admin_contact_2.php'; // Redirect to admin_contact_2.php
                }
            });
        <?php endif; ?>
    </script>


</body>

</html>