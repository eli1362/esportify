<?php
require_once 'backend/config.php';
global $pdo;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and has an admin role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Fetch current settings from the database
$sql = "SELECT * FROM site_settings WHERE id = 1";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

// If no settings are found, use default values
if (!$settings) {
    $settings = [
        'site_title' => 'Esportify',
        'site_description' => 'Votre plateforme e-sport.',
        'site_logo' => 'default_logo.png',
        'admin_email' => 'admin@esportify.com',
        'timezone' => 'UTC',
        'footer_text' => '© 2025 Esportify. Tous droits réservés.',
        'site_language' => 'fr',
        'theme_color' => '#00FFFF', // Default to your Blue-Neon
        'facebook_url' => 'https://facebook.com/esportify',
        'twitter_url' => 'https://twitter.com/esportify'
    ];
}

// Update settings when form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['site_title'];
    $description = $_POST['site_description'];
    $logo = $_POST['site_logo'];
    $admin_email = $_POST['admin_email'];
    $timezone = $_POST['timezone'];
    $footer_text = $_POST['footer_text'];
    $site_language = $_POST['site_language'];
    $theme_color = $_POST['theme_color'];
    $facebook_url = $_POST['facebook_url'];
    $twitter_url = $_POST['twitter_url'];

    $update_sql = "UPDATE site_settings SET 
                    site_title = :title, 
                    site_description = :description, 
                    site_logo = :logo, 
                    admin_email = :admin_email, 
                    timezone = :timezone, 
                    footer_text = :footer_text, 
                    site_language = :site_language, 
                    theme_color = :theme_color, 
                    facebook_url = :facebook_url, 
                    twitter_url = :twitter_url 
                    WHERE id = 1";

    $update_stmt = $pdo->prepare($update_sql);
    $update_stmt->execute([
        ':title' => $title,
        ':description' => $description,
        ':logo' => $logo,
        ':admin_email' => $admin_email,
        ':timezone' => $timezone,
        ':footer_text' => $footer_text,
        ':site_language' => $site_language,
        ':theme_color' => $theme_color,
        ':facebook_url' => $facebook_url,
        ':twitter_url' => $twitter_url,
    ]);

    // Save the theme color in the session
    $_SESSION['theme_color'] = $theme_color;

    header('Location: site-settings.php');
    exit();
}

// Get theme color from session or DB
$theme_color = isset($_SESSION['theme_color']) ? $_SESSION['theme_color'] : $settings['theme_color'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres du Site</title>
    <link rel="icon" type="image/png" href="assets/images/png/circle.png">
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/fonts.css">
    <link rel="stylesheet" href="assets/css/grid.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <!-- Custom Dynamic Color -->
    <style>
        :root {
            --Blue-Neon: #00FFFF;
            --Violet: #1A1A2E;
            --Magenta: #FF00FF;
            --Orange: #FF7F11;
            --Gris: #B0B0B0;
            --white: #FFFFFF;
            --black-text: #333333;
            --blue-text: #082e5c;
            --Green-Blue: #00FFB3;
            --theme-color: <?= htmlspecialchars($theme_color) ?>;
        }
    </style>
</head>

<body>

<header>
<?php include 'header.php'; ?>
</header>
<main>
<section class="settings-section" style="margin-top: 6rem;">
    <div class="container">
        <h2 style="color: var(--theme-color);">Paramètres du Site</h2>

        <form action="site-settings.php" method="POST" class="settings-form" style="margin-top:2rem;">
            <label for="site_title">Titre du site:</label>
            <input type="text" id="site_title" name="site_title" value="<?= htmlspecialchars($settings['site_title']) ?>" required>

            <label for="site_description">Description du site:</label>
            <textarea id="site_description" name="site_description" required><?= htmlspecialchars($settings['site_description']) ?></textarea>

            <label for="site_logo">Logo du site (URL ou chemin):</label>
            <input type="text" id="site_logo" name="site_logo" value="<?= htmlspecialchars($settings['site_logo']) ?>">

            <label for="admin_email">Email de l'administrateur:</label>
            <input type="email" id="admin_email" name="admin_email" value="<?= htmlspecialchars($settings['admin_email']) ?>" required>

            <label for="timezone">Fuseau horaire:</label>
            <input type="text" id="timezone" name="timezone" value="<?= htmlspecialchars($settings['timezone']) ?>" required>

            <label for="footer_text">Texte du pied de page:</label>
            <textarea id="footer_text" name="footer_text"><?= htmlspecialchars($settings['footer_text']) ?></textarea>

            <label for="theme_color">Couleur du thème:</label>
            <input type="color" id="theme_color" name="theme_color" value="<?= htmlspecialchars($settings['theme_color']) ?>" required>

            <label for="facebook_url">URL Facebook:</label>
            <input type="url" id="facebook_url" name="facebook_url" value="<?= htmlspecialchars($settings['facebook_url']) ?>">

            <label for="twitter_url">URL Twitter:</label>
            <input type="url" id="twitter_url" name="twitter_url" value="<?= htmlspecialchars($settings['twitter_url']) ?>">

            <button type="submit" class="btn" style="border:1px solid var(--theme-color); background-color: transparent; color: var(--theme-color); margin-top: 2rem;">
                Mettre à jour les paramètres
            </button>
        </form>
    </div>
</section>
</main>
<footer>
    <?php
    require_once "footer.php"
    ?>
</footer>
<script src="assets/js/script.js"></script>
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
</body>
</html>
