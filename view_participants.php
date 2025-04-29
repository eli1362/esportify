<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header('Location: login.php');
    exit();
}

require_once 'backend/config.php';
global $pdo;

if (!isset($_GET['event_id'])) {
    echo "<p>Erreur : aucun événement spécifié.</p>";
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM events WHERE id = :event_id AND organizer = :username");
$stmt->execute(['event_id' => $_GET['event_id'], 'username' => $_SESSION['username']]);
$event = $stmt->fetch();

if (!$event) {
    echo "<p>Événement non trouvé ou vous n'êtes pas l'organisateur.</p>";
    exit();
}

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
            <h2>Liste des participants</h2>
            <div class="participants-list">
                <?php if ($participants): ?>
                    <?php foreach ($participants as $participant): ?>
                        <div class="participant">
                            <p><strong><?= htmlspecialchars($participant['username']) ?></strong></p>
                            <p>Status: <?= htmlspecialchars($participant['status']) ?></p>

                            <?php if ($participant['status'] === 'pending'): ?>
                                <form method="POST" action="approve_reject_participant.php">
                                    <input type="hidden" name="participant_id" value="<?= $participant['id'] ?>">
                                    <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                    <button type="submit" name="action" value="approve" class="btn accept" onclick="return confirm('Accepter ce participant ?');">Accepter</button>
                                    <button type="submit" name="action" value="reject" class="btn reject" onclick="return confirm('Rejeter ce participant ?');">Rejeter</button>
                                </form>
                            <?php elseif ($participant['status'] === 'accepted'): ?>
                                <button class="btn accepted" disabled>Accepté</button>
                            <?php elseif ($participant['status'] === 'rejected'): ?>
                                <p><em>Participant rejeté le <?= htmlspecialchars($participant['rejected_at']) ?></em></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucun participant trouvé.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="participants-section">
            <h2>Journal des activités</h2>
            <div class="participants-list">
                <?php
                $log_stmt = $pdo->prepare("
                    SELECT sm.*, u.username 
                    FROM session_messages sm
                    JOIN users u ON sm.user_id = u.id
                    WHERE sm.event_id = :event_id
                    ORDER BY sm.created_at DESC
                ");
                $log_stmt->execute(['event_id' => $event['id']]);
                $logs = $log_stmt->fetchAll();

                if ($logs): ?>
                    <?php foreach ($logs as $log): ?>
                        <div class="participant">
                            <p><strong><?= htmlspecialchars($log['username']) ?>:</strong> <?= htmlspecialchars($log['message']) ?></p>
                            <small><?= htmlspecialchars($log['created_at']) ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucune activité enregistrée pour cet événement.</p>
                <?php endif; ?>
            </div>
        </section>

        <a href="dashboard_organizer.php" class="btn">Retour au tableau de bord</a>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success success-message">
                <?= htmlspecialchars($_SESSION['message']) ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
    </div>
    <a href="logout.php" class="btn">Déconnexion</a>
</main>

<footer>
    <?php
    require_once "footer.php";
    ?>
</footer>

<script src="assets/js/script.js"></script>
</body>
</html>