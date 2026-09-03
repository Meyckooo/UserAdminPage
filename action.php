<?php
session_start();
require 'config/config.php';

// Helper function para mag-format o mag-generate ug random unique 4-digit code
function getValidPostCode($code, $conn) {
    if (!empty($code)) {
        // Kuhaon ang intval aron dili mag-convert ngadto sa pure integer ang string
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
if (isset($_POST['register'])){
    $name = $_POST['name'];
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $status = 1; 

    // Random 4-digit post_code
    $post_code = getValidPostCode($_POST['post_code'] ?? null, $conn);

    $checkuserName = $conn->query("SELECT username FROM users WHERE username = '$username' OR email = '$email'");
    if ($checkuserName->num_rows > 0){
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
if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE username = '$username'");
    
    if ($result->num_rows > 0){
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])){
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

        // Kon gi-call via AJAX, mo-return og JSON response para sa SweetAlert
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' || isset($_POST['is_ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => 'User created successfully!']);
            exit();
        }

        // Standard PHP redirect fallback
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
if(isset($_GET['oUserid'])){
    $id = intval($_GET['oUserid']);

    mysqli_query($conn, "DELETE FROM users WHERE oUserid= $id ");
    header("Location: user_account.php");
    exit;
}


// ----------------------------------------------------
// POST ITEMS TO DATABASE (tbl_item_details)
// ----------------------------------------------------
if (isset($_POST['post_items'])) {
    header('Content-Type: application/json');

    // Kuhaon ang gi-pasa nga items array (gikan sa AJAX/JS)
    $items_json = $_POST['items'] ?? '[]';
    $items = json_decode($items_json, true);

    if (empty($items) || !is_array($items)) {
        echo json_encode(['status' => 'error', 'message' => 'There is no item(s) to post!']);
        exit();
    }

    // Gamit tag prepared statement para safe sa SQL Injection
    $stmt = $conn->prepare("INSERT INTO tbl_item_details (item_code, item_barcode, item_no, item_uom, item_desc, item_first_loc, item_sec_loc, item_stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare failed: ' . $conn->error]);
        exit();
    }

    $inserted_count = 0;

    foreach ($items as $item) {
        $itemCode = $item['itemCode'] ?? '';
        $barCode  = $item['barCode'] ?? '';
        $itemNo   = $item['itemNo'] ?? '';
        $itemUom  = $item['uom'] ?? '';
        $desc     = $item['desc'] ?? '';
        $firstLoc = $item['firstLoc'] ?? '';
        $secLoc   = $item['secLoc'] ?? '';
        $itemStock   = $item['itemStock'] ?? '';

        $stmt->bind_param("ssissssi", $itemCode, $barCode, $itemNo, $itemUom, $desc, $firstLoc, $secLoc, $itemStock);
        if ($stmt->execute()) {
            $inserted_count++;
        }
    }

    $stmt->close();

    if ($inserted_count > 0) {
        echo json_encode(['status' => 'success', 'message' => "$inserted_count item(s) successfully posted to database!"]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to post items.']);
    }
    exit();
}

?>