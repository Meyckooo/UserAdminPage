<?php

class AccessModel
{
    private $conn;

    // Nag-initialize sa database connection para magamit sa tanang query methods sa AccessModel
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Nag-fetch sa access row sa user para sa usa ka specific module gikan sa tbl_access
    public function getUserModuleAccess(int $userId, int $moduleId): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM tbl_access WHERE oUserid = ? AND oModuleid = ? LIMIT 1");
        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("ii", $userId, $moduleId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $stmt->close();

        return $row ?: null;
    }
}