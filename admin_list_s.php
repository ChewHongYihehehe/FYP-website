<?php
include 'connect.php';
require 'vendor/autoload.php'; // Autoload PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

// Fetch admins
$admins = [];
$stmt = $conn->prepare("SELECT * FROM admin WHERE role = 'admin'"); // Fetch only admins
$stmt->execute();
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

$error_message = '';


// Handle adding a new admin
if (isset($_POST['add_admin'])) {
    $admin_name = $_POST['admin_name'];
    $admin_email = $_POST['admin_email'];
    $admin_phone = $_POST['admin_phone'];
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT); // Set a default password

    // Check if the email already exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM admin WHERE admin_email = :admin_email");
    $stmt->bindParam(':admin_email', $admin_email);
    $stmt->execute();
    $email_exists = $stmt->fetchColumn() > 0;

    // Check if the phone number already exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM admin WHERE admin_phone = :admin_phone");
    $stmt->bindParam(':admin_phone', $admin_phone);
    $stmt->execute();
    $phone_exists = $stmt->fetchColumn() > 0;



    // If either exists, set an error message
    if ($email_exists) {
        $error_message = "Email already exists.";
    } elseif ($phone_exists) {
        $error_message = "Phone number already exists.";
    } else {
        // Proceed to insert the new admin
        $stmt = $conn->prepare("INSERT INTO admin (admin_password, admin_name, admin_email, admin_phone, admin_status) 
                                 VALUES (:admin_password, :admin_name, :admin_email, :admin_phone, 'Active')");
        $stmt->bindParam(':admin_password', $admin_password);
        $stmt->bindParam(':admin_name', $admin_name);
        $stmt->bindParam(':admin_email', $admin_email);
        $stmt->bindParam(':admin_phone', $admin_phone);

        if ($stmt->execute()) {
            // Send email to the new admin
            sendAdminNotificationEmail($admin_name, $admin_email, $admin_phone, 'admin123'); // Default password



            header("Location: admin_list_s.php");
            exit();
        } else {
            $error_message = "Error adding admin.";
        }
    }
}

echo "<script>var errorMessage = " . json_encode($error_message) . ";</script>";


