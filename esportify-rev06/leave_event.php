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

// Remove the user from the event
$stmt = $pdo->prepare("DELETE FROM user_events WHERE user_id = :user_id AND event_id = :event_id");
$stmt->execute(['user_id' => $user_id, 'event_id' => $event_id]);

$_SESSION['message'] = "Vous avez quitté l'événement.";
header("Location: dashboard_user.php");
exit();


