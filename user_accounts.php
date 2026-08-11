<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Accounts</title>

    <link rel="stylesheet" href="assets/css/style.css>
</head>
<body>
    
    <div id="user-accounts">
        <div class="wrapper">
            <div class="ua_con">
                <h1>User List</h1>
                <a href="add_user.php" class="global_btn">Add User</a>

                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tr>
                        <td>1</td>
                        <td>Codehal</td>
                        <td>codehal@example.com</td>
                        <td>0123456789</td>
                        <td>San Franciso, USA</td>
                        <td>
                            <a class="edit_btn" href=edit.php">Edit</a>
                            <a class="delete_btn" href="#">Delete</a>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>