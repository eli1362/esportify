<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header('Location: login.php');
    exit();
}

require_once 'backend/config.php';
global $pdo;

// Check if event_id is set in the URL
if (!isset($_GET['event_id'])) {
    echo "<p>Erreur : aucun événement spécifié.</p>";
    exit();
}

$event_id = $_GET['event_id'];

// Ensure the user is not already a participant
$stmt = $pdo->prepare("SELECT * FROM event_participants WHERE event_id = :event_id AND user_id = :user_id");
$stmt->execute(['event_id' => $event_id, 'user_id' => $_SESSION['user_id']]);
$existing_participant = $stmt->fetch();

if ($existing_participant) {
    echo "<p>Vous êtes déjà inscrit pour cet événement.</p>";
    exit();
}

// Insert the participant into the event_participants table
$stmt = $pdo->prepare("INSERT INTO event_participants (event_id, user_id, status) VALUES (:event_id, :user_id, :status)");
$stmt->execute([
    'event_id' => $event_id,
    'user_id' => $_SESSION['user_id'],
    'status' => 'pending'  // Initially, the status is pending
]);

echo "<p>Inscription réussie. En attente de validation.</p>";
?>

<a href="view_participants.php?event_id=<?= $event_id ?>" class="btn">Voir les participants</a>
<a href="dashboard_organizer.php" class="btn">Retour au tableau de bord</a>

