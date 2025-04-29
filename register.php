<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Esportify</title>
    <link rel="icon" type="image/png" href="assets/images/png/circle.png">
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/fonts.css">
    <link rel="stylesheet" href="assets/css/grid.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
</head>
<body class="body-login">
<header class="header">
    <div class="nav">
        <div class="logo-container">
            <a href="index.php" class="app-logo">
                <img src="assets/images/svg/logo.svg" alt="logo image" class="app-logo__img">
            </a>
            <a class="logo-container__text" href="index.php"> ESPORTIFY </a>
        </div>
    </div>
</header>
<main>
<div class="login-container">
    <div class="login-form-container">
        <h1 class="login-title">Créer un compte</h1>

        <!-- Display error/success messages -->
        <?php if (isset($_SESSION['register_error'])): ?>
            <div class="error-message"><?= $_SESSION['register_error']; unset($_SESSION['register_error']); ?></div>
        <?php elseif (isset($_SESSION['register_success'])): ?>
            <div class="success-message"><?= $_SESSION['register_success']; unset($_SESSION['register_success']); ?></div>
        <?php endif; ?>

        <form action="register_backend.php" method="POST" class="login-form">
            <label for="username">Nom d'utilisateur</label>
            <input type="text" name="username" id="username" value="<?= isset($_SESSION['old_username']) ? $_SESSION['old_username'] : ''; ?>" required>

            <label for="email">Adresse e-mail</label>
            <input type="email" name="email" id="email" value="<?= isset($_SESSION['old_email']) ? $_SESSION['old_email'] : ''; ?>" required>

            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" required>

            <label for="confirm_password">Confirmer le mot de passe</label>
            <input type="password" name="confirm_password" id="confirm_password" required>

            <!-- Role selection (only visible for admin users) -->
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <label for="role">Sélectionner le rôle</label>
                <select name="role" id="role" required>
                    <option value="user">Utilisateur</option>
                    <option value="employee">Employé</option>
                    <option value="admin">Administrateur</option>
                </select>
            <?php else: ?>
                <input type="hidden" name="role" value="user"> <!-- Default to user for non-admins -->
            <?php endif; ?>

            <button type="submit" class="login-btn">Créer un compte</button>
        </form>

        <div class="login-options">
            <p>Déjà un compte? <a href="login.php">Se connecter</a></p>
            <a href="index.php" style="margin-bottom: 5rem">Accueil ...</a>
        </div>
    </div>
</div>
<?php unset($_SESSION['old_username'], $_SESSION['old_email']); ?>
</main>
<footer>
    <?php
    require_once "footer.php";
    ?>
</footer>
</body>
</html>
