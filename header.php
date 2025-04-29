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
                    <i class="fa-solid fa-house"></i> <?= isset($translations['home']) ? $translations['home'] : 'Accueil' ?>
                </a>
            </li>
            <li class="menu__item">
                <a href="all-events.php" class="menu__link <?= $current_page === 'all-events.php' ? 'menu__link--active' : '' ?>">
                    <i class="fa-solid fa-calendar-days"></i> <?= isset($translations['all_events']) ? $translations['all_events'] : 'Tous événements' ?>
                </a>
            </li>
            <li class="menu__item">
                <a href="contact.php" class="menu__link <?= $current_page === 'contact.php' ? 'menu__link--active' : '' ?>">
                    <i class="fa-solid fa-envelope"></i> <?= isset($translations['contact']) ? $translations['contact'] : 'Contact' ?>
                </a>
            </li>


            <?php if (!$is_logged_in): ?>
                <li class="menu__item">
                    <a href="login.php" class="menu__link <?= $current_page === 'login.php' ? 'menu__link--active' : '' ?>">
                        <i class="fa-solid fa-right-to-bracket"></i> <?= isset($translations['login']) ? $translations['login'] : 'Connexion' ?>
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
                                <?php if ($user_role === 'admin'): ?>
                                    <li><a href="admin_dashboard.php"><i class="fa-solid fa-user"></i> <?= isset($translations['my_profile']) ? $translations['my_profile'] : 'Mon Profil' ?></a></li>
                                <?php elseif ($user_role === 'employee'): ?>
                                    <li><a href="dashboard_organizer.php"><i class="fa-solid fa-user"></i> <?= isset($translations['my_profile']) ? $translations['my_profile'] : 'Mon Profil' ?></a></li>
                                <?php else: ?>
                                    <li><a href="dashboard_user.php"><i class="fa-solid fa-user"></i> <?= isset($translations['my_profile']) ? $translations['my_profile'] : 'Mon Profil' ?></a></li>
                                <?php endif; ?>

                                <?php if ($user_role === 'admin' || $user_role === 'employee'): ?>
                                    <li><a href="my-events.php"><i class="fa-solid fa-calendar-check"></i> <?= isset($translations['my_events']) ? $translations['my_events'] : 'Mes Événements' ?></a></li>
                                <?php endif; ?>

                                <li><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> <?= isset($translations['logout']) ? $translations['logout'] : 'Déconnexion' ?></a></li>

                                <?php if ($user_role === 'admin'): ?>
                                    <li><a href="admin-manage-users.php"><i class="fa-solid fa-users-cog"></i> <?= isset($translations['manage_users']) ? $translations['manage_users'] : 'Gérer les utilisateurs' ?></a></li>
                                    <li><a href="manage-events.php"><i class="fa-solid fa-calendar"></i> <?= isset($translations['manage_events']) ? $translations['manage_events'] : 'Gérer les événements' ?></a></li>
                                    <li><a href="site-settings.php"><i class="fa-solid fa-gear"></i> <?= isset($translations['site_settings']) ? $translations['site_settings'] : 'Paramètres du site' ?></a></li>
                                    <li><a href="create_event.php"><i class="fa-solid fa-plus-circle"></i> <?= isset($translations['add_event']) ? $translations['add_event'] : 'Ajouter un événement' ?></a></li>
                                <?php endif; ?>

                                <?php if ($user_role === 'employee'): ?>
                                    <li><a href="dashboard_organizer.php"><i class="fa-solid fa-briefcase"></i> <?= isset($translations['employee_dashboard']) ? $translations['employee_dashboard'] : 'Tableau de Bord Employé' ?></a></li>
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
                <li><a href="index.php" class="mobile-menu__link <?= $current_page === 'index.php' ? 'mobile-menu__link--active' : '' ?>"><?= isset($translations['home']) ? $translations['home'] : 'Accueil' ?></a></li>
                <li><a href="all-events.php" class="mobile-menu__link <?= $current_page === 'all-events.php' ? 'mobile-menu__link--active' : '' ?>"><?= isset($translations['all_events']) ? $translations['all_events'] : 'Tous événements' ?></a></li>

                <?php if (!$is_logged_in): ?>
                    <li><a href="login.php" class="mobile-menu__link <?= $current_page === 'login.php' ? 'mobile-menu__link--active' : '' ?>"><?= isset($translations['login']) ? $translations['login'] : 'Connexion' ?></a></li>
                <?php else: ?>
                    <?php if ($user_role === 'admin'): ?>
                        <li><a href="admin_dashboard.php" class="mobile-menu__link <?= $current_page === 'admin_dashboard.php' ? 'mobile-menu__link--active' : '' ?>"><?= isset($translations['my_profile']) ? $translations['my_profile'] : 'Mon Profil' ?></a></li>
                        <li><a href="admin-manage-users.php" class="mobile-menu__link <?= $current_page === 'admin-manage-users.php' ? 'mobile-menu__link--active' : '' ?>"><?= isset($translations['manage_users']) ? $translations['manage_users'] : 'Gérer les utilisateurs' ?></a></li>
                        <li><a href="manage-events.php" class="mobile-menu__link <?= $current_page === 'manage-events.php' ? 'mobile-menu__link--active' : '' ?>"><?= isset($translations['manage_events']) ? $translations['manage_events'] : 'Gérer les événements' ?></a></li>
                        <li><a href="site-settings.php" class="mobile-menu__link <?= $current_page === 'site-settings.php' ? 'mobile-menu__link--active' : '' ?>"><?= isset($translations['site_settings']) ? $translations['site_settings'] : 'Paramètres du site' ?></a></li>
                        <li><a href="create_event.php" class="mobile-menu__link <?= $current_page === 'create_event.php' ? 'mobile-menu__link--active' : '' ?>"><?= isset($translations['add_event']) ? $translations['add_event'] : 'Ajouter un événement' ?></a></li>
                        <li><a href="logout.php" class="mobile-menu__link"><?= isset($translations['logout']) ? $translations['logout'] : 'Déconnexion' ?></a></li>
                    <?php elseif ($user_role === 'employee'): ?>
                        <li><a href="dashboard_organizer.php" class="mobile-menu__link <?= $current_page === 'dashboard_organizer.php' ? 'mobile-menu__link--active' : '' ?>"><?= isset($translations['employee_dashboard']) ? $translations['employee_dashboard'] : 'Tableau de Bord Employé' ?></a></li>
                        <li><a href="logout.php" class="mobile-menu__link"><?= isset($translations['logout']) ? $translations['logout'] : 'Déconnexion' ?></a></li>
                    <?php else: ?>
                        <li><a href="dashboard_user.php" class="mobile-menu__link <?= $current_page === 'dashboard_user.php' ? 'mobile-menu__link--active' : '' ?>"><?= isset($translations['my_profile']) ? $translations['my_profile'] : 'Mon Profil' ?></a></li>
                        <li><a href="logout.php" class="mobile-menu__link"><?= isset($translations['logout']) ? $translations['logout'] : 'Déconnexion' ?></a></li>
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
