<!-- MODEL NI SIYA MAO NI ANG MGA QUERY NIYA SA DATABASE -->

<?php
session_start();
require 'config/config.php';

// ADD USERS FROM DATABASE
if(isset($_POST['add'])){
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    mysqli_query($conn, "INSERT INTO users (name, username, password, email, role, status) VALUES ('$name', '$username', '$password', '$email', '$role', '$status')");
    header("Location: user_account.php");
    exit;
}

// Update/Edit FROM DATABASE
if(isset($_POST['update'])){
    $id = $_GET['oUserid'];
    $name = $_POST['name'];
    $username = $_POST['username'];
    $newPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $status = $_POST['status'];

    mysqli_query($conn, "UPDATE users SET name='$name', username='$username', password='$newPassword', email='$email', status='$status',  WHERE oUserid= $id ");
    header("Location: user_account.php");
    exit;
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