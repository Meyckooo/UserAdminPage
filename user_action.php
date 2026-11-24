<?php
// Nag-handle sa tanang user-related requests (register, login, add, update, delete)
session_start();
require 'config/config.php';
require_once __DIR__ . '/controller/UserController.php';

$userController = new UserController($conn);

if (isset($_POST['register'])) {
    $userController->register($_POST);
}

if (isset($_POST['login'])) {
    $userController->login($_POST);
}

if (isset($_POST['add']) || (isset($_POST['is_ajax']) && !isset($_POST['update']))) {
    $userController->addUser($_POST, $_SERVER);
}

if (isset($_POST['update'])) {
    $id = intval($_GET['id']);
    $userController->updateUser($id, $_POST);
}

if (isset($_GET['oUserid'])) {
    $id = intval($_GET['oUserid']);
    $userController->deleteUser($id);
}