<?php
session_start();

require_once 'header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div id="add_user">
        <div class="wrapper">
            <div class="au_con">
                <div class="au_info">
                    <h1>Add User</h1>
                    <form action="action.php" method="POST">
                        <input type="text" name="name" placeholder="Name" required>
                        <input type="text" name="username" placeholder="Username" required>
                        <input type="email" name="email" placeholder="Email" required>
                        <input type="password" name="password" placeholder="Password" required>
                        <input type="text" name="role" placeholder="Role" required>
                        <div class="btn_box">
                            <button type="submit" name="add">Add User</button>
                            <a href="admin_page.php" class="global_btn">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

</html>