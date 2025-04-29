<?php
session_start();
require_once 'backend/config.php';
global $pdo;

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_POST['event_id'])) {
    $event_id = (int) $_POST['event_id'];
    $user_id = $_SESSION['user_id'];

    // Update the user's status to "approved"
    $updateStmt = $pdo->prepare("
        UPDATE user_events 
        SET status = 'approved', finished_at = NULL
        WHERE user_id = :user_id AND event_id = :event_id
    ");
    $updateStmt->execute(['user_id' => $user_id, 'event_id' => $event_id]);

    // Increase the current_participants by 1 in the events table
    $updateEventStmt = $pdo->prepare("
        UPDATE events 
        SET current_participants = current_participants + 1
        WHERE id = :event_id
    ");
    $updateEventStmt->execute(['event_id' => $event_id]);

    $_SESSION['message'] = "Vous avez restauré votre participation à l'événement.";
    header('Location: dashboard_user.php');
    exit();
}


