<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['farmer_id'])) {
    die("Farmer ID not found in session. Please log in again.");
}

$farmer_id = $_SESSION['farmer_id'];

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'farmer') {
    echo "You are not authorized to view this page.";
    exit;
}

$tutorial_id = (int)($_GET['id'] ?? 0);

// Fetch tutorial details securely
$stmt = $conn->prepare("SELECT * FROM tutorials WHERE id = ?");
$stmt->bind_param("i", $tutorial_id);
$stmt->execute();
$tutorial = $stmt->get_result()->fetch_assoc();

// Handle new query submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['query_submit'])) {
    $query = trim($_POST['query']);
    if ($query !== '') {
        $stmt = $conn->prepare("INSERT INTO tutorial_queries (tutorial_id, farmer_id, query, status) VALUES (?, ?, ?, 'Pending')");
        $stmt->bind_param("iis", $tutorial_id, $farmer_id, $query);
        $stmt->execute();
    }
    header("Location: tutorial_details.php?id=$tutorial_id");
    exit;
}

// Handle query update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['query_update'])) {
    $query_id = (int)$_POST['query_id'];
    $query = trim($_POST['query']);
    if ($query !== '') {
        $stmt = $conn->prepare("UPDATE tutorial_queries SET query = ? WHERE id = ? AND farmer_id = ?");
        $stmt->bind_param("sii", $query, $query_id, $farmer_id);
        $stmt->execute();
    }
    header("Location: tutorial_details.php?id=$tutorial_id");
    exit;
}

// Get all queries with farmer details
$queries = [];
$qStmt = $conn->prepare("SELECT tq.id, tq.query, tq.created_at, tq.farmer_id, f.first_name, f.last_name
                         FROM tutorial_queries tq
                         JOIN farmers f ON tq.farmer_id = f.id
                         WHERE tq.tutorial_id = ?
                         ORDER BY tq.created_at DESC");
$qStmt->bind_param("i", $tutorial_id);
$qStmt->execute();
$qResult = $qStmt->get_result();

while ($query = $qResult->fetch_assoc()) {
    $query_id = $query['id'];
    $replies = [];
    $rStmt = $conn->prepare("SELECT * FROM tutorial_replies WHERE query_id = ?");
    $rStmt->bind_param("i", $query_id);
    $rStmt->execute();
    $rResult = $rStmt->get_result();
    while ($reply = $rResult->fetch_assoc()) {
        $replies[] = $reply;
    }
    $query['replies'] = $replies;
    $queries[] = $query;
}

// Function to render media (supports YouTube, video, image)
function renderMedia($media) {
    if (empty($media)) return '';

    // YouTube full URL or short youtu.be link
    if (filter_var($media, FILTER_VALIDATE_URL)) {
        if (strpos($media, 'youtube.com') !== false || strpos($media, 'youtu.be') !== false) {
            $youtubeId = null;

            // Standard YouTube link
            if (strpos($media, 'youtube.com') !== false) {
                parse_str(parse_url($media, PHP_URL_QUERY), $params);
                $youtubeId = $params['v'] ?? null;
            }

            // Short YouTube link
            if (!$youtubeId && strpos($media, 'youtu.be') !== false) {
                $youtubeId = basename(parse_url($media, PHP_URL_PATH));
            }

            if ($youtubeId) {
                return '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . htmlspecialchars($youtubeId) . '" frameborder="0" allowfullscreen></iframe>';
            }
        }
    }

    // MP4 video
    if (strpos($media, '.mp4') !== false) {
        return '<video controls src="' . htmlspecialchars($media) . '" style="max-width:100%;border:2px solid #80e050;border-radius:8px;"></video>';
    }

    // Image
    return '<img src="' . htmlspecialchars($media) . '" alt="Media" style="max-width:100%;border:2px solid #80e050;border-radius:8px;" />';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= htmlspecialchars($tutorial['title'] ?? 'Tutorial Details') ?></title>
    <style>
        body {
            background: #121212;
            color: #b3ffb3;
            font-family: 'Poppins', sans-serif;
            margin: 0; padding: 0;
        }
        nav {
            background: #004d00;
            padding: 20px;
            text-align: center;
            border-bottom: 3px solid #008000;
            user-select: none;
        }
        nav a {
            color: #b3ffb3;
            margin: 0 20px;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }
        nav a:hover, nav a:focus {
            color: #80e050;
            outline: none;
        }
        .container {
            max-width: 1000px;
            margin: 30px auto;
            background: #1a2d1a;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 128, 0, 0.5);
        }
        h2 {
            color: #80e050;
            font-size: 24px;
            margin-bottom: 20px;
        }
        textarea {
            width: 100%;
            background: #263d26;
            color: #b3ffb3;
            border: 1px solid #80e050;
            border-radius: 5px;
            padding: 10px;
            resize: vertical;
            font-family: inherit;
            font-size: 1rem;
        }
        .btn {
            background: #33cc33;
            color: white;
            padding: 10px 20px;
            border: none;
            margin-top: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
        }
        .btn:hover, .btn:focus {
            background: #28a428;
        }
        .query-box {
            background: #1b3a1b;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            border: 1px solid #4d704d;
        }
        .reply {
            background: #1e682e;
            padding: 10px;
            border-left: 5px solid #66ff66;
            margin-top: 10px;
            border-radius: 8px;
        }
        .edit-form {
            margin-top: 10px;
            background: #1a3d1a;
            padding: 20px;
            border-radius: 8px;
        }
        .edit-form .btn {
            background: #007700;
        }
        .edit-form .btn:hover, .edit-form .btn:focus {
            background: #005500;
        }
    </style>
</head>
<body>

<nav>
    <a href="farmer_dashboard.php">Dashboard</a>

</nav>

<div class="container">
    <h2><?= htmlspecialchars($tutorial['title'] ?? 'Tutorial Details') ?></h2>
    <?php if (!empty($tutorial['media'])): ?>
        <?= renderMedia($tutorial['media']); ?>
    <?php endif; ?>
    <p><?= nl2br(htmlspecialchars($tutorial['description'] ?? '')) ?></p>

    <hr style="border-color: #4d704d;">

    <h3>Submit Your Query</h3>
    <form method="POST">
        <textarea name="query" required placeholder="Enter your question about the tutorial..."></textarea>
        <input type="submit" name="query_submit" value="Submit Query" class="btn" />
    </form>

    <hr style="border-color: #4d704d;">

    <h3>All Queries</h3>
    <?php if (empty($queries)): ?>
        <p>No queries yet. Be the first to ask!</p>
    <?php endif; ?>
    <?php foreach ($queries as $query): ?>
        <div class="query-box">
            <p><strong>Query from <?= htmlspecialchars($query['first_name'] . ' ' . $query['last_name']) ?>:</strong> <?= nl2br(htmlspecialchars($query['query'])) ?></p>
            <?php if ($query['farmer_id'] == $farmer_id): ?>
                <form method="POST" class="edit-form">
                    <input type="hidden" name="query_id" value="<?= (int)$query['id'] ?>">
                    <textarea name="query" required><?= htmlspecialchars($query['query']) ?></textarea>
                    <input type="submit" name="query_update" value="Update" class="btn" />
                </form>
            <?php endif; ?>

            <?php if (!empty($query['replies'])): ?>
                <div>
                    <strong>Replies from Agri Officer:</strong>
                    <?php foreach ($query['replies'] as $reply): ?>
                        <div class="reply">
                            <?= nl2br(htmlspecialchars($reply['reply'])) ?><br>
                            <?php if (!empty($reply['media'])): ?>
                                <?= renderMedia($reply['media']); ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
