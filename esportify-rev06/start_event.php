<?php
global $pdo;
session_start();
require_once "backend/config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    $username = $_SESSION['username'];

    // Vérifie si l'utilisateur est bien l'organisateur
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id AND organizer = :organizer");
    $stmt->execute(['id' => $event_id, 'organizer' => $username]);
    $event = $stmt->fetch();

    if ($event) {
        // Met à jour le champ session_started à 1
        $update = $pdo->prepare("UPDATE events SET session_started = 1 WHERE id = :id");
        $update->execute(['id' => $event_id]);

        $_SESSION['message'] = "La session a été démarrée avec succès.";
    } else {
        $_SESSION['message'] = "Erreur : Vous n'êtes pas autorisé à démarrer cette session.";
    }
} else {
    $_SESSION['message'] = "Requête invalide.";
}

header("Location: dashboard_user.php");
exit;
