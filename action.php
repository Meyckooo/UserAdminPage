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

    mysqli_query($conn, "INSERT INTO users (name, username, password, email, role) VALUES ('$name', '$username', '$password', '$email', '$role')");
    header("Location: user_account.php");
    exit;
}

// Update/Edit FROM DATABASE
if(isset($_POST['update'])){
    $id = $_GET['id'];
    $name = $_POST['name'];
    $username = $_POST['username'];
    $newPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $role = $_POST['role'];

    mysqli_query($conn, "UPDATE users SET name='$name', username='$username', password='$newPassword', email='$email', role='$role' WHERE id= $id ");
    header("Location: user_account.php");
    exit;
}

// DELETE FROM DATABASE
if(isset($_GET['id'])){
    $id = $_GET['id'];
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $role = $_POST['role'];

    mysqli_query($conn, "DELETE FROM users WHERE id= $id ");
    header("Location: user_account.php");
    exit;
}

?>