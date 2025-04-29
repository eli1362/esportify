<?php
session_start();
require_once 'backend/config.php';
global $pdo;

// Ensure user is logged in and session data is valid
if (!isset($_SESSION['user_id'], $_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['participant_id'] ?? null;
    $event_id = $_POST['event_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if (
        $user_id && $event_id &&
        ctype_digit($user_id) && ctype_digit($event_id) &&
        in_array($action, ['approve', 'reject'])
    ) {
        // Confirm organizer's authority
        $stmt = $pdo->prepare("SELECT * FROM events WHERE id = :event_id AND organizer = :username");
        $stmt->execute([
            'event_id' => $event_id,
            'username' => $_SESSION['username']
        ]);
        $event = $stmt->fetch();

        if ($event) {
            // Verify participant with pending status
            $check = $pdo->prepare("
                SELECT * FROM event_participants 
                WHERE user_id = :user_id AND event_id = :event_id AND status = 'pending'
            ");
            $check->execute([
                'user_id' => $user_id,
                'event_id' => $event_id
            ]);
            $participant = $check->fetch();

            if ($participant) {
                if ($action === 'approve') {
                    $update = $pdo->prepare("
                        UPDATE event_participants 
                        SET status = 'accepted' 
                        WHERE user_id = :user_id AND event_id = :event_id
                    ");
                    $message = "Participant accepté (ID: $user_id)";
                } else {
                    $update = $pdo->prepare("
                        UPDATE event_participants 
                        SET status = 'rejected', rejected_at = NOW() 
                        WHERE user_id = :user_id AND event_id = :event_id
                    ");
                    $message = "Participant rejeté (ID: $user_id)";
                }

                $update->execute([
                    'user_id' => $user_id,
                    'event_id' => $event_id
                ]);

                // Log action in session_messages
                $log = $pdo->prepare("
                    INSERT INTO session_messages (event_id, user_id, message, created_at)
                    VALUES (:event_id, :user_id, :message, NOW())
                ");
                $log->execute([
                    'event_id' => $event_id,
                    'user_id' => $_SESSION['user_id'], // organizer ID
                    'message' => $message
                ]);

                $_SESSION['message'] = $message;
            } else {
                $_SESSION['message'] = "Erreur : Participant non trouvé ou statut invalide.";
            }
        } else {
            $_SESSION['message'] = "Erreur : Vous n'êtes pas l'organisateur de cet événement.";
        }
    } else {
        $_SESSION['message'] = "Données invalides.";
    }

    // Safe redirect only if $event_id is valid
    header('Location: view_participants.php?event_id=' . urlencode($event_id));
    exit();
} else {
    // If not a POST request
    header('Location: dashboard_organizer.php');
    exit();
}
