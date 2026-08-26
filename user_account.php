<?php
$base_path = "./";

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Database connection
include "config/config.php";

// 1. INCLUDE ACCESS CHECK & VERIFY PERMISSION FOR THIS PAGE
require_once 'check_access.php';
checkAccess(3, 'oMain'); // Module ID 3 corresponds to USER ACCOUNTS in tbl_module

// Fetch user data
$query = mysqli_query($conn, "SELECT * FROM users");

require_once 'header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Accounts</title>
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
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            while ($user = mysqli_fetch_assoc($query)) :
                                // Generous conditional check para sa tanang variation sa status value
                                $rawStatus = trim($user['status']);
                                $isActive = ($rawStatus == '1' || strtolower($rawStatus) === 'active');

                                $statusText = $isActive ? 'ACTIVE' : 'INACTIVE';
                                $statusClass = $isActive ? 'status_active' : 'status_inactive';
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($user['name']) ?></td>
                                    <td><?= htmlspecialchars($user['username']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= htmlspecialchars($user['role']) ?></td>
                                    <td>
                                        <a class="edit_btn" href="edit.php?oUserid=<?= $user['oUserid'] ?>">Edit</a>
                                        <a class="permission_btn" href="permission.php?oUserid=<?= $user['oUserid'] ?>">Permission</a>
                                        <a class="delete_btn" onclick="return confirm('Are you sure you want to delete this user?')" href="action.php?oUserid=<?= $user['oUserid'] ?>">Delete</a>
                                    </td>
                                    <td>
                                        <span class="status_badge <?= $statusClass; ?>">
                                            <?= $statusText; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo $base_path; ?>assets/js/sidebar_button.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('status') === 'updated') {
                Swal.fire({
                    title: 'Updated Successfully!',
                    text: 'User details and status have been updated.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });

                // I-clean up ang URL parameter pagkahuman
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });
    </script>

</body>

</html>