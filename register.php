<?php
require 'connect.php'; // Include database connection

$registration_successful = false; // Flag for checking if registration is successful
$error_message = ""; // Variable to hold error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $country = trim($_POST['country']);
    $region = trim($_POST['region']);
    $address = trim($_POST['address']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Server-side validation as a backup
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$&%])[A-Za-z\d$&%]{8,12}$/", $password)) {
        $error_message = "Password must be 8-12 characters, with uppercase, lowercase, a number, and a special character ($, &, or %).";
    } else {
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO users (username, email, phone, country, region, address, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        if ($stmt) {
            $stmt->bindParam(1, $username);
            $stmt->bindParam(2, $email);
            $stmt->bindParam(3, $phone);
            $stmt->bindParam(4, $country);
            $stmt->bindParam(5, $region);
            $stmt->bindParam(6, $address);
            $stmt->bindParam(7, $hashed_password);

            if ($stmt->execute()) {
                $registration_successful = true; // Set success flag
            } else {
                $error_message = "Error: " . $stmt->errorInfo()[2];
            }
            $stmt->closeCursor();
        } else {
            $error_message = "Error preparing statement: " . $conn->errorInfo()[2]; // Use errorInfo() for PDO
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
                            <input type="text" name="username" required>
                            <label>Username</label>
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
                            <input type="text" name="country" required>
                            <label>Country</label>
                            <ion-icon name="globe-outline"></ion-icon>
                        </div>
                    </div>
                    <div class="form-column">
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
                        <div class="inputbox">
                            <input type="text" name="address" required>
                            <label>Address</label>
                            <ion-icon name="home-outline"></ion-icon>
                        </div>
                        <div class="inputbox">
                            <input type="password" id="password" name="password" required>
                            <label>Password</label>
                            <ion-icon name="lock-closed-outline"></ion-icon>
                        </div>
                        <small class="password-reminder">
                            Password must be 8-12 characters, with uppercase, lowercase, a number, and special characters ($, &, or %).
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

    <!-- JavaScript for Password Validation -->
    <script>
        function checkPasswordMatch() {
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirm_password").value;
            const errorMessage = document.getElementById("password_error");

            if (password !== confirmPassword) {
                errorMessage.textContent = "Passwords do not match!";
            } else {
                errorMessage.textContent = ""; // Clear the message if passwords match
            }
        }

        function validatePasswords() {
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirm_password").value;

            // Password criteria: 8-12 characters, uppercase, lowercase, number, and special character
            const strongPasswordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$&%])[A-Za-z\d$&%]{8,12}$/;

            if (!strongPasswordRegex.test(password)) {
                alert("Please create a stronger password: 8-12 characters, uppercase, lowercase, a number, and a special character ($, &, or %).");
                return false;
            }

            if (password !== confirmPassword) {
                alert("Passwords do not match. Please correct them.");
                return false;
            }
            return true;
        }
    </script>

    <!-- Success Notification Script -->
    <?php if ($registration_successful): ?>
        <script>
            alert("Registration successful! You can now log in.");
            window.location.href = "login.php"; // Redirect to login page after alert
        </script>
    <?php endif; ?>
</body>

</html>