// Handle terminating or reactivating an admin
if (isset($_GET['id']) && isset($_GET['status'])) {
    $admin_id = $_GET['id'];
    $status = $_GET['status'];
    if ($status === 'terminate') {
        // Terminate admin
        $stmt = $conn->prepare("
                UPDATE admin
                SET 
                    admin_status = 'terminated'
                WHERE id = :id
            ");
    } else {
        // Reactivate admin
        $stmt = $conn->prepare("
                UPDATE admin
                SET
                    admin_status = 'active'
                WHERE id = :id
            ");
    }
    $stmt->bindParam(':id', $admin_id, PDO::PARAM_INT);
    $stmt->execute();

    header("Location: admin_list_s.php");
    exit();
}


function sendAdminNotificationEmail($name, $email, $phone, $default_password)
{
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
        $mail->setFrom('step@gmail.com', 'Step Shoes Shop');
        $mail->addAddress($email);


        ob_start();
        include 'admin_email.php';
        $email_body = ob_get_clean();

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Welcome to Step Shoes Shop - Admin Account Created';
        $mail->Body = $email_body;

        $mail->send();
    } catch (Exception $e) {
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admins</title>
    <link rel="stylesheet" type="text/css" href="assets/css/admin_list.css">
</head>

<body>

    <?php include 'sidebar.php'; ?>
    <div class="container">

        <div class="product-display">
            <h1>Manage Admins</h1>

            <div class="btn-container page-top">
                <button class="btn-add-new" id="addAdminBtn" style="margin-bottom:20px;" class="btn">Add Admin</button>
            </div>

            <table class="product-display-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $admin): ?>
                        <tr>
                            <td><?= htmlspecialchars($admin['id']); ?></td>
                            <td><?= htmlspecialchars($admin['admin_name']); ?></td>
                            <td><?= htmlspecialchars($admin['admin_email']); ?></td>
                            <td><?= htmlspecialchars($admin['admin_phone']); ?></td>
                            <td>
                                <span class="<?= strtolower($admin['admin_status']) == 'active' ? 'status-active' : 'status-inactive'; ?>">
                                    <?= htmlspecialchars($admin['admin_status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if (strtolower($admin['admin_status']) == 'terminated'): ?>
                                    <a href="?id=<?= $admin['id']; ?>&status=active"
                                        class="btn btn-reactivate"
                                        onclick="return confirm('Are you sure you want to reactivate this admin?');">
                                        Reactivate
                                    </a>
                                <?php else: ?>
                                    <a href="?id=<?= $admin['id']; ?>&status=terminate"
                                        class="btn btn-terminate"
                                        onclick="return confirm('Are you sure you want to terminate this admin?');">
                                        Terminate
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Add Admin Form -->
    <div id="addAdmin_Modal" class="modal">
        <div class="modal-content">
            <span class="close-button" id="closeAddAdminModal">&times;</span>
            <form method="post" onsubmit="return validateForm()">
                <div class="account-header">
                    <h1 class="account-title">Add Admin</h1>
                </div>
                <?php if (isset($error_message)): ?>
                    <div class="error-message" style="color: red;">
                        <?= htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
                <div class="account-edit">
                    <div class="input-container">
                        <label>Name</label>
                        <input type="text" id="fullname" placeholder="Enter admin name" name="admin_name" required onkeypress="validateFullname(event)">
                        <div id="fullname_error" class="error-message"></div>
                    </div>
                </div>
                <div class="account-edit">
                    <div class="input-container">
                        <label>Email</label>
                        <input type="email" id="email" placeholder="Enter email" name="admin_email" required oninput="validateEmail()">
                        <div id="email_error" class="error-message"></div>
                    </div>
                </div>
                <div class="account-edit">
                    <div class="input-container">
                        <label>Phone</label>
                        <input type="text" id="admin_phone" placeholder="Enter phone number" name="admin_phone" required value="+601" oninput="enforceMalaysianPhoneFormat()">
                        <div id="phone_error" class="error-message"></div>
                    </div>
                </div>
                <div class="btn-container">
                    <button type="submit" class="btn-save" name="add_admin">Add Admin</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addAdmin_Modal = document.getElementById('addAdmin_Modal');
            const closeAddAdminModal = document.getElementById('closeAddAdminModal');
            const addAdminButton = document.getElementById('addAdminBtn');

            addAdminButton.addEventListener('click', function() {
                addAdmin_Modal.style.display = 'block';
            });

            closeAddAdminModal.addEventListener('click', function() {
                addAdmin_Modal.style.display = 'none';
            });

            window.addEventListener('click', function(event) {
                if (event.target === addAdmin_Modal) {
                    addAdmin_Modal.style.display = 'none';
                }
            });
            // Check if there is an error message and display it
            if (errorMessage) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage,
                    confirmButtonText: 'OK'
                });
            }
        });

        function enforceMalaysianPhoneFormat() {
            const phoneInput = document.getElementById("admin_phone");
            const phoneError = document.getElementById("phone_error");
            const prefix = "+601";

            // Ensure the phone number starts with the correct prefix
            if (!phoneInput.value.startsWith(prefix)) {
                phoneInput.value = prefix;
            }

            // Remove the prefix for processing
            let currentInput = phoneInput.value.replace(prefix, '');
            currentInput = currentInput.replace(/[^0-9]/g, ''); // Remove non-numeric characters

            // Limit the length to 8 digits (after the prefix)
            if (currentInput.length > 8) {
                currentInput = currentInput.slice(0, 8);
            }

            // Format the phone number
            if (currentInput.length > 1) {
                currentInput = currentInput.slice(0, 1) + '-' + currentInput.slice(1);
            }
            if (currentInput.length > 5) {
                currentInput = currentInput.slice(0, 5) + ' ' + currentInput.slice(5);
            }

            // Update the input value with the formatted phone number
            phoneInput.value = prefix + currentInput;

            // Validate phone number length
            if (phoneInput.value.length !== 14) {
                phoneError.textContent = "Phone number must be exactly 11 characters long.";
                phoneError.style.display = "block";
            } else {
                phoneError.textContent = ""; // Clear error message if valid
                phoneError.style.display = "none";
            }
        }

        function validateFullname(event) {
            const fullnameInput = document.getElementById("fullname");
            const char = String.fromCharCode(event.which);
            const fullnameError = document.getElementById("fullname_error");

            if (!/^[a-zA-Z\s]*$/.test(char)) {
                event.preventDefault();
                fullnameError.textContent = "Full name can only contain letters and spaces.";
            } else {
                fullnameError.textContent = "";
            }
        }

        function validateEmail() {
            const emailInput = document.getElementById("email");
            const email = emailInput.value;
            const errorMessage = document.getElementById("email_error");

            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

            if (!emailRegex.test(email)) {
                errorMessage.textContent = "Please enter a valid email address.";
                return false;
            }

            const [localPart, domainPart] = email.split("@");
            if (!domainPart) {
                errorMessage.textContent = "Invalid email format.";
                return false;
            }

            const repeatedCharsRegex = /(.)\1{2,}/;
            if (repeatedCharsRegex.test(domainPart)) {
                errorMessage.textContent = "Domain name cannot have repeated characters.";
                return false;
            }

            const commonMisspellings = ["gmal.com", "yaho.com", "hotmal.com", "outlok.com"];
            if (commonMisspellings.includes(domainPart)) {
                errorMessage.textContent = "Did you mean 'gmail.com', 'yahoo.com', 'hotmail.com' or 'outlook.com'?";
                return false;
            }

            const validTLDs = ["com", "org", "net", "edu", "gov", "mil", "info", "io", "co", "me"];
            const domainParts = domainPart.split(".");
            const tld = domainParts[domainParts.length - 1];
            if (!validTLDs.includes(tld)) {
                errorMessage.textContent = "Invalid top-level domain.";
                return false;
            }

            errorMessage.textContent = "";
            return true;
        }

        function validateForm() {
            const fullname = document.getElementById("fullname").value;
            const email = document.getElementById("email").value;
            const phone = document.getElementById("admin_phone").value;
            const phoneError = document.getElementById("phone_error");
            const emailError = document.getElementById("email_error");
            const fullnameError = document.getElementById("fullname_error");

            // Clear previous error messages
            phoneError.textContent = "";
            emailError.textContent = "";
            fullnameError.textContent = "";

            let isValid = true;
            let errorMessages = [];

            console.log("Fullname:", fullname);
            console.log("Email:", email);
            console.log("Phone:", phone);

            // Check if all fields are filled
            if (!fullname) {
                errorMessages.push("Full name is required.");
                isValid = false;
            }

            if (!email) {
                errorMessages.push("Email is required.");
                isValid = false;
            }

            if (!phone) {
                errorMessages.push("Phone number is required.");
                isValid = false;
            }

            // Check phone number length
            if (phone.length !== 14) {
                errorMessages.push("Phone number must be exactly 11 characters long.");
                isValid = false;
            }

            // Check if email is valid
            if (!validateEmail()) {
                isValid = false; // If email is invalid, set isValid to false
            }

            // Check if fullname is valid
            if (fullnameError.textContent) {
                isValid = false;
            }

            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: errorMessages.join('<br>'),
                    confirmButtonText: 'OK'
                });
                return false; // Prevent form submission
            }

            return true; // Allow form submission
        }
    </script>

</body>

</html>