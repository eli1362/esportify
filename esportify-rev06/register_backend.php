<?php
global $pdo;
session_start();
require_once 'backend/config.php';

// Function to sanitize user input
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Initialize variables
$username = sanitize(isset($_POST['username']) ? $_POST['username'] : '');
$email = sanitize(isset($_POST['email']) ? $_POST['email'] : '');
$password = isset($_POST['password']) ? $_POST['password'] : '';
$confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

// Keep old values in session
$_SESSION['old_username'] = $username;
$_SESSION['old_email'] = $email;

// Basic validation
if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
    $_SESSION['register_error'] = "Veuillez remplir tous les champs.";
    header("Location: register.php");
    exit;
}

if (strlen($username) < 3) {
    $_SESSION['register_error'] = "Le nom d'utilisateur doit contenir au moins 3 caractères.";
    header("Location: register.php");
    exit;
}

if ($password !== $confirm_password) {
    $_SESSION['register_error'] = "Les mots de passe ne correspondent pas.";
    header("Location: register.php");
    exit;
}

if (strlen($password) < 6 || !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
    $_SESSION['register_error'] = "Le mot de passe doit contenir au moins 6 caractères et un symbole.";
    header("Location: register.php");
    exit;
}

// Check if the email or username already exists
$sql = "SELECT id FROM users WHERE username = :username OR email = :email LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'username' => $username,
    'email' => $email
]);

if ($stmt->fetch()) {
    $_SESSION['register_error'] = "Nom d'utilisateur ou email déjà utilisé.";
    header("Location: register.php");
    exit;
}

// Determine the user role (from the form)
$role = 'user'; // Default role is 'user'
if (isset($_POST['role']) && in_array($_POST['role'], ['user', 'employee', 'admin'])) {
    $role = $_POST['role']; // Set role based on form selection
}

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert the new user into the database
$insertSql = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)";
$insertStmt = $pdo->prepare($insertSql);
$success = $insertStmt->execute([
    'username' => $username,
    'email' => $email,
    'password' => $hashedPassword,
    'role' => $role
]);

// Handle success or failure
if ($success) {
    unset($_SESSION['old_username'], $_SESSION['old_email']); // Clear old values
    $_SESSION['register_success'] = "Compte créé avec succès ! Vous pouvez vous connecter.";
    header("Location: login.php");
    exit;
} else {
    $_SESSION['register_error'] = "Une erreur est survenue. Veuillez réessayer.";
    header("Location: register.php");
    exit;
}
