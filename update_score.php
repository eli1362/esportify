<?php
session_start();
require_once "backend/config.php";
global $pdo;

// ✅ Only admin or employee can update scores
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'employee'])) {
    $_SESSION['message'] = "Accès refusé.";
    header("Location: dashboard_user.php");
    exit();
}

// ✅ Validate POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['event_id'], $_POST['score'])) {
    $user_id = intval($_POST['user_id']);
    $event_id = intval($_POST['event_id']);
    $score = intval($_POST['score']);

    // ✅ Check if score is in valid range
    if ($score < 0 || $score > 100) {
        $_SESSION['message'] = "Le score doit être entre 0 et 100.";
        header("Location: dashboard_user.php");
        exit();
    }

    try {
        // ✅ Check that user's registration is approved
        $checkStmt = $pdo->prepare("SELECT status FROM user_events WHERE user_id = :user_id AND event_id = :event_id");
        $checkStmt->execute(['user_id' => $user_id, 'event_id' => $event_id]);
        $status = $checkStmt->fetchColumn();

        if ($status !== 'approved') {
            $_SESSION['message'] = "Impossible d'attribuer un score. L'utilisateur n'est pas approuvé pour cet événement.";
        } else {
            // ✅ Update the score
            $stmt = $pdo->prepare("UPDATE user_events 
                                   SET score = :score 
                                   WHERE user_id = :user_id AND event_id = :event_id");
            $stmt->execute([
                'score' => $score,
                'user_id' => $user_id,
                'event_id' => $event_id
            ]);

            $_SESSION['message'] = "Score attribué avec succès.";
        }

    } catch (PDOException $e) {
        $_SESSION['message'] = "Erreur lors de la mise à jour : " . $e->getMessage();
    }

} else {
    $_SESSION['message'] = "Données invalides reçues.";
}

header("Location: dashboard_user.php");
exit();
