<?php
include 'connect.php';
session_start();

// Initialize messages
$success_message = "";
$error_message = "";


if (!isset($_SESSION['admin_id'])) {
    $error_message = 'You must be logged in to view this page.';
} else {

    $admin_id = $_SESSION['admin_id'];
    $stmt = $conn->prepare("SELECT admin_status FROM admin WHERE id = :admin_id");
    $stmt->bindParam(':admin_id', $admin_id);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && strtolower($admin['admin_status']) === 'terminated') {
        $error_message = 'Your account has been terminated. Please contact support.';
    }
}

// Fetch existing videos
$stmt = $conn->prepare("SELECT * FROM videos");
$stmt->execute();
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);


if (isset($_POST['upload_video'])) {

    if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] == 0) {
        $target_dir = "assets/video/";

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_extension = pathinfo($_FILES['video_file']['name'], PATHINFO_EXTENSION);
        $unique_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $unique_filename;


        if (move_uploaded_file($_FILES['video_file']['tmp_name'], $target_file)) {

            $insert_query = "INSERT INTO videos (video_url) VALUES (:video_url)";
            $stmt = $conn->prepare($insert_query);
            $video_url = $target_file;
            $stmt->bindParam(':video_url', $video_url, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $success_message = "Video uploaded successfully!";
            } else {
                $error_message = "Error uploading video.";
            }
        } else {
            $error_message = "Sorry, there was an error uploading your file.";
        }
    } else {
        $error_message = "No file uploaded or there was an upload error.";
    }
}

// Handle video deletion
if (isset($_POST['delete_video'])) {
    $delete_id = $_POST['delete_id'];
    $delete_query = "DELETE FROM videos WHERE id = :id";
    $stmt = $conn->prepare($delete_query);
    $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $success_message = "Video deleted successfully!";
    } else {
        $error_message = "Error deleting video.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Videos</title>
    <link rel="stylesheet" type="text/css" href="assets/css/admin_header.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .btn-container button[name="delete_video"] {
            background-color: #fe4c50;
            color: white;
            margin-bottom: 10px;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-container button[name="delete_video"]:hover {
            background-color: #d43f45;
        }
    </style>
</head>

<body>

    <?php include 'sidebar.php'; ?>
    <div class="container">
        <h1>Manage Videos</h1>

        <form method="post" enctype="multipart/form-data">
            <div class="input-container">
                <label>Upload Video File</label>
                <input type="file" name="video_file" accept="video/*" required>
            </div>
            <div class="btn-container">
                <button type="submit" name="upload_video">Upload Video</button>
            </div>
        </form>

        <h2>Existing Videos</h2>
        <div class="video-list">
            <?php if (count($videos) > 0): ?>
                <ul>
                    <?php foreach ($videos as $video): ?>
                        <li>
                            <iframe width="100%" height="315" src="<?= htmlspecialchars($video['video_url']); ?>" frameborder="0" allowfullscreen></iframe>
                            <div class="btn-container">
                                <form method="post" action="">
                                    <input type="hidden" name="delete_id" value="<?= $video['id']; ?>">
                                    <button type="submit" name="delete_video" onclick="return confirm('Are you sure you want to delete this video?');">Delete</button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No videos uploaded yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        <?php if (!empty($success_message)): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?= $success_message; ?>',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'admin_video.php';
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
                <?php if (strpos($error_message, 'terminated') !== false || strpos($error_message, 'logged in') !== false): ?>
                    window.location.href = 'admin_login.php';
                <?php endif; ?>
            });
        <?php endif; ?>
    </script>

</body>

</html>