<?php
session_start();
include('db_connection.php');

// Check if the agri officer is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'agri_officer') {
    header('Location: agri_officer_login.php');
    exit();
}

$agri_officer_id = $_SESSION['agri_officer_id'];
$agri_officer_name = $_SESSION['agri_officer_name'];

// Insert reply logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reply_content'], $_POST['post_id'])) {
    $reply_content = trim($_POST['reply_content']);
    $post_id = intval($_POST['post_id']);
    $attachment = null;

    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === 0) {
        $attachment_name = basename($_FILES['attachment']['name']);
        $attachment_tmp = $_FILES['attachment']['tmp_name'];
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $attachment = $upload_dir . time() . '_' . $attachment_name;
        move_uploaded_file($attachment_tmp, $attachment);
    }

    $user_type = 'agri_officer';
    $user_id = $agri_officer_id;
    $stmt = $conn->prepare("INSERT INTO replies (post_id, user_type, user_id, content, attachment) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isiss", $post_id, $user_type, $user_id, $reply_content, $attachment);
    $stmt->execute();
    $stmt->close();

    // Redirect to avoid form resubmission
    header("Location: agri_post.php");
    exit();
}

// Handle reply deletion
if (isset($_GET['delete_reply_id'])) {
    $delete_reply_id = intval($_GET['delete_reply_id']);
    $stmt_delete = $conn->prepare("DELETE FROM replies WHERE id = ? AND user_id = ?");
    $stmt_delete->bind_param("ii", $delete_reply_id, $agri_officer_id);
    $stmt_delete->execute();
    $stmt_delete->close();

    header("Location: agri_post.php");
    exit();
}

// Handle reply editing
if (isset($_GET['edit_reply_id']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edited_reply_content'])) {
    $edit_reply_id = intval($_GET['edit_reply_id']);
    $edited_reply_content = trim($_POST['edited_reply_content']);
    $attachment = null;

    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === 0) {
        $attachment_name = basename($_FILES['attachment']['name']);
        $attachment_tmp = $_FILES['attachment']['tmp_name'];
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $attachment = $upload_dir . time() . '_' . $attachment_name;
        move_uploaded_file($attachment_tmp, $attachment);
    }

    $stmt_edit = $conn->prepare("UPDATE replies SET content = ?, attachment = ? WHERE id = ? AND user_id = ?");
    $stmt_edit->bind_param("ssii", $edited_reply_content, $attachment, $edit_reply_id, $agri_officer_id);
    $stmt_edit->execute();
    $stmt_edit->close();

    header("Location: agri_post.php");
    exit();
}

