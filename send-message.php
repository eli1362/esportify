<?php
// Configuration
$to = "admin@esportify.com";
$subject = "Nouveau message depuis le formulaire de contact";

// Database credentials
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "esportify";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize inputs
    $name = htmlspecialchars($_POST["name"]);
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars($_POST["message"]);

    // Send email
    $headers = "From: $email\r\n";
    $email_message = "Nom: $name\nEmail: $email\nMessage:\n$message";
    mail($to, $subject, $email_message, $headers);

    // Save to database
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($conn->connect_error) {
        die("Erreur DB: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $message);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    // Redirect or display success
    header("Location: contact.php");
    exit;
} else {
    echo "Méthode non autorisée.";
}

