<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'employee' && $_SESSION['role'] !== 'admin')) {
    header('Location: login.php');
    exit();
}

require_once 'backend/config.php';
global $pdo;

// Handle accepting or rejecting registration requests
if (isset($_POST['action'], $_POST['record_id'], $_POST['user_id'], $_POST['event_id'])) {
    $action = $_POST['action'];
    $record_id = (int) $_POST['record_id'];
    $user_id = (int) $_POST['user_id'];
    $event_id = (int) $_POST['event_id'];

    // Check if this event is managed by the logged-in user
    $check_event = $pdo->prepare("SELECT id FROM events WHERE id = :event_id AND organizer = :organizer");
    $check_event->execute([
        'event_id' => $event_id,
        'organizer' => $_SESSION['username']
    ]);

    if ($check_event->fetch()) {
        if ($action === 'accept') {
            // Check current status first
            $checkStmt = $pdo->prepare("
                SELECT status FROM user_events 
                WHERE user_id = :user_id AND event_id = :event_id
            ");
            $checkStmt->execute(['user_id' => $user_id, 'event_id' => $event_id]);
            $currentStatus = $checkStmt->fetchColumn();

            if ($currentStatus !== 'approved') {
                // Approve and increment
                $updateStmt = $pdo->prepare("
                    UPDATE user_events 
                    SET status = 'approved' 
                    WHERE id = :record_id
                ");
                $updateStmt->execute(['record_id' => $record_id]);

                $incrementStmt = $pdo->prepare("
                    UPDATE events 
                    SET current_participants = current_participants + 1 
                    WHERE id = :event_id
                ");
                $incrementStmt->execute(['event_id' => $event_id]);
            }

            $_SESSION['message'] = "Demande acceptée.";
        } elseif ($action === 'reject') {
            $update_stmt = $pdo->prepare("UPDATE user_events SET status = 'rejected' WHERE id = :record_id");
            $update_stmt->execute(['record_id' => $record_id]);
            $_SESSION['message'] = "Demande rejetée.";
        }
    } else {
        $_SESSION['message'] = "Erreur : vous n'avez pas l'autorisation de gérer cette demande.";
    }
}

// Action Log: Deleting session logs
if (isset($_POST['delete_log_id'])) {
    $log_id = (int) $_POST['delete_log_id'];

    $check_log = $pdo->prepare("
        SELECT sm.id
        FROM session_messages sm
        JOIN events e ON sm.event_id = e.id
        WHERE sm.id = :log_id AND e.organizer = :employee_id
    ");
    $check_log->execute([
        'log_id' => $log_id,
        'employee_id' => $_SESSION['username']
    ]);

    if ($check_log->fetch()) {
        $delete_log_stmt = $pdo->prepare("DELETE FROM session_messages WHERE id = :log_id");
        $delete_log_stmt->execute(['log_id' => $log_id]);
        $_SESSION['message'] = "Log supprimé avec succès.";
    } else {
        $_SESSION['message'] = "Erreur : Vous ne pouvez pas supprimer ce log.";
    }
}
?>

<!-- The rest of your HTML (same as your original) -->
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
                <p>Découvrez vos événements favoris, créez-en de nouveaux, et suivez vos performances.</p>
                <a href="create_event.php" class="btn user-dashboard__btn">+ Créer un événement</a>
            </div>
            <div class="hero-right">
                <img src="assets/images/png/Work%20time-amico.png" alt="Illustration dashboard">
            </div>
        </section>

        <!-- Created Events Section -->
        <section class="dashboard-section">
            <h2>Événements Créés</h2>
            <div class="cards-grid">
                <?php
                $stmt = $pdo->prepare("SELECT * FROM events WHERE organizer = :organizer");
                $stmt->execute(['organizer' => $_SESSION['username']]);
                $created_events = $stmt->fetchAll();

                if ($created_events): ?>
                    <?php foreach ($created_events as $event): ?>
                        <div class="card event-card">
                            <h3><?= htmlspecialchars($event['name']) ?></h3>
                            <p><?= htmlspecialchars($event['description']) ?></p>
                            <p>Status: <strong><?= htmlspecialchars($event['validation_status']) ?></strong></p>
                            <p>Participants: <?= htmlspecialchars($event['current_participants'] ?? 0) ?>
                                / <?= htmlspecialchars($event['max_participants']) ?></p>
                            <a href="edit_event.php?id=<?= $event['id'] ?>" class="btn btn__like">Modifier</a>
                            <a href="view_participants.php?event_id=<?= $event['id'] ?>" class="btn btn__like">Gérer les participants</a>
                            <form method="POST" action="favorite_event.php">
                                <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                <button type="submit" class="btn btn__like">Like</button>
                            </form>

                            <?php
                            $start_time = isset($event['start_date']) ? strtotime($event['start_date']) : null;
                            $now = time();
                            if ($event['validation_status'] === 'validé' && $event['session_started'] == 0 && $start_time && $now >= ($start_time - 1800)): ?>
                                <form method="POST" action="start_event.php">
                                    <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                    <button type="submit" class="btn start"
                                            onclick="return confirm('Etes-vous sûr de vouloir démarrer cet événement ?');">
                                        Démarrer
                                    </button>
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

        <!-- Repeated Events Section (you might want to merge this) -->
        <section class="dashboard-section">
            <h2>Mes Événements Créés</h2>
            <div class="cards-grid">
                <?php if ($created_events): ?>
                    <?php foreach ($created_events as $event): ?>
                        <div class="card event-card">
                            <h3><?= htmlspecialchars($event['name']) ?></h3>
                            <p><?= htmlspecialchars($event['description']) ?></p>
                            <p>Status: <strong><?= htmlspecialchars($event['validation_status']) ?></strong></p>
                            <?php
                            $current_time = new DateTime();
                            $start_time = new DateTime($event['start_date']);
                            $early_start = clone $start_time;
                            $early_start->modify('-30 minutes');

                            if (
                                $event['validation_status'] == 'validé' &&
                                $current_time >= $early_start &&
                                $event['session_started'] == 0
                            ): ?>
                                <form method="POST" action="start_event.php">
                                    <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                    <button type="submit" class="btn start">Démarrer</button>
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

        <!-- Pending Registration Requests -->
        <section class="dashboard-section">
            <h2>Demandes d'inscription en attente</h2>
            <div class="cards-grid">
                <?php
                $pendingStmt = $pdo->prepare("
                    SELECT ue.id AS record_id, ue.user_id, ue.event_id, ue.status,
                           u.username, e.name AS event_name
                    FROM user_events ue
                    JOIN users u ON ue.user_id = u.id
                    JOIN events e ON ue.event_id = e.id
                    WHERE ue.status = 'pending' AND e.organizer = :organizer
                ");
                $pendingStmt->execute(['organizer' => $_SESSION['username']]);
                $requests = $pendingStmt->fetchAll();

                if ($requests): ?>
                    <?php foreach ($requests as $req): ?>
                        <div class="card">
                            <p><strong><?= htmlspecialchars($req['username']) ?></strong> souhaite rejoindre
                                <strong><?= htmlspecialchars($req['event_name']) ?></strong></p>
                            <form method="POST" action="" style="display:flex; gap: 1rem; margin-top: 10px;">
                                <input type="hidden" name="record_id" value="<?= $req['record_id'] ?>">
                                <input type="hidden" name="user_id" value="<?= $req['user_id'] ?>">
                                <input type="hidden" name="event_id" value="<?= $req['event_id'] ?>">
                                <button name="action" value="accept" class="btn">Accepter</button>
                                <button name="action" value="reject" class="btn btn__like">Rejeter</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucune demande en attente.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Action Log -->
        <section class="dashboard-section">
            <h2>Historique des Actions</h2>
            <div class="cards-grid">
                <?php
                $log_stmt = $pdo->prepare("
                    SELECT sm.*, u.username 
                    FROM session_messages sm
                    JOIN events e ON sm.event_id = e.id
                    JOIN users u ON sm.user_id = u.id
                    WHERE e.organizer = :employee_id
                    ORDER BY sm.created_at DESC
                    LIMIT 10
                ");
                $log_stmt->execute(['employee_id' => $_SESSION['username']]);
                $logs = $log_stmt->fetchAll();

                if ($logs): ?>
                    <?php foreach ($logs as $log): ?>
                        <div class="card event-card">
                            <p><strong><?= htmlspecialchars($log['username']) ?> :</strong> <?= htmlspecialchars($log['message']) ?></p>
                            <small><?= htmlspecialchars($log['created_at']) ?></small>
                            <form method="POST" action="">
                                <input type="hidden" name="delete_log_id" value="<?= $log['id'] ?>">
                                <button type="submit" class="btn btn__like"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce log ?');">
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucune action récente.</p>
                <?php endif; ?>
            </div>
        </section>
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
