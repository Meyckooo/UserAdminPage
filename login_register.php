<?php

session_start();
require 'config/config.php';

    if (isset($_POST['register'])){
            $name = $_POST['name'];
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
            $email = $_POST['email'];
            // If ganahan ka naka hash ang password sa database
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $role = $_POST['role'];


        $checkuserName = $conn->query("SELECT username FROM users WHERE username = '$username' OR email = '$email'");
        if ($checkuserName->num_rows > 0){
            $_SESSION['register_error'] = 'You are already registered!';
            $_SESSION['active_form'] = 'register';
        } else {
            $conn->query("INSERT INTO users (name, username, email, password, role) VALUES ('$name', '$username', '$email', '$password', '$role')");
        }

        header("Location: login.php");
        exit();
    }

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE username = '$username'");
    // mao ni IF sa ELSE naa sa ubos
    if ($result->num_rows > 0){
        $user = $result->fetch_assoc();
        // If naka hash ang password kani gamita
        if (password_verify($password, $user['password'])){
        $_SESSION['name'] = $user['name'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];

        if($user['role'] === 'admin'){
            header("Location: user_account.php");
        }else{
            header("Location: user_page.php");
        }
        exit();
        }
    }

    // ELSE NANI SIYA
    $_SESSION['login_error'] = 'Incorrect Username or password';
    $_SESSION['active_form'] = 'login';
    header("Location: login.php");
    exit();

}

?>