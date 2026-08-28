<!-- MODEL NI SIYA MAO NI ANG MGA QUERY NIYA SA DATABASE -->

<?php
session_start();
require 'config/config.php';

// ----------------------------------------------------
// REGISTRATION PROCESS
// ----------------------------------------------------
if (isset($_POST['register'])){
    $name = $_POST['name'];
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    // Default Status value for newly registered users
    $status = $_POST['active'] = 1; 

    $checkuserName = $conn->query("SELECT username FROM users WHERE username = '$username' OR email = '$email'");
    if ($checkuserName->num_rows > 0){
        $_SESSION['register_error'] = 'You are already registered!';
        $_SESSION['active_form'] = 'register';
    } else {
        // 
        $register_stmt = $conn->prepare("INSERT INTO users (name, username, email, password, role, status) VALUES (?, ?, ?, ?, ?, ?)");
        $register_stmt->bind_param("sssssi", $name, $username, $email, $password, $role, $status);
        
        if ($register_stmt->execute()) {
        $new_userid = $register_stmt->insert_id; // Get generated oUserid

        // 2. Default Access: Give Store Dashboard (Module 4) oMain = 1 Access
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
            
            // STATUS CHECK: Susiha kon Active ba ang account
            $rawStatus = trim($user['status']);
            $isActive = ($rawStatus == '1' || strtolower($rawStatus) === 'active');

            if (!$isActive) {
                $_SESSION['inactive_error'] = "Your account is currently inactive/disabled. Please contact the administrator.";
                $_SESSION['active_form'] = 'login';
                header("Location: login.php");
                exit();
            }

            // Kon Active, i-set ang login sessions
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

// ADD USERS FROM DATABASE

// Sample snippet sa action.php for adding user
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $status = $_POST['status'] = 1;

    // 1. Insert New User
    $add_stmt = $conn->prepare("INSERT INTO users (name, username, email, password, role, status) VALUES (?, ?, ?, ?, ?, ?)");
    $add_stmt->bind_param("sssssi", $name, $username, $email, $password, $role, $status);

    if ($add_stmt->execute()) {
        $new_userid = $add_stmt->insert_id; // Get generated oUserid

        // 2. Default Access: Give Store Dashboard (Module 4) oMain = 1 Access
        $access_add_stmt = $conn->prepare("INSERT INTO tbl_access (oUserid, oModuleid, oMain) VALUES (?, 4, 1)");
        $access_add_stmt->bind_param("i", $new_userid);
        $access_add_stmt->execute();

        header("Location: user_account.php?status=added");
        exit();
    }
}

// Update/Edit FROM DATABASE
if (isset($_POST['update'])) {
    $id = intval($_GET['id']);
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    
    $status_input = $_POST['status']; 
    
    // Convert status para mo-fit sa Integer (1/0) ug String ('Active'/'Inactive')
    if ($status_input === 'Active' || $status_input == '1') {
        $status_val = 1;
        $status_str = 'Active';
    } else {
        $status_val = 0;
        $status_str = 'Inactive';
    }

    // Kon numeric o integer ang column type sa database, $status_val ang gamiton; 
    // Kon VARCHAR, dawaton gihapon kini.
    if (!empty($_POST['new_password'])) {
        $password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $edit_stmt = $conn->prepare("UPDATE users SET name=?, username=?, email=?, password=?, role=?, status=? WHERE oUserid=?");
        $edit_stmt->bind_param("ssssssi", $name, $username, $email, $password, $role, $status_val, $id);
    } else {
        $edit_stmt = $conn->prepare("UPDATE users SET name=?, username=?, email=?, role=?, status=? WHERE oUserid=?");
        $edit_stmt->bind_param("sssssi", $name, $username, $email, $role, $status_val, $id);
    }

    if ($edit_stmt->execute()) {
        header("Location: user_account.php?status=updated");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// DELETE FROM DATABASE
if(isset($_GET['oUserid'])){
    $id = $_GET['oUserid'];
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    mysqli_query($conn, "DELETE FROM users WHERE oUserid= $id ");
    header("Location: user_account.php");
    exit;
}

?>