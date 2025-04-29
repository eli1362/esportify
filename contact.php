<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact | Esportify</title>
    <link rel="icon" type="image/png" href="assets/images/png/circle.png">
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/fonts.css">
    <link rel="stylesheet" href="assets/css/grid.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />


</head>
<body>
<header class="header">
    <!-- start menu -->
    <?php include_once "header.php" ?>
    <!-- finish menu -->
</header>
<main class="main">
    <section class="contact-page">
        <div class="contact-header">
            <h1>Contactez-nous</h1>
            <p>Nous sommes là pour répondre à toutes vos questions !</p>
        </div>

        <div class="contact-content">
            <form action="send-message.php" method="POST" class="contact-form">
                <label for="name">Nom</label>
                <input type="text" name="name" required placeholder="Votre nom"/>

                <label for="email">Email</label>
                <input type="email" name="email" required placeholder="Votre email"/>

                <label for="message">Message</label>
                <textarea name="message" rows="6" required placeholder="Votre message"></textarea>

                <button type="submit"><i class="fas fa-paper-plane"></i> Envoyer</button>
            </form>

            <div class="contact-info">
                <h2>Informations</h2>
                <p><i class="fas fa-envelope"></i> contact@esportify.com</p>
                <p><i class="fas fa-phone"></i> +33 1 23 45 67 89</p>
                <div class="contact-socials">
                    <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-x-twitter"></i></a>
                </div>
            </div>
        </div>
    </section>

</main>
<footer>
    <?php
    require_once "footer.php";
    ?>
</footer>
<script src="assets/js/script.js"></script>

</body>
</html>

