<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="icon" type="image/png" href="assets/images/png/circle.png">
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/fonts.css">
    <link rel="stylesheet" href="assets/css/grid.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
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

<div class="login-container">
    <div class="login-form-container">
        <h1 class="login-title">Connexion au GameHub</h1>

        <!-- Display error messages if there are any -->
        <?php if (isset($_SESSION['login_error'])): ?>
            <div class="error-message"><?= $_SESSION['login_error']; unset($_SESSION['login_error']); ?></div>
        <?php endif; ?>

        <form action="login_backend.php" method="POST" class="login-form">
            <label for="username">Nom d'utilisateur</label>
            <input type="text" name="username" id="username" required autocomplete="username" value="<?= isset($_SESSION['old_login_username']) ? $_SESSION['old_login_username'] : ''; ?>">

            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" required autocomplete="current-password">

            <div class="remember-me">
                <input type="checkbox" name="remember_me" id="remember_me">
                <label for="remember_me">Se souvenir de moi</label>
            </div>

            <button type="submit" class="login-btn" >Se connecter</button>
        </form>

        <div class="login-options">
            <p>Pas encore de compte ? <a href="register.php">S'inscrire</a></p>
            <!-- Forgot Password Link -->
            <p><a href="forgot_password.php">Mot de passe oublié ?</a></p>
        </div>
    </div>
</div>

<?php unset($_SESSION['old_login_username']); ?>
</body>
</html>
