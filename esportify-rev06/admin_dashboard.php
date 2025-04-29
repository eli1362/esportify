<?php
global $pdo;
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include 'header.php';
include 'backend/config.php';
// Total number of users
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$userCount = $stmt->fetchColumn();

// Total events
$stmt = $pdo->query("SELECT COUNT(*) FROM events");
$eventCount = $stmt->fetchColumn();

// Validated events
$stmt = $pdo->prepare("SELECT COUNT(*) FROM events WHERE validation_status = 'validé'");
$stmt->execute();
$approvedEvents = $stmt->fetchColumn();

// Pending events
$stmt = $pdo->prepare("SELECT COUNT(*) FROM events WHERE validation_status = 'en_attente'");
$stmt->execute();
$pendingEvents = $stmt->fetchColumn();

// Ignored events (optional)
$stmt = $pdo->prepare("SELECT COUNT(*) FROM events WHERE validation_status = 'ignoré'");
$stmt->execute();
$ignoredEvents = $stmt->fetchColumn();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="icon" type="image/png" href="assets/images/png/circle.png">
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/fonts.css">
    <link rel="stylesheet" href="assets/css/grid.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<header class="header">
    <!-- start menu -->
    <?php include_once "header.php" ?>
    <!-- finish menu -->
</header>
<main class="main">

    <div class="admin-dashboard">
        <h1>Bienvenue, Admin <?= htmlspecialchars($_SESSION['username']) ?> 👋</h1>
        <p class="welcome-message">Ici vous pouvez administrer l'ensemble du site !</p>

        <!-- Dashboard Stats Section -->
        <section class="dashboard-stats ">
            <div class="stat-card border-gradient">
                <i class="fas fa-users"></i>
                <h3>Utilisateurs</h3>
                <p class="stat-value"><?= $userCount ?></p>
            </div>
            <div class="stat-card border-gradient">
                <i class="fas fa-calendar-alt"></i>
                <h3>Événements</h3>
                <p class="stat-value"><?= $eventCount ?></p>
            </div>
            <div class="stat-card border-gradient">
                <i class="fas fa-clock"></i>
                <h3>En Attente</h3>
                <p class="stat-value"><?= $pendingEvents ?></p>
            </div>
            <div class="stat-card border-gradient">
                <i class="fas fa-check"></i>
                <h3>Validés</h3>
                <p class="stat-value"><?= $approvedEvents ?></p>
            </div>
            <div class="stat-card border-gradient">
                <i class="fas fa-ban"></i>
                <h3>Ignorés</h3>
                <p class="stat-value"><?= $ignoredEvents ?></p>
            </div>
        </section>

        <!-- Additional content or actions can go here -->
    </div>

</main>
<script src="assets/js/script.js"></script>
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
</body>
</html>
