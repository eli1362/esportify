<?php
global $pdo;
session_start();
require_once 'backend/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    require_once 'backend/config.php';

    $id = intval($_POST['id']);
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: admin_dashboard.php?page=1");
    exit;
} else {
    header("Location: admin_dashboard.php");
    exit;
}

