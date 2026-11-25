<?php
// Nag-provide og global helper wrappers nga naka-delegate sa AccessController para sa backward compatibility
require_once __DIR__ . '/controller/AccessController.php';

function getAccessController(): AccessController
{
    global $conn;
    static $controller = null;

    if ($controller === null) {
        $controller = new AccessController($conn);
    }

    return $controller;
}

function hasElevatedModuleAccess($module_id): bool
{
    return getAccessController()->hasElevatedModuleAccess(intval($module_id), $_SESSION ?? []);
}

function hasPermission($module_id, $column): bool
{
    return getAccessController()->hasPermission(intval($module_id), (string) $column, $_SESSION ?? []);
}

function showAccessDeniedAlert($message = "You don't have permission to access this module.", $redirectUrl = 'index.php'): void
{
    getAccessController()->showAccessDeniedAlert((string) $message, (string) $redirectUrl);
}

function checkAccess($module_id, $column, $custom_message = ''): bool
{
    return getAccessController()->checkAccess(intval($module_id), (string) $column, $_SESSION ?? [], (string) $custom_message);
}

function renderModulePermissionScript($module_id): void
{
    getAccessController()->renderModulePermissionScript(intval($module_id), $_SESSION ?? []);
}

function requirePageAccess(
    $module_id,
    $column = 'oMain',
    $custom_message = '',
    $loginUrl = 'login.php',
    $redirectUrl = 'index.php',
    $basePath = '..'
): void {
    getAccessController()->requirePageAccess(
        intval($module_id),
        (string) $column,
        $_SESSION ?? [],
        (string) $custom_message,
        (string) $loginUrl,
        (string) $redirectUrl,
        (string) $basePath
    );
}