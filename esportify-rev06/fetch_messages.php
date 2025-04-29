<?php
global $pdo;
require_once "backend/config.php";
session_start();

if (!isset($_GET['event_id'])) {
    echo json_encode([]);
    exit;
}

$event_id = $_GET['event_id'];

$stmt = $pdo->prepare("
    SELECT ed.id, ed.message, ed.timestamp, ed.user_id, u.username
    FROM event_discussions ed
    JOIN users u ON ed.user_id = u.id
    WHERE ed.event_id = :event_id
    ORDER BY ed.timestamp DESC
    LIMIT 50
");
$stmt->execute(['event_id' => $event_id]);
$messages = array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));

echo json_encode($messages);

