<?php
session_start();

$errors = [
    'login' => $_SESSION['login_error'] ?? '',
    'register'=> $_SESSION['register_error'] ?? ''
];
$activeForm = $_SESSION['active_form'] ?? 'login';

session_unset();

function showError(string $error): string {
    return !empty($error) ? "<p class='error-message'>$error</p>" : '';
}

function isActiveForm(string $formName, string $activeForm): string {
    return $formName === $activeForm ? 'active' : '';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login_page</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="form_parent">
        <div class="container_form">

            <div class="form_box <?= isActiveForm('login', $activeForm); ?>" id="login-form">
                  <h2>Login</h2>
                  <?= showError($errors['login']); ?>
                <form action="login_register.php" method="post">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" name="login">Login</button>
                    <p>Don't have an account? <a href="#" onclick="showForm('register-form')">Register</a></p>
                </form>
            </div>

            <div class="form_box <?= isActiveForm('register', $activeForm); ?>" id="register-form">
                <h2>Register</h2>
                 <?= showError($errors['register']); ?>
                <form action="login_register.php" method="post">
                    <input type="text" name="name" placeholder="Name" required>
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <select name="role" required>
                        <option value="">--Select Role--</option>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                    <button type="submit" name="register">Register</button>
                    <p>Already have an account? <a href="#" onclick="showForm('login-form')">Login</a></p>
                </form>
            </div>
        </div>
    </div>


    <script src="assets/js/script.min.js"></script>

    <!-- https://www.youtube.com/watch?v=LiomRvK7AM8 -->
   
</body>

</html>