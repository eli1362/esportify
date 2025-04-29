<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header('Location: login.php');
    exit();
}

require_once 'backend/config.php';
global $pdo;

if (isset($_GET['event_id']) && isset($_SESSION['user_id'])) {
    // Assume the event_id and user_id are properly sanitized
    $event_id = $_GET['event_id'];
    $user_id = $_SESSION['user_id'];

    // Check if the user is already registered
    $stmt = $pdo->prepare("SELECT * FROM event_participants WHERE event_id = :event_id AND user_id = :user_id");
    $stmt->execute(['event_id' => $event_id, 'user_id' => $user_id]);
    $existing_registration = $stmt->fetch();

    if (!$existing_registration) {
        // User is not yet registered, so insert the new registration with "pending" status
        $stmt = $pdo->prepare("INSERT INTO event_participants (event_id, user_id, status) VALUES (:event_id, :user_id, 'pending')");
        $stmt->execute(['event_id' => $event_id, 'user_id' => $user_id]);

        echo "Vous avez bien demandé à rejoindre l'événement. Votre inscription est en attente d'approbation.";
    } else {
        echo "Vous êtes déjà inscrit à cet événement.";
    }
}

