<?php

require_once __DIR__ . '/../entity/AccessModel.php';

class AccessController
{
    private AccessModel $accessModel;
    private $conn;

    // Nag-instantiate sa AccessModel ug nag-store sa $conn para sa permission checks
    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->accessModel = new AccessModel($conn);
    }

    // Nag-check kung naka-enable ang elevated role columns (SA, Supervisor, Manager) sa module para sa kasamtangang user
    public function hasElevatedModuleAccess(int $moduleId, array $sessionData): bool
    {
        if (!isset($sessionData['oUserid'])) {
            return false;
        }

        $userId = intval($sessionData['oUserid']);
        $modId  = intval($moduleId);

        $row = $this->accessModel->getUserModuleAccess($userId, $modId);
        if (!$row) {
            return false;
        }

        $elevatedColumns = ['oSalesassistant', 'oSupervisor', 'oManager'];
        foreach ($elevatedColumns as $col) {
            if (!empty($row[$col]) && intval($row[$col]) === 1) {
                return true;
            }
        }

        return false;
    }

    // Nag-check kung may access ang user sa usa ka specific column/action sa module (o kung elevated role siya sa maong module)
    public function hasPermission(int $moduleId, string $column, array $sessionData): bool
    {
        if (!isset($sessionData['oUserid'])) {
            return false;
        }

        $userId = intval($sessionData['oUserid']);
        $modId  = intval($moduleId);

        $row = $this->accessModel->getUserModuleAccess($userId, $modId);
        if (!$row) {
            return false;
        }

        if ($this->hasElevatedModuleAccess($modId, $sessionData)) {
            return true;
        }

        if (!array_key_exists($column, $row)) {
            return false;
        }

        return !empty($row[$column]) && intval($row[$column]) === 1;
    }

    // Nag-render og SweetAlert nga "Access Denied" ug mo-redirect balik sa index.php o gi-set nga redirectUrl
    public function showAccessDeniedAlert(string $message = "You don't have permission to access this module.", string $redirectUrl = 'index.php'): void
    {
        $safeMsg = json_encode($message);
        $safeUrl = json_encode($redirectUrl);

        echo "<!DOCTYPE html>
        <html>
        <head>
            <script src='assets/js/sweetalert.js'></script>
        </head>
        <body>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Access Denied',
                        text: {$safeMsg},
                        confirmButtonColor: '#dc3545',
                        allowOutsideClick: false
                    }).then(function() {
                        window.location.href = {$safeUrl};
                    });
                });
            </script>
        </body>
        </html>";
        exit();
    }

    // Nag-enforce sa page/action access; kung walay permission, mo-gawas ang Access Denied alert
    public function checkAccess(int $moduleId, string $column, array $sessionData, string $customMessage = ''): bool
    {
        if (!$this->hasPermission($moduleId, $column, $sessionData)) {
            $msg = $customMessage !== '' ? $customMessage : "You don't have permission to perform this action in this module.";
            $this->showAccessDeniedAlert($msg);
        }
        return true;
    }

    // Nag-build ug nag-echo sa window.USER_PERMISSIONS script tag para sa frontend JavaScript enforcement
    public function renderModulePermissionScript(int $moduleId, array $sessionData): void
    {
        $mod = intval($moduleId);
        $perms = [
            'canMain'       => $this->hasPermission($mod, 'oMain', $sessionData),
            'canAdd'        => $this->hasPermission($mod, 'oAdd', $sessionData),
            'canEdit'       => $this->hasPermission($mod, 'oEdit', $sessionData),
            'canView'       => $this->hasPermission($mod, 'oView', $sessionData),
            'canSave'       => $this->hasPermission($mod, 'oSave', $sessionData),
            'canPost'       => $this->hasPermission($mod, 'oPost', $sessionData),
            'canCancel'     => $this->hasPermission($mod, 'oCancel', $sessionData),
            'canPrint'      => $this->hasPermission($mod, 'oPrint', $sessionData),
            'canDiscount'   => $this->hasPermission($mod, 'oDiscount', $sessionData),
            'canSend'       => $this->hasPermission($mod, 'oSend', $sessionData),
            'canSA'         => $this->hasPermission($mod, 'oSalesassistant', $sessionData),
            'canSupervisor' => $this->hasPermission($mod, 'oSupervisor', $sessionData),
            'canManager'    => $this->hasPermission($mod, 'oManager', $sessionData),
            'canAudit'      => $this->hasPermission($mod, 'oAudit', $sessionData),
        ];

        echo "<script>window.USER_PERMISSIONS = " . json_encode($perms) . ";</script>\n";
    }
}