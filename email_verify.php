<!-- email_template.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Inline styles for email compatibility */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            text-align: center;
        }

        p {
            font-size: 16px;
            line-height: 1.5;
            color: #555;
        }

        .button-container {
            text-align: center;
            margin-top: 20px;
            align-content: center;
        }

        .button-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background: #fe4c50;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background: #fe4c50;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }

        .store-link {
            color: #fe4c50;
            text-decoration: none;
        }

        footer {
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Welcome to STEP Shoes Shop, <?php echo $username; ?>!</h1>
        <p>Thank you for registering with us. To complete your registration, please verify your email address by clicking the button below:</p>
        <a href="<?php echo $verification_link; ?>">Verify Email Address</a>
        <p>If you did not create an account, no further action is required.</p>
        <p>Or, you can <a class="store-link" href="http://localhost/FYP-website-3/home.php">visit our store</a> to browse our products. </p>
        <footer>
            <p>Thank you!<br>Step Shoes Shop Team</p>
        </footer>
    </div>
</body>

</html>