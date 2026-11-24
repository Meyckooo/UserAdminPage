<?php
// check_access.php


// ==========================================
// Helper function to check if the user has permission for a specific module and action
// ==========================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

  // ==========================================
  // Helper function to get the list of allowed permission columns
  // ==========================================
function getAllowedPermissionColumns(): array {
    return [
        'oMain', 'oAdd', 'oEdit', 'oView', 'oSave', 'oPost',
        'oCancel', 'oPrint', 'oDiscount', 'oSend',
        'oSalesassistant', 'oSupervisor', 'oManager', 'oAudit'
    ];
}


  // ==========================================
  // Helper function to sanitize the permission column name
  // ==========================================
function sanitizePermissionColumn($action): string {
    $allowed = getAllowedPermissionColumns();
    return in_array($action, $allowed, true) ? $action : 'oMain';
}

  // ==========================================
  // Helper function to check if the user has elevated access (Sales Assistant, Supervisor, Manager) for a specific module
  // ==========================================
function hasElevatedModuleAccess(int $module_id): bool {
    global $conn;

    if (isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'admin') {
        return true;
    }

    if (!isset($_SESSION['oUserid']) || !$conn) {
        return false;
    }

    $userid = intval($_SESSION['oUserid']);

    $sql = "SELECT oSalesassistant, oSupervisor, oManager 
            FROM tbl_access 
            WHERE oUserid = ? AND oModuleid = ? 
            LIMIT 1";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param("ii", $userid, $module_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row) {
        return false;
    }

    return (
        intval($row['oSalesassistant']) === 1 ||
        intval($row['oSupervisor']) === 1 ||
        intval($row['oManager']) === 1
    );
}

  // ==========================================
  // Helper function to check if the user has permission for a specific module and action
  // ==========================================
function hasPermission($module_id, $action = 'oMain'): bool {
    global $conn;

    if (!isset($_SESSION['oUserid']) || !$conn) {
        return false;
    }

    $mod_id = intval($module_id);
    if (hasElevatedModuleAccess($mod_id)) {
        return true;
    }

    $col = sanitizePermissionColumn($action);
    $userid = intval($_SESSION['oUserid']);

    $sql = "SELECT $col FROM tbl_access WHERE oUserid = ? AND oModuleid = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param("ii", $userid, $mod_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $row ? (intval($row[$col]) === 1) : false;
}

  // ==========================================
  // Helper function to check access and redirect if not allowed
  // ==========================================
function checkAccess($module_id, $action = 'oMain'): bool {
    if (!isset($_SESSION['oUserid'])) {
        header("Location: login.php");
        exit();
    }

    if (!hasPermission($module_id, $action)) {
        showAccessDeniedAlert();
        exit();
    }

    return true;
}

  // ==========================================
  // Helper function to check if the user is an admin
  // ==========================================
function showAccessDeniedAlert() {
    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Access Denied</title>
        <script src="assets/js/sweetalert.js"></script>
        <style>
            body {
                background-color: #f4f6f9;
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            }
        </style>
    </head>
    <body>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: "error",
                    title: "Access Denied",
                    text: "You do not have permission to access this module.",
                    confirmButtonText: "Back",
                    confirmButtonColor: "#dc3545",
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.history.back();
                    }
                });
            });
        </script>
    </body>
    </html>
    ';
}

  // ==========================================
  // Helper function to render the user's permissions for a specific module as a JavaScript object
  // ==========================================
function renderModulePermissionScript($module_id): void {
    $mod = intval($module_id);
    $perms = [
        'canMain'       => hasPermission($mod, 'oMain'),
        'canAdd'        => hasPermission($mod, 'oAdd'),
        'canEdit'       => hasPermission($mod, 'oEdit'),
        'canView'       => hasPermission($mod, 'oView'),
        'canSave'       => hasPermission($mod, 'oSave'),
        'canPost'       => hasPermission($mod, 'oPost'),
        'canCancel'     => hasPermission($mod, 'oCancel'),
        'canPrint'      => hasPermission($mod, 'oPrint'),
        'canDiscount'   => hasPermission($mod, 'oDiscount'),
        'canSend'       => hasPermission($mod, 'oSend'),
        'canSA'         => hasPermission($mod, 'oSalesassistant'),
        'canSupervisor' => hasPermission($mod, 'oSupervisor'),
        'canManager'    => hasPermission($mod, 'oManager'),
        'canAudit'      => hasPermission($mod, 'oAudit'),
    ];

    echo "<script>window.USER_PERMISSIONS = " . json_encode($perms) . ";</script>\n";
}

function checkModuleAccess($module_id, $action = 'oMain'): bool {
    return checkAccess($module_id, $action);
}
?>