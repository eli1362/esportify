<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

global $pdo;
session_start();
require_once 'backend/config.php'; // Assuming the database connection is here
require 'vendor/autoload.php'; // Include PHPMailer autoloader

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['reset_error'] = "L'adresse e-mail fournie est invalide.";
        header("Location: forgot_password.php");
        exit;
    }

    // Check if the email exists in the database
    $sql = "SELECT id, username FROM users WHERE email = :email LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Generate a password reset token
        $token = bin2hex(random_bytes(16));  // Generate a 32-character random token
        $reset_expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));  // Token expiry set for 1 hour

        // Store the token and expiration time in the database
        $sql = "UPDATE users SET password_reset_token = :token, password_reset_expiration = :expiration WHERE id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'token' => $token,
            'expiration' => $reset_expiration,
            'user_id' => $user['id']
        ]);

        // Use PHPMailer to send the email
        $reset_link = "http://localhost:63342/esportify-rev03/reset_password.php?token=$token"; // Your local server
        $subject = "Réinitialisation du mot de passe";
        $message = "Bonjour {$user['username']},\n\nCliquez sur le lien suivant pour réinitialiser votre mot de passe :\n$reset_link\n\nSi vous n'avez pas demandé cette réinitialisation, ignorez ce message.";

        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'khosravielmira@gmail.com';
            $mail->Password = '234680';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('khosravielmira@gmail.com', 'Esportify');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = nl2br($message);

            // Send email
            $mail->send();
            $_SESSION['reset_success'] = "Un lien de réinitialisation a été envoyé à votre adresse e-mail.";
        } catch (Exception $e) {
            $_SESSION['reset_error'] = "Erreur lors de l'envoi du lien de réinitialisation. Veuillez réessayer. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $_SESSION['reset_error'] = "Aucun utilisateur trouvé avec cette adresse e-mail.";
    }

    header("Location: forgot_password.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de Passe Oublié</title>
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

<div class="login-container">
    <div class="login-form-container">
        <h1 class="login-title">Mot de Passe Oublié</h1>

        <!-- Display success/error messages -->
        <?php if (isset($_SESSION['reset_error'])): ?>
            <div class="error-message"><?= $_SESSION['reset_error']; unset($_SESSION['reset_error']); ?></div>
        <?php elseif (isset($_SESSION['reset_success'])): ?>
            <div class="success-message"><?= $_SESSION['reset_success']; unset($_SESSION['reset_success']); ?></div>
        <?php endif; ?>

        <form action="forgot_password.php" method="POST" class="login-form">
            <label for="email">Adresse e-mail</label>
            <input type="email" name="email" id="email" required>

            <button type="submit" class="login-btn">Envoyer le lien de réinitialisation</button>
        </form>

        <div class="login-options">
            <p>Retour à la <a href="login.php">connexion</a></p>
        </div>
    </div>
</div>
</body>
</html>
