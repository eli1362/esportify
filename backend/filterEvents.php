<?php
// Connexion à la base de données
global $pdo;
require 'config.php';

$filter = $_GET['filter'];

// Requête pour filtrer les événements
$query = "SELECT * FROM events WHERE status = 'validé' ORDER BY $filter"; // Tri en fonction du filtre
$result = $pdo->query($query);

$events = [];
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $events[] = $row;
}

echo json_encode($events); // Renvoie les événements filtrés au format JSON
?>

