<?php
require 'backend/config.php';
global $pdo;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Vérifier si l'ID de l'événement est passé dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Aucun événement sélectionné.";
    exit();
}

$event_id = $_GET['id'];

// Préparer la requête pour récupérer les détails de l'événement
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = :event_id");
$stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
$stmt->execute();

$event = $stmt->fetch(PDO::FETCH_ASSOC);

// Si l'événement n'existe pas
if (!$event) {
    echo "Événement non trouvé.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <title>Détails de l'Événement</title>
    <link rel="icon" type="image/png" href="assets/images/png/circle.png">
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/fonts.css">
    <link rel="stylesheet" href="assets/css/grid.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<header>
<?php include 'header.php'; ?>
</header>
<main>
<div class="container">
    <h1>Détails de l'Événement</h1>

    <div class="event-detail">
        <h2><?php echo htmlspecialchars($event['name']); ?></h2>
        <div class="event-card">
            <p><strong>Date de début:</strong> <?php echo $event['start_date'] ? date('d M Y', strtotime($event['start_date'])) : 'Non définie'; ?>
            </p>
            <p><strong>Date de fin:</strong> <?php echo $event['end_date'] ? date('d M Y', strtotime($event['end_date'])) : 'Non définie'; ?>
            </p>
            <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
            <p><strong>Organisateur:</strong> <?php echo htmlspecialchars($event['organizer']); ?></p>
            <p><strong>Participants max:</strong> <?php echo htmlspecialchars($event['max_participants']); ?></p>
            <p><strong>Participants actuels:</strong> <?php echo htmlspecialchars($event['current_participants']); ?>
            </p>
            <p><strong>Statut de validation:</strong> <?php echo htmlspecialchars($event['validation_status']); ?></p>
        </div>
    </div>
    </div>
</main>
<footer>
<?php require_once 'footer.php'; ?>
</footer>
<script src="assets/js/script.js"></script>
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
</body>
</html>
