<?php
session_start();
include 'db_connection.php';

// Check if the user is an Agri Officer
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'agri_officer') {
    echo "You are not authorized to reply to queries.";
    exit;
}

// Check if form is submitted with required fields
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['query_id']) && isset($_POST['query'])) {
    $queryId = intval($_POST['query_id']);
    $replyText = trim($_POST['query']);
    $agriOfficerId = $_SESSION['agri_officer_id']; // Make sure this session variable is set when logging in

    // Handle media upload if provided
    $targetFile = null;
    if (isset($_FILES['media']) && $_FILES['media']['error'] === 0) {
        $media = $_FILES['media'];
        $uploadDir = "uploads/replies/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create directory if not exists
        }
        $targetFile = $uploadDir . basename($media["name"]);
        move_uploaded_file($media["tmp_name"], $targetFile);
    }

    // Prepare and execute insert statement
    $stmt = $conn->prepare("INSERT INTO tutorial_replies (query_id, agri_officer_id, query, media) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $queryId, $agriOfficerId, $replyText, $targetFile);
    
    if ($stmt->execute()) {
        // Redirect back to a relevant page, e.g. officer's tutorial page
        header("Location: agriofficer_tutorials.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
