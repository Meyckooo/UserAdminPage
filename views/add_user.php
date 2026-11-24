<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$base_path = '..';

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/../config/config.php';

function generateUniquePostCode($conn) {
    do {
        $random_code = sprintf("%04d", rand(0, 9999));
        $stmt = $conn->prepare("SELECT post_code FROM users WHERE post_code = ?");
        $stmt->bind_param("s", $random_code);
        $stmt->execute();
        $result = $stmt->get_result();
    } while ($result && $result->num_rows > 0);

    return $random_code;
}

$initial_post_code = generateUniquePostCode($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link rel="stylesheet" href="<?php echo $base_path; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo $base_path; ?>/node_modules/sweetalert2/dist/sweetalert2.min.css">
</head>

<body>
    <?php include $base_path . '/includes/sidebar.php'; ?>
    <div id="main">
        <div class="wrapper">
            <div class="main_con">
                <div class="au_info">
                    <h1>Add User</h1>
                    <form id="addUserForm" action="<?php echo $base_path; ?>/user_action.php" method="POST">
                        <input type="text" name="name" placeholder="Name" required>
                        <input type="text" name="username" placeholder="Username" required>
                        <input type="email" name="email" placeholder="Email" required>
                        <input class="password" type="password" name="password" placeholder="Password" autocomplete="new-password" required>
                        <input type="text" name="role" style="text-transform:capitalize;" placeholder="Role" required>
                        
                        <div class="postcode-input-group">
                            <input type="text" id="post_code" name="post_code" value="<?= htmlspecialchars($initial_post_code); ?>" placeholder="Post Code" maxlength="4" pattern="\d{4}" required readonly style="background-color: #f8f9fa;">
                            <button type="button" class="btn-generate" onclick="generateRandomPostCode()">Generate Code</button>
                        </div>

                        <select name="status" required>
                            <option value="Active">Active</option>
                        </select>
                        <div class="btn_box">
                            <button type="submit" name="add">Add User</button>
                            <a href="user_account.php" class="global_btn">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo $base_path; ?>/node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="<?php echo $base_path; ?>/assets/js/sidebar_button.js"></script>
    <script src="<?php echo $base_path; ?>/assets/js/add_user.js"></script>
</body>

</html>