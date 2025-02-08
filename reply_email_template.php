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
        <h1>Reply from Step Shoes Shop</h1>
        <p>Dear <?php echo htmlspecialchars($username); ?>,</p>
        <p>Thank you for reaching out to us! Below is our response to your message:</p>
        <p><strong>Your Message:</strong></p>
        <p><?php echo nl2br(htmlspecialchars($user_message)); ?></p>
        <p><strong>Our Reply:</strong></p>
        <p><?php echo nl2br(htmlspecialchars($reply_message)); ?></p>
        <footer>
            <p>Thank you!<br>Step Shoes Shop Team</p>
        </footer>
    </div>
</body>

</html>