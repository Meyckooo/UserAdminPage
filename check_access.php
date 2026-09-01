<?php
// check_access.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Checks if user has permission. If NOT, stops execution and shows SweetAlert.
 */
function checkAccess(String $module_id, $action = 'oMain') {
    global $conn;

    if (!isset($_SESSION['oUserid'])) {
        header("Location: login.php");
        exit();
    }

    $userid = intval($_SESSION['oUserid']);

    $sql = "SELECT $action FROM tbl_access WHERE oUserid = ? AND oModuleid = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userid, $module_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $has_access = false;

    if ($row = $result->fetch_assoc()) {
        if (intval($row[$action]) === 1) {
            $has_access = true;
        }
    }

    if (!$has_access) {
        showAccessDeniedAlert();
        exit();
    }

    return true;
}

/**
 * Helper function for Sidebar / UI condition checks (returns true or false)
 */
function hasPermission(String $module_id, $action = 'oMain') {
    global $conn;

    if (!isset($_SESSION['oUserid']) || !$conn) {
        return false;
    }

    $userid = intval($_SESSION['oUserid']);

    $sql = "SELECT $action FROM tbl_access WHERE oUserid = ? AND oModuleid = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userid, $module_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return intval($row[$action]) === 1;
    }

    return false;
}

// Professional English SweetAlert
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

function checkModuleAccess(String $module_id, $action = 'oMain') {
    return checkAccess($module_id, $action);
}
?>