<?php
session_start();
require_once 'backend/config.php';
global $pdo;

// Require login
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Flash message display
if (isset($_SESSION['message'])) {
    echo '<div class="flash-message">' . htmlspecialchars($_SESSION['message']) . '</div>';
    unset($_SESSION['message']);
}

// Initialize variables
$name = $description = $start_date = $end_date = "";
$max_participants = 0;
$name_err = $description_err = $start_date_err = $end_date_err = $max_participants_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"] ?? '');
    $description = trim($_POST["description"] ?? '');
    $start_date = trim($_POST["start_date"] ?? '');
    $end_date = trim($_POST["end_date"] ?? '');
    $max_participants = (int) ($_POST["max_participants"] ?? 0);

    // Validation
    if (empty($name)) $name_err = "Nom de l'événement est requis.";
    if (empty($description)) $description_err = "La description est requise.";
    if (empty($start_date)) $start_date_err = "Date de début requise.";
    if (empty($end_date)) $end_date_err = "Date de fin requise.";
    if ($max_participants <= 0) $max_participants_err = "Veuillez indiquer un nombre valide de participants.";

    if (!empty($start_date) && !empty($end_date)) {
        if (strtotime($end_date) <= strtotime($start_date)) {
            $end_date_err = "La date de fin doit être après la date de début.";
        }
    }

    // Insert if no errors
    if (empty($name_err) && empty($description_err) && empty($start_date_err) && empty($end_date_err) && empty($max_participants_err)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO events 
                (name, description, start_date, end_date, max_participants, is_validated, validation_status, organizer) 
                VALUES 
                (:name, :description, :start_date, :end_date, :max_participants, 0, 'en_attente', :organizer)");

            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":description", $description);
            $stmt->bindParam(":start_date", $start_date);
            $stmt->bindParam(":end_date", $end_date);
            $stmt->bindParam(":max_participants", $max_participants);
            $stmt->bindParam(":organizer", $_SESSION['username']);

            $stmt->execute();

            // Set a success message in session
            $_SESSION['message'] = "Votre événement a été créé avec succès et est en attente de validation.";

            // Redirect based on the user's role
            if ($_SESSION['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($_SESSION['role'] === 'employee') {
                header("Location: dashboard_organizer.php");
            } else {
                header("Location: dashboard_user.php");
            }
            exit;

        } catch (PDOException $e) {
            echo "Erreur SQL: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Événement</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container event-dashboard">
    <h1>Créer un nouvel événement</h1>

    <form method="POST" action="create_event.php">
        <div class="form-group">
            <label for="name">Nom de l'événement:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>
            <span class="error"><?= $name_err ?></span>
        </div>

        <div class="form-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" required><?= htmlspecialchars($description) ?></textarea>
            <span class="error"><?= $description_err ?></span>
        </div>

        <div class="form-group">
            <label for="start_date">Date de début:</label>
            <input type="datetime-local" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" required>
            <span class="error"><?= $start_date_err ?></span>
        </div>

        <div class="form-group">
            <label for="end_date">Date de fin:</label>
            <input type="datetime-local" id="end_date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" required>
            <span class="error"><?= $end_date_err ?></span>
        </div>

        <div class="form-group">
            <label for="max_participants">Nombre maximal de participants:</label>
            <input type="number" id="max_participants" name="max_participants" min="1" value="<?= htmlspecialchars($max_participants) ?>" required>
            <span class="error"><?= $max_participants_err ?></span>
        </div>

        <div class="form-button-wrapper">
            <button type="submit" class="login-btn btn__events">Créer l'événement</button>
        </div>
    </form>
</div>

</body>
</html>
