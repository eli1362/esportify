<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header('Location: login.php');
    exit();
}

require_once 'backend/config.php';
global $pdo;

if (!isset($_GET['id'])) {
    die("ID de l'événement manquant.");
}

$event_id = (int) $_GET['id'];

// Fetch event details
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id AND organizer = :organizer");
$stmt->execute(['id' => $event_id, 'organizer' => $_SESSION['username']]);
$event = $stmt->fetch();

if (!$event) {
    die("Événement introuvable ou vous n'avez pas la permission de le modifier.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $max_participants = (int) ($_POST['max_participants'] ?? 0);
    $start_date = $_POST['start_date'] ?? '';  // Changed to start_date instead of start_time

    $update = $pdo->prepare("UPDATE events SET name = :name, description = :description, max_participants = :max_participants, start_date = :start_date WHERE id = :id AND organizer = :organizer");
    $update->execute([
        'name' => $name,
        'description' => $description,
        'max_participants' => $max_participants,
        'start_date' => $start_date,  // Changed to start_date
        'id' => $event_id,
        'organizer' => $_SESSION['username']
    ]);

    echo "<p class='success-message' style='margin-top: 3rem'>Événement mis à jour avec succès.</p>";
    // Optionally redirect after save
    // header("Location: dashboard_organizer.php"); exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier l'événement</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">

    <h1 style="margin: 3rem 0">Modifier l'événement</h1>

    <form method="POST">
        <label>Nom de l'événement:</label><br>
        <input type="text" name="name" value="<?= htmlspecialchars($event['name']) ?>" required><br><br>

        <label>Description:</label><br>
        <textarea name="description" required><?= htmlspecialchars($event['description']) ?></textarea><br><br>

        <label>Nombre maximum de participants:</label><br>
        <input type="number" name="max_participants" value="<?= (int)$event['max_participants'] ?>" required><br><br>

        <label>Date de début:</label><br>
        <?php
        // No more start_time, just start_date (modified from previous version)
        $formatted_start_date = '';
        if (!empty($event['start_date']) && strtotime($event['start_date']) !== false) {
            $formatted_start_date = date('Y-m-d', strtotime($event['start_date']));
        }
        ?>
        <input type="date" id="start_date" name="start_date" value="<?= $formatted_start_date ?>" required>
        <br><br>

        <button type="submit" class="btn btn__like" style="min-height: 31.6px">Enregistrer</button>
    </form>

    <a href="dashboard_organizer.php" class="btn btn__like" style="margin-bottom: 10rem">Retour</a>
</div>
</body>
</html>
