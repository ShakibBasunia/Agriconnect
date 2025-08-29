<?php
session_start();
include 'db_connection.php';

// Ensure session variable 'role' is set and is 'agri_officer'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'agri_officer') {
    echo "You are not authorized to upload tutorials.";
    exit;
}

// Handling tutorial upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['media'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $media = $_FILES['media'];

    // Handling media upload
    $targetDir = "uploads/tutorials/";
    $targetFile = $targetDir . basename($media["name"]);
    move_uploaded_file($media["tmp_name"], $targetFile);

    // Insert into the tutorials table
    $agriOfficerId = $_SESSION['agri_officer_id'];  // Assuming session stores Agri Officer ID
    $sql = "INSERT INTO tutorials (title, description, media, agri_officer_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $title, $description, $targetFile, $agriOfficerId);
    $stmt->execute();

    header("Location: agriofficer_tutorialmanage.php");  // Redirect back to the Agri Officer portal
    exit;
}

// Fetch tutorials uploaded by this officer
$agriOfficerId = $_SESSION['agri_officer_id'];
$tutorials = $conn->query("SELECT * FROM tutorials WHERE agri_officer_id = $agriOfficerId ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Upload Tutorial</title>
<style>
/* General Styles */
body {
    margin: 0;
    padding: 0;
    font-family: 'Poppins', sans-serif;
    background: #121212;
    color: #fff;
}

/* Navbar */
nav {
    background: #004d7a;
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    box-shadow: 0 4px 8px rgba(0,0,0,0.5);
}
nav a {
    color: #fff;
    margin-left: 20px;
    text-decoration: none;
    font-size: 18px;
    transition: color 0.3s ease, transform 0.3s ease;
}
nav a:hover { color: #80ff80; transform: translateY(-3px); }

/* Form */
.container {
    max-width: 700px;
    margin: 20px auto;
    padding: 20px;
    background: #1e1e1e;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.5);
    text-align: center;
}
.container h2 {
    margin-bottom: 20px;
    color: #80ff80;
}
input[type="text"], textarea, input[type="file"] {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border-radius: 8px;
    background: #333;
    color: #fff;
    border: none;
    transition: 0.3s;
}
input[type="text"]:focus, textarea:focus, input[type="file"]:focus {
    background: #444;
    transform: scale(1.02);
}
input[type="submit"] {
    background: linear-gradient(45deg,#80ff80,#2e7d32);
    color: #121212;
    border: none;
    padding: 12px 25px;
    font-size: 16px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    transition: transform 0.3s, box-shadow 0.3s;
}
input[type="submit"]:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 10px 20px rgba(128,255,128,0.6);
}

/* Tutorial Cards */
.tutorial-card {
    background: #1e1e1e;
    border: 1px solid #2e7d32;
    border-radius: 12px;
    padding: 20px;
    margin: 15px 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    transition: 0.3s;
}
.tutorial-card:hover {
    background: #14d11dbd;
    color: #121212;
}
.tutorial-info {
    max-width: 70%;
}
.tutorial-info h3 { margin: 0 0 10px 0; }
.tutorial-info p { margin: 0; color: #cfead1; }
.tutorial-actions a {
    text-decoration: none;
    margin-left: 10px;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: bold;
    transition: 0.3s;
}
.update-btn {
    background: #ffcc00;
    color: #121212;
}
.update-btn:hover {
    background: #e6b800;
    transform: scale(1.05);
}
.delete-btn {
    background: #c51111ff;
    color: #fff;
}
.delete-btn:hover {
    background: #cc0000;
    transform: scale(1.05);
}

/* Footer */
footer {
    background: #004d7a;
    padding: 10px;
    text-align: center;
    color: #fff;
    margin-top: 30px;
    font-size: 14px;
}
footer a { color: #fff; text-decoration: none; }
footer a:hover { color: #80ff80; }
</style>
</head>
<body>

<nav>
    <div><strong>AgriConnect - Agri Officer</strong></div>
    <div><a href="agri_officer_dashboard.php">Dashboard</a><a href="logout.php">Logout</a></div>
</nav>

<div class="container">
    <h2>Upload Tutorial</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Tutorial Title" required>
        <textarea name="description" placeholder="Tutorial Description" required></textarea>
        <input type="file" name="media" accept="video/*,image/*" required>
        <input type="submit" value="Upload Tutorial">
    </form>
</div>

<div class="container">
    <h2>Your Tutorials</h2>
    <?php if($tutorials->num_rows > 0): ?>
        <?php while($row = $tutorials->fetch_assoc()): ?>
            <div class="tutorial-card">
                <div class="tutorial-info">
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                    <small>File: <?php echo htmlspecialchars(basename($row['media'])); ?></small>
                </div>
                <div class="tutorial-actions">
                    <a href="update_tutorial.php?id=<?php echo $row['id']; ?>" class="update-btn">Update</a>
                    <a href="delete_tutorial.php?id=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?');">Delete</a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No tutorials uploaded yet.</p>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; 2025 AgriConnect. All rights reserved.</p>
</footer>

</body>
</html>
