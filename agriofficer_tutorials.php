<?php
session_start();
include 'db_connection.php';

// Check authorization
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'agri_officer') {
    echo "You are not authorized to access this page.";
    exit;
}

$agriOfficerId = $_SESSION['agri_officer_id'];

// --- Handle Upload Tutorial ---
if (isset($_POST['upload_tutorial'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $mediaPath = '';

    // Handle file upload
    if (!empty($_FILES['media']['name'])) {
        $targetDir = "uploads/";
        $fileName = basename($_FILES['media']['name']);
        $targetFile = $targetDir . time() . "_" . $fileName;
        move_uploaded_file($_FILES['media']['tmp_name'], $targetFile);
        $mediaPath = $targetFile;
    }

    // Handle YouTube URL if no file
    if (empty($mediaPath) && !empty($_POST['youtube_url'])) {
        $mediaPath = $_POST['youtube_url'];
    }

    $stmt = $conn->prepare("INSERT INTO tutorials (agri_officer_id, title, description, media) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $agriOfficerId, $title, $description, $mediaPath);
    $stmt->execute();
    $stmt->close();
}

// --- Handle Update Tutorial ---
if (isset($_POST['update_tutorial'])) {
    $tutorialId = $_POST['tutorial_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $mediaPath = $_POST['existing_media'];

    // Replace file if new uploaded
    if (!empty($_FILES['media']['name'])) {
        $targetDir = "uploads/";
        $fileName = basename($_FILES['media']['name']);
        $targetFile = $targetDir . time() . "_" . $fileName;
        move_uploaded_file($_FILES['media']['tmp_name'], $targetFile);
        $mediaPath = $targetFile;
    }

    // Replace with YouTube URL if provided
    if (!empty($_POST['youtube_url'])) {
        $mediaPath = $_POST['youtube_url'];
    }

    $stmt = $conn->prepare("UPDATE tutorials SET title=?, description=?, media=? WHERE id=? AND agri_officer_id=?");
    $stmt->bind_param("sssii", $title, $description, $mediaPath, $tutorialId, $agriOfficerId);
    $stmt->execute();
    $stmt->close();
}

// --- Handle Delete Tutorial ---
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM tutorials WHERE id=? AND agri_officer_id=?");
    $stmt->bind_param("ii", $deleteId, $agriOfficerId);
    $stmt->execute();
    $stmt->close();
}

// --- Handle Reply ---
if (isset($_POST['reply_submit'])) {
    $queryId = $_POST['query_id'];
    $replyText = $_POST['reply'];
    $mediaPath = '';

    if (!empty($_FILES['reply_media']['name'])) {
        $targetDir = "uploads/";
        $fileName = basename($_FILES['reply_media']['name']);
        $targetFile = $targetDir . time() . "_" . $fileName;
        move_uploaded_file($_FILES['reply_media']['tmp_name'], $targetFile);
        $mediaPath = $targetFile;
    }

    if (!empty($_POST['reply_youtube_url'])) {
        $mediaPath = $_POST['reply_youtube_url'];
    }

    $stmt = $conn->prepare("INSERT INTO tutorial_replies (query_id, reply, media) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $queryId, $replyText, $mediaPath);
    $stmt->execute();
    $stmt->close();
}

// --- Handle Reply Update ---
if (isset($_POST['edit_reply_submit'])) {
    $replyId = $_POST['reply_id'];
    $replyText = $_POST['updated_reply'];
    $mediaPath = $_POST['existing_media'];

    if (!empty($_FILES['updated_reply_media']['name'])) {
        $targetDir = "uploads/";
        $fileName = basename($_FILES['updated_reply_media']['name']);
        $targetFile = $targetDir . time() . "_" . $fileName;
        move_uploaded_file($_FILES['updated_reply_media']['tmp_name'], $targetFile);
        $mediaPath = $targetFile;
    }

    if (!empty($_POST['updated_reply_youtube_url'])) {
        $mediaPath = $_POST['updated_reply_youtube_url'];
    }

    $stmt = $conn->prepare("UPDATE tutorial_replies SET reply=?, media=? WHERE id=?");
    $stmt->bind_param("ssi", $replyText, $mediaPath, $replyId);
    $stmt->execute();
    $stmt->close();
}

// --- Handle Delete Reply ---
if (isset($_GET['delete_reply_id'])) {
    $deleteReplyId = $_GET['delete_reply_id'];
    $stmt = $conn->prepare("DELETE FROM tutorial_replies WHERE id=?");
    $stmt->bind_param("i", $deleteReplyId);
    $stmt->execute();
    $stmt->close();
}

// --- Fetch Tutorials + Queries + Replies ---
$tutorials = [];
$stmt = $conn->prepare("SELECT * FROM tutorials WHERE agri_officer_id=?");
$stmt->bind_param("i", $agriOfficerId);
$stmt->execute();
$result = $stmt->get_result();

while ($tutorial = $result->fetch_assoc()) {
    $tutorialId = $tutorial['id'];
    $queries = [];
    $qStmt = $conn->prepare("SELECT * FROM tutorial_queries WHERE tutorial_id=?");
    $qStmt->bind_param("i", $tutorialId);
    $qStmt->execute();
    $qResult = $qStmt->get_result();

    while ($query = $qResult->fetch_assoc()) {
        $queryId = $query['id'];
        $replies = [];
        $rStmt = $conn->prepare("SELECT * FROM tutorial_replies WHERE query_id=?");
        $rStmt->bind_param("i", $queryId);
        $rStmt->execute();
        $rResult = $rStmt->get_result();
        while ($reply = $rResult->fetch_assoc()) {
            $replies[] = $reply;
        }
        $query['replies'] = $replies;
        $queries[] = $query;
    }
    $tutorial['queries'] = $queries;
    $tutorials[] = $tutorial;
    $qStmt->close();
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Agri Officer - Manage Tutorials</title>
<style>
:root{
  --bg-1:#07130b; --bg-2:#0b2a12; --card:#07120f; --accent:#80ff80; --accent-dark:#2e7d32; --muted:#cfead1; --glass: rgba(255,255,255,0.03); --card-radius:16px; --gap:28px;
}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Poppins',sans-serif;color:var(--muted);background:linear-gradient(180deg,var(--bg-1),var(--bg-2));padding-bottom:60px;}
nav{background:linear-gradient(90deg,var(--bg-1),var(--bg-2));padding:18px 32px;display:flex;justify-content:space-between;align-items:center;}
nav div:first-child{font-weight:700;font-size:1.3rem;color:var(--muted);}
nav div:last-child a{color:var(--muted);text-decoration:none;margin-left:20px;font-weight:600;padding:8px 12px;border-radius:10px;transition:all 0.3s;}
nav div:last-child a:hover{background:var(--glass);color:var(--accent);}
.container{max-width:800px;margin:30px auto;padding:30px;background-color:rgba(0,0,0,0.85);border-radius:var(--card-radius);box-shadow:0 6px 30px rgba(0,0,0,0.4);}
h2{text-align:center;color:var(--accent);margin-bottom:20px;}
input,textarea{width:100%;padding:10px;margin-top:8px;border-radius:6px;border:none;background:#262626;color:var(--muted);}
input:focus,textarea:focus{outline:none;border:2px solid var(--accent);background:#333;}
input[type="submit"],.button{background:linear-gradient(90deg,var(--accent-dark),#1E90FF);color:#06110a;border:none;border-radius:6px;padding:12px 20px;margin-top:10px;cursor:pointer;font-weight:700;}
input[type="submit"]:hover,.button:hover{transform:scale(1.05);}
table{width:100%;border-collapse:collapse;margin-top:20px;}
th,td{padding:10px;border:1px solid #444;vertical-align:top;}
th{background:linear-gradient(90deg,var(--accent-dark),#1E90FF);color:#06110a;}
td{background-color:rgba(0,0,0,0.7);}
video,img,iframe{max-width:150px;border-radius:6px;box-shadow:0 2px 6px rgba(0,0,0,0.5);}
.reply-box{margin-top:10px;background:#333;padding:10px;border-radius:5px;}
</style>
</head>
<body>

<nav>
<div>AgriConnect - Agri Officer</div>
<div>
<a href="agri_officer_dashboard.php">Dashboard</a>
<a href="agriofficerlogout.php">Logout</a>
</div>
</nav>

<div class="container">
<h2>Upload New Tutorial</h2>
<form method="POST" enctype="multipart/form-data">
<input type="text" name="title" placeholder="Tutorial Title" required>
<textarea name="description" placeholder="Tutorial Description" required></textarea>
<input type="file" name="media" accept="image/*,video/*">
<label for="youtube_url">Or YouTube URL (optional):</label>
<input type="url" id="youtube_url" name="youtube_url" placeholder="https://www.youtube.com/watch?v=...">
<input type="submit" name="upload_tutorial" value="Upload Tutorial">
</form>
</div>

<div class="container">
<h2>Your Tutorials</h2>
<?php if(count($tutorials)==0): ?>
<p style="text-align:center;">No tutorials found.</p>
<?php else: ?>
<table>
<thead><tr><th>Title</th><th>Description</th><th>Media</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($tutorials as $tutorial): ?>
<tr>
<td><?= htmlspecialchars($tutorial['title']) ?></td>
<td><?= nl2br(htmlspecialchars($tutorial['description'])) ?></td>
<td>
<?php 
$media=$tutorial['media'];
if(filter_var($media,FILTER_VALIDATE_URL)&&strpos($media,'youtube.com')!==false){
parse_str(parse_url($media,PHP_URL_QUERY),$youtubeParams);
$youtubeId=$youtubeParams['v']??null;
if($youtubeId){echo '<iframe width="150" height="100" src="https://www.youtube.com/embed/'.htmlspecialchars($youtubeId).'" frameborder="0" allowfullscreen></iframe>';}
else{echo 'Invalid YouTube URL';}
}elseif(strpos($media,'.mp4')!==false){echo '<video src="'.htmlspecialchars($media).'" controls></video>';}
elseif($media){echo '<img src="'.htmlspecialchars($media).'" alt="Media">';}
else{echo 'No media';}
?>
</td>
<td>
<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="tutorial_id" value="<?= $tutorial['id'] ?>">
<input type="hidden" name="existing_media" value="<?= htmlspecialchars($tutorial['media']) ?>">
<input type="text" name="title" value="<?= htmlspecialchars($tutorial['title']) ?>" required>
<textarea name="description" required><?= htmlspecialchars($tutorial['description']) ?></textarea>
<input type="file" name="media" accept="image/*,video/*">
<input type="url" name="youtube_url" placeholder="YouTube URL">
<input type="submit" name="update_tutorial" value="Update" class="button">
</form>
<a href="?delete_id=<?= $tutorial['id'] ?>" class="button" onclick="return confirm('Delete this tutorial?')">Delete</a>
</td>
</tr>
<?php foreach($tutorial['queries'] as $query): ?>
<tr><td colspan="4">
<p><strong>Query:</strong> <?= htmlspecialchars($query['query']) ?></p>
<form method="POST" enctype="multipart/form-data">
<textarea name="reply" placeholder="Write your reply" required></textarea>
<input type="file" name="reply_media" accept="image/*,video/*">
<input type="url" name="reply_youtube_url" placeholder="YouTube URL">
<input type="hidden" name="query_id" value="<?= $query['id'] ?>">
<input type="submit" name="reply_submit" value="Submit Reply" class="button">
</form>
<?php foreach($query['replies'] as $reply): ?>
<div class="reply-box">
<p><strong>Reply:</strong> <?= nl2br(htmlspecialchars($reply['reply'])) ?></p>
<?php 
$media=$reply['media'];
if(filter_var($media,FILTER_VALIDATE_URL)&&strpos($media,'youtube.com')!==false){
parse_str(parse_url($media,PHP_URL_QUERY),$youtubeParams);
$youtubeId=$youtubeParams['v']??null;
if($youtubeId){echo '<iframe width="150" height="100" src="https://www.youtube.com/embed/'.htmlspecialchars($youtubeId).'" frameborder="0" allowfullscreen></iframe>';}
}elseif(strpos($media,'.mp4')!==false){echo '<video src="'.htmlspecialchars($media).'" controls></video>';}
elseif($media){echo '<img src="'.htmlspecialchars($media).'" alt="Reply media">';}
?>
<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="reply_id" value="<?= $reply['id'] ?>">
<textarea name="updated_reply" required><?= htmlspecialchars($reply['reply']) ?></textarea>
<input type="file" name="updated_reply_media" accept="image/*,video/*">
<input type="url" name="updated_reply_youtube_url" placeholder="YouTube URL">
<input type="hidden" name="existing_media" value="<?= htmlspecialchars($reply['media']) ?>">
<input type="submit" name="edit_reply_submit" value="Update Reply" class="button">
</form>
<a href="?delete_reply_id=<?= $reply['id'] ?>" class="button" onclick="return confirm('Delete this reply?')">Delete</a>
</div>
<?php endforeach; ?>
</td></tr>
<?php endforeach; ?>
<?php endforeach; ?>
</tbody>
</table>
<?php endif; ?>
</div>
</body>
</html>
