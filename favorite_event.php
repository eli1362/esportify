<?php
session_start();
require_once 'backend/config.php';
global $pdo;

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'] ?? null;

    if ($event_id) {
        // Check if the event is already in the user's favorites
        $stmt = $pdo->prepare("SELECT * FROM user_favorites WHERE event_id = :event_id AND user_id = :user_id");
        $stmt->execute(['event_id' => $event_id, 'user_id' => $_SESSION['user_id']]);

        if ($stmt->rowCount() === 0) {
            // Add to favorites
            $stmt = $pdo->prepare("INSERT INTO user_favorites (user_id, event_id) VALUES (:user_id, :event_id)");
            $stmt->execute(['user_id' => $_SESSION['user_id'], 'event_id' => $event_id]);
            $_SESSION['message'] = "Événement ajouté aux favoris.";
        } else {
            // Remove from favorites
            $stmt = $pdo->prepare("DELETE FROM user_favorites WHERE user_id = :user_id AND event_id = :event_id");
            $stmt->execute(['user_id' => $_SESSION['user_id'], 'event_id' => $event_id]);
            $_SESSION['message'] = "Événement retiré des favoris.";
        }
    } else {
        $_SESSION['message'] = "Données invalides.";
    }
}

header("Location: all-events.php");
exit();

