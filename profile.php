<?php
include 'connect.php';

session_start();

// Initialize cart count to 0
$cart_count = 0;

// Check if user is logged in before querying cart
if (isset($_SESSION['user_id'])) {
    try {
        $user_id = $_SESSION['user_id'];
        $cart_count_query = "SELECT COUNT(*) as cart_count FROM cart WHERE user_id = ?";
        $stmt = $conn->prepare($cart_count_query);
        $stmt->execute([$user_id]);
        $cart_count_result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Safely assign cart count
        $cart_count = $cart_count_result['cart_count'] ?? 0;
    } catch (PDOException $e) {
        // Log the error or handle it appropriately
        error_log("Error fetching cart count: " . $e->getMessage());
        $cart_count = 0;
    }
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Fetch user information from the database
    $user_query = "SELECT fullname, email, phone FROM users WHERE id = :user_id ";
    $stmt = $conn->prepare($user_query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    //Fetch user addresses
    $addresses_query = "SELECT * FROM user_addresses WHERE user_id = :user_id";
    $stmt = $conn->prepare($addresses_query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user_addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $user_id = '';
    $user_addresses = [];
}

$address_error = "";

// Handle address submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['save_address'])) {
        // Validate and sanitize input
        $first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
        $last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
        $address_line1 = isset($_POST['address_line1']) ? trim($_POST['address_line1']) : '';
        $address_line2 = isset($_POST['address_line2']) ? trim($_POST['address_line2']) : '';
        $city = isset($_POST['city']) ? trim($_POST['city']) : '';
        $zip_code = isset($_POST['zip_code']) ? trim($_POST['zip_code']) : '';
        $state = isset($_POST['state']) ? trim($_POST['state']) : '';

        // Validate required fields
        if (
            empty($first_name) || empty($last_name) || empty($address_line1) ||
            empty($city) || empty($zip_code) || empty($state)
        ) {
            $address_error = "All fields are required except Address Line 2.";
        } else {
            // Prepare SQL to insert address
            $insert_address_query = "INSERT INTO user_addresses (
                user_id, first_name, last_name, address_line1, 
                address_line2, city, zip_code, state
            ) VALUES (
                :user_id, :first_name, :last_name, :address_line1, 
                :address_line2, :city, :zip_code, :state
            )";

            try {
                $stmt = $conn->prepare($insert_address_query);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
                $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
                $stmt->bindParam(':address_line1', $address_line1, PDO::PARAM_STR);
                $stmt->bindParam(':address_line2', $address_line2, PDO::PARAM_STR);
                $stmt->bindParam(':city', $city, PDO::PARAM_STR);
                $stmt->bindParam(':zip_code', $zip_code, PDO::PARAM_STR);
                $stmt->bindParam(':state', $state, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    // Refresh addresses
                    $addresses_query = "SELECT * FROM user_addresses WHERE user_id = :user_id";
                    $stmt = $conn->prepare($addresses_query);
                    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                    $stmt->execute();
                    $user_addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } else {
                    $address_error = "Failed to save address. Please try again.";
                }
            } catch (PDOException $e) {
                $address_error = "Error: " . $e->getMessage();
            }
        }
    }


    // Handle address update
    if (isset($_POST['update_address'])) {
        $address_id = isset($_POST['address_id']) ? $_POST['address_id'] : null;
        $first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
        $last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
        $address_line1 = isset($_POST['address_line1']) ? trim($_POST['address_line1']) : '';
        $address_line2 = isset($_POST['address_line2']) ? trim($_POST['address_line2']) : '';
        $city = isset($_POST['city']) ? trim($_POST['city']) : '';
        $zip_code = isset($_POST['zip_code']) ? trim($_POST['zip_code']) : '';
        $state = isset($_POST['state']) ? trim($_POST['state']) : '';

        // Validate required fields
        if (
            empty($first_name) || empty($last_name) || empty($address_line1) ||
            empty($city) || empty($zip_code) || empty($state)
        ) {
            $address_error = "All fields are required except Address Line 2.";
        } else {
            // Prepare SQL to update address
            $update_address_query = "UPDATE user_addresses SET 
            first_name = :first_name, 
            last_name = :last_name, 
            address_line1 = :address_line1, 
            address_line2 = :address_line2, 
            city = :city, 
            zip_code = :zip_code, 
            state = :state 
            WHERE id = :address_id AND user_id = :user_id";

            try {
                $stmt = $conn->prepare($update_address_query);
                $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
                $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
                $stmt->bindParam(':address_line1', $address_line1, PDO::PARAM_STR);
                $stmt->bindParam(':address_line2', $address_line2, PDO::PARAM_STR);
                $stmt->bindParam(':city', $city, PDO::PARAM_STR);
                $stmt->bindParam(':zip_code', $zip_code, PDO::PARAM_STR);
                $stmt->bindParam(':state', $state, PDO::PARAM_STR);
                $stmt->bindParam(':address_id', $address_id, PDO::PARAM_INT);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    // Refresh addresses
                    $addresses_query = "SELECT * FROM user_addresses WHERE user_id = :user_id";
                    $stmt = $conn->prepare($addresses_query);
                    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                    $stmt->execute();
                    $user_addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } else {
                    $address_error = "Failed to update address. Please try again.";
                }
            } catch (PDOException $e) {
                $address_error = "Error: " . $e->getMessage();
            }
        }
    }

    // Handle address deletion
    if (isset($_POST['delete_address'])) {
        $address_id = $_POST['address_id'];

        $delete_query = "DELETE FROM user_addresses WHERE id = :address_id AND user_id = :user_id";
        $stmt = $conn->prepare($delete_query);
        $stmt->bindParam(':address_id', $address_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Refresh addresses
            $addresses_query = "SELECT * FROM user_addresses WHERE user_id = :user_id";
            $stmt = $conn->prepare($addresses_query);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $user_addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] = "POST") {
    //Update user information
    if (isset($_POST['update_info'])) {
        $fullname = trim($_POST['fullname']);
        $phone = trim($_POST['phone']);

        if (empty($fullname) || empty($phone)) {
            $error_message = "Full name and phone number are required.";
        } else {
            //Update user information in the database
            $update_query = "UPDATE users SET fullname = :fullname, phone = :phone WHERE id = :user_id ";
            $stmt = $conn->prepare($update_query);
            $stmt->bindparam(':fullname', $fullname, PDO::PARAM_STR);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                //Refresh user data
                $user['fullname'] = $fullname;
                $user['phone'] = $phone;
            } else {
                $error_message = "Failed to update information";
            }
        }
    }

    if (isset($_POST['change_password'])) {
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        //Validate old password
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($old_password, $row['password'])) {
            if ($new_password !== $confirm_password) {
                $error_message = "New pasword do not match.";
            } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{8,12}$/", $new_password)) {
                $error_message = "Password must be 8-12 characters, with at least one uppercase, one lowercase, one number, and one special character.";
            } else {
                // Update password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_password_query = "UPDATE users SET password = :password WHERE id = :user_id";
                $stmt = $conn->prepare($update_password_query);
                $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    $success_message = "Password changed successfully.";
                } else {
                    $error_message = "Failed to change password.";
                }
            }
        } else {
            $error_message = "Old password is incorrect.";
        }
    }
}

