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
        <h1>Welcome to Step Shoes Shop, <?= htmlspecialchars($name); ?>!</h1>
        <p>Congratulations! You have been added as an admin of Step Shoes Shop.</p>
        <p>Your information:</p>
        <ul>
            <li><strong>Name:</strong> <?= htmlspecialchars($name); ?></li>
            <li><strong>Email:</strong> <?= htmlspecialchars($email); ?></li>
            <li><strong>Phone:</strong> <?= htmlspecialchars($phone); ?></li>
            <li><strong>Default Password:</strong> <?= htmlspecialchars($default_password); ?></li>
        </ul>
        <p>You can change your password after logging in.</p>
        <p>To log in, please click the button below:</p>
        <div class="button-container">
            <a href="http://localhost/FYP-website/admin_login.php" class="button-link">Login to Admin Panel</a>
        </div>
        <p>Thank you for being a part of our team!</p>
        <div class="footer">
            <p>Best Regards,<br>Step Shoes Shop Team</p>
        </div>
    </div>
</body>

</html>