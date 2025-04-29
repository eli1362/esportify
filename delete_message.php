<?php
global $pdo;
require_once "backend/config.php";
session_start();

if (!isset($_SESSION['id']) || !isset($_GET['id'])) exit;

// Optionally check if user owns the message or is admin
$stmt = $pdo->prepare("DELETE FROM event_discussions WHERE id = :id AND user_id = :user_id");
$stmt->execute(['id' => $_GET['id'], 'user_id' => $_SESSION['id']]);

