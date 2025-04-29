<?php
global $pdo;
require 'backend/config.php';
session_start();

// Only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$query = "SELECT * FROM users ORDER BY username ASC";
$users = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gérer les utilisateurs</title>
    <link rel="icon" type="image/png" href="assets/images/png/circle.png">
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/fonts.css">
    <link rel="stylesheet" href="assets/css/grid.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<header class="header">
    <?php include 'header.php'; ?>
</header>

<main class="main admin-users">
    <div class="container">
        <div class="user__wrapper">
            <h1 class="evenements__title">Gestion des Utilisateurs</h1>

            <table class="events-table">
                <thead>
                <tr>
                    <th>Pseudo</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Modifier</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr id="user-<?= $user['id'] ?>">
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td class="role-cell"><?= ucfirst($user['role']) ?></td>
                        <td>
                            <form method="POST" class="update-role-form">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <select name="new_role" class="update-form__select">
                                    <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>Utilisateur</option>
                                    <option value="employee" <?= $user['role'] === 'employee' ? 'selected' : '' ?>>Organisateur</option>
                                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrateur</option>
                                </select>
                                <button type="submit" class="btn green">Mettre à jour</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
    $(document).ready(function(){
        $(".update-role-form").submit(function(e){
            e.preventDefault();

            const form = $(this);
            const userId = form.find('input[name="user_id"]').val();
            const newRole = form.find('select[name="new_role"]').val();

            $.ajax({
                url: "update-user-role.php",
                type: "POST",
                data: { user_id: userId, new_role: newRole },
                dataType: "json",
                success: function(response) {
                    if (response.status === "success") {
                        $("#user-" + userId).find(".role-cell").text(newRole.charAt(0).toUpperCase() + newRole.slice(1));
                        alert("Rôle mis à jour !");
                    } else {
                        alert("Erreur: " + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", error);
                    alert("Erreur lors de la mise à jour du rôle.");
                }
            });
        });
    });
</script>

<script src="assets/js/script.js"></script>
</body>
</html>
