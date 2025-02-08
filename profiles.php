<?php

include 'connect.php';
session_start();

$error_message = '';
$success_message = '';


// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $error_message = 'You must be logged in to view this page.';
} else {
    // Fetch user details
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT status FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the user's status is terminated
    if ($user && strtolower($user['status']) === 'terminated') {
        $error_message = 'Your account has been terminated. Please contact support.';
    }
}


include 'header.php';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $updated_fullname = $_POST['fullname'];
    $updated_phone = $_POST['phone']; // Ensure this is capturing the phone number

    try {
        $update_query = "UPDATE users SET fullname = :fullname, phone = :phone WHERE id = :user_id";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bindParam(':fullname', $updated_fullname);
        $update_stmt->bindParam(':phone', $updated_phone); // Ensure this is bound correctly
        $update_stmt->bindParam(':user_id', $user_id);
        $update_stmt->execute();

        $success_message = "Profile updated successfully.";
    } catch (PDOException $e) {
        $error_message = "Error updating user data: " . $e->getMessage(); // Set error message
    }
}


// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    // Check if new passwords match
    if ($new_password !== $confirm_new_password) {
        $error_message = "New passwords do not match.";
    } else {
        // Check current password
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

        // Assuming passwords are hashed
        if (password_verify($current_password, $user_data['password'])) {
            // Update password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare("UPDATE users SET password = :password WHERE id = :user_id");
            $update_stmt->bindParam(':password', $hashed_password);
            $update_stmt->bindParam(':user_id', $user_id);
            $update_stmt->execute();
            $success_message = "Password changed successfully.";
        } else {
            $error_message = "Current password is incorrect."; // Set error message
        }
    }
}

