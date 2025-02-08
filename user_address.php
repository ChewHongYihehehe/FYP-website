<?php

include 'connect.php';
session_start();

$error_messages = '';


// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $error_messages = 'You must be logged in to view this page.';
} else {
    // Fetch user details
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT status FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the user's status is terminated
    if ($user && strtolower($user['status']) === 'terminated') {
        $error_messages = 'Your account has been terminated. Please contact support.';
    }
}

include 'header.php';

//Fetch user information
$stmt = $conn->prepare("SELECT id,fullname, email, phone FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

$user_fullname = $user['fullname'] ?? '';
$email = $user['email'] ?? '';
$phone = $user['phone'] ?? '';

//Fetch user addresses
$stmt = $conn->prepare("SELECT id, fullname, address_line, city, postcode, state, is_default FROM user_addresses WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

$addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

//Handle adding a new address
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_address'])) {
    $address_fullname = $_POST['fullname'];
    $address_line = $_POST['address_line'];
    $city = $_POST['city'];
    $postcode = $_POST['postcode'];
    $state = $_POST['state'];

    //Check if this is the first address or if default is checked
    $count_stmt = $conn->prepare("SELECT COUNT(*) as address_count FROM user_addresses WHERE user_id = :user_id");
    $count_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $count_stmt->execute();
    $count_result = $count_stmt->fetch(PDO::FETCH_ASSOC);

    $is_default = 0;
    if ($count_result['address_count'] == 0 || isset($_POST['is_default'])) {

        $reset_stmt = $conn->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = :user_id");
        $reset_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $reset_stmt->execute();

        $is_default = 1;
    }

    //Prepare the SQL statement to insert a new address
    $stmt = $conn->prepare("INSERT INTO user_addresses (user_id, fullname, address_line, city, postcode, state, is_default) VALUES (:user_id, :fullname, :address_line, :city, :postcode, :state, :is_default)");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':fullname', $address_fullname);
    $stmt->bindParam(':address_line', $address_line);
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':postcode', $postcode);
    $stmt->bindParam(':state', $state);
    $stmt->bindParam(':is_default', $is_default, PDO::PARAM_INT);
    $stmt->execute();

    $stmt = $conn->prepare("SELECT id, fullname, address_line, city, postcode, state, is_default FROM user_addresses WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

//Handle editing an address
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_address'])) {
    $address_id = $_POST['address_id'];
    $address_fullname = $_POST['fullname'];
    $address_line = $_POST['address_line'];
    $city = $_POST['city'];
    $postcode = $_POST['postcode'];
    $state = $_POST['state'];

    $is_default = isset($_POST['is_default']) ? 1 : 0;

    if ($is_default) {
        $reset_stmt = $conn->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = :user_id");
        $reset_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $reset_stmt->execute();
    }

    $stmt = $conn->prepare("UPDATE user_addresses SET fullname = :fullname, address_line = :address_line, city = :city, postcode = :postcode, state = :state, is_default = :is_default WHERE id = :address_id AND user_id = :user_id");
    $stmt->bindParam(':fullname', $address_fullname);
    $stmt->bindParam(':address_line', $address_line);
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':postcode', $postcode);
    $stmt->bindParam(':state', $state);
    $stmt->bindParam(':is_default', $is_default, PDO::PARAM_INT);
    $stmt->bindParam(':address_id', $address_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    if ($stmt->execute()) {

        // Fetch addresses with default first
        $stmt = $conn->prepare("SELECT id, fullname, address_line, city, postcode, state, is_default FROM user_addresses WHERE user_id = :user_id ORDER BY is_default DESC");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_address'])) {
    $address_id = $_POST['address_id'];

    //Prepare the SQL statement to delete the address
    $stmt = $conn->prepare("DELETE FROM user_addresses WHERE id = :address_id AND user_id = :user_id");
    $stmt->bindParam(':address_id', $address_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $stmt = $conn->prepare("SELECT COUNT(*) as address_count FROM user_addresses WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $count_result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($count_result['address_count'] > 0) {
        $stmt = $conn->prepare("UPDATE user_addresses SET is_default = 1 WHERE user_id = :user_id LIMIT 1");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Fetch updated addresses
    $stmt = $conn->prepare("SELECT id, fullname, address_line, city, postcode, state, is_default FROM user_addresses WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$current_page = basename($_SERVER['PHP_SELF']);

$malaysian_states = [
    "Johor",
    "Kedah",
    "Kelantan",
    "Melacca",
    "Negeri Sembilan",
    "Pahang",
    "Penang",
    "Perak",
    "Perlis",
    "Selangor",
    "Terengganu",
    "Sabah",
    "Sarawak",
    "Kuala Lumpur",
    "Putrajaya",
    "Labuan"
];
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
                        <li><a href="user_address.php"><i class="fa fa-angle-right" aria-hidden="true"></i>Shipping Addresses</a></li>
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
                                    <h1 class="profile-title"><?php echo htmlspecialchars($user_fullname); ?></h1>
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
                                <h1 class="account-title">Addresses</h1>
                                <div class="btn-container">
                                    <button class="btn-save" id="addButton">Add</button>
                                </div>
                            </div>

                            <?php foreach ($addresses as $index => $address): ?>
                                <div class="address-box <?php echo $address['is_default'] ? 'default-address' : ''; ?>">
                                    <div class="address-content">
                                        <div class="address-header">
                                            <h2><?php echo htmlspecialchars($address['fullname']); ?>
                                                <?php if ($address['is_default']) : ?>
                                                    <span class="default-badge">Default</span>
                                                <?php endif; ?>
                                            </h2>
                                            <div class="address-action">
                                                <button class="edit-address-btn"
                                                    data-id="<?php echo $address['id']; ?>"
                                                    data-fullname="<?php echo htmlspecialchars($address['fullname']); ?>"
                                                    data-address-line="<?php echo htmlspecialchars($address['address_line']); ?>"
                                                    data-city="<?php echo htmlspecialchars($address['city']); ?>"
                                                    data-postcode="<?php echo htmlspecialchars($address['postcode']); ?>"
                                                    data-state="<?php echo htmlspecialchars($address['state']); ?>"
                                                    data-is-default=" <?php echo $address['is_default'] ? '1' : '0'; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form method="POST" action="" style="display:inline;">
                                                    <input type="hidden" name="delete_address" value="1">
                                                    <input type="hidden" name="address_id" value="<?php echo $address['id']; ?>">
                                                    <button type="submit" class="delete-address-btn" onclick="return confirmDelete();">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <p class="address-details">
                                            <?php echo htmlspecialchars($address['address_line']); ?>,
                                            <?php echo htmlspecialchars($address['city']); ?>
                                            <?php echo htmlspecialchars($address['postcode']); ?>
                                            <?php echo htmlspecialchars($address['state']); ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div id="editAddressModal" class="modal">
                            <div class="modal-content">
                                <span class="close-button" id="closeAddressModal">&times;</span>
                                <div class="account-header">
                                    <h1 class="account-title">Edit Address</h1>
                                </div>
                                <form id="editForm" method="POST" action="">
                                    <input type="hidden" name="edit_address" value="1">
                                    <input type="hidden" name="address_id" value="">
                                    <div class="account-edit">
                                        <div class="input-container">
                                            <label>
                                                Full Name
                                            </label>
                                            <input type="text" name="fullname" value="<?php echo htmlspecialchars($addresses[0]['fullname'] ?? ''); ?>" placeholder="Full Name" required />
                                        </div>
                                    </div>
                                    <div class="account-edit">
                                        <div class="input-container">
                                            <label>
                                                Address Line
                                            </label>
                                            <input type="text" name="address_line" value="<?php echo htmlspecialchars($addresses[0]['address_line'] ?? ''); ?>" placeholder="Address Line" required />
                                        </div>
                                    </div>

                                    <div class="account-edit">
                                        <div class="input-container">
                                            <label>City</label>
                                            <input type="text" name="city" value="<?php echo htmlspecialchars($addresses[0]['city'] ?? ''); ?>" placeholder="City" required />
                                        </div>
                                    </div>

                                    <div class="account-edit">
                                        <div class="input-container">
                                            <label>Postcode</label>
                                            <input type="text" name="postcode" value="<?php echo htmlspecialchars($addresses[0]['postcode'] ?? ''); ?>" placeholder="Postcode" required />
                                        </div>
                                    </div>


                                    <div class="account-edit">
                                        <div class="input-container">
                                            <label>State</label>
                                            <select name="state" required>
                                                <option value="">Select State</option>
                                                <?php foreach ($malaysian_states as $state): ?>
                                                    <option value="<?php echo htmlspecialchars($state); ?>" <?php echo (isset($addresses[0]['state']) && $addresses[0]['state'] === $state) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($state); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="account-edit">
                                        <div class="input-container">
                                            <label>
                                                <input type="checkbox" name="is_default" id="isDefaultAddress" <?php echo (isset($addresses[0]['is_default']) && $addresses[0]['is_default']) ? 'checked' : ''; ?>>
                                                Set as Default Address
                                            </label>
                                        </div>
                                    </div>


                                    <div class="btn-container">
                                        <button type="submit" class="btn-save">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div id="addModal" class="modal">
                            <div class="modal-content">
                                <span class="close-button" id="closeAddModal">&times;</span>
                                <div class="account-header">
                                    <h1 class="account-title">Add Address</h1>
                                </div>
                                <form id="addForm" method="POST" action="">

                                    <div class="account-edit">
                                        <div class="input-container">
                                            <label>Full Name</label>
                                            <input type="text" name="fullname" placeholder="Full Name" required />
                                        </div>
                                    </div>
                                    <div class="account-edit">
                                        <div class="input-container">
                                            <label>Address Line</label>
                                            <input type="text" name="address_line" placeholder="Address Line" required />
                                        </div>
                                    </div>

                                    <div class="account-edit">
                                        <div class="input-container">
                                            <label>City</label>
                                            <input type="text" name="city" placeholder="City" required />
                                        </div>
                                    </div>
                                    <div class="account-edit">
                                        <div class="input-container">
                                            <label>Postcode</label>
                                            <input type="text" name="postcode" placeholder="Postcode" required pattern="^\d{5}$" maxlength="5" title="Please enter exactly 5 digits." />
                                        </div>
                                    </div>

                                    <div class="account-edit">
                                        <div class="input-container">
                                            <label>State</label>
                                            <select name="state" required>
                                                <option value="">Select State</option>
                                                <?php foreach ($malaysian_states as $state): ?>
                                                    <option value="<?php echo htmlspecialchars($state); ?>"><?php echo htmlspecialchars($state); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="account-edit">
                                        <div class="input-container">
                                            <label>
                                                <input type="checkbox" name="is_default" id="addIsDefaultAddress">
                                                Set as Default Address
                                            </label>
                                        </div>
                                    </div>

                                    <div class="btn-container">
                                        <button type="submit" name="add_address" class="btn-save">Add Address</button>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/profiles.js"></script>
    <script>
        var errorMessages = <?= json_encode($error_messages); ?>;

        window.onload = function() {
            if (errorMessages)
                Swal.fire({
                    icon: 'error',
                    title: 'Access Denied',
                    text: errorMessages,
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'login.php';
                });
        };
    </script>
</body>


</html>