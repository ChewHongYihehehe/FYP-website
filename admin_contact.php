<?php
include 'connect.php';
require 'vendor/autoload.php'; // Autoload PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();


$error_message = '';

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



//Fetch messages
$messages = [];
$stmt = $conn->prepare("SELECT * FROM messages");
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

//Handle deletion of a messages
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    //Delete the message from the message table
    $stmt = $conn->prepare("DELETE FROM messages WHERE id = :id");
    $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
    $stmt->execute();

    header("Location:admin_contact.php");
    exit();
}



if (isset($_POST['send_reply'])) {
    $user_email = $_POST['user_email'];
    $reply_message = $_POST['reply_message'];
    $username = $_POST['user_name'];
    $message_id = $_POST['message_id'];

    // Fetch the original message from the database
    $stmt = $conn->prepare("SELECT message FROM messages WHERE id = :id");
    $stmt->bindParam(':id', $message_id, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $original_message = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_message = $original_message['message']; // Get the original message
    }

    // Prepare the email
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'huangjiaze81@gmail.com'; // Replace with your email
        $mail->Password = 'eqygfyfgaoywwvqj'; // Replace with your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('step@gmail.com', 'Step Shoes Shop'); // Replace with your email
        $mail->addAddress($user_email);


        ob_start();
        include 'reply_email_template.php';
        $email_body = ob_get_clean();


        $mail->isHTML(true);
        $mail->Subject = 'Reply to Your Message';
        $mail->Body = $email_body;

        // Send the email
        $mail->send();

        $update_query = "UPDATE messages SET reply_message = :reply_message WHERE id=:id";
        $stmt = $conn->prepare($update_query);
        $stmt->bindParam(':reply_message', $reply_message, PDO::PARAM_STR);
        $stmt->bindParam(':id', $message_id, PDO::PARAM_INT);
        $stmt->execute();


        echo "<script>alert('Reply sent successfully!');</script>";
    } catch (Exception $e) {
        echo "<script>alert('Failed to send reply. Mailer Error: {$mail->ErrorInfo}');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage User Messages</title>
    <link rel="stylesheet" type="text/css" href="assets/css/admin_contact.css">
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
</head>

<body>

    <?php include 'sidebar.php'; ?>
    <div class="container">
        <h1>Manage User Messages</h1>

        <div class="product-display">
            <table class="product-display-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Number</th>
                        <th>Message</th>
                        <th>Reply Message</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $row_count = 1;
                    foreach ($messages as $message): ?>
                        <tr>
                            <td><?= $row_count++; ?></td>
                            <td><?= htmlspecialchars($message['name']); ?></td>
                            <td><?= htmlspecialchars($message['email']); ?></td>
                            <td><?= htmlspecialchars($message['number']); ?></td>
                            <td><?= htmlspecialchars($message['message']); ?></td>
                            <td><?php if (empty($message['reply_message'])): ?>
                                    <span style="color: #fe4c50;">No reply yet</span>
                                <?php else: ?>
                                    <?= htmlspecialchars($message['reply_message']); ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn edit-category-btn" onclick="openReplyModal('<?= htmlspecialchars($message['email']); ?>', '<?= htmlspecialchars($message['name']); ?>', '<?= htmlspecialchars($message['id']); ?>')">
                                    Reply
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div id="replyModal" class="modal">
            <div class="modal-content">
                <span class="close-button" id="closeReplyModal">&times;</span>
                <form id="replyForm" method="POST" action="admin_contact.php">
                    <input type="hidden" name="user_email" id="replyUser Email">
                    <input type="hidden" name="user_name" id="replyUser Name">
                    <input type="hidden" name="message_id" id="replyMessageId">
                    <div class="account-header">
                        <h1 class="account-title">Reply to user</h1>
                    </div>
                    <div class="account-edit">
                        <div class="input-container">
                            <textarea name="reply_message" required placeholder="Type your reply here..." class="box"></textarea>
                        </div>
                    </div>
                    <div class="btn-container">
                        <button type="submit" class="btn-save" name="send_reply">Send Reply</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        function openReplyModal(email, name, messageId) {
            document.getElementById('replyUser Email').value = email;
            document.getElementById('replyUser Name').value = name;
            document.getElementById('replyMessageId').value = messageId;
            document.getElementById('replyModal').style.display = 'block';
        }

        document.getElementById('closeReplyModal').onclick = function() {
            document.getElementById('replyModal').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('replyModal')) {
                document.getElementById('replyModal').style.display = 'none';
            }
        }
    </script>
</body>

</html>