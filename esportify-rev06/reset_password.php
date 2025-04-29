<?php
global $pdo;
session_start();
require_once 'backend/config.php';

// Get the token from URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if token exists and is not expired
    $sql = "SELECT id, username, password_reset_expiration FROM users WHERE password_reset_token = :token LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Check if the token is expired
        if (strtotime($user['password_reset_expiration']) < time()) {
            $_SESSION['reset_error'] = "Le lien de réinitialisation a expiré.";
            header("Location: forgot_password.php");
            exit;
        }

        // Reset the password when form is submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            // Validate passwords match
            if ($new_password !== $confirm_password) {
                $_SESSION['reset_error'] = "Les mots de passe ne correspondent pas.";
                header("Location: reset_password.php?token=$token");
                exit;
            }

            // Hash the new password
            $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

            // Update the password in the database
            $sql = "UPDATE users SET password = :password, password_reset_token = NULL, password_reset_expiration = NULL WHERE id = :user_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'password' => $hashedPassword,
                'user_id' => $user['id']
            ]);

            $_SESSION['reset_success'] = "Votre mot de passe a été réinitialisé avec succès. Vous pouvez vous connecter.";
            header("Location: login.php");
            exit;
        }
    } else {
        $_SESSION['reset_error'] = "Lien de réinitialisation invalide.";
        header("Location: forgot_password.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le mot de passe</title>
</head>
<body>
<h1>Réinitialiser le mot de passe</h1>

<?php if (isset($_SESSION['reset_error'])): ?>
    <div class="error-message"><?= $_SESSION['reset_error']; unset($_SESSION['reset_error']); ?></div>
<?php elseif (isset($_SESSION['reset_success'])): ?>
    <div class="success-message"><?= $_SESSION['reset_success']; unset($_SESSION['reset_success']); ?></div>
<?php endif; ?>

<form action="reset_password.php?token=<?= $token ?>" method="POST">
    <label for="password">Nouveau mot de passe</label>
    <input type="password" name="password" id="password" required>

    <label for="confirm_password">Confirmer le mot de passe</label>
    <input type="password" name="confirm_password" id="confirm_password" required>

    <button type="submit">Réinitialiser le mot de passe</button>
</form>

<a href="login.php">Retour à la connexion</a>
</body>
</html>
