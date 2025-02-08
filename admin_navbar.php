<?php
include 'connect.php';
session_start();

// Fetch existing navbar items
$stmt = $conn->prepare("SELECT * FROM navbar_menu ORDER BY position ASC");
$stmt->execute();
$navbar_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

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


if (isset($_POST['update_navbar'])) {
    // Loop through each navbar item
    foreach ($_POST['navbar_items'] as $id => $item) {
        $title = $item['title'];
        $link = $item['link'];
        $position = $item['position'];

        // Update the navbar item
        $update_query = "UPDATE navbar_menu SET title = :title, link = :link WHERE id = :id";
        $stmt = $conn->prepare($update_query);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':link', $link, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if (!$stmt->execute()) {
            $error_message = "Error updating navbar items.";
        }
    }
    if (empty($error_message)) {
        $success_message = "Navbar items updated successfully!";
    }
}


if (isset($_POST['add_navbar'])) {
    $new_title = $_POST['new_title'];
    $new_link = $_POST['new_link'];

    // Determine the new position
    $new_position = count($navbar_items) + 1;

    // Insert the new navbar item
    $insert_query = "INSERT INTO navbar_menu (title, link, position) VALUES (:title, :link, :position)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bindParam(':title', $new_title, PDO::PARAM_STR);
    $stmt->bindParam(':link', $new_link, PDO::PARAM_STR);
    $stmt->bindParam(':position', $new_position, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $success_message = "New navbar item added successfully!";
    } else {
        $error_message = "Error adding new navbar item.";
    }
}

// Handle deletion of a navbar item
if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];

    // Delete the navbar item
    $delete_query = "DELETE FROM navbar_menu WHERE id = :id";
    $stmt = $conn->prepare($delete_query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $success_message = "Navbar item deleted successfully!";
    } else {
        $error_message = "Error deleting navbar item.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Navbar Menu</title>
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
    <style>
        .input-container {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .input-container label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .position-number {
            font-weight: bold;
            color: #fe4c50;
            margin-right: 10px;
        }

        .btn-container {
            margin-top: 20px;
        }

        button {
            padding: 10px 15px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            opacity: 0.9;
            /* Slightly change opacity on hover */
        }

        .add-navbar-button {
            background-color: #fe4c50;
        }

        .add-navbar-button:hover {
            background-color: #e03e3e;
        }

        .delete-navbar-button {
            background-color: crimson;
            margin-bottom: 10px;
        }

        .delete-navbar-button:hover {
            background-color: darkred;
        }

        .add-navbar-form {
            margin-top: 40px;
        }
    </style>
</head>

<body>

    <?php include 'sidebar.php'; ?>
    <div class="container">
        <h1>Edit Navbar Menu</h1>

        <form method="post">
            <?php foreach ($navbar_items as $item): ?>
                <div class="input-container">
                    <div class="position-number">Position: <?= htmlspecialchars($item['position']); ?></div>
                    <label>Title</label>
                    <input type="text" name="navbar_items[<?= $item['id']; ?>][title]" value="<?= htmlspecialchars($item['title']); ?>" required>
                    <label>Link</label>
                    <input type="text" name="navbar_items[<?= $item['id']; ?>][link]" value="<?= htmlspecialchars($item['link']); ?>" required>
                    <input type="hidden" name="navbar_items[<?= $item['id']; ?>][position]" value="<?= htmlspecialchars($item['position']); ?>">
                </div>
                <div class="btn-container">
                    <input type="hidden" name="delete_id" value="<?= $item['id']; ?>">
                    <button type="button" class="delete-navbar-button" onclick="confirmDelete(<?= $item['id']; ?>)">Delete</button>
                </div>
            <?php endforeach; ?>
            <div class="btn-container">
                <button type="submit" name="update_navbar">Update Navbar Menu</button>
            </div>
        </form>


        <h2 class="add-navbar-form">Add New Navbar Item</h2>
        <form method="post">
            <div class="input-container">
                <label>Title</label>
                <input type="text" name="new_title" required>
                <label>Link</label>
                <input type="text" name="new_link" required>
                <label>Position</label>
                <input type="text" value="<?= count($navbar_items) + 1; ?>" readonly>
            </div>
            <div class="btn-container">
                <button type="submit" name="add_navbar" class="add-navbar-button">Add Navbar Item</button>
            </div>
        </form>
    </div>


    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#fe4c50',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create a form to submit the delete request
                    const form = document.createElement('form');
                    form.method = 'post';
                    form.innerHTML = `<input type="hidden" name="delete_id" value="${id}">`;
                    document.body.appendChild(form);
                    form.submit(); // Submit the form
                }
            });
        }


        // Check if the success message is set
        <?php if (!empty($success_message)): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?= $success_message; ?>',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'admin_navbar.php'; // Redirect to admin navbar page
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
            });
        <?php endif; ?>
    </script>

</body>

</html>