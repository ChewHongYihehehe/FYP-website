<?php

include 'connect.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //Get the updated values from the form
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    //Prepare the SQL statement to update the user inforamtion
    $stmt = $conn->prepare("UPDATE users SET fullname = :fullname, email = :email, phone = :phone WHERE id = :user_id");
    $stmt->bindParam(':fullname', $fullname);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
}

include 'header.php';

//Fetch user information
$stmt = $conn->prepare("SELECT fullname, email, phone FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

$fullname = $user['fullname'] ?? '';
$email = $user['email'] ?? '';
$phone = $user['phone'] ?? '';

$current_page = basename($_SERVER['PHP_SELF']);
?>


<link rel="stylesheet" type="text/css" href="assets/css/profiles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">


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
                                            <label>
                                                Email
                                            </label>
                                            <div class="email-input-wrapper">
                                                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>"
                                                    placeholder="Email"
                                                    readonly />
                                            </div>
                                        </div>
                                    </div>


                                    <div class="account-edit">
                                        <div class="input-container">
                                            <label>
                                                Full Name
                                            </label>
                                            <input type="text" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" placeholder="Full Name" required />
                                        </div>
                                    </div>

                                    <div class="account-edit">
                                        <div class="input-container">
                                            <label>
                                                Phone Number
                                            </label>
                                            <input type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>" placeholder="Phone Number" required />
                                        </div>
                                    </div>


                                    <div class="btn-container">
                                        <button type="submit" class="btn-save">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/profiles.js"></script>
</body>


</html>