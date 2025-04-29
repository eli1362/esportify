<?php
session_start();
require_once 'backend/config.php';
global $pdo;

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $remember_me = isset($_POST['remember_me']);

    // First check in 'users' table
    $stmt = $pdo->prepare("SELECT *, 'users' AS source FROM users WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Then check in 'employee' table if not found
    if (!$user) {
        $stmt = $pdo->prepare("SELECT *, 'employee' AS source FROM employee WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $user['role'] = 'employee'; // Force naming consistency
        }
    }

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role']; // 'user', 'organizer', or 'admin'
        $_SESSION['table'] = $user['source'];
        $_SESSION['user_logged_in'] = true;

        // Remember Me functionality
        if ($remember_me) {
            $remember_token = bin2hex(random_bytes(16));
            $expiration = date('Y-m-d H:i:s', strtotime('+7 days'));

            $updateQuery = $user['source'] === 'users'
                ? "UPDATE users SET remember_token = :token, remember_token_expiration = :expiration WHERE id = :id"
                : "UPDATE employee SET remember_token = :token, remember_token_expiration = :expiration WHERE id = :id";

            $stmt = $pdo->prepare($updateQuery);
            $stmt->execute([
                'token' => $remember_token,
                'expiration' => $expiration,
                'id' => $user['id']
            ]);

            setcookie("remember_username", $user['username'], time() + (86400 * 7), "/");
            setcookie("remember_token", $remember_token, time() + (86400 * 7), "/");
        }

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } elseif ($user['role'] === 'employee') {
            header("Location: dashboard_organizer.php");
        } else {
            header("Location: dashboard_user.php");
        }
        exit;

    } else {
        $_SESSION['login_error'] = "Nom d'utilisateur ou mot de passe incorrect.";
        header("Location: login.php");
        exit;
    }
}
?>
