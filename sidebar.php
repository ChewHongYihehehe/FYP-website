<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>sidebar</title>
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    a {
        text-decoration: none;
    }

    li {
        list-style: none;
    }

    body {
        background-color: #eee;
    }

    #sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 280px;
        height: 100%;
        background-color: #f9f9f9;
        z-index: 1000;
        font-family: 'Times New Roman', Times, serif;
        transition: .3s ease;
        overflow-x: hidden;
    }

    #sidebar .brand {
        font-size: 26px;
        font-weight: 700;
        height: 56px;
        display: flex;
        align-items: center;
        background: linear-gradient(90deg, red, black);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    #sidebar .brand .bx {
        min-width: 60px;
        display: flex;
        justify-content: center;
    }

    #sidebar .side-menu {
        width: 100%;
        margin-top: 48px;
    }

    #sidebar .side-menu li {
        height: 48px;
        background-color: transparent;
        margin-left: 6px;
        border-radius: 48px 0 0 48px;
        padding: 4px;
    }

    #sidebar .side-menu li a {
        width: 100%;
        height: 100%;
        background-color: #f9f9f9;
        display: flex;
        align-items: center;
        border-radius: 48px;
        font-size: 16px;
        color: rgb(32, 149, 196);
        font-size: 18px;
        white-space: nowrap;
        overflow: hidden;
    }

    #sidebar .side-menu li a.logout {
        color: red;
    }

    #sidebar .side-menu.top li a:hover {
        color: rgb(0, 0, 0);
    }

    #sidebar .side-menu li a .bx {
        min-width: 60px;
        display: flex;
        justify-content: center;
    }

    .dropdown {
        display: none;
        margin-left: 20px;
    }

    .dropdown a {
        display: block;
        padding: 5px 0;
        color: #555;
        text-decoration: none;
    }

    .dropdown a:hover {
        color: #000;
    }

    .has-dropdown.active .dropdown {
        display: block;
    }

    button {
        color: darkred; /* Change button text color */
    }
</style>

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
                <a href="#">
                    <i class="bx bxs-shopping-bag-alt"></i>
                    <span class="text">My Store</span>
                </a>
            </li>
            <li>
                <a href="admin_product.html">
                    <i class='bx bxs-data'></i>
                    <span class="text">Product</span>
                </a>
            </li>
            <li class="has-dropdown">
                <a href="javascript:void(0)" onclick="toggleDropdown(this)">
                    <i class='bx bx-category'></i>
                    <span class="text">Category</span>
                </a>
                <div class="dropdown">
                    <a href="admin_category.php">Category</a>
                    <a href="admin_brand.html">Brand</a>
                    <a href="admin_color.html">Color</a>
                    <a href="admin_size.html">Size</a>
                </div>
            </li>
            <li>
                <a href="admin_order.html">
                    <i class="bx bxs-doughnut-chart"></i>
                    <span class="text">Order</span>
                </a>
            </li>
            <li>
                <a href="admin_totalsale.html">
                    <i class='bx bx-dollar'></i>
                    <span class="text">Total Sale</span>
                </a>
            </li>
            <li>
                <a href="admin_payment.html">
                    <i class="bx bxs-wallet"></i>
                    <span class="text">Payment</span>
                </a>
            </li>
            <li>
                <a href="admin_message.html">
                    <i class="bx bxs-message-dots"></i>
                    <span class="text">Message</span>
                </a>
            </li>
            <li>
                <a href="admin_user.html">
                    <i class="bx bxs-user"></i>
                    <span class="text">User</span>
                </a>
            </li>
            <li>
                <a href="admin_a.html">
                    <i class='bx bxs-group'></i>
                    <span class="text">Admin</span>
                </a>
            </li>
            <li>
                <a href="admin_profile">
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
            const parentLi = element.parentElement;
            const dropdown = parentLi.querySelector('.dropdown');
            const isActive = parentLi.classList.toggle('active');

            if (isActive) {
                dropdown.style.display = 'block';
                dropdown.style.animation = 'moveDown 0.3s forwards';
            } else {
                dropdown.style.animation = 'moveUp 0.3s forwards';
                setTimeout(() => {
                    dropdown.style.display = 'none';
                }, 300);
            }
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
