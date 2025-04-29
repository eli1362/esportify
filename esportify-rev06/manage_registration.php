<?php
session_start();
require_once 'backend/config.php';
global $pdo;

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? null;
    $event_id = $_POST['event_id'] ?? null;
    $action = $_POST['action'] ?? null;  // 'accept' or 'reject'

    // Validate the action and ensure the user ID and event ID are set
    if ($user_id && $event_id && in_array($action, ['accept', 'reject'])) {
        $status = $action === 'accept' ? 'approved' : 'rejected';  // Set status based on action

        // Update the registration status in the user_events table
        $stmt = $pdo->prepare("UPDATE user_events SET status = :status WHERE user_id = :user_id AND event_id = :event_id");
        $stmt->execute([
            'status' => $status,
            'user_id' => $user_id,
            'event_id' => $event_id
        ]);

        $_SESSION['message'] = "Inscription mise à jour avec succès.";
    } else {
        $_SESSION['message'] = "Données invalides ou action non autorisée.";
    }
}

header("Location: dashboard_organizer.php");
exit();