// Fetch all farmer posts
$stmt_posts = $conn->prepare("SELECT posts.*, CONCAT(farmers.first_name, ' ', farmers.last_name) AS farmer_name 
                              FROM posts 
                              JOIN farmers ON posts.user_id = farmers.id 
                              WHERE posts.user_type = 'farmer' 
                              ORDER BY posts.created_at DESC");
$stmt_posts->execute();
$posts_result = $stmt_posts->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Agri Officer Posts</title>

<style>
  :root {
    --bg-dark: #0b2a12;
    --bg-card: #0e3a1e;
    --color-accent: #80ff80;
    --color-accent-dark: #2e7d32;
    --text-light: #e6f3db;
    --text-muted: #a3d9a5;
    --btn-bg: #2e7d32;
    --btn-hover: #62c462;
    --border-radius: 12px;
    --gap: 1rem;
    --shadow: rgba(0,0,0,0.4);
    --transition: 0.3s ease;
  }

  * {
    box-sizing: border-box;
  }

  body {
    margin: 0;
    background: var(--bg-dark);
    color: var(--text-light);
    font-family: 'Poppins', sans-serif;
    padding: 2rem;
    min-height: 100vh;
  }

  nav {
    background: var(--bg-card);
    padding: 1rem 2rem;
    border-radius: var(--border-radius);
    box-shadow: 0 6px 20px var(--shadow);
    display: flex;
    justify-content: flex-end;
    gap: var(--gap);
    margin-bottom: 2rem;
  }

  nav a {
    color: var(--color-accent);
    text-decoration: none;
    font-weight: 600;
    padding: 0.6rem 1.2rem;
    border-radius: var(--border-radius);
    background-color: var(--btn-bg);
    box-shadow: 0 4px 14px rgba(46,125,50,0.5);
    transition: background-color var(--transition), transform var(--transition);
  }

  nav a:hover {
    background-color: var(--btn-hover);
    transform: translateY(-2px);
  }

  .container {
    max-width: 900px;
    margin: 0 auto;
  }

  h2 {
    text-align: center;
    color: var(--color-accent);
    font-weight: 700;
    font-size: 2.5rem;
    margin-bottom: 2rem;
    text-shadow: 1px 1px 4px var(--color-accent-dark);
  }

  .post {
    background: var(--bg-card);
    border-radius: var(--border-radius);
    padding: 1.5rem 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 8px 28px var(--shadow);
    transition: transform var(--transition), box-shadow var(--transition);
  }

  .post:hover {
    transform: scale(1.02);
    box-shadow: 0 12px 40px var(--shadow);
  }

  .post-header {
    font-weight: 700;
    font-size: 1.3rem;
    color: var(--color-accent);
    margin-bottom: 0.8rem;
  }

  .post-content {
    font-size: 1.1rem;
    line-height: 1.5;
    margin-bottom: 0.8rem;
  }

  .post-content a {
    color: var(--btn-hover);
    text-decoration: underline;
  }

  .post-footer {
    font-size: 0.85rem;
    color: var(--text-muted);
    text-align: right;
  }

  /* Reply Section */
  .reply-section {
    margin-top: 1.5rem;
  }

  form.reply-form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    background: #164d1b;
    padding: 1rem;
    border-radius: var(--border-radius);
    box-shadow: 0 6px 20px rgba(46,125,50,0.4);
  }

  form.reply-form textarea {
    width: 100%;
    min-height: 70px;
    border-radius: 25px;
    border: none;
    padding: 0.8rem 1.2rem;
    font-size: 1rem;
    font-family: inherit;
    resize: vertical;
    transition: box-shadow var(--transition);
  }

  form.reply-form textarea:focus {
    outline: none;
    box-shadow: 0 0 10px var(--color-accent);
    background-color: #1f5924;
    color: var(--text-light);
  }

  form.reply-form input[type="file"] {
    border-radius: var(--border-radius);
    padding: 0.4rem 0.6rem;
    background-color: var(--btn-bg);
    color: var(--text-light);
    cursor: pointer;
    border: none;
    max-width: 180px;
    transition: background-color var(--transition);
  }

  form.reply-form input[type="file"]:hover {
    background-color: var(--btn-hover);
  }

  form.reply-form button {
    background-color: var(--btn-bg);
    color: var(--text-light);
    border: none;
    border-radius: 30px;
    font-weight: 700;
    font-size: 1.1rem;
    padding: 0.8rem 0;
    cursor: pointer;
    box-shadow: 0 6px 18px rgba(46,125,50,0.6);
    transition: background-color var(--transition), transform var(--transition);
  }

  form.reply-form button:hover {
    background-color: var(--btn-hover);
    transform: translateY(-2px);
  }

  /* Replies */
  .reply {
    background: #164d1b;
    border-radius: var(--border-radius);
    padding: 1rem 1.5rem;
    margin-top: 1.5rem;
    box-shadow: 0 6px 20px rgba(46,125,50,0.3);
    transition: box-shadow var(--transition);
  }

  .reply:hover {
    box-shadow: 0 8px 30px rgba(46,125,50,0.6);
  }

  .reply .post-header {
    font-weight: 700;
    font-size: 1.1rem;
    color: var(--color-accent);
    margin-bottom: 0.5rem;
  }

  .reply .post-content {
    color: var(--text-light);
    font-size: 1rem;
    margin-bottom: 0.5rem;
  }

  .reply .post-content a {
    color: var(--btn-hover);
    text-decoration: underline;
  }

  .reply .post-footer {
    font-size: 0.8rem;
    color: var(--text-muted);
    text-align: right;
  }

  /* Reply Actions */
  .reply-actions {
    margin-top: 0.7rem;
    text-align: right;
  }

  .reply-actions a {
    color: var(--btn-hover);
    text-decoration: none;
    margin-left: 1rem;
    font-size: 0.9rem;
    font-weight: 600;
    transition: color var(--transition);
  }

  .reply-actions a:hover {
    color: var(--color-accent);
  }

  .reply-actions a.delete {
    color: #e57373;
  }

  .reply-actions a.delete:hover {
    color: #ef5350;
  }

  /* Edit reply form */
  .edit-reply-form {
    background: #205927;
    padding: 1rem 1.5rem;
    border-radius: var(--border-radius);
    margin-top: 1rem;
    box-shadow: 0 6px 20px rgba(46,125,50,0.4);
  }

  .edit-reply-form textarea {
    width: 100%;
    min-height: 70px;
    border-radius: 25px;
    border: none;
    padding: 0.8rem 1.2rem;
    font-size: 1rem;
    font-family: inherit;
    resize: vertical;
    margin-bottom: 0.8rem;
  }

  .edit-reply-form input[type="file"] {
    border-radius: var(--border-radius);
    padding: 0.4rem 0.6rem;
    background-color: var(--btn-bg);
    color: var(--text-light);
    cursor: pointer;
    border: none;
    max-width: 180px;
    margin-bottom: 1rem;
  }

  .edit-reply-form button {
    background-color: var(--btn-bg);
    color: var(--text-light);
    border: none;
    border-radius: 30px;
    font-weight: 700;
    font-size: 1.1rem;
    padding: 0.7rem 0;
    cursor: pointer;
    box-shadow: 0 6px 18px rgba(46,125,50,0.6);
    transition: background-color var(--transition), transform var(--transition);
  }

  .edit-reply-form button:hover {
    background-color: var(--btn-hover);
    transform: translateY(-2px);
  }

  /* Responsive */
  @media (max-width: 640px) {
    body {
      padding: 1rem;
    }
    nav {
      justify-content: center;
      gap: 1rem;
    }
    .post, .reply, form.reply-form, .edit-reply-form {
      padding: 1rem;
    }
  }
