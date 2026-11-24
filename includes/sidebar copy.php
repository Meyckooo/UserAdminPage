<?php
$base_path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

require_once 'config/config.php'; // Kinahanglan naa ni para sa $conn
// Siguraduha nga naka-include ang database connection ug helper function
require_once 'check_access.php';
?>

<script src="<?php echo $base_path; ?>../assets/js/sidebar_button.js"></script>

<div class="sidebar">
    <a href="<?php echo $base_path; ?>/index.php">
        <div class="main_logo">
            <img src="<?php echo $base_path; ?>/assets/images/atlantichardware_logo_with_since1963.png" alt="Main_Logo" class="sidebar-logo">
        </div>
    </a>

    <ul class="menu-list">
        <!-- Module 1: MY ACCOUNT -->
        <?php if (hasPermission(1, 'oMain')): ?>
            <li class="menu-item mb-2">
                <a href="<?php echo $base_path; ?>/my_account.php" class="my_acc menu-link d-flex align-items-center justify-content-between">
                    My Account
                </a>
            </li>
        <?php endif; ?>

        <!-- Module 2: ADMINISTRATIVE TOOLS -->
        <?php if (hasPermission(2, 'oMain')): ?>
            <li class="menu-item mb-2">
                <a href="<?php echo $base_path; ?>/administrative_tools.php" class="admin_tools menu-link d-flex align-items-center justify-content-between">
                    Administrative Tools
                </a>
            </li>
        <?php endif; ?>

        <!-- Module 3: USER ACCOUNTS -->
        <?php if (hasPermission(3, 'oMain')): ?>
            <li class="menu-item mb-2">
                <a href="<?php echo $base_path; ?>/user_account.php"
                    data-subpages="add_user.php, edit.php, permission.php"
                    class="user_acc menu-link d-flex align-items-center justify-content-between">
                    User Accounts
                </a>
            </li>
        <?php endif; ?>

        <!-- Module 4: STORE DASHBOARD - MAIN -->
        <?php if (hasPermission(4, 'oMain')): ?>
            <li class="menu-item current_page_item mb-2">
                <a href="<?php echo $base_path; ?>/index.php" class="store_dashboard menu-link d-flex align-items-center justify-content-between">
                    Dashboard
                </a>
            </li> 
        <?php endif; ?>

        <!-- Module 16: ITEM MODAL -->
        <?php if (hasPermission(16, 'oMain')): ?>
            <li class="menu-item mb-2">
                <a href="<?php echo $base_path; ?>/item_modal.php" class="user_acc menu-link d-flex align-items-center justify-content-between">
                    Item Modal
                </a>
            </li>
        <?php endif; ?>

        <!-- LOGOUT (Walay module restriction) -->
        <li class="menu-item mb-2">
            <a href="<?php echo $base_path; ?>/logout.php" class="sidebar_logout menu-link d-flex align-items-center justify-content-between">
                Logout
            </a>
        </li>
    </ul>
</div>