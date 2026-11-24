<?php
// Nag-handle sa tanang item/locator requests (post items ug fetch posted items)
session_start();
require 'config/config.php';
require_once __DIR__ . '/controller/ItemController.php';

$itemController = new ItemController($conn, 16);

if (isset($_POST['post_items'])) {
    $itemController->postItems($_POST, $_SESSION);
}

if (isset($_POST['get_posted_items'])) {
    $itemController->getPostedItems($_POST);
}