<?php
session_start();
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Item.php';
require_once __DIR__ . '/../classes/Rental.php';

$database = new Database();
$db = $database->connect();

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . baseUrl() . 'login.php');
        exit;
    }
}
function requireAdmin() {
    requireLogin();
    if ($_SESSION['role'] !== 'admin') {
        header('Location: ' . baseUrl() . 'user/dashboard.php');
        exit;
    }
}
function baseUrl() {
    // Path relatif dari folder saat ini ke root project
    $script = $_SERVER['SCRIPT_NAME'];
    if (strpos($script, '/admin/') !== false || strpos($script, '/user/') !== false) {
        return '../';
    }
    return '';
}
function e($str) { return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8'); }
function rupiah($n) { return 'Rp ' . number_format((float)$n, 0, ',', '.'); }
