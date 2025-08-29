<?php
session_start();
include('db_connection.php');

// Check if the user is logged in and is a farmer
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'farmer') {
    header('Location: farmer_login.php');
    exit();
}

$error = "";
$success = "";

// Handle new post creation
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['content']) && !isset($_POST['post_id'])) {
    $content = trim($_POST['content']);
    $user_id = $_SESSION['farmer_id'];
    $user_type = 'farmer';

    // Handle multimedia upload (image, video, or audio)
    $attachment = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $attachment = "uploads/" . basename($_FILES['attachment']['name']);
        move_uploaded_file($_FILES['attachment']['tmp_name'], $attachment);
    } else {
        $attachment = null;
    }

    $stmt = $conn->prepare("INSERT INTO posts (user_type, user_id, content, attachment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siss", $user_type, $user_id, $content, $attachment);

    if ($stmt->execute()) {
        $success = "Post created successfully!";
    } else {
        $error = "Failed to create post.";
    }
}

// Handle post editing
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];
    $content = trim($_POST['content']);
    $user_id = $_SESSION['farmer_id'];

    // Handle multimedia upload (image, video, or audio)
    $attachment = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $attachment = "uploads/" . basename($_FILES['attachment']['name']);
        move_uploaded_file($_FILES['attachment']['tmp_name'], $attachment);
    }

    $stmt = $conn->prepare("UPDATE posts SET content = ?, attachment = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssii", $content, $attachment, $post_id, $user_id);

    if ($stmt->execute()) {
        $success = "Post updated successfully!";
    } else {
        $error = "Failed to update post.";
    }
}

// Handle reply creation
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['reply_content'], $_POST['post_id']) && !isset($_POST['reply_id'])) {
    $reply_content = trim($_POST['reply_content']);
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['farmer_id'];
    $user_type = 'farmer';

    // Handle multimedia upload (image, video, or audio) for replies
    $attachment = null;
    if (isset($_FILES['reply_attachment']) && $_FILES['reply_attachment']['error'] === UPLOAD_ERR_OK) {
        $attachment = "uploads/" . basename($_FILES['reply_attachment']['name']);
        move_uploaded_file($_FILES['reply_attachment']['tmp_name'], $attachment);
    }

    $stmt = $conn->prepare("INSERT INTO replies (post_id, user_type, user_id, content, attachment) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sisss", $post_id, $user_type, $user_id, $reply_content, $attachment);

    if ($stmt->execute()) {
        $success = "Reply posted successfully!";
    } else {
        $error = "Failed to post reply.";
    }
}

// Handle reply editing
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['reply_id'])) {
    $reply_id = $_POST['reply_id'];
    $reply_content = trim($_POST['reply_content']);
    $user_id = $_SESSION['farmer_id'];

    // Handle multimedia upload (image, video, or audio) for replies
    $attachment = null;
    if (isset($_FILES['reply_attachment']) && $_FILES['reply_attachment']['error'] === UPLOAD_ERR_OK) {
        $attachment = "uploads/" . basename($_FILES['reply_attachment']['name']);
        move_uploaded_file($_FILES['reply_attachment']['tmp_name'], $attachment);
    }

    $stmt = $conn->prepare("UPDATE replies SET content = ?, attachment = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssii", $reply_content, $attachment, $reply_id, $user_id);

    if ($stmt->execute()) {
        $success = "Reply updated successfully!";
    } else {
        $error = "Failed to update reply.";
    }
}

// Fetch posts with the author's name
$posts_query = "SELECT posts.id, posts.user_id, posts.user_type, posts.content, posts.attachment, posts.created_at, 
                        IF(posts.user_type = 'farmer', CONCAT(farmers.first_name, ' ', farmers.last_name), 
                        CONCAT(agri_officers.first_name, ' ', agri_officers.last_name)) AS posted_by
                FROM posts
                LEFT JOIN farmers ON posts.user_id = farmers.id
                LEFT JOIN agri_officers ON posts.user_id = agri_officers.id
                ORDER BY posts.created_at DESC";
$posts_result = $conn->query($posts_query);

// Fetch replies for posts
$replies_query = "SELECT replies.id, replies.content, replies.attachment, replies.created_at, 
                        IF(replies.user_type = 'farmer', CONCAT(farmers.first_name, ' ', farmers.last_name), 
                        CONCAT(agri_officers.first_name, ' ', agri_officers.last_name)) AS replied_by
                FROM replies
                LEFT JOIN farmers ON replies.user_id = farmers.id
                LEFT JOIN agri_officers ON replies.user_id = agri_officers.id
                ORDER BY replies.created_at DESC";
$replies_result = $conn->query($replies_query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Post</title>
    <style>
        :root {
  --bg-main: #0d0d0d;
  --bg-secondary: #1a1a1a;
  --accent: #80ff80;
  --accent-dark: #004d00;
  --text-light: #d4f5c3;
  --text-muted: #a5c49e;
  --card-radius: 12px;
  --gap: 20px;
  --font-family: 'Poppins', sans-serif;
  --shadow-glow: 0 0 10px #004d00;
  --shadow-glow-strong: 0 0 20px #80ff80;
}

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
  font-family: var(--font-family);
  color: var(--text-light);
}

