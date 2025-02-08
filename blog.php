<?php

include 'connect.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/categories.css"> <!-- Use your existing CSS -->
    <title>Blog Page</title>
    <style>
        .video {
            text-align: center;
            margin-bottom: 20px;
        }

        .breadcrumbs {
            margin-left: 20px;
        }

        .video-list {
            margin-left: 100px;
            margin-bottom: 20px;
            margin-right: 100px;
        }

        .video-list h3 {
            margin-left: 20px;
        }
    </style>
</head>

<body>

    <?php include 'header.php'; ?> <!-- Include your sidebar -->

    <section>
        <div class="form-box">
            <h2>Blog</h2>

            <div class="breadcrumbs d-flex flex-row align-items-center">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="blog.php"><i class="fa fa-angle-right" aria-hidden="true"></i>Blog</a></li>
                </ul>
            </div>
            <div class="video-list">
                <h3>Video List</h3>
                <?php
                try {
                    // Prepare and execute the SQL statement
                    $stmt = $conn->prepare("SELECT video_url FROM videos");
                    $stmt->execute();

                    // Fetch all results
                    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Display each video
                    foreach ($videos as $row) {
                        echo '<div class="video">';
                        echo '<iframe width="100%" height="315" src="' . htmlspecialchars($row['video_url']) . '" frameborder="0" allowfullscreen></iframe>';
                        echo '</div>';
                    }
                } catch (PDOException $e) {
                    echo "Error fetching videos: " . $e->getMessage();
                }
                ?>
            </div>
        </div>
    </section>

</body>

</html>