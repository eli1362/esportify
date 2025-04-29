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

// Fetch event details
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = :event_id AND organizer = :username");
$stmt->execute(['event_id' => $_GET['event_id'], 'username' => $_SESSION['username']]);
$event = $stmt->fetch();

if (!$event) {
    echo "<p>Événement non trouvé ou vous n'êtes pas l'organisateur.</p>";
    exit();
}

// Fetch participants for the event
$stmt = $pdo->prepare("SELECT users.id, users.username, event_participants.status, event_participants.rejected_at
                       FROM event_participants
                       JOIN users ON event_participants.user_id = users.id
                       WHERE event_participants.event_id = :event_id");
$stmt->execute(['event_id' => $_GET['event_id']]);
$participants = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gérer les Participants</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/images/png/circle.png">
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/fonts.css">
    <link rel="stylesheet" href="assets/css/grid.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/user-dashboard.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
</head>
<body>

<header class="header">
    <?php include_once "header.php"; ?>
</header>

<main class="main">
    <div class="container user-dashboard">
        <h1 class="user-title">Gérer les participants pour <?= htmlspecialchars($event['name']) ?></h1>

        <section class="participants-section">
            <h2 style="margin: 4rem 0">Liste des participants</h2>
            <div class="participants-list">
                <?php if ($participants): ?>
                    <?php foreach ($participants as $participant): ?>
                        <div class="card participant-card">
                            <div class="card-content">
                                <p><strong><?= htmlspecialchars($participant['username']) ?></strong></p>
                                <p>Status: <?= htmlspecialchars($participant['status']) ?></p>
                                <?php if ($participant['status'] == 'pending'): ?>
                                    <form method="POST" action="reject_participant.php">
                                        <input type="hidden" name="participant_id" value="<?= $participant['id'] ?>">
                                        <button type="submit" class="btn reject btn__like" onclick="return confirm('Êtes-vous sûr de vouloir rejeter ce participant ?');">Rejeter</button>
                                    </form>
                                <?php endif; ?>
                                <?php if ($participant['status'] == 'rejected'): ?>
                                    <p><em>Participant rejeté le <?= htmlspecialchars($participant['rejected_at']) ?></em></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="card no-participants-card">
                        <p>Aucun participant trouvé.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Button to join event if it's not started -->
        <?php if ($event['session_started'] == 0): ?>
            <a href="register_participant.php?event_id=<?= $event['id'] ?>" class="btn join" style="border">Rejoindre l'événement</a>
        <?php else: ?>
            <p><em>La session de cet événement a déjà commencé.</em></p>
        <?php endif; ?>

        <a href="dashboard_organizer.php" class="btn btn__like" style="border-radius: 50px;max-height: 46.2px;margin:10px 0 0 .5rem;padding: 8px 15px">Retour au tableau de bord</a>
    </div>
</main>

<footer>
    <a href="logout.php" class="btn">Déconnexion</a>
</footer>

<script src="assets/js/script.js"></script>
</body>
</html>
