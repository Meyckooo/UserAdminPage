<?php
session_start();
require 'config/config.php';

// Helper function para mag-format o mag-generate ug random unique 4-digit code
function getValidPostCode($code, $conn)
{
    if (!empty($code)) {
        return sprintf("%04d", $code);
    }

    do {
        $random_code = sprintf("%04d", rand(0, 9999));
        $check = mysqli_query($conn, "SELECT post_code FROM users WHERE post_code = '$random_code'");
    } while (mysqli_num_rows($check) > 0);

    return $random_code;
}

// ----------------------------------------------------
// REGISTRATION PROCESS
// ----------------------------------------------------
if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $status = 1;

    $post_code = getValidPostCode($_POST['post_code'] ?? null, $conn);

    $checkuserName = $conn->query("SELECT username FROM users WHERE username = '$username' OR email = '$email'");
    if ($checkuserName->num_rows > 0) {
        $_SESSION['register_error'] = 'You are already registered!';
        $_SESSION['active_form'] = 'register';
    } else {
        $register_stmt = $conn->prepare("INSERT INTO users (name, username, email, password, role, status, post_code) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $register_stmt->bind_param("sssssis", $name, $username, $email, $password, $role, $status, $post_code);

        if ($register_stmt->execute()) {
            $new_userid = $register_stmt->insert_id;

            $access_register_stmt = $conn->prepare("INSERT INTO tbl_access (oUserid, oModuleid, oMain) VALUES (?, 4, 1)");
            $access_register_stmt->bind_param("i", $new_userid);
            $access_register_stmt->execute();
        }
    }

    header("Location: login.php");
    exit();
}

// ----------------------------------------------------
// LOGIN PROCESS
// ----------------------------------------------------
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE username = '$username'");

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $rawStatus = trim($user['status']);
            $isActive = ($rawStatus == '1' || strtolower($rawStatus) === 'active');

            if (!$isActive) {
                $_SESSION['inactive_error'] = "Your account is currently inactive/disabled. Please contact the administrator.";
                $_SESSION['active_form'] = 'login';
                header("Location: login.php");
                exit();
            }

            $_SESSION['name']     = $user['name'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email']    = $user['email'];
            $_SESSION['oUserid']  = $user['oUserid'];
            $_SESSION['role']     = strtolower($user['role']);

            header("Location: index.php");
            exit();
        }
    }

    $_SESSION['login_error'] = 'Incorrect Username or password';
    $_SESSION['active_form'] = 'login';
    header("Location: login.php");
    exit();
}

