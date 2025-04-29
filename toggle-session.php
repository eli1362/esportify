<?php

global $pdo;
session_start();
require 'backend/config.php';

// Redirect if not admin or employee
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'employee')) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $event_id = $_GET['id'];
    $status = $_GET['status'];

    // Validate status is either 0 or 1
    if ($status == 0 || $status == 1) {
        try {
            // Update session_started status in the database
            $stmt = $pdo->prepare("UPDATE events SET session_started = :status WHERE id = :event_id");
            $stmt->execute([
                'status' => $status,
                'event_id' => $event_id
            ]);

            // Redirect back to the manage events page with a success message
            $_SESSION['message'] = 'Le statut de la session a été mis à jour avec succès.';
            header("Location: manage-events.php");
            exit;
        } catch (PDOException $e) {
            $_SESSION['message'] = 'Erreur lors de la mise à jour du statut: ' . $e->getMessage();
            header("Location: manage-events.php");
            exit;
        }
    }
}

$_SESSION['message'] = 'Paramètres invalides.';
header("Location: manage-events.php");
exit;