body {
  background-color: var(--bg-main);
  padding: 0;
  margin: 0;
  min-height: 100vh;
  line-height: 1.5;
}

nav {
  background-color: var(--accent-dark);
  padding: 15px 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: var(--shadow-glow);
  position: sticky;
  top: 0;
  z-index: 1000;
}

nav .logo {
  font-weight: 700;
  font-size: 1.5rem;
  color: var(--accent);
  user-select: none;
}

nav ul {
  list-style: none;
  display: flex;
  gap: 25px;
}

nav ul li a {
  text-decoration: none;
  color: var(--text-light);
  font-weight: 600;
  transition: color 0.3s ease;
}

nav ul li a:hover,
nav ul li a:focus {
  color: var(--accent);
  outline: none;
}

.container {
  max-width: 900px;
  background-color: var(--bg-secondary);
  margin: 40px auto;
  padding: 30px 40px;
  border-radius: var(--card-radius);
  box-shadow: var(--shadow-glow);
}

h2 {
  margin-bottom: var(--gap);
  color: var(--accent);
  font-weight: 700;
  user-select: none;
}

form textarea {
  width: 100%;
  min-height: 100px;
  background-color: var(--bg-main);
  border: 1.5px solid var(--accent-dark);
  border-radius: var(--card-radius);
  padding: 15px;
  font-size: 1rem;
  color: var(--text-light);
  resize: vertical;
  transition: border-color 0.3s ease;
}

form textarea:focus {
  border-color: var(--accent);
  outline: none;
  box-shadow: var(--shadow-glow-strong);
}

input[type="file"] {
  margin-top: 12px;
  color: var(--text-muted);
}

label {
  margin-top: 15px;
  display: block;
  font-weight: 600;
  color: var(--accent);
}