// Add this in your existing profile.php code
$wishlist_query = "SELECT w.*, p.name, p.category, p.brand, 
                          pv.color, pv.size, pv.price, pv.image1_display 
                   FROM wishlist w
                   JOIN products p ON w.product_id = p.id
                   JOIN product_variants pv ON p.id = pv.product_id 
                   WHERE w.user_id = :user_id AND pv.color = w.color";
$stmt = $conn->prepare($wishlist_query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$wishlist_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>Colo Shop</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Colo Shop Template">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="stylesheet" type="text/css" href="assets/css/owl.carousel.css">
    <link rel="stylesheet" type="text/css" href="assets/css/owl.theme.default.css">
    <link rel="stylesheet" type="text/css" href="assets/css/main_styles.css">
    <link rel="stylesheet" type="text/css" href="assets/css/responsive.css">
    <link rel="stylesheet" type="text/css" href="assets/css/profile.css">
</head>

<body>
    <!-- Header -->
    <div class="header-container">

        <div class="super_container">

            <header class="header trans_300">




                <!-- Main Navigation -->
                <div class="main_nav_container">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12 text-right">
                                <div class="logo_container">
                                    <a href="#">St<span>ep</span></a>
                                </div>
                                <nav class="navbar">
                                    <ul class="navbar_menu">
                                        <li><a href="#">home</a></li>
                                        <li><a href="#">Brands</a></li>
                                        <li><a href="#">WOMEN</a></li>
                                        <li><a href="#">MEN</a></li>
                                        <li><a href="#">KIDS</a></li>
                                        <li><a href="contact.html">contact</a></li>
                                    </ul>
                                    <ul class="navbar_user">
                                        <li><a href="#"><i class="fa fa-search" aria-hidden="true"></i></a></li>
                                        <li><a href="#"><i class="fa fa-user" aria-hidden="true"></i></a></li>
                                        <li class="checkout">
                                            <a href="add_to_cart.php" id="cart-icon">
                                                <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                                <span class="cart-count"><?php echo $cart_count; ?></span>
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="hamburger_container">
                                        <i class="fa fa-bars" aria-hidden="true"></i>
                                    </div>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
        </div>



        <div class="profile-container">
            <div class="overlay" id="overlay"></div>
            <div class="leftbox">
                <nav>
                    <a onclick="tabs(0)" class="tab active">
                        <img src="assets/image/user.png">
                    </a>
                    <a onclick="tabs(1)" class="tab">
                        <img src="assets/image/delivery-box.png">
                    </a>
                    <a onclick="tabs(2)" class="tab">
                        <img src="assets/image/order-history.png">
                    </a>
                </nav>
            </div>
            <div class="rightbox">
                <div class="profile tabShow">
                    <h1>My Account</h1>
                    <h2>Full Name</h2>
                    <input type="text" class="input" id="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" readonly>
                    <h2>Email</h2>
                    <input type="text" class="input" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                    <h2>Phone</h2>
                    <input type="text" class="input" id="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" readonly>
                    <h2>Password</h2>

                    <input type="password" class="input" value="********" readonly>
                    <button class="btn" id="editPasswordBtn" onclick="togglePasswordForm()">Edit Password</button>
                    <button class="btn" id="updateBtn">Edit Account</button>
                </div>

                <div class="address-form update-info-form" id="updateForm" style="display: none;">
                    <h1>Update Information</h1>
                    <hr class="red-line">
                    <form method="POST" action="">
                        <div class="inputbox">
                            <input type="text" id="updateEmail" value="<?php echo htmlspecialchars($user['email']); ?>" readonly placeholder=" ">
                            <label for="updateEmail">Email</label>
                        </div>
                        <div class="inputbox">
                            <input type="text" id="updateFullName" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required placeholder=" ">
                            <label for="updateFullName">Full Name</label>
                        </div>
                        <div class="inputbox">
                            <input type="text" id="updatePhone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required placeholder=" ">
                            <label for="updatePhone">Phone</label>
                        </div>
                        <span class="error-message"><?php echo $error_message; ?></span>
                        <i class="fas fa-times cancel-icon" onclick="toggleUpdateForm()"></i>
                        <div class="inputbox">
                            <button class="btn" type="submit" name="update_info">Save Changes</button>
                        </div>
                    </form>
                </div>

                <div class="address-form password-update-form" id="passwordUpdateForm" style="display: none;">
                    <h1>Change Password</h1>
                    <hr class="red-line">
                    <form method="POST" action="">
                        <div class="inputbox">
                            <input type="password" id="oldPassword" name="old_password" required placeholder=" ">
                            <label for="oldPassword">Old Password</label>
                        </div>
                        <div class="inputbox">
                            <input type="password" id="newPassword" name="new_password" required placeholder=" ">
                            <label for="newPassword">New Password</label>
                        </div>
                        <div class="inputbox">
                            <input type="password" id="confirmPassword" name="confirm_password" required placeholder=" ">
                            <label for="confirmPassword">Confirm Password</label>
                        </div>
                        <span class="error-message"><?php echo $error_message; ?></span>
                        <div class="inputbox">
                            <button class="btn" type="submit" name="change_password">Save Changes</button>
                        </div>
                    </form>
                    <i class="fas fa-times cancel-icon" onclick="togglePasswordForm()"></i>
                </div>

                <div class="payment tabShow">
                    <h1>Billing Address</h1>
                    <button class="add-address-btn" onclick="toggleAddressForm()">
                        <span class="address-count"><?php echo count($user_addresses); ?></span>
                        Add Address<i class="fas fa-map-marker-alt"></i>
                    </button>

                    <div class="existing-addresses">
                        <?php foreach ($user_addresses as $address): ?>
                            <div class="address-card">
                                <div class="address-content">
                                    <h3><?php echo htmlspecialchars($address['first_name'] . ' ' . $address['last_name']); ?></h3>
                                    <p><?php echo htmlspecialchars($address['address_line1']); ?></p>
                                    <?php if (!empty($address['address_line2'])): ?>
                                        <p><?php echo htmlspecialchars($address['address_line2']); ?></p>
                                    <?php endif; ?>
                                    <p><?php echo htmlspecialchars($address['city'] . ', ' . $address['state'] . ' ' . $address['zip_code']); ?></p>
                                </div>
                                <div class="address-actions">
                                    <form method="POST" action="" style="display:inline;">
                                        <input type="hidden" name="address_id" value="<?php echo $address['id']; ?>">
                                        <button type="button" class="edit-address-btn" onclick="editAddress(
                        '<?php echo $address['id']; ?>',
                        '<?php echo htmlspecialchars($address['first_name']); ?>',
                        '<?php echo htmlspecialchars($address['last_name']); ?>',
                        '<?php echo htmlspecialchars($address['address_line1']); ?>',
                        '<?php echo htmlspecialchars($address['address_line2']); ?>',
                        '<?php echo htmlspecialchars($address['city']); ?>',
                        '<?php echo htmlspecialchars($address['zip_code']); ?>',
                        '<?php echo htmlspecialchars($address['state']); ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="submit" name="delete_address" class="delete-address-btn">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="address-form" id="addressForm">
                        <h1>New Address</h1>
                        <hr class="red-line">
                        <?php if (!empty($address_error)): ?>
                            <div class="error-message"><?php echo $address_error; ?></div>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <div class="inputbox-container">
                                <div class="inputbox half">
                                    <input type="text" id="addressFirstName" name="first_name" required placeholder=" ">
                                    <label for="addressFirstName">First Name</label>
                                </div>
                                <div class="inputbox half">
                                    <input type="text" id="addressLastName" name="last_name" required placeholder=" ">
                                    <label for="addressLastName">Last Name</label>
                                </div>
                            </div>
                            <i class="fas fa-times cancel-icon" onclick="toggleAddressForm()"></i>
                            <div class="inputbox">
                                <input type="text" id="addressLine1" name="address_line1" required placeholder=" ">
                                <label for="addressLine1">Address Line 1</label>
                            </div>
                            <div class="inputbox">
                                <input type="text" id="addressLine2" name="address_line2" placeholder=" ">
                                <label for="addressLine2">Address Line 2 (Optional)</label>
                            </div>
                            <div class="inputbox-container">
                                <div class="inputbox half">
                                    <input type="text" id="city" name="city" required placeholder=" ">
                                    <label for="city">City</label>
                                </div>
                                <div class="inputbox half">
                                    <input type="text" id="zipCode" name="zip_code" required placeholder=" ">
                                    <label for="zipCode">Zip Code</label>
                                </div>
                            </div>
                            <div class="inputbox">
                                <div class="select-wrapper">
                                    <select id="state" name="state" required>
                                        <option value="" disabled selected>Select State</option>
                                        <option value="Johor">Johor</option>
                                        <option value="Kedah">Kedah</option>
                                        <option value="Kelantan">Kelantan</option>
                                        <option value="Malacca">Malacca</option>
                                        <option value="Negeri Sembilan">Negeri Sembilan</option>
                                        <option value="Pahang">Pahang</option>
                                        <option value="Penang">Penang</option>
                                        <option value="Perak">Perak</option>
                                        <option value="Perlis">Perlis</option>
                                        <option value="Sabah">Sabah</option>
                                        <option value="Sarawak">Sarawak</option>
                                        <option value="Selangor">Selangor</option>
                                        <option value="Terengganu">Terengganu</option>
                                        <option value="Kuala Lumpur">Kuala Lumpur</option>
                                        <option value="Putrajaya">Putrajaya</option>
                                        <option value="Labuan">Labuan</option>
                                    </select>
                                    <label for="state">State</label>
                                    <i class="fas fa-angle-down"></i>
                                </div>
                            </div>
                            <div class="inputbox">
                                <button class="btn" type="submit" name="save_address">Save Address</button>
                            </div>
                        </form>
                    </div>

                    <div class="address-form" id="editAddressForm" style="display: none;">
                        <h1>Edit Address</h1>
                        <hr class="red-line">
                        <?php if (!empty($address_error)): ?>
                            <div class="error-message"><?php echo $address_error; ?></div>
                        <?php endif; ?>
                        <form method="POST" action="" id="editAddressFormContent">
                            <input type="hidden" name="address_id" id="editAddressId" value="">
                            <div class="inputbox-container">
                                <div class="inputbox half">
                                    <input type="text" id="editFirstName" name="first_name" required placeholder=" ">
                                    <label for="editFirstName">First Name</label>
                                </div>
                                <div class="inputbox half">
                                    <input type="text" id="editLastName" name="last_name" required placeholder=" ">
                                    <label for="editLastName">Last Name</label>
                                </div>
                            </div>
                            <i class="fas fa-times cancel-icon" onclick="toggleEditAddressForm()"></i>


                            <div class="inputbox">
                                <input type="text" id="editAddressLine1" name="address_line1" required placeholder=" ">
                                <label for="editAddressLine1">Address Line 1</label>
                            </div>
                            <div class="inputbox">
                                <input type="text" id="editAddressLine2" name="address_line2" placeholder=" ">
                                <label for="editAddressLine2">Address Line 2 (Optional)</label>
                            </div>
                            <div class="inputbox-container">
                                <div class="inputbox half">
                                    <input type="text" id="editCity" name="city" required placeholder=" ">
                                    <label for="editCity">City</label>
                                </div>
                                <div class="inputbox half">
                                    <input type="text" id="editZipCode" name="zip_code" required placeholder=" ">
                                    <label for="editZipCode">Zip Code</label>
                                </div>
                            </div>
                            <div class="inputbox">
                                <div class="select-wrapper">
                                    <select id="editState" name="state" required>
                                        <option value="" disabled selected>Select State</option>
                                        <option value="Johor">Johor</option>
                                        <option value="Kedah">Kedah
                                        <option value="Kelantan">Kelantan</option>
                                        <option value="Malacca">Malacca</option>
                                        <option value="Negeri Sembilan">Negeri Sembilan</option>
                                        <option value="Pahang">Pahang</option>
                                        <option value="Penang">Penang</option>
                                        <option value="Perak">Perak</option>
                                        <option value="Perlis">Perlis</option>
                                        <option value="Sabah">Sabah</option>
                                        <option value="Sarawak">Sarawak</option>
                                        <option value="Selangor">Selangor</option>
                                        <option value="Terengganu">Terengganu</option>
                                        <option value="Kuala Lumpur">Kuala Lumpur</option>
                                        <option value="Putrajaya">Putrajaya</option>
                                        <option value="Labuan">Labuan</option>
                                    </select>
                                    <label for="editState">State</label>
                                    <i class="fas fa-angle-down"></i>
                                </div>
                            </div>
                            <div class="inputbox">
                                <button class="btn" type="submit" name="update_address">Update Address</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="subscription tabShow">
                    <h1>Order History</h1>
                    <h2>Payment Date</h2>
                    <p>May 12, 2019</p>
                    <h2>Next Charges</h2>
                    <p>$80.56 <span>includes tax</span></p>
                    <h2>Plan</h2>
                    <p>Limited Plan</p>
                    <h2>Monthly</h2>
                    <p>$107.99/month</p>
                    <h2>Password</h2>
                    <button class="btn">Update</button>
                </div>
            </div>
            <div class="settings tabShow">
                <h1>My Reviews</h1>
                <h2>Sync WatchList</h2>
                <h2>Hold Subscription</h2>
                <p></p>
                <h2>Cancel Subscription</h2>
                <p></p>
                <h2>Your Devices</h2>
                <p></p>
                <h2>Referrals </h2>
                <p></p>
                <button class="btn">Update</button>
            </div>
        </div>
    </div>

    <script src="assets/js/profile.js"></script>
</body>

</html>