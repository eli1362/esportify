<?php
global $db;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'backend/config.php';
    session_start();

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header("Location: login.php");
        exit;
    }

    $eventId = intval($_POST['event_id']);
    $action = $_POST['action'];

    $status = $action === 'approve' ? 'validé' : 'non-validé';

    $stmt = $db->prepare("UPDATE events SET status = :status WHERE id = :id");
    $stmt->execute(['status' => $status, 'id' => $eventId]);

    header("Location: manage-events.php");
    exit;
}

