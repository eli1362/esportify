<?php
session_start();
require_once "backend/config.php";
global $pdo;

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Retrieve favorited events (based on user_favorites table)
$stmt = $pdo->prepare("SELECT events.* 
                       FROM events 
                       JOIN user_favorites ON events.id = user_favorites.event_id
                       WHERE user_favorites.user_id = :user_id AND events.is_validated = 1");
$stmt->execute(['user_id' => $user_id]);
$favorited_events = $stmt->fetchAll();

// Retrieve events created by the user (organizer)
$stmt = $pdo->prepare("SELECT * FROM events WHERE organizer = :username");
$stmt->execute(['username' => $_SESSION['username']]);
$created_events = $stmt->fetchAll();

// Retrieve user's event participation history (including scores and status, + ids)
$stmt = $pdo->prepare("SELECT events.name, events.id AS event_id, user_events.user_id, user_events.score, user_events.status 
                       FROM user_events
                       JOIN events ON user_events.event_id = events.id 
                       WHERE user_events.user_id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user_events = $stmt->fetchAll();

// Fetch all approved events for the user
$stmt = $pdo->prepare("
    SELECT e.id, e.name, e.description, e.current_participants, ue.user_id, ue.event_id, ue.status
    FROM user_events ue
    JOIN events e ON ue.event_id = e.id
    WHERE ue.user_id = :user_id AND ue.status = 'approved'
");
$stmt->execute(['user_id' => $user_id]);
$approved_events = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord Utilisateur</title>
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

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert success-message"><?= htmlspecialchars($_SESSION['message']) ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <section class="hero-welcome">
            <div class="hero-left">
                <h2>Bienvenue <?= htmlspecialchars($_SESSION['username']) ?> !</h2>
                <p>Découvrez vos événements favoris, créez-en de nouveaux, et suivez vos performances.</p>

            </div>
            <div class="hero-right">
                <img src="assets/images/png/dashboard_illustration.png" alt="Illustration dashboard">
            </div>
        </section>

        <section class="dashboard-section">
            <h2>Événements Favoris</h2>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert"><?= htmlspecialchars($_SESSION['message']) ?></div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <div class="cards-grid">
                <?php if ($favorited_events): ?>
                    <?php foreach ($favorited_events as $event): ?>
                        <div class="card event-card">
                            <h3><?= htmlspecialchars($event['name']) ?></h3>
                            <p><?= htmlspecialchars($event['description']) ?></p>

                            <?php
                            $statusStmt = $pdo->prepare("SELECT status FROM user_events WHERE user_id = :user_id AND event_id = :event_id LIMIT 1");
                            $statusStmt->execute([
                                'user_id' => $_SESSION['user_id'],
                                'event_id' => $event['id']
                            ]);
                            $status = $statusStmt->fetchColumn();

                            if ($status === 'approved') {
                                echo "<p><em>Inscription acceptée ✅</em></p>";
                            } elseif ($status === 'rejected') {
                                echo "<p><em>Inscription refusée ❌</em></p>";
                            } elseif ($status === 'pending') {
                                echo "<p><em>En attente de validation ⏳</em></p>";
                            } else {
                                echo '<a href="register_event.php?event_id=' . $event['id'] . '" class="btn">S\'inscrire</a>';
                            }
                            ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucun événement favori trouvé.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="dashboard-section">
            <h2>Événements Approuvés</h2>
            <div class="cards-grid">
                <?php if ($approved_events): ?>
                    <?php foreach ($approved_events as $event): ?>
                        <div class="card">
                            <h3><?= htmlspecialchars($event['name']) ?></h3>
                            <p><?= htmlspecialchars($event['description']) ?></p>
                            <p>Participants actuels: <?= htmlspecialchars($event['current_participants']) ?></p>
                            <form method="POST" action="leave_event.php" style="display: flex; gap: 1rem; margin-top: 10px;">
                                <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">
                                <input type="hidden" name="user_id" value="<?= $event['user_id'] ?>">
                                <button type="submit" name="action" value="quit" class="btn btn__like">Quitter</button>
                                <button type="submit" name="action" value="finish" class="btn">Finir</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucun événement approuvé pour le moment.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="dashboard-section">
            <h2>Mes Événements Terminés</h2>
            <div class="cards-grid">
                <?php
                // Fetch the events where the user has finished participating
                $finishedStmt = $pdo->prepare("
            SELECT e.name, e.description, e.id, ue.finished_at
            FROM user_events ue
            JOIN events e ON ue.event_id = e.id
            WHERE ue.user_id = :user_id AND ue.status = 'finished'
        ");
                $finishedStmt->execute(['user_id' => $_SESSION['user_id']]);
                $finished_events = $finishedStmt->fetchAll();

                if ($finished_events):
                    foreach ($finished_events as $event): ?>
                        <div class="card event-card">
                            <h3><?= htmlspecialchars($event['name']) ?></h3>
                            <p><?= htmlspecialchars($event['description']) ?></p>
                            <p>Terminé le : <?= date("d/m/Y H:i", strtotime($event['finished_at'])) ?></p>

                            <form method="POST" action="restore_event.php">
                                <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                <button type="submit" class="btn restore">Restaurer</button>
                            </form>
                        </div>
                    <?php endforeach;
                else: ?>
                    <p>Aucun événement terminé pour le moment.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="dashboard-section">
            <h2>Historique des Scores</h2>

            <div class="cards-grid">
                <?php if ($user_events): ?>
                    <?php foreach ($user_events as $event): ?>
                        <div class="card">
                            <h3><?= htmlspecialchars($event['name']) ?></h3>
                            <p>Status: <?= htmlspecialchars($event['status']) ?></p>
                            <p>Score: <?= is_null($event['score']) ? 'Non attribué' : htmlspecialchars($event['score']) ?></p>

                            <?php if (($event['status'] === 'approved' || $event['status'] === 'rejected') && is_null($event['score']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'employee')): ?>
                                <form method="POST" action="update_score.php">
                                    <input type="hidden" name="user_id" value="<?= $event['user_id'] ?>">
                                    <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">
                                    <input type="number" name="score" placeholder="Score" required>
                                    <button type="submit">Attribuer Score</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucun score enregistré.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>
</main>
<footer>
    <?php
    require_once "footer.php";
    ?>
</footer>

<script src="assets/js/script.js"></script>
</body>
</html>
