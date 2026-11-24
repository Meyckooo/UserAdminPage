<?php

class PermissionModel
{
    private $conn;

    public const ACTIONS = [
        'oMain', 'oAdd', 'oEdit', 'oView', 'oSave', 'oPost',
        'oCancel', 'oPrint', 'oDiscount', 'oSend', 'oSalesassistant',
        'oSupervisor', 'oManager', 'oAudit'
    ];

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // Kuhaon ang access rights sa logged-in user para sa specific module
    public function getUserModuleAccess(int $userId, int $moduleId): ?array
    {
        $stmt = $this->conn->prepare("SELECT oMain, oEdit, oView, oSave FROM tbl_access WHERE oUserid = ? AND oModuleid = ?");
        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("ii", $userId, $moduleId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $row ?: null;
    }

    // Kuhaon ang target user details
    public function getUserById(int $userId): ?array
    {
        $stmt = $this->conn->prepare("SELECT oUserid, name, role FROM users WHERE oUserid = ?");
        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return $user ?: null;
    }

    // Kuhaon ang existing permissions sa target user naka-index sa oModuleid
    public function getPermissionsByUserId(int $userId): array
    {
        $accessData = [];
        $stmt = $this->conn->prepare("SELECT * FROM tbl_access WHERE oUserid = ?");
        if (!$stmt) {
            return $accessData;
        }

        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $accessData[$row['oModuleid']] = $row;
        }

        $stmt->close();
        return $accessData;
    }

    // Kuhaon ang tanang modules
    public function getAllModules(): array
    {
        $modules = [];
        $result = mysqli_query($this->conn, "SELECT * FROM tbl_module ORDER BY oModuleid ASC");
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $modules[] = $row;
            }
        }
        return $modules;
    }

    // Kuhaon ang existing oModuleid list sa target user sa tbl_access
    public function getExistingModuleIds(int $targetUserId): array
    {
        $existingModules = [];
        $stmt = $this->conn->prepare("SELECT oModuleid FROM tbl_access WHERE oUserid = ?");
        if (!$stmt) {
            return $existingModules;
        }

        $stmt->bind_param("i", $targetUserId);
        $stmt->execute();
        $res = $stmt->get_result();

        while ($row = $res->fetch_assoc()) {
            $existingModules[] = intval($row['oModuleid']);
        }

        $stmt->close();
        return $existingModules;
    }

    // I-save / i-update gamit ang ON DUPLICATE KEY UPDATE
    public function upsertPermissions(int $targetUserId, array $submittedPermissions): bool
    {
        $upsertSql = "INSERT INTO tbl_access (
            oUserid, oModuleid, oMain, oAdd, oEdit, oView, oSave, oPost,
            oCancel, oPrint, oDiscount, oSend, oSalesassistant, oSupervisor, oManager, oAudit
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            oMain = VALUES(oMain), oAdd = VALUES(oAdd), oEdit = VALUES(oEdit),
            oView = VALUES(oView), oSave = VALUES(oSave), oPost = VALUES(oPost),
            oCancel = VALUES(oCancel), oPrint = VALUES(oPrint), oDiscount = VALUES(oDiscount),
            oSend = VALUES(oSend), oSalesassistant = VALUES(oSalesassistant),
            oSupervisor = VALUES(oSupervisor), oManager = VALUES(oManager), oAudit = VALUES(oAudit)";

        $upsertStmt = $this->conn->prepare($upsertSql);
        if (!$upsertStmt) {
            return false;
        }

        $existingModules = $this->getExistingModuleIds($targetUserId);
        $submittedModIds = array_map('intval', array_keys($submittedPermissions));
        $modulesToProcess = array_unique(array_merge($existingModules, $submittedModIds));

        foreach ($modulesToProcess as $modId) {
            $vals = [];
            foreach (self::ACTIONS as $act) {
                $vals[$act] = isset($submittedPermissions[$modId][$act]) ? 1 : 0;
            }

            $upsertStmt->bind_param(
                "iiiiiiiiiiiiiiii",
                $targetUserId, $modId,
                $vals['oMain'], $vals['oAdd'], $vals['oEdit'], $vals['oView'],
                $vals['oSave'], $vals['oPost'], $vals['oCancel'], $vals['oPrint'],
                $vals['oDiscount'], $vals['oSend'], $vals['oSalesassistant'],
                $vals['oSupervisor'], $vals['oManager'], $vals['oAudit']
            );
            $upsertStmt->execute();
        }

        $upsertStmt->close();
        return true;
    }

    // I-resequence ang oAccessid ug i-adjust ang AUTO_INCREMENT
    public function resequenceAccessIds(): void
    {
        mysqli_query($this->conn, "SET @count = 0");
        mysqli_query($this->conn, "UPDATE tbl_access SET oAccessid = (@count := @count + 1) ORDER BY oAccessid ASC");

        $maxRes = mysqli_query($this->conn, "SELECT MAX(oAccessid) as max_id FROM tbl_access");
        $maxRow = $maxRes ? mysqli_fetch_assoc($maxRes) : null;
        $nextId = ($maxRow['max_id'] ?? 0) + 1;
        mysqli_query($this->conn, "ALTER TABLE tbl_access AUTO_INCREMENT = $nextId");
    }
}