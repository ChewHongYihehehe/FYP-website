<?php
// Centralized user status check
function checkTerminatedUser()
{
    // Ensure session is started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }


    if (isset($_SESSION['user_id'])) {

        include 'connect.php';

        // Prepare statement to check user status
        $stmt = $conn->prepare("SELECT status FROM users WHERE id = :user_id");
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // If user is terminated
        if ($user && $user['status'] === 'terminated') {

            $_SESSION['account_terminated'] = true;
            // Redirect to login page
            header("Location: login.php?terminated=true");
            exit();
        }
    }
}

// Automatically run the check when this file is included
checkTerminatedUser();
