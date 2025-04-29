<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_logged_in = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Font Awesome (RECOMMENDED CDN) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<div class="nav">
    <div class="logo-container">
        <a href="index.php" class="app-logo">
            <img src="assets/images/svg/logo.svg" alt="logo image" class="app-logo__img">
        </a>
        <a class="logo-container__text" href="index.php"> ESPORTIFY </a>
    </div>

    <div class="menu-icon__wrapper">
        <ul class="menu">
            <li class="menu__item">
                <a href="index.php" class="menu__link <?= $current_page === 'index.php' ? 'menu__link--active' : '' ?>">
                    <i class="fa-solid fa-house"></i> Accueil
                </a>
            </li>
            <li class="menu__item">
                <a href="all-events.php" class="menu__link <?= $current_page === 'events.php' ? 'menu__link--active' : '' ?>">
                    <i class="fa-solid fa-calendar-days"></i> Tous événements
                </a>
            </li>
            <li class="menu__item">
                <a href="contact.php" class="menu__link <?= $current_page === 'contact.php' ? 'menu__link--active' : '' ?>">
                    <i class="fa-solid fa-envelope"></i> Contact
                </a>
            </li>

            <?php if (!$is_logged_in): ?>
                <li class="menu__item">
                    <a href="login.php" class="menu__link <?= $current_page === 'login.php' ? 'menu__link--active' : '' ?>">
                        <i class="fa-solid fa-right-to-bracket"></i> Connexion
                    </a>
                </li>
            <?php else: ?>
                <li class="menu__item">
                    <div class="icon-dropdown">
                        <div class="icon-dropdown-icon__container" id="toggleDropdown">
                            <i class="fa-solid fa-user icon-dropdown__icon"></i>
                            <i class="fa-solid fa-plus icon-dropdown__icon--plus" id="toggleIcon"></i>
                        </div>
                        <div class="dropdown-menu" id="userDropdown">
                            <ul>
                                <li><a href="dashboard_user.php"><i class="fa-solid fa-user"></i> Mon Profil</a></li>
                                <li><a href="my-events.php"><i class="fa-solid fa-calendar-check"></i> Mes Événements</a></li>
                                <li><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a></li>

                                <?php if ($user_role === 'admin'): ?>
                                    <li><a href="admin_dashboard.php"><i class="fa-solid fa-user"></i> Mon Profil</a></li>
                                    <li><a href="admin-manage-users.php"><i class="fa-solid fa-users-cog"></i> Gérer les utilisateurs</a></li>
                                    <li><a href="manage-events.php"><i class="fa-solid fa-calendar"></i> Gérer les événements</a></li>
                                    <li><a href="site-settings.php"><i class="fa-solid fa-gear"></i> Paramètres du site</a></li>
                                    <li><a href="create_event.php"><i class="fa-solid fa-plus-circle"></i> Ajouter un événement</a></li>
                                <?php endif; ?>

                                <?php if ($user_role === 'employee'): ?>
                                    <li><a href="employee_dashboard.php"><i class="fa-solid fa-briefcase"></i> Tableau de Bord Employé</a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </li>
            <?php endif; ?>
        </ul>

        <!-- Mobile Navigation -->
        <div class="nav-menu">
            <ul class="mobile-menu">
                <li><a href="index.php" class="mobile-menu__link <?= $current_page === 'index.php' ? 'mobile-menu__link--active' : '' ?>">Accueil</a></li>
                <li><a href="events.php" class="mobile-menu__link <?= $current_page === 'events.php' ? 'mobile-menu__link--active' : '' ?>">Tous événements</a></li>
                <?php if (!$is_logged_in): ?>
                    <li><a href="login.php" class="mobile-menu__link <?= $current_page === 'login.php' ? 'mobile-menu__link--active' : '' ?>">Connexion</a></li>
                <?php else: ?>
                    <li><a href="dashboard_user.php" class="mobile-menu__link <?= $current_page === 'my-events.php' ? 'mobile-menu__link--active' : '' ?>">Mon Profile</a></li>
                    <li><a href="my-events.php" class="mobile-menu__link <?= $current_page === 'my-events.php' ? 'mobile-menu__link--active' : '' ?>">Mes Événements</a></li>
                    <li><a href="logout.php" class="mobile-menu__link <?= $current_page === 'logout.php' ? 'mobile-menu__link--active' : '' ?>">Déconnexion</a></li>

                    <?php if ($user_role === 'admin'): ?>
                        <li><a href="admin_dashboard.php" class="mobile-menu__link <?= $current_page === 'profile.php' ? 'mobile-menu__link--active' : '' ?>">Mon Profil</a></li>
                        <li><a href="admin-manage-users.php" class="mobile-menu__link <?= $current_page === 'admin_dashboard.php' ? 'mobile-menu__link--active' : '' ?>">Gérer les utilisateurs</a></li>
                        <li><a href="manage-events.php" class="mobile-menu__link <?= $current_page === 'manage-events.php' ? 'mobile-menu__link--active' : '' ?>">Gérer les événements</a></li>
                        <li><a href="site-settings.php" class="mobile-menu__link <?= $current_page === 'site-settings.php' ? 'mobile-menu__link--active' : '' ?>">Paramètres du site</a></li>
                        <li><a href="add-event.php" class="mobile-menu__link <?= $current_page === 'add-event.php' ? 'mobile-menu__link--active' : '' ?>">Ajouter un événement</a></li>
                    <?php endif; ?>

                    <?php if ($user_role === 'employee'): ?>
                        <li><a href="employee_dashboard.php" class="mobile-menu__link <?= $current_page === 'employee_dashboard.php' ? 'mobile-menu__link--active' : '' ?>">Tableau de Bord Employé</a></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Mobile Menu Toggle Button -->
        <div class="nav__btn">
            <span class="nav__btn-line"></span>
        </div>
    </div>
</div>

<!--<script>-->
<!--    document.addEventListener('DOMContentLoaded', function() {-->
<!--        const menuButton = document.querySelector('.nav__btn');-->
<!--        const mobileMenu = document.querySelector('.nav-menu');-->
<!---->
<!--        menuButton.addEventListener('click', function() {-->
<!--            mobileMenu.classList.toggle('nav-menu--open');-->
<!--            menuButton.classList.toggle('nav__btn--open');-->
<!--        });-->
<!--    });-->
<!--</script>-->
