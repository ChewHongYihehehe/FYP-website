<?php
// verify.php
require 'connect.php'; // Include the database connection

$error_message = "";
$success_message = "";

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token exists and is valid
    $query = "SELECT * FROM users WHERE verification_token = :token";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Token is valid, update the user's verification status
        $update_query = "UPDATE users SET is_verified = 1, verification_token = NULL WHERE verification_token = :token";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bindParam(':token', $token, PDO::PARAM_STR);

        if ($update_stmt->execute()) {
            // Set success message to be displayed in SweetAlert
            $success_message = "Your email has been successfully verified. You can now log in.";
        } else {
            $error_message = "An error occurred while verifying your email. Please try again.";
        }
    } else {
        $error_message = "This token has already been used. Please register again.";
    }
} else {
    $error_message = "No token provided.";
}

$conn = null; // Close the connection
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <title>Email Verification</title>
</head>

<body>
    <div class="container">
        <?php if (!empty($success_message)): ?>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                Swal.fire({
                    title: 'Success!',
                    text: '<?php echo $success_message; ?>',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'login.php'; // Redirect to login page
                    }
                });
            </script>
        <?php elseif (!empty($error_message)): ?>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                Swal.fire({
                    title: 'Error!',
                    text: '<?php echo $error_message; ?>',
                    icon: 'error',
                    confirmButtonText: 'Register Again'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'register.php'; // Redirect to register page
                    }
                });
            </script>
        <?php endif; ?>
    </div>
</body>

</html>