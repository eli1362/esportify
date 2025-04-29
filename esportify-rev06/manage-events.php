<?php
global $pdo;
require 'backend/config.php';
session_start();

// Redirect if not admin or employee
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'employee')) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gérer les événements</title>
    <link rel="icon" type="image/png" href="assets/images/png/circle.png">
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/fonts.css">
    <link rel="stylesheet" href="assets/css/grid.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .flash-message {
            margin-top: 8rem;
            margin-bottom: -5rem;
            color: var(--Green-Blue);
            padding: 10px;
            text-align: center;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .events-table {
            min-width: 700px;
            width: 100%;
            border-collapse: collapse;
        }

        .events__wrapper {
            padding: 1rem;
            box-sizing: border-box;
        }
    </style>
</head>
<body>

<header class="header">
    <?php include 'header.php'; ?>
</header>

<main>
<div class="container container__events">


        <div class="events__wrapper">

            <!-- Flash Message -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="flash-message"><?= htmlspecialchars($_SESSION['message']) ?></div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <!-- Events List Section -->
            <div class="events-list">
                <h1 class="evenements__title">Gestion des Événements</h1>

                <?php
                $where = "WHERE 1";

                if (!empty($_GET['event_date'])) {
                    $date = $_GET['event_date'];
                    $where .= " AND e.start_date = '$date'";
                }
                if (!empty($_GET['min_players'])) {
                    $min_players = intval($_GET['min_players']);
                    $where .= " AND e.max_participants >= $min_players";
                }
                if (!empty($_GET['start_date_range'])) {
                    $start_date_range = $_GET['start_date_range'];
                    $where .= " AND e.start_date >= '$start_date_range'";
                }
                if (!empty($_GET['end_date_range'])) {
                    $end_date_range = $_GET['end_date_range'];
                    $where .= " AND e.end_date <= '$end_date_range'";
                }
                if (!empty($_GET['organizer_status'])) {
                    $organizer_status = $_GET['organizer_status'];
                    $where .= " AND e.validation_status = '$organizer_status'";
                }

                $query = "SELECT * FROM events e $where ORDER BY e.start_date DESC";

                try {
                    $result = $pdo->query($query);

                    if ($result->rowCount() > 0): ?>
                        <div class="table-responsive">
                            <table class="events-table">
                                <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Date Début</th>
                                    <th>Date Fin</th>
                                    <th>Participants max</th>
                                    <th>Validation</th>
                                    <th>Session Démarrée</th>
                                    <th>Détails</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php while ($event = $result->fetch(PDO::FETCH_ASSOC)): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($event['name']) ?></td>
                                        <td><?= htmlspecialchars($event['start_date']) ?></td>
                                        <td><?= htmlspecialchars($event['end_date']) ?></td>
                                        <td><?= htmlspecialchars($event['max_participants']) ?></td>
                                        <td>
                                            <?php if ($event['validation_status'] == 'en_attente'): ?>
                                                <a href="validate_event.php?id=<?= $event['id'] ?>&action=valider" class="btn green">
                                                    <i class="fas fa-check-circle"></i> Valider
                                                </a>
                                                <a href="validate_event.php?id=<?= $event['id'] ?>&action=ignorer" class="btn red">
                                                    <i class="fas fa-times-circle"></i> Ignoré
                                                </a>
                                            <?php elseif ($event['validation_status'] == 'validé'): ?>
                                                <span><i class="fas fa-check-circle"></i> Validé</span>
                                            <?php elseif ($event['validation_status'] == 'ignoré'): ?>
                                                <span><i class="fas fa-times-circle"></i> Ignoré</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($event['session_started'] == 0): ?>
                                                <a href="toggle-session.php?id=<?= $event['id'] ?>&status=1" class="btn green">
                                                    <i class="fas fa-play-circle"></i> Démarrer la session
                                                </a>
                                            <?php else: ?>
                                                <span><i class="fas fa-pause-circle"></i> Session démarrée</span>
                                                <a href="toggle-session.php?id=<?= $event['id'] ?>&status=0" class="btn red">
                                                    <i class="fas fa-stop-circle"></i> Arrêter la session
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="get-event-details.php?id=<?= $event['id'] ?>" class="btn green">
                                                Voir détails
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p style="color: var(--white)">Aucun événement trouvé.</p>
                    <?php endif;
                } catch (PDOException $e) {
                    echo "<p style='color:red;'>Erreur: " . $e->getMessage() . "</p>";
                }
                ?>
            </div>

            <!-- Filter Form -->
            <div class="evenements__wrapper">
                <form method="GET" action="manage-events.php" class="filter-form">
                    <label>Date Début:</label>
                    <input type="date" name="event_date" value="<?= $_GET['event_date'] ?? '' ?>">

                    <label>Joueurs minimum:</label>
                    <input type="number" name="min_players" min="1" value="<?= $_GET['min_players'] ?? '' ?>">

                    <label>Date Début (Plage):</label>
                    <input type="date" name="start_date_range" value="<?= $_GET['start_date_range'] ?? '' ?>">

                    <label>Date Fin (Plage):</label>
                    <input type="date" name="end_date_range" value="<?= $_GET['end_date_range'] ?? '' ?>">

                    <label>Statut de l'Organisateur:</label>
                    <div class="radio-buttons">
                        <label><input type="radio" name="organizer_status"
                                      value="en_attente" <?= (($_GET['organizer_status'] ?? '') === 'en_attente') ? 'checked' : '' ?>> En attente</label>
                        <label><input type="radio" name="organizer_status"
                                      value="validé" <?= (($_GET['organizer_status'] ?? '') === 'validé') ? 'checked' : '' ?>> Validé</label>
                        <label><input type="radio" name="organizer_status"
                                      value="ignoré" <?= (($_GET['organizer_status'] ?? '') === 'ignoré') ? 'checked' : '' ?>> Ignoré</label>
                    </div>

                    <button type="submit" class="btn evenements__btn">Filtrer</button>
                </form>
            </div>

        </div>
</div>
</main>

<script src="assets/js/script.js"></script>
</body>
</html>
