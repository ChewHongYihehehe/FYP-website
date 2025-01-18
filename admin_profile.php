<?php
session_start();
include 'connect.php';


$admin_id = $_SESSION['admin_id'];


try {
    $query = "SELECT id, admin_name, admin_email, admin_phone, admin_status,role FROM admin WHERE id = :admin_id"; // Ensure this matches the column name
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_STR);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        die("No admin found with ID $admin_id.");
    }

    $fullname = $admin['admin_name'];
    $email = $admin['admin_email'];
    $phone = $admin['admin_phone'];
} catch (PDOException $e) {
    die("Error fetching admin data: " . $e->getMessage());
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated_fullname = $_POST['fullname'];
    $updated_phone = $_POST['phone'];

    try {
        $update_query = "UPDATE admin SET admin_name = :fullname, admin_phone = :phone WHERE id = :admin_id"; // Ensure this matches the column name
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bindParam(':fullname', $updated_fullname);
        $update_stmt->bindParam(':phone', $updated_phone);
        $update_stmt->bindParam(':admin_id', $admin_id);
        $update_stmt->execute();


        header("Location: admin_profile.php");
        exit();
    } catch (PDOException $e) {
        die("Error updating admin data: " . $e->getMessage());
    }
}
?>
<link rel="stylesheet" type="text/css" href="assets/css/admin_profile.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">

<body>
    <div class="container product_section_container">
        <?php include 'sidebar.php'; ?>
        <div class="row">
            <div class="col product_section clearfix">
                <div class="section">
                    <div class="account">
                        <div class="profile-text-container">
                            <h1 class="profile-title">
                                <span class="welcome-text">Welcome,</span> <?php echo htmlspecialchars($fullname); ?>
                            </h1>
                        </div>
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
                                        <label>Phone Number</label>
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

    <script src="assets/js/profiles.js"></script>
</body>

</html>