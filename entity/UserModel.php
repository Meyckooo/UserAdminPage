<?php

class UserModel
{
    private $conn;

    // Nag-initialize sa database connection para magamit sa tanang query methods sa UserModel
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Nag-check kung naa na bay existing user nga naggamit sa gi-generate o gi-input nga post_code
    public function postCodeExists(string $code): bool
    {
        $escaped = mysqli_real_escape_string($this->conn, $code);
        $check = mysqli_query($this->conn, "SELECT post_code FROM users WHERE post_code = '$escaped'");
        return $check && mysqli_num_rows($check) > 0;
    }

    // Nag-check kung nag-exist na ang username o email sa users table para malikayan ang duplicate account
    public function userExistsByUsernameOrEmail(string $username, string $email): bool
    {
        $u = mysqli_real_escape_string($this->conn, $username);
        $e = mysqli_real_escape_string($this->conn, $email);
        $checkuserName = $this->conn->query("SELECT username FROM users WHERE username = '$u' OR email = '$e'");
        return $checkuserName && $checkuserName->num_rows > 0;
    }

    // Nag-fetch sa user record base sa username (gamiton sa login verification)
    public function findByUsername(string $username): ?array
    {
        $u = mysqli_real_escape_string($this->conn, $username);
        $result = $this->conn->query("SELECT * FROM users WHERE username = '$u'");
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    // Nag-insert og bag-ong user account sa users table ug nagbalik sa bag-ong oUserid
    public function createUser(array $data): int|false
    {
        $register_stmt = $this->conn->prepare(
            "INSERT INTO users (name, username, email, password, role, status, post_code) VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $register_stmt->bind_param(
            "sssssis",
            $data['name'],
            $data['username'],
            $data['email'],
            $data['password'],
            $data['role'],
            $data['status'],
            $data['post_code']
        );

        if ($register_stmt->execute()) {
            $new_userid = $register_stmt->insert_id;
            $register_stmt->close();
            return $new_userid;
        }

        $register_stmt->close();
        return false;
    }

    // Naghatag og default access sa module 4 (oMain = 1) sa bag-ong na-create nga user sa tbl_access
    public function grantDefaultDashboardAccess(int $userId): bool
    {
        $access_stmt = $this->conn->prepare("INSERT INTO tbl_access (oUserid, oModuleid, oMain) VALUES (?, 4, 1)");
        $access_stmt->bind_param("i", $userId);
        $ok = $access_stmt->execute();
        $access_stmt->close();
        return $ok;
    }

    // Nag-update sa existing user details sa users table (apil na ang bag-ong password kung gi-set)
    public function updateUser(int $id, array $data): bool
    {
        if (!empty($data['new_password_hash'])) {
            $edit_stmt = $this->conn->prepare(
                "UPDATE users SET name=?, username=?, email=?, password=?, role=?, status=?, post_code=? WHERE oUserid=?"
            );
            $edit_stmt->bind_param(
                "sssssisi",
                $data['name'],
                $data['username'],
                $data['email'],
                $data['new_password_hash'],
                $data['role'],
                $data['status_val'],
                $data['post_code'],
                $id
            );
        } else {
            $edit_stmt = $this->conn->prepare(
                "UPDATE users SET name=?, username=?, email=?, role=?, status=?, post_code=? WHERE oUserid=?"
            );
            $edit_stmt->bind_param(
                "ssssssi",
                $data['name'],
                $data['username'],
                $data['email'],
                $data['role'],
                $data['status_val'],
                $data['post_code'],
                $id
            );
        }

        $ok = $edit_stmt->execute();
        $edit_stmt->close();
        return $ok;
    }

    // Nag-delete sa user record base sa oUserid
    public function deleteUser(int $id): bool
    {
        return (bool) mysqli_query($this->conn, "DELETE FROM users WHERE oUserid= $id ");
    }

    // Nagbalik sa pinaka-ulahing database error message gikan sa $conn
    public function getLastError(): string
    {
        return $this->conn->error;
    }
}