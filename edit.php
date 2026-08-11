<?php
session_start();

require_once 'header.php';
include "config/config.php";

// Set og variable na ID para makuha nimo ang ID sa DATABASE
$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE id= $id");

// Ge kuha na diri ang data gikan sa Database base sa iyang variable na QUERY
$user = mysqli_fetch_assoc($query);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div id="edit_user">
        <div class="wrapper">
            <div class="eu_con">
                <div class="eu_info">
                    <h1>Edit User</h1>
                    <form action="action.php?id=<?= $id ?>" method="POST">
                        <input type="text" name="name" placeholder="Name" value="<?= $user['name'] ?>" required>
                        <input type="text" name="username" placeholder="Username" value="<?= $user['username'] ?>" required>
                        <input type="email" name="email" placeholder="Email" value="<?= $user['email'] ?>" required>
                        <input type="password" name="password" placeholder="Password" value="<?= $user['password'] ?>" required>
                        <input type="text" name="role" placeholder="Role" value="<?= $user['role'] ?>" required>
                        <div class="btn_box">
                            <button type="submit" name="update">Update User</button>
                            <a href="admin_page.php" class="global_btn">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

</html>