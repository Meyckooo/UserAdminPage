<?php
session_start();
$base_path = './';
require_once 'header.php';
include "config/config.php";

$current_logged_userid = $_SESSION['user_id'] ?? $_SESSION['oUserid'] ?? null;

if (!$current_logged_userid) {
    header("Location: index.php");
    exit();
}

// Module ID para sa User Accounts / Permissions
$permission_module_id = 3; 

// Fetch current user access rights
$chk_stmt = $conn->prepare("SELECT oMain, oEdit, oSave FROM tbl_access WHERE oUserid = ? AND oModuleid = ?");
$chk_stmt->bind_param("ii", $current_logged_userid, $permission_module_id);
$chk_stmt->execute();
$current_user_access = $chk_stmt->get_result()->fetch_assoc();

$is_admin = isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'admin';

// View Access Check (pwede ra siya makasulod para makakita)
$has_view_access = $current_user_access && ($current_user_access['oMain'] == 1 || $current_user_access['oView'] == 1 || $current_user_access['oEdit'] == 1);

if (!$is_admin && !$has_view_access) {
    header("Location: user_account.php?error=unauthorized");
    exit();
}

// Save/Edit Permission Check (Ipasa sa JavaScript)
$can_save_permission = $is_admin || ($current_user_access && ($current_user_access['oSave'] == 1 || $current_user_access['oEdit'] == 1));

// Validate Target User ID
if (!isset($_GET['oUserid']) || empty($_GET['oUserid'])) {
    header("Location: user_account.php");
    exit();
}

$target_userid = intval($_GET['oUserid']);

$user_stmt = $conn->prepare("SELECT name, role FROM users WHERE oUserid = ?");
$user_stmt->bind_param("i", $target_userid);
$user_stmt->execute();
$target_user = $user_stmt->get_result()->fetch_assoc();

if (!$target_user) {
    header("Location: user_account.php");
    exit();
}

// Fetch existing permissions for target user
$access_data = [];
$acc_stmt = $conn->prepare("SELECT * FROM tbl_access WHERE oUserid = ?");
$acc_stmt->bind_param("i", $target_userid);
$acc_stmt->execute();
$acc_result = $acc_stmt->get_result();

while ($row = $acc_result->fetch_assoc()) {
    $access_data[$row['oModuleid']] = $row;
}

