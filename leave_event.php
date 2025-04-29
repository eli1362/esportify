<?php
session_start();
require_once 'backend/config.php';
global $pdo;

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_POST['action'], $_POST['event_id'], $_POST['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $event_id = (int) $_POST['event_id'];
    $action = $_POST['action']; // "quit" or "finish"

    if ($action == 'quit') {
        // Update the user_event status to 'quit' and decrease the number of participants
        $updateStmt = $pdo->prepare("UPDATE user_events 
                                      SET status = 'rejected'  // Mark as rejected if quit 
                                      WHERE user_id = :user_id AND event_id = :event_id");
        $updateStmt->execute(['user_id' => $user_id, 'event_id' => $event_id]);

        // Decrease the current_participants by 1
        $updateEventStmt = $pdo->prepare("UPDATE events 
                                          SET current_participants = current_participants - 1 
                                          WHERE id = :event_id");
        $updateEventStmt->execute(['event_id' => $event_id]);

        $_SESSION['message'] = "Vous avez quitté l'événement.";
    } elseif ($action == 'finish') {
        // Update the user_event status to 'finished' and set the finished_at timestamp
        $updateStmt = $pdo->prepare("UPDATE user_events 
                                      SET status = 'finished', finished_at = NOW() 
                                      WHERE user_id = :user_id AND event_id = :event_id");
        $updateStmt->execute(['user_id' => $user_id, 'event_id' => $event_id]);

        // Decrease the current_participants by 1
        $updateEventStmt = $pdo->prepare("UPDATE events 
                                          SET current_participants = current_participants - 1 
                                          WHERE id = :event_id");
        $updateEventStmt->execute(['event_id' => $event_id]);

        $_SESSION['message'] = "Vous avez terminé l'événement.";
    }

    // Redirect to the user dashboard after the action
    header('Location: dashboard_user.php');
    exit();
} else {
    // In case of missing parameters, redirect back to the dashboard
    header('Location:dashboard_user.php');
    exit();
}
