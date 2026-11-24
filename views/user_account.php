<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$base_path = "..";

// Database connection
require_once __DIR__ . '/../config/config.php';

// 1. INCLUDE ACCESS CHECK & VERIFY PERMISSION FOR THIS PAGE
require_once __DIR__ . '/../check_access.php';
checkAccess(3, 'oMain'); // Module ID 3 corresponds to USER ACCOUNTS in tbl_module

// Fetch user data
$query = mysqli_query($conn, "SELECT * FROM users");

require_once __DIR__ . '/header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Accounts</title>
    <link rel="stylesheet" href="<?php echo $base_path; ?>/assets/css/style.css">
</head>

<body class="user_account">
    <?php include $base_path . '/includes/sidebar.php'; ?>

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
                                        <a class="delete_btn delete-user-btn"
                                            href="<?= $base_path; ?>/user_action.php?oUserid=<?= $user['oUserid'] ?>"
                                            data-username="<?= htmlspecialchars($user['username']) ?>">
                                            Delete
                                        </a>
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
    <script src="<?php echo $base_path; ?>/assets/js/sweetalert.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');

            if (status === 'added') {
                Swal.fire({
                    title: 'Created Successfully!',
                    text: 'New user account has been created.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });

                window.history.replaceState({}, document.title, window.location.pathname);
            } else if (status === 'updated') {
                Swal.fire({
                    title: 'Updated Successfully!',
                    text: 'User details and status have been updated.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });

                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });
    </script>

    <script>
        document.querySelectorAll('.delete-user-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();

                const deleteUrl = this.getAttribute('href');
                const username = this.getAttribute('data-username') || 'this user';

                Swal.fire({
                    title: 'Delete User?',
                    text: `Are you sure you want to delete "${username}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = deleteUrl;
                    }
                });
            });
        });
    </script>

</body>

</html>