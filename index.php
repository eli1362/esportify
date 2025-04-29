<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esportify</title>
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
    <section class="container">

        <div class="main-welcome__wrapper">
            <!-- Welcome title -->
            <h1 class="main-welcome__title animated-title">
                <span>B</span><span>i</span><span>e</span><span>n</span><span>v</span><span>e</span><span>n</span><span>u</span><span>e</span>
                <span> </span>
                <span>s</span><span>u</span><span>r</span>
                <span> </span>
                <span>E</span><span>s</span><span>p</span><span>o</span><span>r</span><span>t</span><span>i</span><span>f</span><span
                        class="letter">y</span>
            </h1>

            <p class="text">
                Esportify spécialise dans l'organisation de compétitions de jeux vidéo.
            </p>

            <!-- "Plus" button to show company description -->
            <button onclick="toggleDescription()" id="toggleBtn">À propos...</button>

            <!-- About Company Description -->

            <div id="companyDescription" style="display: none;">
                <section class="about-section" id="presentation">
                    <div class="about-container">
                        <header class="about-header">
                            <h2 class="about-title">Présentation de l’entreprise</h2>
                            <meta itemprop="description"
                                  content="Esportify est une entreprise dédiée à l'organisation de compétitions e-sport et d'événements gaming en France.">
                        </header>

                        <article class="about-content">
                            <p>
                                <strong>Esportify</strong> est une entreprise française innovante, spécialisée dans
                                <strong>l’organisation de compétitions e-sport</strong> et d’<strong>événements
                                    gaming</strong>.
                                Notre mission est de fédérer une communauté de gamers passionnés autour d'expériences
                                immersives et de tournois compétitifs de haut niveau.
                            </p>

                            <p>
                                Nous mettons en place des plateformes performantes, diffusons les matchs en direct, et
                                accompagnons les joueurs pour faire émerger les talents de demain.
                                Que vous soyez amateur ou professionnel, <strong>Esportify</strong> vous propose une
                                scène où briller.
                            </p>

                            <ul class="about-list">
                                <li> Tournois e-sport en ligne et présentiels</li>
                                <li> Diffusion live, streaming & production vidéo</li>
                                <li> Événements communautaires et LAN parties</li>
                                <li> Coaching et accompagnement des joueurs</li>
                            </ul>

                            <p class="about-slogan">
                                <em>“Rejoignez l’arène. Vivez l’expérience Esportify.”</em>
                            </p>

                            <p>
                                <strong>Esportify</strong> spécialise dans l'organisation de compétitions de jeux vidéo.
                                Nous mettons en place des tournois en ligne, des événements en direct, ainsi que des
                                plateformes de coaching pour aider les joueurs à atteindre leur plein potentiel. Nous
                                croyons que chaque joueur mérite une chance de briller, et <strong>Esportify</strong>
                                est là pour offrir cette opportunité.
                            </p>
                        </article>
                    </div>
                </section>
            </div>


            <!-- Slider -->
            <div class="slider-container">
                <div class="slides">
                    <div class="slide">
                        <img src="assets/images/image/1.jpg" alt="Image 1">
                    </div>
                    <div class="slide">
                        <img src="assets/images/image/2.jpg" alt="Image 1">
                    </div>
                    <div class="slide">
                        <img src="assets/images/image/3.jpg" alt="Image 3">
                    </div>
                    <div class="slide">
                        <img src="assets/images/image/4.jpg" alt="Image 4">
                    </div>
                    <div class="slide">
                        <img src="assets/images/image/5.jpg" alt="Image 5">
                    </div>
                </div>
                <div class="dots-container">
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                </div>
            </div>

        </div>
        <!-- Start Events-section -->
        <?php include_once "events-section.php" ?>
        <!-- End Events-section -->

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
