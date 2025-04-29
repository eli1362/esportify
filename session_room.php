<?php
global $pdo;
require_once "backend/config.php";
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id'];
$username = $_SESSION['username'];
$event_id = $_GET['event_id'] ?? null;

if (!$event_id) {
    echo "Aucun événement spécifié.";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM events WHERE id = :event_id AND validation_status = 'validé'");
$stmt->execute(['event_id' => $event_id]);
$event = $stmt->fetch();

if (!$event || $event['session_started'] != 1) {
    echo "Événement non trouvé ou session non démarrée.";
    exit;
}

// Vérifier si un message système existe déjà pour cette session
$checkMsg = $pdo->prepare("
    SELECT 1 FROM event_discussions 
    WHERE event_id = :event_id AND user_id = :user_id 
    AND message LIKE '[SYSTEM]%' LIMIT 1
");
$checkMsg->execute([
    'event_id' => $event_id,
    'user_id' => $user_id
]);

if ($checkMsg->rowCount() === 0) {
    $pdo->prepare("
        INSERT INTO event_discussions (event_id, user_id, message) 
        VALUES (:event_id, :user_id, :message)
    ")->execute([
        'event_id' => $event_id,
        'user_id' => $user_id,
        'message' => '[SYSTEM] ' . htmlspecialchars($username) . ' a rejoint la session.'
    ]);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Session - <?= htmlspecialchars($event['name']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/fonts.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/chat.css">
    <style>
        .chat-message {
            margin-bottom: 0.5em;
        }
        .chat-btn {
            background: none;
            border: none;
            color: #888;
            cursor: pointer;
            margin-left: 5px;
            font-size: 0.8em;
        }
        .chat-btn:hover {
            color: red;
        }
    </style>
</head>
<body>

<header class="header">
    <?php include_once "header.php"; ?>
</header>

<main class="main">
    <div class="container chat-room">
        <h1>Session : <?= htmlspecialchars($event['name']) ?></h1>

        <div class="chat-container">
            <div id="chat-box" class="chat-box"></div>
            <div class="chat-form">
                <input type="text" id="chat-message" placeholder="Écrivez un message..." onkeydown="if(event.key === 'Enter') sendMessage()">
                <button onclick="sendMessage()">Envoyer</button>
            </div>
        </div>
    </div>
</main>
<footer>
    <?php
    require_once "footer.php";
    ?>
</footer>
<script>
    const eventId = <?= (int) $event['id'] ?>;
    const currentUser = <?= json_encode($_SESSION['username']) ?>;
    const chatBox = document.getElementById("chat-box");
    const input = document.getElementById("chat-message");

    function fetchMessages() {
        fetch(`fetch_messages.php?event_id=${eventId}`)
            .then(res => res.json())
            .then(messages => {
                chatBox.innerHTML = '';
                messages.forEach(msg => {
                    const time = new Date(msg.timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    chatBox.innerHTML += `
                        <div class="chat-message" data-id="${msg.id}">
                            <p>
                                <strong>${msg.username}</strong>
                                <small>[${time}]</small>: ${msg.message}
                                <button onclick="reportMessage(${msg.id})" class="chat-btn">🚩</button>
                                ${msg.username === currentUser ? `<button onclick="deleteMessage(${msg.id})" class="chat-btn">❌</button>` : ''}
                            </p>
                        </div>
                    `;
                });
                chatBox.scrollTop = chatBox.scrollHeight;
            });
    }

    function sendMessage() {
        const message = input.value.trim();
        if (!message) return;

        const formData = new FormData();
        formData.append('event_id', eventId);
        formData.append('message', message);

        fetch('send_message.php', {
            method: 'POST',
            body: formData
        }).then(() => {
            input.value = '';
            fetchMessages();
        });
    }

    function reportMessage(messageId) {
        fetch(`report_message.php?id=${messageId}`, { method: 'POST' })
            .then(() => alert('Message signalé.'));
    }

    function deleteMessage(messageId) {
        if (confirm("Supprimer ce message ?")) {
            fetch(`delete_message.php?id=${messageId}`, { method: 'POST' })
                .then(() => fetchMessages());
        }
    }

    setInterval(fetchMessages, 3000);
    fetchMessages();
</script>

</body>
</html>
