<?php
require_once "backend/config.php";
session_start();
global $pdo;

if (!isset($_SESSION['id']) || empty($_POST['message']) || empty($_POST['event_id'])) {
    http_response_code(400);
    echo "Requête invalide.";
    exit;
}

$user_id = $_SESSION['id'];
$event_id = (int) $_POST['event_id'];
$message = trim($_POST['message']);

$stmt = $pdo->prepare("
    INSERT INTO event_discussions (event_id, user_id, message) 
    VALUES (:event_id, :user_id, :message)
");
$stmt->execute([
    'event_id' => $event_id,
    'user_id' => $user_id,
    'message' => $message
]);

http_response_code(200);
echo "Message envoyé.";

