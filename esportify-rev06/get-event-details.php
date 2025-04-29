<?php
global $pdo;
require 'backend/config.php'; // ✅ Make sure this comes first

// Start session if needed
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if no event ID is provided
if (!isset($_GET['id'])) {
    die("Missing event ID");
}

$eventId = intval($_GET['id']);

// Fetch the event details
try {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id");
    $stmt->execute([':id' => $eventId]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        die("Event not found.");
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Handle the message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message']) && !empty($_POST['message'])) {
    $message = htmlspecialchars($_POST['message']);
    $userId = $_SESSION['user_id']; // Assuming the user is logged in

    try {
        $stmt = $pdo->prepare("INSERT INTO event_discussions (event_id, user_id, message) VALUES (:event_id, :user_id, :message)");
        $stmt->execute([
            ':event_id' => $eventId,
            ':user_id' => $userId,
            ':message' => $message
        ]);
    } catch (PDOException $e) {
        echo '<p style="color: red;">Erreur : ' . $e->getMessage() . '</p>';
    }
}

// Pagination settings
$messagesPerPage = 5;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $messagesPerPage;

// Fetch messages for this event with pagination
try {
    $stmt = $pdo->prepare("SELECT u.username, m.message, m.timestamp, m.id AS message_id FROM event_discussions m JOIN users u ON m.user_id = u.id WHERE m.event_id = :event_id ORDER BY m.timestamp ASC LIMIT :limit OFFSET :offset");
    $stmt->bindParam(':event_id', $eventId, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $messagesPerPage, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total number of messages for pagination
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM event_discussions WHERE event_id = :event_id");
    $stmt->execute([':event_id' => $eventId]);
    $totalMessages = $stmt->fetchColumn();
    $totalPages = ceil($totalMessages / $messagesPerPage);
} catch (PDOException $e) {
    echo '<p style="color: red;">Erreur : ' . $e->getMessage() . '</p>';
}

// Handle message deletion (only for admin)
if (isset($_GET['delete_message']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $messageId = intval($_GET['delete_message']);

    try {
        $stmt = $pdo->prepare("DELETE FROM event_discussions WHERE id = :message_id AND event_id = :event_id");
        $stmt->execute([':message_id' => $messageId, ':event_id' => $eventId]);
        header("Location: get-event-details.php?id=$eventId"); // Refresh the page after deletion
    } catch (PDOException $e) {
        echo '<p style="color: red;">Erreur lors de la suppression du message : ' . $e->getMessage() . '</p>';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails de l'événement</title>
    <link rel="icon" type="image/png" href="assets/images/png/circle.png">
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/fonts.css">
    <link rel="stylesheet" href="assets/css/grid.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
</head>
<body>

<header class="header">
    <?php include 'header.php'; ?>
</header>

<main class="main">
    <div class="container">
        <h1 class="evenements__title detail">Détails de l'événement</h1>

        <div class="event-detail-box">
            <p><strong>Nom :</strong> <?= htmlspecialchars($event['name']) ?></p>
            <p><strong>Description :</strong> <?= nl2br(htmlspecialchars($event['description'])) ?></p>
            <p><strong>Date de début :</strong> <?= htmlspecialchars($event['start_date']) ?></p>
            <p><strong>Date de fin :</strong> <?= htmlspecialchars($event['end_date']) ?></p>
            <p><strong>Participants maximum :</strong> <?= htmlspecialchars($event['max_participants']) ?></p>
            <p><strong>Événement validé :</strong> <?= $event['is_validated'] ? 'Oui' : 'Non' ?></p>
        </div>

        <div class="event-discussion">
            <h2 class="about-title margin-zero" style="font-size: 2rem">Discussion sur l'événement</h2>

            <!-- Display previous messages -->
            <div id="discussion-messages">
                <?php foreach ($messages as $message): ?>
                    <div class="message">
                        <strong><?= htmlspecialchars($message['username']) ?>:</strong>
                        <p><?= nl2br(htmlspecialchars($message['message'])) ?></p>
                        <small><?= $message['timestamp'] ?></small>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <a href="get-event-details.php?id=<?= $eventId ?>&delete_message=<?= $message['message_id'] ?>" class="btn red">Supprimer</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Input form for new message -->
            <form method="POST" action="">
                <textarea name="message" rows="4" placeholder="Entrez votre message..." required></textarea>
                <button type="submit" style="color: black">Envoyer</button>
            </form>

            <!-- Pagination -->
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="get-event-details.php?id=<?= $eventId ?>&page=<?= $page - 1 ?>" class="btn">Précédent</a>
                <?php endif; ?>
                <span>Page <?= $page ?> sur <?= $totalPages ?></span>
                <?php if ($page < $totalPages): ?>
                    <a href="get-event-details.php?id=<?= $eventId ?>&page=<?= $page + 1 ?>" class="btn">Suivant</a>
                <?php endif; ?>
            </div>
        </div>

        <a href="manage-events.php" class="btn">⬅ Retour à la gestion</a>
    </div>
</main>

<script src="assets/js/script.js"></script>

</body>
</html>
