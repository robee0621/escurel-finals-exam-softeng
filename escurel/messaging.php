<?php
session_start();
include_once 'models.php';
include_once 'handleForms.php';

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user']['id'];
$userRole = $_SESSION['user']['role'];

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $receiverId = $_POST['receiver_id'];
    $message = trim($_POST['message']);
    
    if (!empty($message)) {
        sendMessage($userId, $receiverId, $message);
    }
}

$selectedReceiverId = isset($_GET['receiver_id']) ? $_GET['receiver_id'] : null;
$conversations = getConversations($userId);
$messages = [];
if ($selectedReceiverId) {
    $messages = getMessagesBetweenUsers($userId, $selectedReceiverId);
    $selectedUser = getUserById($selectedReceiverId);
}

$hrUsers = [];
if ($userRole == 'Applicant') {
    $hrUsers = getAllHRUsers();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background: #1a1a1a;
            margin: 0;
            padding: 20px;
            font-family: 'Inter', sans-serif;
        }

        .app-container {
            max-width: 1200px;
            margin: 0 auto;
            background: #2d2d2d;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            overflow: hidden;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 32px;
            background: #333;
            border-bottom: 1px solid #444;
        }

        .top-bar-title {
            font-size: 28px;
            color: #ff6b00;
            font-weight: 700;
        }

        .nav-actions {
            display: flex;
            gap: 12px;
        }

        .nav-link {
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            color: #fff;
            background: #444;
            border: 1px solid #555;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            background: #ff6b00;
            transform: translateY(-1px);
            border-color: #ff6b00;
        }

        .btn-logout {
            background: #ff6b00;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 600;
        }

        .btn-logout:hover {
            background: #ff8533;
            transform: translateY(-1px);
        }

        .messages-container {
            display: flex;
            height: calc(100vh - 180px);
        }

        .conversations-list {
            width: 320px;
            border-right: 1px solid #444;
            padding: 24px;
            background: #333;
            overflow-y: auto;
        }

        .messages-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 24px;
            background: #2d2d2d;
        }

        .section-title {
            font-size: 20px;
            color: #ff6b00;
            margin-bottom: 16px;
            font-weight: 700;
        }

        .conversation-item {
            padding: 16px;
            margin: 8px 0;
            border-radius: 12px;
            background: #444;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 1px solid #555;
            color: #fff;
        }

        .conversation-item:hover {
            background: #ff6b00;
            transform: translateX(4px);
            border-color: #ff8533;
        }

        .messages-list {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
            background: #333;
            border-radius: 16px;
            margin-bottom: 24px;
        }

        .message-box {
            max-width: 65%;
            padding: 16px;
            margin: 12px 0;
            border-radius: 16px;
            position: relative;
        }

        .sent-message {
            background: #ff6b00;
            color: white;
            margin-left: auto;
            border-bottom-right-radius: 4px;
        }

        .received-message {
            background: #444;
            color: #fff;
            margin-right: auto;
            border-bottom-left-radius: 4px;
        }

        .message-form {
            background: #333;
            padding: 24px;
            border-radius: 16px;
            box-shadow: 0 -4px 12px rgba(0,0,0,0.2);
        }

        .message-input {
            width: 100%;
            padding: 16px;
            border: 2px solid #444;
            border-radius: 12px;
            resize: none;
            margin-bottom: 12px;
            font-family: inherit;
            font-size: 15px;
            transition: border-color 0.2s ease;
            background: #2d2d2d;
            color: #fff;
        }

        .message-input:focus {
            outline: none;
            border-color: #ff6b00;
        }

        .btn-send {
            width: 100%;
            padding: 14px;
            background: #ff6b00;
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.2s ease;
        }

        .btn-send:hover {
            background: #ff8533;
            transform: translateY(-2px);
        }

        .timestamp {
            font-size: 12px;
            opacity: 0.8;
            margin-top: 6px;
        }

        .no-messages {
            text-align: center;
            color: #888;
            padding: 48px;
            font-style: italic;
            font-size: 16px;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #333;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: #ff6b00;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #ff8533;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <div class="top-bar">
            <div class="top-bar-title">Messages</div>
            <div class="nav-actions">
                <a href="<?php echo ($userRole == 'Applicant') ? 'applicant_Dashboard.php' : 'hr_Dashboard.php'; ?>" class="nav-link">Dashboard</a>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>

        <div class="messages-container">
            <div class="conversations-list">
                <h2 class="section-title">Conversations</h2>
                <?php if ($userRole == 'Applicant' && !empty($hrUsers)): ?>
                    <h3 class="section-title">HR Representatives</h3>
                    <?php foreach ($hrUsers as $hr): ?>
                        <div class="conversation-item" onclick="window.location.href='messaging.php?receiver_id=<?= $hr['id'] ?>'">
                            <?= htmlspecialchars($hr['username']) ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if (!empty($conversations)): ?>
                    <h3 class="section-title">Recent Chats</h3>
                    <?php foreach ($conversations as $conversation): ?>
                        <div class="conversation-item" onclick="window.location.href='messaging.php?receiver_id=<?= $conversation['id'] ?>'">
                            <?= htmlspecialchars($conversation['username']) ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="messages-area">
                <?php if ($selectedReceiverId): ?>
                    <h2 class="section-title">Chat with <?= htmlspecialchars($selectedUser['username']) ?></h2>
                    <div class="messages-list">
                        <?php if (empty($messages)): ?>
                            <div class="no-messages">No messages yet. Start the conversation!</div>
                        <?php else: ?>
                            <?php foreach ($messages as $message): ?>
                                <div class="message-box <?= $message['sender_id'] == $userId ? 'sent-message' : 'received-message' ?>">
                                    <p><?= htmlspecialchars($message['message']) ?></p>
                                    <div class="timestamp"><?= date('M d, Y H:i', strtotime($message['timestamp'])) ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <form method="POST" class="message-form">
                        <input type="hidden" name="receiver_id" value="<?= $selectedReceiverId ?>">
                        <textarea name="message" class="message-input" rows="3" placeholder="Type your message..." required></textarea>
                        <button type="submit" class="btn-send">Send Message</button>
                    </form>
                <?php else: ?>
                    <div class="no-messages">Select a conversation to start messaging</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Auto-scroll to bottom of messages
        const messagesList = document.querySelector('.messages-list');
        if (messagesList) {
            messagesList.scrollTop = messagesList.scrollHeight;
        }
    </script>
</body>
</html>
