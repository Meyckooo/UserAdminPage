<?php
$base_path = "./";

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// need ni para maka sulod sa database kani na module
include "config/config.php";

// Need ni Para ma display tong naa sa table Data
$query = mysqli_query($conn, "SELECT * FROM users");

require_once 'header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/style.css">
</head>

<body class="user_account">
    <?php include $base_path . 'includes/sidebar.php'; ?>

    <div id="main">
        <div class="wrapper">
            <div class="main_con">
                <div class="table_header_info">
                    <h2>User List</h2>
                    <a href="add_user.php" class="global_btn">Add User</a>
                </div>

                <div class="table_user">
                    <table>
                        <thead>
                            <tr>
                                <th>Id No.</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <?php
                        $no = 1;
                        while ($user = mysqli_fetch_assoc($query)) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $user['name'] ?></td>
                                <td><?= $user['username'] ?></td>
                                <td><?= $user['email'] ?></td>
                                <td><?= $user['role'] ?></td>
                                <td>
                                    <a class="edit_btn" href="edit.php?id=<?= $user['id'] ?>">Edit</a>
                                    <a class="delete_btn" onclick="return confirm('Are you sure you want to delete this user?')" href="action.php?id=<?= $user['id'] ?>">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo $base_path; ?>assets/js/sidebar_button.js"></script>

</body>

</html>