<?php
global $pdo;
require_once "backend/config.php";
session_start();

if (!isset($_SESSION['id']) || !isset($_GET['id'])) exit;

$stmt = $pdo->prepare("UPDATE event_discussions SET reported = 1 WHERE id = :id");
$stmt->execute(['id' => $_GET['id']]);

