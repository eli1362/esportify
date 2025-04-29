<?php
session_start();
require_once 'backend/config.php';
global $pdo;

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $record_id = $_POST['record_id'];
    $action = $_POST['action'];
    $event_id = $_POST['event_id'];
    $user_id = $_POST['user_id'];

    if ($action === 'accept') {
        // Update user_events table
        $pdo->prepare("UPDATE user_events SET status = 'approved' WHERE id = :id")
            ->execute(['id' => $record_id]);

        // Add log message
        $message = "L'organisateur {$_SESSION['username']} a accepté l'inscription de l'utilisateur ID $user_id.";
        $pdo->prepare("INSERT INTO session_messages (event_id, user_id, message, created_at) VALUES (:event_id, :user_id, :message, NOW())")
            ->execute([
                'event_id' => $event_id,
                'user_id' => $_SESSION['user_id'],
                'message' => $message
            ]);

        // Increment participant count
        $pdo->prepare("UPDATE events SET current_participants = current_participants + 1 WHERE id = :id")
            ->execute(['id' => $event_id]);

        $_SESSION['message'] = "Inscription approuvée.";
    } elseif ($action === 'reject') {
        $pdo->prepare("UPDATE user_events SET status = 'rejected' WHERE id = :id")
            ->execute(['id' => $record_id]);

        $message = "L'organisateur {$_SESSION['username']} a rejeté l'inscription de l'utilisateur ID $user_id.";
        $pdo->prepare("INSERT INTO session_messages (event_id, user_id, message, created_at) VALUES (:event_id, :user_id, :message, NOW())")
            ->execute([
                'event_id' => $event_id,
                'user_id' => $_SESSION['user_id'],
                'message' => $message
            ]);

        $_SESSION['message'] = "Inscription rejetée.";
    }
}

header("Location: dashboard_organizer.php");
exit();

