<?php
session_start();
require 'config/config.php';

$errors = [
    'login' => $_SESSION['login_error'] ?? '',
    'register'=> $_SESSION['register_error'] ?? ''
];
$activeForm = $_SESSION['active_form'] ?? 'login';

// Catch status error message for SweetAlert
$inactiveError = $_SESSION['inactive_error'] ?? '';

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
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="form_parent">
        <div class="container_form">

            <div class="form_box <?= isActiveForm('login', $activeForm); ?>" id="login-form">
                <h2>Login</h2>
                <?= showError($errors['login']); ?>
                <form action="action.php" method="post">
                    <input type="text" name="username" placeholder="Username" required>
                    <div class="login_input_field">
                        <input id="password" type="password" name="password" placeholder="Password" required>
                        <i><img src="assets/images/eyeclose.png" id="eyeicon"></i>
                    </div>
                    <button type="submit" name="login">Login</button>
                    <p>Don't have an account? <a href="#" onclick="showForm('register-form')">Register</a></p>
                </form>
            </div>

            <div class="form_box <?= isActiveForm('register', $activeForm); ?>" id="register-form">
                <h2>Register</h2>
                <?= showError($errors['register']); ?>
                <form action="action.php" method="post">
                    <input type="text" name="name" placeholder="Name" required>
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <div class="login_input_field">
                        <input id="password_reg" type="password" name="password" placeholder="Password" required>
                        <i><img src="assets/images/eyeclose.png" id="eyeicon_reg"></i>
                    </div>
                    <select name="role" required>
                        <option value="">--Select Role--</option>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                    <?php
                        
                    ?>
                    <input type="text" name="active" value="Active" style="pointer-events:none;">
                    <button type="submit" name="register">Register</button>
                    <p>Already have an account? <a href="#" onclick="showForm('login-form')">Login</a></p>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
    <script src="assets/js/sweetalert.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const inactiveMsg = <?= json_encode($inactiveError); ?>;
        
        if (inactiveMsg) {
            Swal.fire({
                icon: 'error',
                title: 'Account Disabled',
                text: inactiveMsg,
                confirmButtonColor: '#d33'
            });
        }
    });
    </script>
</body>
</html>