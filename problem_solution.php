<?php
session_start();
include 'db_connection.php';

// Redirect if not logged in
if (!isset($_SESSION['farmer_id'])) {
    header('Location: farmer_login.php');
    exit();
}

// Get farmer data
$farmer_id = $_SESSION['farmer_id'];
$farmer_name = $_SESSION['farmer_name'];
$farmer_image = $_SESSION['farmer_image'];

// Handle posting a new question
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title'], $_POST['content'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $stmt = $conn->prepare("INSERT INTO posts (farmer_id, title, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $farmer_id, $title, $content);
    $stmt->execute();
    echo '<script>alert("Post created successfully!");</script>';
}

// Handle sending a new message
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'], $_POST['receiver_id'])) {
    $receiver_id = $_POST['receiver_id'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $farmer_id, $receiver_id, $message);
    $stmt->execute();
    echo '<script>alert("Message sent successfully!");</script>';
}

// Fetch posts for this farmer
$stmt = $conn->prepare("SELECT * FROM posts WHERE farmer_id = ?");
$stmt->bind_param("i", $farmer_id);
$stmt->execute();
$posts = $stmt->get_result();

// Fetch messages for this farmer
$stmt = $conn->prepare("SELECT * FROM messages WHERE receiver_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $farmer_id);
$stmt->execute();
$messages = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Farmer Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #0d0d0d;
            color: #fff;
            margin: 0;
            padding: 0;
        }

        nav {
            background-color: #004d00;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #d4f5c3;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 25px;
        }

        nav ul li a {
            text-decoration: none;
            color: #d4f5c3;
            font-weight: 500;
        }

        .container {
            display: flex;
            padding: 20px;
        }

        .sidebar {
            width: 25%;
            background: #333;
            padding: 15px;
            margin-right: 20px;
        }

        .sidebar .chat-btn, .sidebar .posts-btn {
            background: #008000;
            color: white;
            padding: 10px;
            margin-bottom: 10px;
            border: none;
            cursor: pointer;
            width: 100%;
            text-align: left;
        }

        .sidebar .chat-btn:hover, .sidebar .posts-btn:hover {
            background: #00cc44;
        }

        .main-content {
            width: 75%;
        }

        .post, .message {
            background: #1a1a1a;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .post h3 {
            color: #80ff80;
        }

        .post p {
            color: #d4f5c3;
        }

        textarea {
            width: 100%;
            padding: 10px;
            background: #333;
            color: #d4f5c3;
            border: none;
            border-radius: 5px;
            margin-top: 10px;
            resize: vertical;
        }

        button {
            background-color: #008000;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }

        button:hover {
            background-color: #00cc44;
        }

        .chat-box {
            display: none;
        }

        .message-list {
            max-height: 300px;
            overflow-y: scroll;
        }

        .message p {
            color: #fff;
        }

    </style>
</head>
<body>

<!-- Navbar -->
<nav>
    <div class="logo">AgriConnect</div>
    <ul>
        <li><a href="farmer_dashboard.php">Dashboard</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
        <button class="chat-btn" onclick="toggleChat()">Chat</button>
        <button class="posts-btn" onclick="togglePosts()">Posts</button>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Chat Box -->
        <div id="chat-box" class="chat-box">
            <div class="message-list">
                <?php while ($message = $messages->fetch_assoc()): ?>
                    <div class="message">
                        <p><strong>Sender:</strong> <?= $message['sender_id'] ?> <br>
                            <?= $message['message'] ?> <br>
                            <small><?= $message['created_at'] ?></small></p>
                    </div>
                <?php endwhile; ?>
            </div>
            <textarea id="newMessage" placeholder="Type your message..."></textarea>
            <button onclick="sendMessage()">Send Message</button>
        </div>

        <!-- Post Box -->
        <div id="posts-box">
            <h2>Your Posts</h2>
            <?php while ($post = $posts->fetch_assoc()): ?>
                <div class="post">
                    <h3><?= $post['title'] ?></h3>
                    <p><?= $post['content'] ?></p>
                    <small><?= $post['created_at'] ?></small>
                    <button onclick="viewComments(<?= $post['id'] ?>)">View Comments</button>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- New Post Form -->
        <h3>Create a New Post</h3>
        <form method="POST">
            <input type="text" name="title" placeholder="Post Title" required />
            <textarea name="content" placeholder="Post Content" required></textarea>
            <button type="submit">Create Post</button>
        </form>
    </div>
</div>

<script>
    // Toggle chat box visibility
    function toggleChat() {
        const chatBox = document.getElementById('chat-box');
        chatBox.style.display = chatBox.style.display === 'none' ? 'block' : 'none';
    }

    // Toggle post box visibility
    function togglePosts() {
        const postsBox = document.getElementById('posts-box');
        postsBox.style.display = postsBox.style.display === 'none' ? 'block' : 'none';
    }

    // Handle sending a message (AJAX or direct form submission)
    function sendMessage() {
        const message = document.getElementById('newMessage').value;
        if (message.trim() !== '') {
            // Send the message (AJAX or direct PHP form)
            alert('Message sent: ' + message); // Here you'd use an AJAX call or form submission
        }
    }

    // View comments on a post
    function viewComments(postId) {
        alert('View comments for post ID: ' + postId); // Implement comment display logic here
    }
</script>

</body>
</html>
