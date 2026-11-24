<?php

require_once __DIR__ . '/../entity/PermissionModel.php';

class PermissionController
{
    private PermissionModel $permissionModel;
    private int $permissionModuleId = 3;

    public function __construct($conn)
    {
        $this->permissionModel = new PermissionModel($conn);
    }

    public function getActions(): array
    {
        return PermissionModel::ACTIONS;
    }

    // Para sa views/permission.php
    public function loadPermissionViewData(int $loggedInUserId, string $role, int $targetUserId): array
    {
        $isAdmin = strtolower(trim($role)) === 'admin';
        $currentUserAccess = $this->permissionModel->getUserModuleAccess($loggedInUserId, $this->permissionModuleId);

        $hasViewAccess = $currentUserAccess && (
            ($currentUserAccess['oMain'] ?? 0) == 1 ||
            ($currentUserAccess['oView'] ?? 0) == 1 ||
            ($currentUserAccess['oEdit'] ?? 0) == 1
        );

        if (!$isAdmin && !$hasViewAccess) {
            return [
                'ok' => false,
                'redirect' => 'user_account.php?error=unauthorized'
            ];
        }

        $targetUser = $this->permissionModel->getUserById($targetUserId);
        if (!$targetUser) {
            return [
                'ok' => false,
                'redirect' => 'user_account.php'
            ];
        }

        $canSavePermission = $isAdmin || ($currentUserAccess && (
            ($currentUserAccess['oSave'] ?? 0) == 1 ||
            ($currentUserAccess['oEdit'] ?? 0) == 1
        ));

        return [
            'ok' => true,
            'target_user' => $targetUser,
            'access_data' => $this->permissionModel->getPermissionsByUserId($targetUserId),
            'modules' => $this->permissionModel->getAllModules(),
            'actions' => PermissionModel::ACTIONS,
            'can_save_permission' => $canSavePermission
        ];
    }

    // Para sa save_permissions.php (POST)
    public function savePermissions(array $postData, array $sessionData): void
    {
        $currentUserId = $sessionData['user_id'] ?? $sessionData['oUserid'] ?? null;

        if (!$currentUserId) {
            header("Location: views/index.php");
            exit();
        }

        $targetUserId = intval($postData['target_userid'] ?? 0);
        $role = $sessionData['role'] ?? '';
        $isAdmin = strtolower(trim($role)) === 'admin';

        $userAccess = $this->permissionModel->getUserModuleAccess((int)$currentUserId, $this->permissionModuleId);
        $canEdit = $userAccess && (($userAccess['oEdit'] ?? 0) == 1 || ($userAccess['oSave'] ?? 0) == 1);

        if (!$canEdit && !$isAdmin) {
            header("Location: views/permission.php?oUserid=" . $targetUserId . "&error=no_permission");
            exit();
        }

        $submittedPermissions = (isset($postData['permissions']) && is_array($postData['permissions']))
            ? $postData['permissions']
            : [];

        $this->permissionModel->upsertPermissions($targetUserId, $submittedPermissions);
        $this->permissionModel->resequenceAccessIds();

        header("Location: views/permission.php?oUserid=" . $targetUserId . "&status=saved");
        exit();
    }
}