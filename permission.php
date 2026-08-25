<?php
session_start();
$base_path = './';
require_once 'header.php';
include "config/config.php";

// Fetch modules from database
$query = "SELECT * FROM tbl_module ORDER BY oModuleid ASC";
$result = mysqli_query($conn, $query); // Replace $conn with your database connection variable if different
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
    <div id="main">
        <div class="wrapper">
            <div class="main_con">
                <!-- Wrap table in a form to save permissions -->
                <form action="save_permissions.php" method="POST">
                    <div class="permissions_container">
                        <h2 class="permissions_title">USER PERMISSIONS</h2>
                        <div class="permissions_table_wrapper">
                            <table class="styled_permissions_table">
                                <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th>MODULE NAME</th>
                                        <th class="text-center">MAIN</th>
                                        <th class="text-center">ADD</th>
                                        <th class="text-center">EDIT</th>
                                        <th class="text-center">VIEW</th>
                                        <th class="text-center">SAVE</th>
                                        <th class="text-center">POST</th>
                                        <th class="text-center">CANCEL</th>
                                        <th class="text-center">PRINT</th>
                                        <th class="text-center">DISCOUNT</th>
                                        <th class="text-center">SEND</th>
                                        <th class="text-center">SA</th>
                                        <th class="text-center">SUPERVISOR</th>
                                        <th class="text-center">MANAGER</th>
                                        <th class="text-center">AUDIT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if ($result && mysqli_num_rows($result) > 0): 
                                        while ($row = mysqli_fetch_assoc($result)): 
                                            $mod_id = $row['oModuleid'];
                                            $mod_name = $row['oModulename'];
                                    ?>
                                        <tr>
                                            <td class="text-center"><?php echo htmlspecialchars($mod_id); ?></td>
                                            <td class="text-left module_name"><?php echo htmlspecialchars($mod_name); ?></td>
                                            <td class="text-center"><input type="checkbox" name="permissions[<?php echo $mod_id; ?>][main]" class="custom_checkbox" value="1"></td>
                                            <td class="text-center"><input type="checkbox" name="permissions[<?php echo $mod_id; ?>][add]" class="custom_checkbox" value="1"></td>
                                            <td class="text-center"><input type="checkbox" name="permissions[<?php echo $mod_id; ?>][edit]" class="custom_checkbox" value="1"></td>
                                            <td class="text-center"><input type="checkbox" name="permissions[<?php echo $mod_id; ?>][view]" class="custom_checkbox" value="1"></td>
                                            <td class="text-center"><input type="checkbox" name="permissions[<?php echo $mod_id; ?>][save]" class="custom_checkbox" value="1"></td>
                                            <td class="text-center"><input type="checkbox" name="permissions[<?php echo $mod_id; ?>][post]" class="custom_checkbox" value="1"></td>
                                            <td class="text-center"><input type="checkbox" name="permissions[<?php echo $mod_id; ?>][cancel]" class="custom_checkbox" value="1"></td>
                                            <td class="text-center"><input type="checkbox" name="permissions[<?php echo $mod_id; ?>][print]" class="custom_checkbox" value="1"></td>
                                            <td class="text-center"><input type="checkbox" name="permissions[<?php echo $mod_id; ?>][discount]" class="custom_checkbox" value="1"></td>
                                            <td class="text-center"><input type="checkbox" name="permissions[<?php echo $mod_id; ?>][send]" class="custom_checkbox" value="1"></td>
                                            <td class="text-center"><input type="checkbox" name="permissions[<?php echo $mod_id; ?>][sa]" class="custom_checkbox" value="1"></td>
                                            <td class="text-center"><input type="checkbox" name="permissions[<?php echo $mod_id; ?>][supervisor]" class="custom_checkbox" value="1"></td>
                                            <td class="text-center"><input type="checkbox" name="permissions[<?php echo $mod_id; ?>][manager]" class="custom_checkbox" value="1"></td>
                                            <td class="text-center"><input type="checkbox" name="permissions[<?php echo $mod_id; ?>][audit]" class="custom_checkbox" value="1"></td>
                                        </tr>
                                    <?php 
                                        endwhile; 
                                    else: 
                                    ?>
                                        <tr>
                                            <td colspan="16" class="text-center">No modules found in the database.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="permission_buttons">
                        <button type="submit" class="global_btn2">Save</button>
                        <a class="global_btn" href="user_account.php">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo $base_path; ?>assets/js/sidebar_button.js"></script>
    <script src="<?php echo $base_path; ?>assets/js/script.js"></script>
    <script src="node_modules/sweetalert2/dist/sweetalert2.min.js"></script>

</body>

</html>