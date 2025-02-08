<?php
include 'connect.php';
session_start();

// Initialize messages
$success_message = "";
$error_message = "";


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



// Fetch existing home page content
$stmt = $conn->prepare("SELECT * FROM home_page_content WHERE id = 1");
$stmt->execute();
$home_content = $stmt->fetch(PDO::FETCH_ASSOC);


// Handle form submission
if (isset($_POST['update_home_content'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];

    // Handle file upload
    if (isset($_FILES['home_image']) && $_FILES['home_image']['error'] == 0) {
        $target_dir = "assets/image/";

        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Generate unique filename
        $file_extension = pathinfo($_FILES['home_image']['name'], PATHINFO_EXTENSION);
        $unique_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $unique_filename;

        // Move uploaded file
        if (move_uploaded_file($_FILES['home_image']['tmp_name'], $target_file)) {
            // Update the home page content with the new image
            $update_query = "UPDATE home_page_content SET title = :title, description = :description, image = :image WHERE id = 1";
            $stmt = $conn->prepare($update_query);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':image', $unique_filename, PDO::PARAM_STR);
        } else {
            $error_message = "Sorry, there was an error uploading your file.";
        }
    } else {
        // Update without changing the image
        $update_query = "UPDATE home_page_content SET title = :title, description = :description WHERE id = 1";
        $stmt = $conn->prepare($update_query);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
    }

    // Execute the statement
    if ($stmt->execute()) {
        $success_message = "Home page content updated successfully!";
    } else {
        $error_message = "Error updating home page content.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Home Page Content</title>
    <link rel="stylesheet" type="text/css" href="assets/css/admin_header.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <?php include 'sidebar.php'; ?>
    <div class="container">
        <h1>Edit Home Page Content</h1>

        <form method="post" enctype="multipart/form-data">
            <div class="input-container">
                <label>Title</label>
                <input type="text" name="title" value="<?= htmlspecialchars($home_content['title']); ?>" required>
            </div>
            <div class="input-container">
                <label>Description</label>
                <textarea name="description" required><?= htmlspecialchars($home_content['description']); ?></textarea>
            </div>
            <div class="input-container">
                <label>Current Image</label>
                <?php if (!empty($home_content['image'])): ?>
                    <img src="assets/image/<?= htmlspecialchars($home_content['image']); ?>" alt="Current Home Image" style="max-width: 200px;">
                <?php else: ?>
                    <p>No image uploaded.</p>
                <?php endif; ?>
            </div>
            <div class="input-container">
                <label>Upload New Image</label>
                <input type="file" name="home_image" accept="image/*">
            </div>
            <div class="btn-container">
                <button type="submit" name="update_home_content">Update Home Content</button>
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
                    window.location.href = 'admin_header.php'; // Redirect to admin_header.php
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
            }).then(() => {
                // If the error is related to termination or not logged in, redirect to login page
                <?php if (strpos($error_message, 'terminated') !== false || strpos($error_message, 'logged in') !== false): ?>
                    window.location.href = 'admin_login.php';
                <?php endif; ?>
            });
        <?php endif; ?>
    </script>

</body>

</html>