button[type="submit"] {
  margin-top: 20px;
  background-color: var(--accent);
  border: none;
  color: var(--bg-main);
  font-weight: 700;
  padding: 12px 28px;
  border-radius: var(--card-radius);
  cursor: pointer;
  box-shadow: var(--shadow-glow);
  transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

button[type="submit"]:hover,
button[type="submit"]:focus {
  background-color: var(--accent-dark);
  box-shadow: var(--shadow-glow-strong);
  outline: none;
}

.error {
  margin-top: 10px;
  color: #f44336;
  font-weight: 600;
}

.success {
  margin-top: 10px;
  color: #4caf50;
  font-weight: 600;
}

/* Posts List */

.post-container {
  background-color: var(--bg-main);
  border-radius: var(--card-radius);
  padding: 25px 30px;
  margin-bottom: 30px;
  box-shadow: var(--shadow-glow);
}

.post-content {
  font-size: 1.1rem;
  margin-bottom: 12px;
  color: var(--text-light);
  user-select: text;
}

.post-content strong {
  color: var(--accent);
}

.attachment a {
  color: var(--accent);
  font-weight: 700;
  text-decoration: none;
  user-select: text;
}

.attachment a:hover,
.attachment a:focus {
  text-decoration: underline;
  outline: none;
}

.edit-button {
  background-color: var(--accent-dark);
  color: var(--text-light);
  border: none;
  padding: 10px 22px;
  margin-top: 15px;
  border-radius: var(--card-radius);
  cursor: pointer;
  font-weight: 700;
  box-shadow: var(--shadow-glow);
  transition: background-color 0.3s ease;
}

.edit-button:hover,
.edit-button:focus {
  background-color: var(--accent);
  color: var(--bg-main);
  outline: none;
}

/* Reply Section */

.reply-container {
  margin-top: 25px;
  background-color: var(--bg-secondary);
  padding: 18px 24px;
  border-radius: var(--card-radius);
  box-shadow: inset 0 0 12px #004d0011;
}

.reply-container h3 {
  color: var(--accent);
  margin-bottom: 18px;
  user-select: none;
}

.reply-content {
  margin-bottom: 16px;
  padding-left: 20px;
  border-left: 3px solid var(--accent-dark);
  color: var(--text-muted);
  font-size: 1rem;
  user-select: text;
}

.reply-content strong {
  color: var(--accent);
  user-select: none;
}

.reply-content a {
  display: inline-block;
  margin-top: 6px;
  color: var(--accent);
  text-decoration: none;
  font-weight: 600;
  user-select: text;
}

.reply-content a:hover,
.reply-content a:focus {
  text-decoration: underline;
  outline: none;
}

.reply-container textarea {
  width: 90%;
  min-height: 80px;
  background-color: var(--bg-main);
  border: 1.5px solid var(--accent-dark);
  border-radius: var(--card-radius);
  padding: 12px 14px;
  font-size: 1rem;
  color: var(--text-light);
  resize: vertical;
  transition: border-color 0.3s ease;
}

.reply-container textarea:focus {
  border-color: var(--accent);
  outline: none;
  box-shadow: var(--shadow-glow-strong);
}

.reply-container label {
  color: var(--accent);
  margin-top: 10px;
  font-weight: 600;
  display: block;
}

.reply-container input[type="file"] {
  margin-top: 10px;
  color: var(--text-muted);
}

.reply-container button[type="submit"] {
  margin-top: 12px;
  background-color: var(--accent);
  border: none;
  color: var(--bg-main);
  font-weight: 700;
  padding: 10px 24px;
  border-radius: var(--card-radius);
  cursor: pointer;
  box-shadow: var(--shadow-glow);
  transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

.reply-container button[type="submit"]:hover,
.reply-container button[type="submit"]:focus {
  background-color: var(--accent-dark);
  box-shadow: var(--shadow-glow-strong);
  outline: none;
}

/* Responsive */

@media (max-width: 768px) {
  .container {
    width: 90%;
    padding: 25px 20px;
  }

  nav ul {
    gap: 15px;
  }

  .reply-container textarea,
  form textarea {
    width: 100%;
  }

  .reply-container button[type="submit"],
  button[type="submit"] {
    width: 100%;
  }
}

@media (max-width: 480px) {
  .container {
    width: 95%;
    padding: 20px 15px;
  }

  nav {
    flex-direction: column;
    gap: 10px;
  }
}

    </style>
</head>
<body>

<nav>
    <div class="logo">AgriConnect</div>
    <ul>
        <li><a href="farmer_dashboard.php">Dashboard</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<div class="container">
    <h2>Create or Edit Post</h2>
    <form method="POST" enctype="multipart/form-data">
        <?php if (isset($_GET['edit_post_id'])): ?>
            <?php
            $post_id = $_GET['edit_post_id'];
            $post_query = $conn->prepare("SELECT * FROM posts WHERE id = ?");
            $post_query->bind_param("i", $post_id);
            $post_query->execute();
            $post_result = $post_query->get_result();
            $post_data = $post_result->fetch_assoc();
            ?>
            <input type="hidden" name="post_id" value="<?= $post_id ?>">
            <textarea name="content"><?= $post_data['content'] ?></textarea>
            <?php if ($post_data['attachment']): ?>
                <div class="attachment">
                    <a href="<?= $post_data['attachment'] ?>" target="_blank">View Attachment</a>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <textarea name="content" placeholder="Write your post..." required></textarea>
        <?php endif; ?>

        <label for="attachment">Attach files:</label>
        <input type="file" name="attachment" accept="image/*,video/*,audio/*">

        <button type="submit"><?= isset($_GET['edit_post_id']) ? 'Update' : 'Post' ?></button>

        <div class="error"><?= $error ?></div>
        <div class="success"><?= $success ?></div>
    </form>
</div>

<div class="container">
    <h2>All Posts</h2>
    <?php while ($post = $posts_result->fetch_assoc()) { ?>
        <div class="post-container">
            <div class="post-content">
                <strong>Posted by <?= $post['posted_by'] ?>:</strong> <?= $post['content'] ?>
            </div>
            <?php if ($post['attachment']) { ?>
                <div class="attachment">
                    <a href="<?= $post['attachment'] ?>" target="_blank">View Attachment</a>
                </div>
            <?php } ?>

            <?php if ($post['user_id'] == $_SESSION['farmer_id']) { ?>
                <button class="edit-button" onclick="window.location.href='farmer_post.php?edit_post_id=<?= $post['id'] ?>'">Edit Post</button>
            <?php } ?>

            <!-- Reply Section -->
            <div class="reply-container">
                <h3>Replies</h3>
                <form method="POST" enctype="multipart/form-data">
                    <textarea name="reply_content" placeholder="Write your reply..." required></textarea>
                    <label for="reply_attachment">Attach files:</label>
                    <input type="file" name="reply_attachment" accept="image/*,video/*,audio/*">
                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                    <button type="submit">Reply</button>
                </form>

                <?php
                // Fetch replies for this post
                $replies_query = "SELECT * FROM replies WHERE post_id = ? ORDER BY created_at DESC";
                $replies_stmt = $conn->prepare($replies_query);
                $replies_stmt->bind_param("i", $post['id']);
                $replies_stmt->execute();
                $replies_result = $replies_stmt->get_result();

                while ($reply = $replies_result->fetch_assoc()) {
                    echo "<div class='reply-content'>";
                    echo "<strong>Replied by " . ($reply['user_type'] == 'farmer' ? "Farmer" : "Agri Officer") . ":</strong> " . $reply['content'];
                    if ($reply['attachment']) {
                        echo "<br><a href='" . $reply['attachment'] . "' target='_blank'>View Reply Attachment</a>";
                    }
                    echo "</div>";
                }
                ?>
            </div>
        </div>
    <?php } ?>
</div>

</body>
</html>
