<?php
global $pdo;
require 'backend/config.php';
session_start();

// Vérifie que l'utilisateur est admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id']) && isset($_GET['action'])) {
    $event_id = intval($_GET['id']);
    $action = $_GET['action'];

    // Déterminer le statut et is_validated
    if ($action === 'valider') {
        $status = 'validé';
        $is_validated = 1;
    } elseif ($action === 'ignorer') {
        $status = 'ignoré';
        $is_validated = 0;
    } else {
        $_SESSION['message'] = "Action invalide.";
        header("Location: manage-events.php");
        exit;
    }

    // Mise à jour dans la base de données
    $stmt = $pdo->prepare("UPDATE events SET validation_status = :status, is_validated = :is_validated WHERE id = :id");
    $stmt->execute([
        'status' => $status,
        'is_validated' => $is_validated,
        'id' => $event_id
    ]);

    $_SESSION['message'] = "Événement mis à jour avec succès.";
} else {
    $_SESSION['message'] = "Données manquantes.";
}

header("Location: manage-events.php");
exit;
