<?php
session_start();
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/controller/PermissionController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new PermissionController($conn);
    $controller->savePermissions($_POST, $_SESSION);
}