<?php
session_start();
include 'db_connection.php';

// Check if a farmer is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'farmer') {
    echo "You are not authorized to view this page.";
    exit;
}

// Fetch tutorials from the database
$result = $conn->query("SELECT * FROM tutorials");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Portal - Tutorials</title>
    <style>
      /* General page layout */
body {
    font-family: Arial, sans-serif;
    background-color: #1a1a1a; /* Dark background */
    color: #e0ffe0; /* Light green text */
    margin: 0;
    padding: 0;
}

/* Navbar styling */
nav {
    background-color: #003300; /* Dark green */
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid #4CAF50; /* Green border for separation */
}

nav a {
    color: #fff;
    text-decoration: none;
    padding: 10px;
    transition: all 0.3s ease;
}

nav a:hover {
    background-color: #4CAF50;
    border-radius: 5px;
}

/* Tutorials list */
.tutorials-list {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    padding: 20px;
    justify-content: center;
}

/* Tutorial box with green accents */
.tutorial-box {
    width: 250px;
    padding: 15px;
    border-radius: 10px;
    background-color: #2d2d2d; /* Dark box background */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
}

.tutorial-box:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
}

.tutorial-box h3 {
    font-size: 18px;
    margin: 10px 0;
    color: #80ff80; /* Light green color for titles */
}

.tutorial-box p {
    font-size: 14px;
    color: #bbb; /* Light gray for descriptions */
}

.tutorial-box a {
    display: inline-block;
    color: #4CAF50;
    font-weight: bold;
    margin-top: 10px;
    text-decoration: none;
}

.tutorial-box a:hover {
    color: #45a049;
}

/* Preview image or video in the box */
.tutorial-preview {
    width: 100%;
    height: 150px;
    overflow: hidden;
    margin-bottom: 10px;
    border-radius: 5px;
}

.tutorial-preview img,
.tutorial-preview video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Button styling */
.btn {
    background: #33cc33; /* Bright green for buttons */
    color: white;
    padding: 10px 20px;
    border: none;
    margin-top: 10px;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
}

.btn:hover {
    background: #28a428;
}

/* Minimalistic box for sections */
.container {
    max-width: 1000px;
    margin: 30px auto;
    background: #2d2d2d;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 10px #006600; /* Green shadow */
    border: 1px solid #4CAF50; /* Green border */
}

/* General form styles */
input[type="text"],
textarea {
    width: 100%;
    background: #263d26;
    color: #e0ffe0;
    border: 1px solid #80ff80;
    border-radius: 5px;
    padding: 10px;
}

input[type="file"] {
    background: #263d26;
    color: #e0ffe0;
    border: 1px solid #80ff80;
    border-radius: 5px;
    padding: 10px;
}

input[type="submit"],
button {
    background: #33cc33;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    margin-top: 10px;
    cursor: pointer;
    transition: 0.3s;
}

input[type="submit"]:hover,
button:hover {
    background: #28a428;
}

/* Modal (edit reply) */
.edit-form {
    margin-top: 10px;
    background-color: #2d2d2d; /* Dark background for the edit form */
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Minimalistic reply box */
.reply {
    background: #336633;
    padding: 10px;
    border-left: 5px solid #66ff66; /* Bright green for left border */
    margin-top: 10px;
    border-radius: 5px;
}

    </style>
</head>
<body>

<nav>
    <div>Farmer Portal</div>
    <div><a href="logout.php">Logout</a></div>
</nav>

<div class="tutorials-list">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($tutorial = $result->fetch_assoc()): ?>
            <div class="tutorial-box" onclick="window.location.href='tutorial_details.php?id=<?= $tutorial['id'] ?>'">
                <div class="tutorial-preview">
                    <?php if (!empty($tutorial['media'])): ?>
                        <?php if (strpos($tutorial['media'], '.mp4') !== false): ?>
                            <video src="<?= $tutorial['media'] ?>" controls></video>
                        <?php else: ?>
                            <img src="<?= $tutorial['media'] ?>" alt="Tutorial Preview">
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <h3><?= $tutorial['title'] ?></h3>
                <p><?= substr($tutorial['description'], 0, 50) . '...' ?></p>
                <a href="tutorial_details.php?id=<?= $tutorial['id'] ?>">View Details</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No tutorials available yet.</p>
    <?php endif; ?>
</div>

</body>
</html>
