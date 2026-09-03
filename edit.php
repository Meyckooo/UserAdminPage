<?php
session_start();
$base_path = './';
require_once 'header.php';
include "config/config.php";

if (!isset($_GET['oUserid']) || empty($_GET['oUserid'])) {
    header("Location: user_account.php");
    exit();
}

$id = intval($_GET['oUserid']);
$query = mysqli_query($conn, "SELECT * FROM users WHERE oUserid = $id");
$user = mysqli_fetch_assoc($query);

if (!$user) {
    header("Location: user_account.php");
    exit();
}

$currentStatus = (strtolower($user['status']) === 'active' || $user['status'] == 1) ? 'Active' : 'Inactive';
$formatted_post_code = !empty($user['post_code']) ? sprintf("%04d", $user['post_code']) : sprintf("%04d", rand(0, 9999));
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
                <div class="eu_info">
                    <h1>Edit User</h1>
                    <form id="editUserForm" action="action.php?id=<?= $id ?>" method="POST">
                        <input type="hidden" name="update" value="1">
                        <input type="text" name="name" placeholder="Name" value="<?= htmlspecialchars($user['name']); ?>" required>
                        <input type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($user['username']); ?>" required>
                        <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($user['email']); ?>" required>
                        <input type="password" name="new_password" placeholder="New Password (leave blank if unchanged)">
                        <input type="text" name="role" placeholder="Role" style="text-transform: capitalize;" value="<?= htmlspecialchars($user['role']); ?>" required>
                        
                        <div class="postcode-input-group">
                            <input type="text" id="post_code" name="post_code" placeholder="Post Code" value="<?= htmlspecialchars($formatted_post_code); ?>" maxlength="4" pattern="\d{4}" title="Post code must be exactly 4 digits" required>
                            <button type="button" class="btn-generate" onclick="generateRandomPostCode()">Generate Code</button>
                        </div>

                        <select name="status" required>
                            <option value="Active" <?= ($currentStatus === 'Active') ? 'selected' : ''; ?>>Active</option>
                            <option value="Inactive" <?= ($currentStatus === 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                        </select>

                        <div class="btn_box">
                            <button type="submit" id="btnUpdate" disabled style="opacity: 0.5; cursor: not-allowed;">Update</button>
                            <a href="user_account.php" class="global_btn">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="node_modules/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="<?php echo $base_path; ?>assets/js/sidebar_button.js"></script>

    <script>
    const userId = <?= $id ?>;

    function generateRandomPostCode() {
        const randomCode = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
        const postInput = document.getElementById('post_code');
        postInput.value = randomCode;
        postInput.dispatchEvent(new Event('input'));
    }

    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById('editUserForm');
        const btnUpdate = document.getElementById('btnUpdate');
        const inputs = form.querySelectorAll('input:not([type="hidden"]), select');

        const initialValues = {};
        inputs.forEach((input) => {
            initialValues[input.name] = input.value;
        });

        function checkChanges() {
            let hasChanged = false;

            inputs.forEach((input) => {
                if (input.value !== initialValues[input.name]) {
                    hasChanged = true;
                }
            });

            if (hasChanged) {
                btnUpdate.disabled = false;
                btnUpdate.style.opacity = '1';
                btnUpdate.style.cursor = 'pointer';
            } else {
                btnUpdate.disabled = true;
                btnUpdate.style.opacity = '0.5';
                btnUpdate.style.cursor = 'not-allowed';
            }
        }

        inputs.forEach((input) => {
            input.addEventListener('input', checkChanges);
            input.addEventListener('change', checkChanges);
        });

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            if (btnUpdate.disabled) return;

            const postCode = document.getElementById('post_code').value;

            // AJAX validation para sa Post Code duplicate
            fetch('check_postcode.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'post_code=' + encodeURIComponent(postCode) + '&user_id=' + userId
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'exists') {
                    Swal.fire({
                        title: 'Post Code Already Exists!',
                        text: 'The post code "' + postCode + '" is assigned to another user. Please generate or enter another code.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                } else {
                    // Kon walang duplicate, mangutana og confirmation
                    Swal.fire({
                        title: 'Update User Details?',
                        text: "Are you sure you want to save these changes?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes',
                        cancelButtonText: 'No'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
    </script>
</body>

</html>