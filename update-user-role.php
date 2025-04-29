<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

global $pdo;
require 'backend/config.php'; // Ensure this file correctly connects to the database
session_start();

// Only admin can do this
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Non autorisé"]);
    exit;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $new_role = $_POST['new_role'];

    // Validate role before proceeding
    if (in_array($new_role, ['user', 'employee', 'admin'])) {
        try {
            // First, update the user's role
            $stmt = $pdo->prepare("UPDATE users SET role = :role WHERE id = :id");
            $stmt->bindParam(':role', $new_role, PDO::PARAM_STR);
            $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            // If the new role is 'employee', we need to update the events where this user should be the organizer
            if ($new_role === 'employee') {
                // Fetch the username of the employee
                $stmt = $pdo->prepare("SELECT username FROM users WHERE id = :id");
                $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    // Update the organizer field in the events table for all events that should be managed by this employee
                    $stmt = $pdo->prepare("UPDATE events SET organizer = :organizer WHERE organizer = :user_id");
                    $stmt->bindParam(':organizer', $user['username'], PDO::PARAM_STR);
                    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                    $stmt->execute();
                }
            }

            echo json_encode(["status" => "success"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Rôle invalide"]);
    }
}
