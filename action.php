<!-- MODEL NI SIYA MAO NI ANG MGA QUERY NIYA SA DATABASE -->

<?php
session_start();
require 'config/config.php';

// ADD USERS FROM DATABASE
// Sample snippet sa action.php for adding user
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $status = $_POST['status'];

    // 1. Insert New User
    $stmt = $conn->prepare("INSERT INTO users (name, username, email, password, role, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $name, $username, $email, $password, $role, $status);

    if ($stmt->execute()) {
        $new_userid = $stmt->insert_id; // Get generated oUserid

        // 2. Default Access: Give Store Dashboard (Module 4) oMain = 1 Access
        $access_stmt = $conn->prepare("INSERT INTO tbl_access (oUserid, oModuleid, oMain) VALUES (?, 4, 1)");
        $access_stmt->bind_param("i", $new_userid);
        $access_stmt->execute();

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
        $stmt = $conn->prepare("UPDATE users SET name=?, username=?, email=?, password=?, role=?, status=? WHERE oUserid=?");
        $stmt->bind_param("ssssssi", $name, $username, $email, $password, $role, $status_val, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, username=?, email=?, role=?, status=? WHERE oUserid=?");
        $stmt->bind_param("sssssi", $name, $username, $email, $role, $status_val, $id);
    }

    if ($stmt->execute()) {
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