$modules_result = mysqli_query($conn, "SELECT * FROM tbl_module ORDER BY oModuleid ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Permissions - <?= htmlspecialchars($target_user['name']); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="node_modules/sweetalert2/dist/sweetalert2.min.css">
</head>
<body>
    <?php include $base_path . 'includes/sidebar.php'; ?>
    <div id="main">
        <div class="wrapper">
            <div class="main_con">
                <form id="permissionsForm" action="save_permissions.php" method="POST">
                    <input type="hidden" name="target_userid" value="<?= $target_userid; ?>">
                    
                    <div class="permissions_container">
                        <h2 class="permissions_title">PERMISSIONS FOR: <?= strtoupper(htmlspecialchars($target_user['name'])); ?></h2>
                        <div class="permissions_table_wrapper">
                            <table class="styled_permissions_table">
                                <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th>MODULE NAME</th>
                                        <th class="text-center">MAIN</th>
                                        <th class="text-center">ADD</th>
                                        <th class="text-center">EDIT</th>
                                        <th class="text-center">VIEW</th>
                                        <th class="text-center">SAVE</th>
                                        <th class="text-center">POST</th>
                                        <th class="text-center">CANCEL</th>
                                        <th class="text-center">PRINT</th>
                                        <th class="text-center">DISCOUNT</th>
                                        <th class="text-center">SEND</th>
                                        <th class="text-center">SA</th>
                                        <th class="text-center">SUPERVISOR</th>
                                        <th class="text-center">MANAGER</th>
                                        <th class="text-center">AUDIT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if ($modules_result && mysqli_num_rows($modules_result) > 0): 
                                        $actions = ['oMain', 'oAdd', 'oEdit', 'oView', 'oSave', 'oPost', 'oCancel', 'oPrint', 'oDiscount', 'oSend', 'oSalesassistant', 'oSupervisor', 'oManager', 'oAudit'];
                                        
                                        while ($mod = mysqli_fetch_assoc($modules_result)): 
                                            $mod_id = $mod['oModuleid'];
                                            $mod_name = $mod['oModulename'];
                                            $user_acc = $access_data[$mod_id] ?? [];
                                            $is_dashboard = ($mod_id == 4);
                                    ?>
                                        <tr>
                                            <td class="text-center"><?= $mod_id; ?></td>
                                            <td class="text-left module_name"><?= htmlspecialchars($mod_name); ?></td>
                                            <?php foreach ($actions as $action): 
                                                if ($is_dashboard && $action === 'oMain') {
                                                    $is_checked = 'checked';
                                                    $is_disabled = 'disabled';
                                                } else {
                                                    $is_checked = isset($user_acc[$action]) && $user_acc[$action] == 1 ? 'checked' : '';
                                                    $is_disabled = '';
                                                }
                                            ?>
                                                <td class="text-center">
                                                    <input type="checkbox" 
                                                           name="permissions[<?= $mod_id; ?>][<?= $action; ?>]" 
                                                           class="custom_checkbox" 
                                                           value="1" 
                                                           <?= $is_checked; ?> 
                                                           <?= $is_disabled; ?>>
                                                    
                                                    <?php if ($is_dashboard && $action === 'oMain'): ?>
                                                        <input type="hidden" name="permissions[4][oMain]" value="1">
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php 
                                        endwhile; 
                                    else: 
                                    ?>
                                        <tr>
                                            <td colspan="16" class="text-center">No modules found in the database.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="permission_buttons">
                        <button type="button" id="btnSave" class="global_btn2">Save</button>
                        <a class="global_btn" href="user_account.php">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= $base_path; ?>assets/js/sidebar_button.js"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const btnSave = document.getElementById('btnSave');
        const form = document.getElementById('permissionsForm');
        const checkboxes = form.querySelectorAll('input[type="checkbox"]:not([disabled])');
        
        // Dynamic PHP permission variable
        const userCanSave = <?= json_encode($can_save_permission); ?>;

        const initialState = {};
        checkboxes.forEach((cb, index) => {
            initialState[index] = cb.checked;
        });

        btnSave.disabled = true;
        btnSave.style.opacity = '0.5';
        btnSave.style.cursor = 'not-allowed';

        checkboxes.forEach((cb) => {
            cb.addEventListener('change', function() {
                let hasChanged = false;

                checkboxes.forEach((checkbox, index) => {
                    if (checkbox.checked !== initialState[index]) {
                        hasChanged = true;
                    }
                });

                if (hasChanged) {
                    btnSave.disabled = false;
                    btnSave.style.opacity = '1';
                    btnSave.style.cursor = 'pointer';
                } else {
                    btnSave.disabled = true;
                    btnSave.style.opacity = '0.5';
                    btnSave.style.cursor = 'not-allowed';
                }
            });
        });

        // SweetAlert Confirmation & Permission Check Logic
        btnSave.addEventListener('click', function(e) {
            e.preventDefault();

            if (btnSave.disabled) return;

            Swal.fire({
                title: 'Save Changes?',
                text: "Are you sure you want to update permissions for <?= htmlspecialchars($target_user['name']); ?>?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, save it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Susiha kon naay save access:
                    if (!userCanSave) {
                        // Pakita og Access Denied SweetAlert kon walay permiso
                        Swal.fire({
                            icon: 'error',
                            title: 'Access Denied!',
                            text: "You don't have permission to save or update these details.",
                            confirmButtonColor: '#d33'
                        });
                    } else {
                        // Kon permitted, i-submit ang form
                        form.submit();
                    }
                }
            });
        });

        // Success Alert Handler
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('status') === 'saved') {
            Swal.fire({
                title: 'Saved Successfully!',
                text: 'Permissions for <?= htmlspecialchars($target_user['name']); ?> have been updated.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });

            window.history.replaceState({}, document.title, window.location.pathname + "?oUserid=<?= $target_userid; ?>");
        }
    });
    </script>
</body>
</html>