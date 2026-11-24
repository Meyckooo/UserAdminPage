<?php
// Server-side includes (kanunay sakto bisan asa i-include ang sidebar)
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../check_access.php';

// $basePath padulong sa root (para sa assets ug logout.php)
// $viewPath para sa mga pages nga naa sa sulod sa views/
$basePath = '..';
$viewPath = '.';
?>

<script src="<?= $basePath; ?>/assets/js/sidebar_button.js"></script>

<div class="sidebar">
    <a href="<?= $viewPath; ?>/index.php">
        <div class="main_logo">
            <img src="<?= $basePath; ?>/assets/images/atlantichardware_logo_with_since1963.png" alt="Main_Logo" class="sidebar-logo">
        </div>
    </a>

    <ul class="menu-list">
        <!-- Module 3: USER ACCOUNTS -->
        <?php if (hasPermission(3, 'oMain')): ?>
            <li class="menu-item mb-2">
                <a href="<?= $viewPath; ?>/user_account.php"
                    data-subpages="add_user.php, edit.php, permission.php"
                    class="user_acc menu-link d-flex align-items-center justify-content-between">
                    User Accounts
                </a>
            </li>
        <?php endif; ?>

        <!-- Module 4: STORE DASHBOARD - MAIN -->
        <?php if (hasPermission(4, 'oMain')): ?>
            <li class="menu-item current_page_item mb-2">
                <a href="<?= $viewPath; ?>/index.php" class="store_dashboard menu-link d-flex align-items-center justify-content-between">
                    Dashboard
                </a>
            </li>
        <?php endif; ?>

        <!-- Module 16: ITEM MODAL -->
        <?php if (hasPermission(16, 'oMain')): ?>
            <li class="menu-item mb-2">
                <a href="<?= $viewPath; ?>/item_modal.php" class="user_acc menu-link d-flex align-items-center justify-content-between">
                    Item Modal
                </a>
            </li>
        <?php endif; ?>

        <!-- LOGOUT (Walay module restriction) -->
        <li class="menu-item mb-2">
            <a href="<?= $basePath; ?>/logout.php" class="sidebar_logout menu-link d-flex align-items-center justify-content-between">
                Logout
            </a>
        </li>
    </ul>
</div>