// Fetch user information
$stmt = $conn->prepare("SELECT fullname, email, phone FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

$fullname = $user['fullname'] ?? '';
$email = $user['email'] ?? '';
$phone = $user['phone'] ?? '';
?>


<link rel="stylesheet" type="text/css" href="assets/css/profiles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<body>

    <div class="container product_section_container">
        <div class="row">
            <div class="col product_section clearfix">


                <!-- Breadcrumbs -->
                <div class="breadcrumbs d-flex flex-row align-items-center">
                    <ul>
                        <li><a href="home.php">Home</a></li>
                        <li><a href="profile.php"><i class="fa fa-angle-right" aria-hidden="true"></i>Profile</a></li>
                    </ul>
                </div>


                <!-- Sidebar -->

                <div class="sidebar">
                    <div class="sidebar_section">
                        <div class="sidebar_title">
                            <h5>My Account</h5>
                        </div>
                    </div>


                    <div class="section">
                        <div class="profile">
                            <div class="profile-header">
                                <div class="profile-text-container">
                                    <h1 class="profile-title"><?php echo htmlspecialchars($fullname); ?></h1>
                                    <p class="profile-email"><?php echo htmlspecialchars($email); ?></p>
                                </div>
                            </div>

                            <div class="menu">
                                <a href="profiles.php" class="menu-link <?php echo ($current_page == 'profiles.php') ? 'active' : ''; ?>">
                                    <i class="fa-solid fa-circle-user menu-icon"></i>Account</a>
                                <a href="user_address.php" class="menu-link <?php echo ($current_page == 'user_address.php') ? 'active' : ''; ?>">
                                    <i class="fa-solid fa-bell menu-icon"></i>Shipping Address
                                </a>
                                <a href="order_history.php" class="menu-link <?php echo ($current_page == 'order_history.php') ? 'active' : ''; ?>">
                                    <i class="fa-solid fa-gear menu-icon"></i>Order history
                                </a>
                                <a href="logout.php" onclick="return confirm('Are you sure you want to log out?');" class="menu-link"><i class="fas fa-sign-out-alt"></i>Logout</a>
                            </div>
                        </div>

                        <div class="account">
                            <div class="account-header">
                                <h1 class="account-title">Personal Information</h1>
                                <div class="btn-container">
                                    <button class="btn-save" id="editButton">Edit</button>
                                </div>
                            </div>

                            <div class="account-edit">
                                <div class="input-container">
                                    <label>Email</label>
                                    <div class="user-info"><?php echo htmlspecialchars($email); ?></div>
                                </div>
                            </div>

                            <div class="account-edit">
                                <div class="input-container">
                                    <label>Full Name</label>
                                    <div class="user-info"><?php echo htmlspecialchars($fullname); ?></div>
                                </div>
                            </div>

                            <div class="account-edit">
                                <div class="input-container">
                                    <label>Phone Number</label>
                                    <div class="user-info"><?php echo htmlspecialchars($phone); ?></div>
                                </div>
                            </div>

                            <div class="account-edit">
                                <div class="input-container">
                                    <label>Change Password</label>
                                    <span id="changePasswordText" class="user-info" style="cursor: pointer; color: #fe4c50;">Edit Password</span>
                                </div>
                            </div>
                        </div>
                        <div id="changePasswordModal" class="modal">
                            <div class="modal-content">
                                <span class="close-button" id="closeChangePasswordModal">&times;</span>
                                <div class="account-header">
                                    <h1 class="account-title">Change Password</h1>
                                </div>
                                <form id="changePasswordForm" method="POST" action="">
                                    <div class="account-edit">
                                        <div class="input-container">
                                            <label>Current Password</label>
                                            <input type="password" name="current_password" placeholder="Current Password" required />
                                        </div>
                                    </div>

                                    <div class="account-edit">
                                        <div class="input-container">
                                            <label>New Password</label>
                                            <input type="password" name="new_password" placeholder="New Password" required />
                                        </div>
                                    </div>
                                    <div id="new_password_error" class="error-message" style="color: red;"></div>

                                    <div class="account-edit">
                                        <div class="input-container">
                                            <label>Confirm New Password</label>
                                            <input type="password" name="confirm_new_password" placeholder="Confirm New Password" required />
                                        </div>
                                    </div>
                                    <div id="confirm_password_error" class="error-message" style="color: red;"></div> <!-- Error message for confirm password -->

                                    <div class="btn-container">
                                        <button type="submit" class="btn-save" name="change_password">Change Password</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div id="editModal" class="modal">
                            <div class="modal-content">
                                <span class="close-button" id="closeModal">&times;</span>
                                <div class="account-header">
                                    <h1 class="account-title">Edit Account</h1>
                                </div>
                                <form id="editForm" method="POST" action="">
                                    <div class="account-edit">
                                        <div class="input-container">
                                            <label>Email</label>
                                            <div class="email-input-wrapper">
                                                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Email" readonly />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="account-edit">
                                        <div class="input-container">
                                            <label>Full Name</label>
                                            <input type="text" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" placeholder="Full Name" required />
                                        </div>
                                    </div>

                                    <div class="account-edit">
                                        <div class="input-container">
                                            <label>Phone</label>
                                            <input type="text" id="admin_phone" placeholder="Enter phone number" name="phone" required value="<?php echo htmlspecialchars($phone); ?>" oninput="enforceMalaysianPhoneFormat()">
                                        </div>
                                    </div>
                                    <div id="phone_error" class="error-message"></div>

                                    <div class="btn-container">
                                        <button type="submit" class="btn-save" name="update_profile">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <script src="assets/js/profiles.js"></script>
        <script>
            // Phone number validation function
            function enforceMalaysianPhoneFormat() {
                const phoneInput = document.getElementById("admin_phone");
                const phoneError = document.getElementById("phone_error");
                const prefix = "+601";

                // Remove any existing spaces
                phoneInput.value = phoneInput.value.replace(/\s+/g, '');

                // Ensure the phone number starts with the correct prefix
                if (!phoneInput.value.startsWith(prefix)) {
                    phoneInput.value = prefix; // Set to prefix if it doesn't start with it
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
                if (phoneInput.value.length !== 14) { // +601 + 8 digits
                    phoneError.textContent = "Phone number must be exactly 11 digits long";
                    phoneError.style.display = "block";
                } else {
                    phoneError.textContent = ""; // Clear error message if valid
                    phoneError.style.display = "none";
                }
            }

            // Validate form before submission
            document.getElementById("editForm").addEventListener("submit", function(event) {
                enforceMalaysianPhoneFormat(); // Validate phone format before submission
                const phoneError = document.getElementById("phone_error").textContent;

                // Prevent form submission if there is an error
                if (phoneError) {
                    event.preventDefault();
                }
            });

            // Check if there is an error message to display
            var errorMessage = <?= json_encode($error_message); ?>; // Convert PHP variable to JavaScript
            var successMessage = <?= json_encode($success_message); ?>; // Convert PHP variable to JavaScript

            window.onload = function() {
                if (errorMessage) {
                    let title = 'Error';
                    let redirectUrl = null;

                    // Determine the title and redirect URL based on the error message
                    if (errorMessage === 'You must be logged in to view this page.') {
                        title = 'Access Denied';
                        redirectUrl = 'login.php';
                    } else if (errorMessage === 'Current password is incorrect.') {
                        title = 'Incorrect Password';
                    }

                    Swal.fire({
                        icon: 'error',
                        title: title,
                        text: errorMessage,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Redirect if the error is about not being logged in
                        if (redirectUrl) {
                            window.location.href = redirectUrl;
                        }
                    });
                }

                if (successMessage) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: successMessage,
                        confirmButtonText: 'OK'
                    });
                }
            };
        </script>
        <script>
            document.getElementById('changePasswordText').onclick = function() {
                document.getElementById('changePasswordModal').style.display = 'block';
            };

            document.getElementById('closeChangePasswordModal').onclick = function() {
                document.getElementById('changePasswordModal').style.display = 'none';
            };

            // Close modal when clicking outside of it
            window.onclick = function(event) {
                if (event.target == document.getElementById('changePasswordModal')) {
                    document.getElementById('changePasswordModal').style.display = 'none';
                }
            };

            // Function to validate new password
            function validateNewPassword() {
                const newPassword = document.querySelector('input[name="new_password"]').value;
                const newPasswordError = document.getElementById("new_password_error");
                newPasswordError.textContent = ""; // Clear previous error message

                if (newPassword.length < 8 || newPassword.length > 12) {
                    newPasswordError.textContent = "Password must be 8-12 characters long.";
                } else if (!/[A-Z]/.test(newPassword)) {
                    newPasswordError.textContent = "Password must contain at least one uppercase letter.";
                } else if (!/[a-z]/.test(newPassword)) {
                    newPasswordError.textContent = "Password must contain at least one lowercase letter.";
                } else if (!/\d/.test(newPassword)) {
                    newPasswordError.textContent = "Password must contain at least one number.";
                } else if (!/[\W_]/.test(newPassword)) {
                    newPasswordError.textContent = "Password must contain at least one special character.";
                }
            }

            // Function to validate confirm password
            function validateConfirmPassword() {
                const newPassword = document.querySelector('input[name="new_password"]').value;
                const confirmPassword = document.querySelector('input[name="confirm_new_password"]').value;
                const confirmPasswordError = document.getElementById("confirm_password_error");
                confirmPasswordError.textContent = ""; // Clear previous error message

                if (newPassword !== confirmPassword) {
                    confirmPasswordError.textContent = "Passwords do not match.";
                }
            }

            // Add event listeners for real-time validation
            document.querySelector('input[name="new_password"]').addEventListener('input', validateNewPassword);
            document.querySelector('input[name="confirm_new_password"]').addEventListener('input', validateConfirmPassword);

            document.getElementById("changePasswordForm").addEventListener("submit", function(event) {
                let isValid = true;
                const newPasswordError = document.getElementById("new_password_error").textContent;
                const confirmPasswordError = document.getElementById("confirm_password_error").textContent;

                // Check if there are any error messages
                if (newPasswordError || confirmPasswordError) {
                    isValid = false;
                }

                // Prevent form submission if validation fails
                if (!isValid) {
                    event.preventDefault();
                }
            });
        </script>




</body>


</html>