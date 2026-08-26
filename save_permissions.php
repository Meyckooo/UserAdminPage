<?php
session_start();
require 'config/config.php';

$current_user_id = $_SESSION['user_id'] ?? $_SESSION['oUserid'] ?? null;

if (!$current_user_id) {
    header("Location: index.php");
    exit();
}

$permission_module_id = 3; 

$access_stmt = $conn->prepare("SELECT oEdit, oSave, oMain FROM tbl_access WHERE oUserid = ? AND oModuleid = ?");
$access_stmt->bind_param("ii", $current_user_id, $permission_module_id);
$access_stmt->execute();
$access_res = $access_stmt->get_result();
$user_access = $access_res->fetch_assoc();

$can_edit = $user_access && ($user_access['oEdit'] == 1 || $user_access['oSave'] == 1);
$is_admin = isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $target_userid = intval($_POST['target_userid']);

    // Kon walay permission ug dili admin, i-redirect nga naay error parameter
    if (!$can_edit && !$is_admin) {
        header("Location: Permission.php?oUserid=" . $target_userid . "&error=no_permission");
        exit();
    }

    $submitted_permissions = $_POST['permissions'] ?? [];

    $actions_list = [
        'oMain', 'oAdd', 'oEdit', 'oView', 'oSave', 'oPost', 
        'oCancel', 'oPrint', 'oDiscount', 'oSend', 'oSalesassistant', 
        'oSupervisor', 'oManager', 'oAudit'
    ];

    $upsert_sql = "INSERT INTO tbl_access (
        oUserid, oModuleid, oMain, oAdd, oEdit, oView, oSave, oPost, 
        oCancel, oPrint, oDiscount, oSend, oSalesassistant, oSupervisor, oManager, oAudit
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        oMain = VALUES(oMain), oAdd = VALUES(oAdd), oEdit = VALUES(oEdit), 
        oView = VALUES(oView), oSave = VALUES(oSave), oPost = VALUES(oPost), 
        oCancel = VALUES(oCancel), oPrint = VALUES(oPrint), oDiscount = VALUES(oDiscount), 
        oSend = VALUES(oSend), oSalesassistant = VALUES(oSalesassistant), 
        oSupervisor = VALUES(oSupervisor), oManager = VALUES(oManager), oAudit = VALUES(oAudit)";

    $upsert_stmt = $conn->prepare($upsert_sql);

    $existing_modules = [];
    $ex_stmt = $conn->prepare("SELECT oModuleid FROM tbl_access WHERE oUserid = ?");
    $ex_stmt->bind_param("i", $target_userid);
    $ex_stmt->execute();
    $ex_res = $ex_stmt->get_result();
    while ($row = $ex_res->fetch_assoc()) {
        $existing_modules[] = intval($row['oModuleid']);
    }

    $submitted_mod_ids = array_map('intval', array_keys($submitted_permissions));
    $modules_to_process = array_unique(array_merge($existing_modules, $submitted_mod_ids));

    foreach ($modules_to_process as $mod_id) {
        $vals = [];

        foreach ($actions_list as $act) {
            $vals[$act] = isset($submitted_permissions[$mod_id][$act]) ? 1 : 0;
        }

        $upsert_stmt->bind_param(
            "iiiiiiiiiiiiiiii",
            $target_userid, $mod_id,
            $vals['oMain'], $vals['oAdd'], $vals['oEdit'], $vals['oView'],
            $vals['oSave'], $vals['oPost'], $vals['oCancel'], $vals['oPrint'],
            $vals['oDiscount'], $vals['oSend'], $vals['oSalesassistant'],
            $vals['oSupervisor'], $vals['oManager'], $vals['oAudit']
        );
        $upsert_stmt->execute();
    }

    mysqli_query($conn, "SET @count = 0");
    mysqli_query($conn, "UPDATE tbl_access SET oAccessid = (@count := @count + 1) ORDER BY oAccessid ASC");
    
    $max_res = mysqli_query($conn, "SELECT MAX(oAccessid) as max_id FROM tbl_access");
    $max_row = mysqli_fetch_assoc($max_res);
    $next_id = ($max_row['max_id'] ?? 0) + 1;
    mysqli_query($conn, "ALTER TABLE tbl_access AUTO_INCREMENT = $next_id");

    header("Location: Permission.php?oUserid=" . $target_userid . "&status=saved");
    exit();
}