// ----------------------------------------------------
// ADD USER PROCESS
// ----------------------------------------------------
if (isset($_POST['add']) || (isset($_POST['is_ajax']) && !isset($_POST['update']))) {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $post_code = getValidPostCode($_POST['post_code'] ?? null, $conn);
    $status = 1;

    $add_stmt = $conn->prepare("INSERT INTO users (name, username, email, password, role, status, post_code) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $add_stmt->bind_param("sssssis", $name, $username, $email, $password, $role, $status, $post_code);

    if ($add_stmt->execute()) {
        $new_userid = $add_stmt->insert_id;

        $access_add_stmt = $conn->prepare("INSERT INTO tbl_access (oUserid, oModuleid, oMain) VALUES (?, 4, 1)");
        $access_add_stmt->bind_param("i", $new_userid);
        $access_add_stmt->execute();

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' || isset($_POST['is_ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'User created successfully!']);
            exit();
        }

        header("Location: user_account.php?status=added");
        exit();
    } else {
        if (isset($_POST['is_ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $conn->error]);
            exit();
        }
        echo "Error adding record: " . $conn->error;
    }
}

// ----------------------------------------------------
// UPDATE/EDIT USER PROCESS
// ----------------------------------------------------
if (isset($_POST['update'])) {
    $id = intval($_GET['id']);
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $post_code = getValidPostCode($_POST['post_code'], $conn);

    $status_input = $_POST['status'];

    if ($status_input === 'Active' || $status_input == '1') {
        $status_val = 1;
    } else {
        $status_val = 0;
    }

    if (!empty($_POST['new_password'])) {
        $password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $edit_stmt = $conn->prepare("UPDATE users SET name=?, username=?, email=?, password=?, role=?, status=?, post_code=? WHERE oUserid=?");
        $edit_stmt->bind_param("sssssisi", $name, $username, $email, $password, $role, $status_val, $post_code, $id);
    } else {
        $edit_stmt = $conn->prepare("UPDATE users SET name=?, username=?, email=?, role=?, status=?, post_code=? WHERE oUserid=?");
        $edit_stmt->bind_param("ssssssi", $name, $username, $email, $role, $status_val, $post_code, $id);
    }

    if ($edit_stmt->execute()) {
        header("Location: user_account.php?status=updated");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// ----------------------------------------------------
// DELETE USER PROCESS
// ----------------------------------------------------
if (isset($_GET['oUserid'])) {
    $id = intval($_GET['oUserid']);

    mysqli_query($conn, "DELETE FROM users WHERE oUserid= $id ");
    header("Location: user_account.php");
    exit;
}

// ----------------------------------------------------
// POST ITEMS TO DATABASE (tbl_item_details) - FIXED
// ----------------------------------------------------

require_once 'check_access.php';
$locator_module_id = 16;

if (isset($_POST['post_items'])) {
    header('Content-Type: application/json');

    if (!isset($_SESSION['oUserid'])) {
        echo json_encode(['status' => 'error', 'message' => 'Session expired. Please log in again.']);
        exit();
    }

    if (!hasPermission($locator_module_id, 'oPost')) {
        echo json_encode([
            'status' => 'error',
            'code'   => 'NO_PERMISSION',
            'message' => "You don't have permission to post items in this module."
        ]);
        exit();
    }

    $items_json = $_POST['items'] ?? '[]';
    $items = json_decode($items_json, true);

    $locator_details_json = $_POST['locator_details'] ?? '{}';
    $locator_details = json_decode($locator_details_json, true);

    $locatorItem    = trim($_POST['locator_item'] ?? ($locator_details['locator_code'] ?? ''));
    $defaultRemarks = trim($locator_details['remarks'] ?? '');
    $defaultAddedBy = trim($locator_details['added_by'] ?? ($_SESSION['name'] ?? $_SESSION['username'] ?? ''));

    if (empty($items) || !is_array($items)) {
        echo json_encode(['status' => 'error', 'message' => 'There is no item(s) to post!']);
        exit();
    }

    // 10 columns + NOW() = 11th column (created_at)
    $post_stmt = $conn->prepare("
        INSERT INTO tbl_item_details 
        (item_locator, item_code, item_barcode, item_no, item_uom, item_desc, item_first_loc, item_sec_loc, item_stock, remarks, added_by, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    if (!$post_stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]);
        exit();
    }

    $inserted_count = 0;

    foreach ($items as $item) {
        $itemLocator = !empty($item['locatorItem']) ? $item['locatorItem'] : $locatorItem;
        $itemCode    = $item['itemCode'] ?? '';
        $barCode     = $item['barCode'] ?? '';
        $itemNo      = $item['itemNo'] ?? '';
        $itemUom     = $item['uom'] ?? '';
        $desc        = $item['desc'] ?? '';
        $firstLocInt = intval($item['firstLocChecked'] ?? 0);
        $secLocInt   = intval($item['secLocChecked'] ?? 0);
        $itemStock   = intval($item['itemStock'] ?? 0);
        $remarks     = isset($item['remarks']) && trim($item['remarks']) !== '' ? trim($item['remarks']) : $defaultRemarks;
        $addedBy     = isset($item['addedBy']) && trim($item['addedBy']) !== '' ? trim($item['addedBy']) : $defaultAddedBy;
        // 8 strings ("ssssssss") + 3 integers ("iii") = 11 parameters
        $post_stmt->bind_param(
            "ssssssiiiss",
            $itemLocator,
            $itemCode,
            $barCode,
            $itemNo,
            $itemUom,
            $desc,
            $firstLocInt,
            $secLocInt,
            $itemStock,
            $remarks,
            $addedBy
        );

        if ($post_stmt->execute()) {
            $inserted_count++;
        }
    }

    $post_stmt->close();

    if ($inserted_count > 0) {
        $byText = $defaultAddedBy !== '' ? " by {$defaultAddedBy}" : "";
        echo json_encode([
            'status' => 'success',
            'message' => "$inserted_count item(s) successfully posted{$byText}!"
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to post items.']);
    }
    exit();
}

// ----------------------------------------------------
// FETCH POSTED ITEMS (FIXED FOR CURRENT DAY DEFAULT)
// ----------------------------------------------------
error_reporting(E_ALL);
ini_set('display_errors', 0); // I-set sa 0 aron dili mo-print og HTML errors

if (isset($_POST['get_posted_items'])) {
    header('Content-Type: application/json');

    $response = [
        'status' => 'error',
        'items' => [],
        'message' => ''
    ];

    // DEFAULT: Filter sa Current Day / Karon nga adlaw lang
    $where_clause = " WHERE DATE(created_at) = CURDATE()";

    // Kon may napili nga custom date/range sa filter input
    if (!empty($_POST['date_range'])) {
        $dates = explode(' to ', $_POST['date_range']);
        if (count($dates) == 2) {
            $start_date = mysqli_real_escape_string($conn, trim($dates[0]) . ' 00:00:00');
            $end_date   = mysqli_real_escape_string($conn, trim($dates[1]) . ' 23:59:59');
            $where_clause = " WHERE created_at BETWEEN '$start_date' AND '$end_date'";
        } else if (count($dates) == 1) {
            $single_date = mysqli_real_escape_string($conn, trim($dates[0]));
            $where_clause = " WHERE DATE(created_at) = '$single_date'";
        }
    }

    $query = "SELECT * FROM tbl_item_details $where_clause ORDER BY created_at DESC";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $items = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $items[] = [
                'item_locator'   => $row['item_locator'] ?? '',
                'item_code'      => $row['item_code'] ?? '',
                'item_barcode'   => $row['item_barcode'] ?? '',
                'item_no'        => $row['item_no'] ?? '',
                'item_uom'       => $row['item_uom'] ?? '',
                'item_desc'      => $row['item_desc'] ?? '',
                'item_first_loc' => $row['item_first_loc'] ?? '',
                'item_sec_loc'   => $row['item_sec_loc'] ?? '',
                'remarks'     => $row['remarks'] ?? '',
                'added_by'     => $row['added_by'] ?? '',
                'created_at'     => $row['created_at'] ?? ''
            ];
        }
        $response['status'] = 'success';
        $response['items']  = $items;
    } else {
        $response['message'] = mysqli_error($conn);
    }

    echo json_encode($response);
    exit();
}
