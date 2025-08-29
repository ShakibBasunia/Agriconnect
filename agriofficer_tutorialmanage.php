<?php
session_start();
include 'db_connection.php';

// Ensure session variable 'role' is set and is 'agri_officer'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'agri_officer') {
    echo "You are not authorized to view this page.";
    exit;
}

// Fetch tutorials uploaded by the Agri Officer
$agriOfficerId = $_SESSION['agri_officer_id'];
$result = $conn->query("SELECT * FROM tutorials WHERE agri_officer_id = $agriOfficerId");

// Handle tutorial deletion
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $conn->query("DELETE FROM tutorials WHERE id = $deleteId");
    header("Location: agriofficer_manage_tutorial.php"); // Redirect after deletion
    exit;
}

// Handle tutorial update
if (isset($_POST['update_tutorial'])) {
    $tutorialId = $_POST['tutorial_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $media = $_FILES['media'];

    // Handle media upload (optional)
    if ($media['name']) {
        $targetDir = "uploads/tutorials/";
        $targetFile = $targetDir . basename($media["name"]);
        move_uploaded_file($media["tmp_name"], $targetFile);
        $mediaFile = $targetFile;
    } else {
        $mediaFile = $_POST['existing_media']; // Use existing media if no new file uploaded
    }

    // Update tutorial information in database
    $sql = "UPDATE tutorials SET title = ?, description = ?, media = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $title, $description, $mediaFile, $tutorialId);
    $stmt->execute();
    
    header("Location: agriofficer_manage_tutorial.php"); // Redirect after update
    exit;
}

// Fetch farmer queries for each tutorial
$tutorials = [];
while ($tutorial = $result->fetch_assoc()) {
    $tutorialId = $tutorial['id'];
    $queriesResult = $conn->query("SELECT * FROM tutorial_queries WHERE tutorial_id = $tutorialId");
    $queries = [];
    while ($query = $queriesResult->fetch_assoc()) {
        // Get replies for each query
        $queryId = $query['id'];
        $repliesResult = $conn->query("SELECT * FROM query_replies WHERE query_id = $queryId");
        $replies = [];
        while ($reply = $repliesResult->fetch_assoc()) {
            $replies[] = $reply;
        }
        $query['replies'] = $replies;
        $queries[] = $query;
    }
    $tutorial['queries'] = $queries;
    $tutorials[] = $tutorial;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tutorials - AgriConnect</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #121212;
            color: #fff;
            margin: 0;
            padding: 0;
        }

        nav {
            background: #004d7a;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
        }

        nav a {
            color: #fff;
            margin-left: 20px;
            text-decoration: none;
            font-size: 18px;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        nav a:hover {
            color: #80ff80;
            transform: translateY(-3px);
        }

        .tutorial-container {
            margin: 30px auto;
            padding: 40px;
            max-width: 1000px;
            background: #1e1e1e;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            text-align: center;
        }

        .tutorial-container h2 {
            color: #80ff80;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border: 1px solid #333;
            text-align: center;
        }

        th {
            background: #004d7a;
        }

        td {
            background: #333;
        }

        .button {
            padding: 8px 20px;
            background: #006f9e;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .button:hover {
            background: #008fb3;
            transform: translateY(-3px);
        }

        .reply-form textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            background: #333;
            color: #fff;
            border: 1px solid #444;
            border-radius: 5px;
        }

        .reply-form input[type="submit"] {
            background: #80ff80;
            border: none;
            cursor: pointer;
        }

        .reply-form input[type="submit"]:hover {
            background: #66cc66;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav>
    <div><strong>AgriConnect - Agri Officer Dashboard</strong></div>
    <div><a href="agriofficer_dashboard.php">Dashboard</a><a href="logout.php">Logout</a></div>
</nav>

<!-- Manage Tutorials Section -->
<div class="tutorial-container">
    <h2>Your Tutorials</h2>
    <?php if (count($tutorials) == 0): ?>
        <p>No tutorials uploaded yet.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Media</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tutorials as $tutorial): ?>
                    <tr>
                        <td><?= $tutorial['title'] ?></td>
                        <td><?= $tutorial['description'] ?></td>
                        <td><video src="<?= $tutorial['media'] ?>" width="100" controls></video></td>
                        <td>
                            <!-- Edit Button -->
                            <button class="button" onclick="showUpdateForm(<?= $tutorial['id'] ?>, '<?= $tutorial['title'] ?>', '<?= $tutorial['description'] ?>', '<?= $tutorial['media'] ?>')">Edit</button>
                            
                            <!-- Delete Button -->
                            <a href="?delete_id=<?= $tutorial['id'] ?>" class="button" onclick="return confirm('Are you sure you want to delete this tutorial?')">Delete</a>
                        </td>
                    </tr>
                    <!-- Display Queries and Replies -->
                    <tr>
                        <td colspan="4">
                            <h4>Farmer Queries:</h4>
                            <table>
                                <tr>
                                    <th>Query</th>
                                    <th>Status</th>
                                    <th>Reply</th>
                                    <th>Actions</th>
                                </tr>
                                <?php foreach ($tutorial['queries'] as $query): ?>
                                    <tr>
                                        <td><?= $query['query'] ?></td>
                                        <td><?= $query['status'] ?></td>
                                        <td>
                                            <?php if (!empty($query['replies'])): ?>
                                                <ul>
                                                    <?php foreach ($query['replies'] as $reply): ?>
                                                        <li>
                                                            <p><?= $reply['reply'] ?></p>
                                                            <a href="?edit_reply_id=<?= $reply['id'] ?>">Edit</a> | <a href="?delete_reply_id=<?= $reply['id'] ?>" onclick="return confirm('Are you sure you want to delete this reply?')">Delete</a>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else: ?>
                                                <p>No reply yet.</p>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <!-- Reply Form -->
                                            <form method="POST" class="reply-form">
                                                <textarea name="reply" placeholder="Reply to query" required></textarea>
                                                <input type="hidden" name="query_id" value="<?= $query['id'] ?>">
                                                <input type="submit" value="Reply">
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script>
    // Show update form with current tutorial data
    function showUpdateForm(id, title, description, media) {
        document.getElementById('updateForm').style.display = 'block';
        document.getElementById('tutorial_id').value = id;
        document.getElementById('title').value = title;
        document.getElementById('description').value = description;
        document.getElementById('existing_media').value = media;
    }
</script>

</body>
</html>
