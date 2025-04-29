<?php
// Connexion à la base de données
global $pdo;
require 'config.php';

$data = json_decode(file_get_contents('php://input'), true);
$eventId = $data['eventId'];

// Vérification que l'utilisateur est connecté
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Veuillez vous connecter']);
    exit;
}

// Vérification si l'utilisateur peut rejoindre l'événement
$query = "SELECT * FROM events WHERE id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$eventId]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if ($event && $event['maxPlayers'] > 0) {
    // Inscription de l'utilisateur à l'événement
    $query = "INSERT INTO event_participants (event_id, user_id) VALUES (?, ?)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$eventId, $_SESSION['user_id']]);

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Impossible de rejoindre cet événement']);
}
?>

