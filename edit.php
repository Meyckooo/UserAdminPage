<?php
session_start();
$base_path = './';
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
    <link rel="stylesheet" href="node_modules/sweetalert2/dist/sweetalert2.min.css">
</head>

<body>
    <?php include $base_path . 'includes/sidebar.php'; ?>
    <div id="edit_user">
        <div class="wrapper">
            <div class="eu_con">
                <div class="eu_info">
                    <h1>Edit User</h1>
                    <form id="editUserForm" action="action.php?id=<?= $id ?>" method="POST">
                        <input type="hidden" name="update" value="1">
                        <input type="text" name="name" placeholder="Name" value="<?= $user['name'] ?>" required>
                        <input type="text" name="username" placeholder="Username" value="<?= $user['username'] ?>" required>
                        <input type="email" name="email" placeholder="Email" value="<?= $user['email'] ?>" required>
                        <input type="password" name="new_password" placeholder="New Password" value="">
                        <input type="text" name="role" placeholder="Role" value="<?= $user['role'] ?>" required>
                        <div class="btn_box">
                            <button type="submit">Update User</button>
                            <a href="admin_page.php" class="global_btn">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo $base_path; ?>assets/js/sidebar_button.js"></script>
    <script src="assets/js/script.js"></script>
    <script src="node_modules/sweetalert2/dist/sweetalert2.min.js"></script>

</body>

</html>