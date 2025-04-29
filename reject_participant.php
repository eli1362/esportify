<?php
global $pdo;
session_start();
require_once 'backend/config.php';

// Check if the user is authorized
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header('Location: login.php');
    exit();
}

if (isset($_POST['participant_id'])) {
    $participant_id = (int)$_POST['participant_id'];

    // Update the participant status to "rejected"
    $stmt = $pdo->prepare("UPDATE event_participants SET status = 'rejected', rejected_at = NOW() WHERE id = :id");
    $stmt->execute(['id' => $participant_id]);

    echo "<p>Participant rejeté avec succès !</p>";
    // Optionally redirect back to the participants page
    header("Location: view_participants.php?event_id=" . $_GET['event_id']);
}


