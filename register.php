<?php
require 'connect.php'; // Include database connection

$registration_successful = false; // Flag for checking if registration is successful
$error_message = ""; // Variable to hold error messages
、br
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $postcode = trim($_POST['postcode']); // Capture postcode input
    $region = trim($_POST['region']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate input
    if (empty($username) || empty($email) || empty($phone) || empty($postcode) || empty($region) || empty($password) || empty($confirm_password)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } elseif (!preg_match("/^\d{5}$/", $postcode)) { // Check if postcode is exactly 5 digits
        $error_message = "Invalid postcode. It must be a 5-digit number.";
    } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{8,12}$/", $password)) {
        $error_message = "Password must be 8-12 characters, with uppercase, lowercase, a number, and a special character.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Check if email already exists
        $check_email_query = "SELECT * FROM users WHERE email = :email";
        $stmt = $conn->prepare($check_email_query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $error_message = "An account with this email already exists.";
        } else {
            // Insert new user into database
            $stmt = $conn->prepare("INSERT INTO users (fullname, email, phone, postcode, region, password) VALUES (:fullname, :email, :phone, :postcode, :region, :password)");
            
            if ($stmt) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash password
                $stmt->bindParam(':fullname', $username, PDO::PARAM_STR);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
                $stmt->bindParam(':postcode', $postcode, PDO::PARAM_STR);
                $stmt->bindParam(':region', $region, PDO::PARAM_STR);
                $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    $registration_successful = true; // Set success flag
                } else {
                    $error_message = "Error: " . $stmt->errorInfo()[2]; // Get detailed error message
                }
            } else {
                $error_message = "Error preparing statement: " . $conn->errorInfo()[2];
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/5.5.2/collection/components/icon/icon.min.css">
    <title>Register</title>
</head>
<body>
    <section>
        <div class="form-box">
            <h2>Register</h2>
            <form action="register.php" method="post" onsubmit="return validatePasswords()">
                <div class="form-row">
                    <div class="form-column">
                        <div class="inputbox">
                            <input type="text" name="fullname" required>
                            <label>Full name</label>
                            <ion-icon name="person-outline"></ion-icon>
                        </div>
                        <div class="inputbox">
                            <input type="email" name="email" required>
                            <label>Email</label>
                            <ion-icon name="mail-outline"></ion-icon>
                        </div>
                        <div class="inputbox">
                            <input type="tel" name="phone" required>
                            <label>Phone</label>
                            <ion-icon name="call-outline"></ion-icon>
                        </div>
                        <div class="inputbox">
                            <select name="region" required>
                                <option value="" disabled selected>Select Region</option>
                                <option value="Johor">Johor</option>
                                <option value="Kedah">Kedah</option>
                                <option value="Kelantan">Kelantan</option>
                                <option value="Kuala Lumpur">Kuala Lumpur</option>
                                <option value="Labuan">Labuan</option>
                                <option value="Melaka">Melaka</option>
                                <option value="Negeri Sembilan">Negeri Sembilan</option>
                                <option value="Pahang">Pahang</option>
                                <option value="Penang">Penang</option>
                                <option value="Perak">Perak</option>
                                <option value="Perlis">Perlis</option>
                                <option value="Putrajaya">Putrajaya</option>
                                <option value="Sabah">Sabah</option>
                                <option value="Sarawak">Sarawak</option>
                                <option value="Selangor">Selangor</option>
                                <option value="Terengganu">Terengganu</option>
                            </select>
                            <ion-icon name="map-outline"></ion-icon>
                        </div>
                    </div>
                    <div class="form-column">
                        <div class="inputbox">
                            <input type="text" name="postcode" required>
                            <label>Postcode</label>
                            <ion-icon name="location-outline"></ion-icon>
                        </div>
                        <div class="inputbox">
                            <input type="password" id="password" name="password" required>
                            <label>Password</label>
                            <ion-icon name="lock-closed-outline"></ion-icon>
                        </div>
                        <small class="password-reminder">
                            Password must be 8-12 characters, with uppercase, lowercase, a number, and a special character.
                        </small>
                        <div class="inputbox">
                            <input type="password" id="confirm_password" name="confirm_password" required oninput="checkPasswordMatch()">
                            <label>Confirm Password</label>
                            <ion-icon name="lock-closed-outline"></ion-icon>
                        </div>
                        <span id="password_error" class="error-message"><?php echo $error_message; ?></span>
                    </div>
                </div>
                <input type="submit" value="Register">
                <div class="login">
                    <p>Already have an account? <a href="login.php">Login here</a>.</p>
                </div>
            </form>
        </div>
    </section>

    <script src="https://unpkg.com/ionicons@5.5.2/dist/ionicons.js"></script>
    <script>
        function checkPasswordMatch() {
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirm_password").value;
            const errorMessage = document.getElementById("password_error");

            if (password !== confirmPassword) {
                errorMessage.textContent = "Passwords do not match!";
            } else {
                errorMessage.textContent = "";
            }
        }

        function validatePasswords() {
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirm_password").value;
            const strongPasswordRegex = /^(?=.*[a-z])(?=.*[a-z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{8,12}$/;

            if (!strongPasswordRegex.test(password)) {
                alert("Password must meet the required criteria.");
                return false;
            }

            if (password !== confirmPassword) {
                alert("Passwords do not match.");
                return false;
            }
            return true;
        }
    </script>

    <?php if ($registration_successful): ?>
        <script>
            alert("Registration successful! You can now log in.");
            window.location.href = "login.php";
        </script>
    <?php endif; ?>
</body>
</html>
