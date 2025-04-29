<?php
global $pdo;
session_start();
require_once "backend/config.php";

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$event_id = $_GET['id'];

// Check if user is already joined or if event is full
$stmt = $pdo->prepare("SELECT 1 FROM user_events WHERE user_id = :user_id AND event_id = :event_id");
$stmt->execute(['user_id' => $user_id, 'event_id' => $event_id]);
$userJoined = $stmt->rowCount() > 0;

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM user_events WHERE event_id = :event_id");
$countStmt->execute(['event_id' => $event_id]);
$currentParticipants = $countStmt->fetchColumn();

$stmt = $pdo->prepare("SELECT max_participants FROM events WHERE id = :event_id");
$stmt->execute(['event_id' => $event_id]);
$event = $stmt->fetch();

if ($userJoined) {
    $_SESSION['message'] = "Vous avez déjà rejoint cet événement.";
    header("Location: dashboard_user.php");
    exit();
} elseif ($currentParticipants >= $event['max_participants']) {
    $_SESSION['message'] = "Cet événement est déjà complet.";
    header("Location: dashboard_user.php");
    exit();
} else {
    $stmt = $pdo->prepare("INSERT INTO user_events (user_id, event_id) VALUES (:user_id, :event_id)");
    $stmt->execute(['user_id' => $user_id, 'event_id' => $event_id]);

    $_SESSION['message'] = "Vous avez rejoint l'événement avec succès.";
    header("Location: dashboard_user.php");
    exit();
}

