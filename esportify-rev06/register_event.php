<?php
session_start();
require_once 'backend/config.php';
global $pdo;

// Vérifie si l'utilisateur est connecté et est un utilisateur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$event_id = $_GET['event_id'] ?? null;
$user_id = $_SESSION['user_id'];

if ($event_id && is_numeric($event_id)) {
    try {
        // Vérifie si l'utilisateur est déjà inscrit à cet événement
        $check = $pdo->prepare("SELECT * FROM user_events WHERE user_id = :user_id AND event_id = :event_id");
        $check->execute([
            'user_id' => $user_id,
            'event_id' => $event_id
        ]);

        if ($check->rowCount() > 0) {
            $_SESSION['message'] = "Vous êtes déjà inscrit à cet événement.";
        } else {
            // Vérifie le nombre de participants actuels
            $event_check = $pdo->prepare("SELECT COUNT(*) FROM user_events WHERE event_id = :event_id AND status IN ('pending', 'approved')");
            $event_check->execute(['event_id' => $event_id]);
            $event_participants = $event_check->fetchColumn();

            // Récupère la limite de participants pour cet événement
            $event_stmt = $pdo->prepare("SELECT max_participants FROM events WHERE id = :event_id");
            $event_stmt->execute(['event_id' => $event_id]);
            $event = $event_stmt->fetch();

            if (!$event) {
                $_SESSION['message'] = "Événement non trouvé.";
            } elseif ($event_participants >= $event['max_participants']) {
                $_SESSION['message'] = "Désolé, cet événement est complet.";
            } else {
                // Insère l'inscription avec le statut 'pending'
                $stmt = $pdo->prepare("INSERT INTO user_events (user_id, event_id, status) VALUES (:user_id, :event_id, 'pending')");
                $stmt->execute([
                    'user_id' => $user_id,
                    'event_id' => $event_id
                ]);
                $_SESSION['message'] = "Inscription envoyée pour validation.";
            }
        }

    } catch (PDOException $e) {
        $_SESSION['message'] = "Erreur lors de l'inscription: " . $e->getMessage();
    }
} else {
    $_SESSION['message'] = "Événement invalide.";
}

header("Location: dashboard_user.php");
exit();
