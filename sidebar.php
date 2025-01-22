<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar</title>
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
</head>


<body>
    <section id="sidebar">
        <a href="#" class="brand">
            <span class="text">STEP ADMIN PAGE</span>
        </a>

        <button id="toggleSidebar" class="toggle-btn">
            <i class="bx bx-menu"></i>
        </button>

        <ul class="side-menu top">
            <li class="active">
                <a href="admin.php">
                    <i class="bx bxs-shopping-bag-alt"></i>
                    <span class="text">My Store</span>
                </a>
            </li>
            <li class="has-dropdown">
                <a href="javascript:void(0)" onclick="toggleDropdown(this)">
                    <i class='bx bxs-data'></i>
                    <span class="text">Product</span>
                </a>
                <div class="dropdown">
                    <a href="admin_product.php">Product Info</a>
                    <a href="admin_product_size.php">Product Sizes and Stock</a>
                </div>
            </li>
            <li class="has-dropdown">
                <a href="javascript:void(0)" onclick="toggleDropdown(this)">
                    <i class='bx bx-category'></i>
                    <span class="text">Category</span>
                </a>
                <div class="dropdown">
                    <a href="admin_category.php">Category</a>
                    <a href="admin_category_brands.php">Brand</a>
                    <a href="admin_category_color.php">Color</a>
                    <a href="admin_category_sizes.php">Size</a>
                </div>
            </li>
            <li class="has-dropdown">
                <a href="javascript:void(0)" onclick="toggleDropdown(this)">
                    <i class='fas fa-trash-restore-alt'></i>
                    <span class="text">Restore</span>
                </a>
                <div class="dropdown" id="restoreProductDropdown">
                    <a href="javascript:void(0)" onclick="toggleSubDropdown(this, 'restoreProductInfoDropdown')">Restore Product</a>
                    <div class="sub-dropdown" id="restoreProductInfoDropdown">
                        <a href="admin_restore_products.php">Restore Product Info</a>
                        <a href="admin_restore_product_size.php">Restore Product Sizes</a>
                    </div>
                    <a href="javascript:void(0)" onclick="toggleSubDropdown(this, 'restoreCategoryDropdown')">Restore Category</a>
                    <div class="sub-dropdown" id="restoreCategoryDropdown">
                        <a href="admin_restore_categories.php">Restore Category</a>
                        <a href="admin_restore_brands.php">Restore Brand</a>
                        <a href="admin_restore_colors.php">Restore Color</a>
                        <a href="admin_restore_sizes.php">Restore Size</a>
                    </div>
                </div>
            </li>
            <li>
                <a href="admin_order.php">
                    <i class="bx bxs-doughnut-chart"></i>
                    <span class="text">Order</span>
                </a>
            </li>
            <li>
                <a href="admin_sales_report.php">
                    <i class='bx bx-dollar'></i>
                    <span class="text">Sale Report</span>
                </a>
            </li>
            <li>
                <a href="admin_contact.php">
                    <i class="bx bxs-message-dots"></i>
                    <span class="text">Message</span>
                </a>
            </li>
            <li>
                <a href="admin_user.php">
                    <i class="bx bxs-user"></i>
                    <span class="text">User </span>
                </a>
            </li>
            <li>
                <a href="admin_list.php">
                    <i class='bx bxs-group'></i>
                    <span class="text">Admin</span>
                </a>
            </li>
            <li>
                <a href="admin_profile.php">
                    <i class='bx bx-user-circle'></i>
                    <span class="text">My Profile</span>
                </a>
            </li>
            <li>
                <a href="#" class="logout">
                    <i class='bx bx-log-out'></i>
                    <span class="text">Log Out</span>
                </a>
            </li>
        </ul>
    </section>

    <script>
        const sidebar = document.getElementById('sidebar');
        const toggleButton = document.getElementById('toggleSidebar');

        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('hidden');
        });

        function toggleDropdown(element) {
            const dropdown = element.nextElementSibling; // Get the next sibling (the dropdown)
            dropdown.classList.toggle('show'); // Toggle the 'show' class

            // Hide all other dropdowns
            document.querySelectorAll('.dropdown').forEach(d => {
                if (d !== dropdown) {
                    d.classList.remove('show');
                }
            });
        }

        function toggleSubDropdown(element, subDropdownId) {
            const subDropdown = document.getElementById(subDropdownId);
            subDropdown.classList.toggle('show'); // Toggle the 'show' class

            // Hide all other sub-dropdowns
            document.querySelectorAll('.sub-dropdown').forEach(sd => {
                if (sd !== subDropdown) {
                    sd.classList.remove('show');
                }
            });
        }
        const style = document.createElement('style');
        style.innerHTML = `
            @keyframes moveDown {
                from {
                    transform: translateY(-10px);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            @keyframes moveUp {
                from {
                    transform: translateY(0);
                    opacity: 1;
                }
                to {
                    transform: translateY(-10px);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>

</html>