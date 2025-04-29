<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header('Location: login.php');
    exit();
}

require_once 'backend/config.php';
global $pdo;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord Organisateur</title>
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
        <h1 class="user-title">Bienvenue, <?= htmlspecialchars($_SESSION['username']) ?> 👋</h1>

        <section class="hero-welcome">
            <div class="hero-left">
                <h2>Bienvenue <?= htmlspecialchars($_SESSION['username']) ?> !</h2>
                <p>Voici vos événements créés, vos inscriptions et la gestion des utilisateurs.</p>
                <a href="create_event.php" class="btn user-dashboard__btn">+ Créer un événement</a>
            </div>
            <div class="hero-right">
                <img src="assets/images/png/dashboard_illustration.png" alt="Illustration dashboard">
            </div>
        </section>

        <section class="dashboard-section">
            <h2>Événements Créés</h2>
            <div class="cards-grid">
                <?php
                $stmt = $pdo->prepare("SELECT * FROM events WHERE organizer = :username");
                $stmt->execute(['username' => $_SESSION['username']]);
                $created_events = $stmt->fetchAll();

                if ($created_events): ?>
                    <?php foreach ($created_events as $event): ?>
                        <div class="card event-card">
                            <h3><?= htmlspecialchars($event['name']) ?></h3>
                            <p><?= htmlspecialchars($event['description']) ?></p>
                            <p>Status: <strong><?= htmlspecialchars($event['validation_status']) ?></strong></p>
                            <p>Participants: <?= htmlspecialchars($event['current_participants'] ?? 0) ?> / <?= htmlspecialchars($event['max_participants']) ?></p>
                            <a href="edit_event.php?id=<?= $event['id'] ?>" class="btn btn__like">Modifier</a>
                            <a href="view_participants.php?event_id=<?= $event['id'] ?>" class="btn btn__like">Gérer les participants</a>

                            <!-- Like/Favorite button -->
                            <form method="POST" action="favorite_event.php">
                                <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                <button type="submit" class="btn btn__like">Like</button>
                            </form>

                            <?php
                            $start_time = isset($event['start_time']) ? strtotime($event['start_time']) : null;
                            $now = time();
                            if ($event['validation_status'] === 'validé' && $event['session_started'] == 0 && $start_time && $now >= ($start_time - 1800)): ?>
                                <form method="POST" action="start_event.php">
                                    <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                    <button type="submit" class="btn start" onclick="return confirm('Etes-vous sûr de vouloir démarrer cet événement ?');">Démarrer</button>
                                </form>
                            <?php elseif ($event['session_started'] == 1): ?>
                                <p><em>Session en cours</em></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucun événement créé.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>
</main>

<footer>
    <a href="logout.php" class="btn">Déconnexion</a>
</footer>

<script src="assets/js/script.js"></script>
</body>
</html>