</style>

</head>
<body>

<nav>
    <a href="agri_officer_dashboard.php">Dashboard</a>
    <a href="home.php">Logout</a>
</nav>

<div class="container">
    <h2>Farmer Posts</h2>

    <?php while ($post = $posts_result->fetch_assoc()): ?>
        <div class="post" id="post-<?= htmlspecialchars($post['id']) ?>">
            <div class="post-header">
                Posted by: <?= htmlspecialchars($post['farmer_name']) ?>
            </div>
            <div class="post-content">
                <?= nl2br(htmlspecialchars($post['content'])) ?>
                <?php if ($post['attachment']): ?>
                    <div>
                        <a href="<?= htmlspecialchars($post['attachment']) ?>" target="_blank" rel="noopener noreferrer">View Attachment</a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="post-footer">
                Posted on: <?= htmlspecialchars($post['created_at']) ?>
            </div>

            <div class="reply-section">

                <?php 
                // If we are editing a reply, show the edit form here
                if (isset($_GET['edit_reply_id']) && intval($_GET['edit_reply_id']) && $_GET['edit_reply_id'] == $post['id']):
                    $edit_reply_id = intval($_GET['edit_reply_id']);
                    // Fetch reply content for editing
                    $stmt_edit_fetch = $conn->prepare("SELECT content, attachment FROM replies WHERE id = ? AND user_id = ?");
                    $stmt_edit_fetch->bind_param("ii", $edit_reply_id, $agri_officer_id);
                    $stmt_edit_fetch->execute();
                    $edit_reply = $stmt_edit_fetch->get_result()->fetch_assoc();
                    $stmt_edit_fetch->close();
                ?>
                    <form method="POST" enctype="multipart/form-data" class="edit-reply-form">
                        <textarea name="edited_reply_content" required><?= htmlspecialchars($edit_reply['content'] ?? '') ?></textarea>
                        <input type="file" name="attachment" accept="image/*,video/*,audio/*" />
                        <button type="submit">Update Reply</button>
                        <a href="agri_post.php" style="margin-left:1rem; color: var(--color-accent); font-weight: 600;">Cancel</a>
                    </form>
                <?php else: ?>
                
                <!-- Reply form -->
                <form method="POST" enctype="multipart/form-data" class="reply-form" aria-label="Reply form for post <?= htmlspecialchars($post['id']) ?>">
                    <textarea name="reply_content" placeholder="Write a reply..." rows="3" required></textarea>
                    <input type="file" name="attachment" accept="image/*,video/*,audio/*" />
                    <input type="hidden" name="post_id" value="<?= htmlspecialchars($post['id']) ?>">
                    <button type="submit">Reply</button>
                </form>
                
                <?php endif; ?>

                <?php
                // Fetch replies for this post
                $stmt_replies = $conn->prepare("SELECT replies.*, CONCAT(agri_officers.first_name, ' ', agri_officers.last_name) AS officer_name 
                                                FROM replies 
                                                JOIN agri_officers ON replies.user_id = agri_officers.id
                                                WHERE replies.post_id = ?
                                                ORDER BY replies.created_at ASC");
                $stmt_replies->bind_param("i", $post['id']);
                $stmt_replies->execute();
                $replies_result = $stmt_replies->get_result();
                ?>

                <?php while ($reply = $replies_result->fetch_assoc()): ?>
                    <div class="reply">
                        <div class="post-header">
                            Reply by: <?= htmlspecialchars($reply['officer_name']) ?>
                        </div>
                        <div class="post-content">
                            <?= nl2br(htmlspecialchars($reply['content'])) ?>
                            <?php if ($reply['attachment']): ?>
                                <div>
                                    <a href="<?= htmlspecialchars($reply['attachment']) ?>" target="_blank" rel="noopener noreferrer">View Attachment</a>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="post-footer">
                            Posted on: <?= htmlspecialchars($reply['created_at']) ?>
                        </div>

                        <?php if ($reply['user_id'] == $agri_officer_id): ?>
                            <div class="reply-actions">
                                <a href="agri_post.php?edit_reply_id=<?= $reply['id'] ?>">Edit</a>
                                <a href="agri_post.php?delete_reply_id=<?= $reply['id'] ?>" onclick="return confirm('Are you sure you want to delete this reply?')"
                                   class="delete">Delete</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>
