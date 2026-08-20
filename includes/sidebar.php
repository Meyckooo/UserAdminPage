<?php
$base_path = "./";

// /** @var string $base_path */
// /** @var bool $isSuperAdmin */
// /** @var array $allowedModules */

?>

<script src="<?php echo $base_path; ?>../assets/js/sidebar_button.js"></script>

<div class="sidebar">
    <a href="<?php echo $base_path; ?>index.php">
        <div class="main_logo">
            <img src="<?php echo $base_path; ?>assets/images/atlantichardware_logo_with_since1963.png" alt="Main_Logo" class="sidebar-logo">
        </div>
    </a>

    <ul class="menu-list">
        <!-- Module 1: MY ACCOUNT -->
            <li class="menu-item mb-2">
                <a href="<?php echo $base_path; ?>/my_account.php" class="my_acc menu-link d-flex align-items-center justify-content-between">
                    My Account
                </a>
            </li>

        <!-- Module 2: ADMINISTRATIVE TOOLS -->
            <li class="menu-item mb-2">
                <a href="<?php echo $base_path; ?>/administrative_tools.php" class="admin_tools menu-link d-flex align-items-center justify-content-between">
                    Administrative Tools
                </a>
            </li>

        <!-- Module 3: USER ACCOUNTS -->
            <li class="menu-item mb-2">
                <a href="<?php echo $base_path; ?>/user_accounts.php"
                    data-subpages="add_user_account.php, edit_user_account.php"
                    class="user_acc menu-link d-flex align-items-center justify-content-between">
                    User Accounts
                </a>
            </li>

        <!-- Module 4: STORE DASHBOARD - MAIN -->
            <li class="menu-item current_page_item mb-2">
                <a href="<?php echo $base_path; ?>index.php" class="store_dashboard menu-link d-flex align-items-center justify-content-between">
                    Dashboard
                </a>
            </li> 

        <li class="menu-item mb-2">
            <a href="<?php echo $base_path; ?>logout.php" class="sidebar_logout menu-link d-flex align-items-center justify-content-between">
                Logout
            </a>
        </li>
    </ul>
</div>