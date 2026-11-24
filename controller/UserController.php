<?php

require_once __DIR__ . '/../entity/UserModel.php';

class UserController
{
    private UserModel $userModel;

    // Nag-instantiate sa UserModel gamit ang database connection
    public function __construct($conn)
    {
        $this->userModel = new UserModel($conn);
    }

    // Nag-format sa gi-hatag nga post_code o nag-generate og unique 4-digit code kung walay sulod
    public function getValidPostCode($code): string
    {
        if (!empty($code)) {
            return sprintf("%04d", $code);
        }

        do {
            $random_code = sprintf("%04d", rand(0, 9999));
        } while ($this->userModel->postCodeExists($random_code));

        return $random_code;
    }

    // Nag-handle sa pag-register og bag-ong user account ug pag-redirect padulong sa login.php
    public function register(array $postData): void
    {
        $name = $postData['name'];
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = $postData['email'];
        $password = password_hash($postData['password'], PASSWORD_DEFAULT);
        $role = $postData['role'];
        $status = 1;

        $post_code = $this->getValidPostCode($postData['post_code'] ?? null);

        if ($this->userModel->userExistsByUsernameOrEmail($username, $email)) {
            $_SESSION['register_error'] = 'You are already registered!';
            $_SESSION['active_form'] = 'register';
        } else {
            $new_userid = $this->userModel->createUser([
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'role' => $role,
                'status' => $status,
                'post_code' => $post_code
            ]);

            if ($new_userid !== false) {
                $this->userModel->grantDefaultDashboardAccess($new_userid);
            }
        }

        header("Location: views/login.php");
        exit();
    }

    // Nag-verify sa credentials ug status sa user para sa login, ug nag-set sa session values
    public function login(array $postData): void
    {
        $username = $postData['username'];
        $password = $postData['password'];

        $user = $this->userModel->findByUsername($username);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                $rawStatus = trim($user['status']);
                $isActive = ($rawStatus == '1' || strtolower($rawStatus) === 'active');

                if (!$isActive) {
                    $_SESSION['inactive_error'] = "Your account is currently inactive/disabled. Please contact the administrator.";
                    $_SESSION['active_form'] = 'login';
                    header("Location: views/login.php");
                    exit();
                }

                $_SESSION['name']     = $user['name'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email']    = $user['email'];
                $_SESSION['oUserid']  = $user['oUserid'];
                $_SESSION['role']     = strtolower($user['role']);

                header("Location: views/index.php");
                exit();
            }
        }

        $_SESSION['login_error'] = 'Incorrect Username or password';
        $_SESSION['active_form'] = 'login';
        header("Location: views/login.php");
        exit();
    }

    // Nag-handle sa pagdugang og user gikan sa admin/user management (suporta sa AJAX ug normal form submit)
    public function addUser(array $postData, array $serverData): void
    {
        $name = $postData['name'];
        $username = $postData['username'];
        $email = $postData['email'];
        $password = password_hash($postData['password'], PASSWORD_DEFAULT);
        $role = $postData['role'];
        $post_code = $this->getValidPostCode($postData['post_code'] ?? null);
        $status = 1;

        $new_userid = $this->userModel->createUser([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'role' => $role,
            'status' => $status,
            'post_code' => $post_code
        ]);

        if ($new_userid !== false) {
            $this->userModel->grantDefaultDashboardAccess($new_userid);

            if (!empty($serverData['HTTP_X_REQUESTED_WITH']) && strtolower($serverData['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' || isset($postData['is_ajax'])) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => 'User created successfully!']);
                exit();
            }

            header("Location: views/user_account.php?status=added");
            exit();
        } else {
            $err = $this->userModel->getLastError();
            if (isset($postData['is_ajax'])) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => $err]);
                exit();
            }
            echo "Error adding record: " . $err;
        }
    }

    // Nag-handle sa pag-edit/update sa existing user profile, status, ug password
    public function updateUser(int $id, array $postData): void
    {
        $name = $postData['name'];
        $username = $postData['username'];
        $email = $postData['email'];
        $role = $postData['role'];
        $post_code = $this->getValidPostCode($postData['post_code']);

        $status_input = $postData['status'];

        if ($status_input === 'Active' || $status_input == '1') {
            $status_val = 1;
        } else {
            $status_val = 0;
        }

        $newPasswordHash = !empty($postData['new_password'])
            ? password_hash($postData['new_password'], PASSWORD_DEFAULT)
            : null;

        $ok = $this->userModel->updateUser($id, [
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'role' => $role,
            'status_val' => $status_val,
            'post_code' => $post_code,
            'new_password_hash' => $newPasswordHash
        ]);

        if ($ok) {
            header("Location: views/user_account.php?status=updated");
            exit();
        } else {
            echo "Error updating record: " . $this->userModel->getLastError();
        }
    }

    // Nag-handle sa pag-delete sa user account ug pagbalik sa user_account.php
    public function deleteUser(int $id): void
    {
        $this->userModel->deleteUser($id);
        header("Location: views/user_account.php");
        exit;
    }
}