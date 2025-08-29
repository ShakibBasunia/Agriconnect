<?php
session_start();
include 'db_connection.php';

$announcement = '';
$announcement_id = null;
$pdf_link = '';
$success = '';
$error = '';
$pdf_path = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        // Delete announcement logic (optional, handled separately)
        // You might want to implement delete_announcement.php for this
    } else {
        $message = trim($_POST['message']);

        // Handle PDF upload
        if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['pdf_file']['tmp_name'];
            $file_name = basename($_FILES['pdf_file']['name']);
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0755);
            $target_file = $target_dir . time() . "_" . $file_name;

            if (move_uploaded_file($file_tmp, $target_file)) {
                $pdf_path = $target_file;
            }
        }

        // Check if announcement exists
        $result = $conn->query("SELECT * FROM announcements ORDER BY id DESC LIMIT 1");
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $announcement_id = $row['id'];

            if ($pdf_path) {
                $stmt = $conn->prepare("UPDATE announcements SET message = ?, pdf_path = ? WHERE id = ?");
                $stmt->bind_param("ssi", $message, $pdf_path, $announcement_id);
            } else {
                $stmt = $conn->prepare("UPDATE announcements SET message = ? WHERE id = ?");
                $stmt->bind_param("si", $message, $announcement_id);
            }

        } else {
            if ($pdf_path) {
                $stmt = $conn->prepare("INSERT INTO announcements (message, pdf_path) VALUES (?, ?)");
                $stmt->bind_param("ss", $message, $pdf_path);
            } else {
                $stmt = $conn->prepare("INSERT INTO announcements (message) VALUES (?)");
                $stmt->bind_param("s", $message);
            }
        }

        if ($stmt->execute()) {
            $success = "Announcement saved successfully.";
        } else {
            $error = "Failed to save announcement.";
        }
    }
}

// Load the current announcement
$result = $conn->query("SELECT * FROM announcements ORDER BY id DESC LIMIT 1");
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $announcement = $row['message'];
    $announcement_id = $row['id'];
    $pdf_link = $row['pdf_path'] ?? '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Upload Announcement - AgriConnect</title>
  <style>
    :root {
      --bg-1: #07130b;
      --bg-2: #0b2a12;
      --card: #07120f;
      --accent: #80ff80;
      --accent-dark: #2e7d32;
      --muted: #cfead1;
      --glass: rgba(255,255,255,0.03);
      --glass-2: rgba(255,255,255,0.02);
      --card-radius: 16px;
      --gap: 28px;
    }
    * {
      margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
    }
    html, body {
      height: 100%;
      background: radial-gradient(1000px 600px at 10% 10%, rgba(128,255,128,0.05), transparent 10%), linear-gradient(180deg,var(--bg-1), var(--bg-2) 60%);
      color: var(--muted);
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      padding-bottom: 60px;
    }
    header.site-header {
      position: relative; z-index: 10;
      padding: 18px 32px;
      display: flex; align-items: center; justify-content: space-between;
      gap: 12px;
      background: linear-gradient(90deg, var(--bg-1), var(--bg-2));
      box-shadow: 0 4px 12px rgba(0,0,0,0.7);
    }
    .logo {
      color: var(--muted);
      font-weight: 700;
      font-size: 1.25rem;
      text-decoration: none;
    }
    nav {
      display: flex; align-items: center; gap: 18px;
    }
    nav a {
      color: var(--muted);
      text-decoration: none;
      padding: 8px 12px;
      border-radius: 10px;
      font-weight: 600;
      position: relative;
      transition: all 0.3s ease;
    }
    nav a::after {
      content: "";
      position: absolute;
      left: 0;
      bottom: -5px;
      width: 0%;
      height: 3px;
      background: var(--accent);
      transition: width 0.3s ease;
      border-radius: 3px;
    }
    nav a:hover,
    nav a:focus {
      background: var(--glass);
      color: var(--accent);
      outline: none;
      transform: translateY(-2px);
    }
    nav a:hover::after,
    nav a:focus::after {
      width: 100%;
    }
    .container {
      max-width: 600px;
      margin: 100px auto;
      background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
      padding: 30px;
      border-radius: var(--card-radius);
      box-shadow: 0 8px 26px rgba(2,6,2,0.6);
      border: 1px solid rgba(255,255,255,0.03);
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: var(--accent);
      text-shadow: 0 4px 10px rgba(0,0,0,0.4);
    }
    textarea {
      width: 100%;
      height: 150px;
      padding: 15px;
      background-color: rgba(255,255,255,0.05);
      border: none;
      border-radius: 12px;
      color: var(--muted);
      resize: none;
      font-size: 16px;
      box-shadow: inset 0 0 8px rgba(0,0,0,0.3);
      font-weight: 600;
    }
    textarea::placeholder {
      color: var(--muted);
      opacity: 0.7;
    }
    input[type="file"] {
      margin-top: 15px;
      color: var(--muted);
      font-weight: 600;
    }
    button {
      margin-top: 20px;
      padding: 12px 25px;
      background: linear-gradient(90deg, var(--accent-dark), #1E90FF);
      color: #06110a;
      border: none;
      border-radius: 12px;
      font-size: 16px;
      font-weight: 700;
      cursor: pointer;
      box-shadow: 0 6px 18px rgba(0,0,0,0.45);
      transition: background-color 0.3s ease, transform 0.3s ease;
    }
    button:hover {
      transform: translateY(-4px) scale(1.02);
      box-shadow: 0 12px 30px rgba(0,0,0,0.6);
    }
    .message {
      text-align: center;
      margin-top: 15px;
      font-weight: 700;
      color: #80ffaa;
      text-shadow: 0 1px 3px rgba(0,0,0,0.5);
    }
    .message.error {
      color: #ff6b6b;
    }
    .actions {
      display: flex;
      justify-content: center;
      gap: 15px;
      flex-wrap: wrap;
    }
    .delete-btn {
      background-color: #cc000033;
      color: #ff6666;
      border-radius: 12px;
      font-weight: 700;
      padding: 12px 25px;
      cursor: pointer;
      border: none;
      transition: background-color 0.3s ease;
    }
    .delete-btn:hover {
      background-color: #ff6666;
      color: #610000;
    }

    @media (max-width: 640px) {
      .container {
        margin: 60px 10px;
        padding: 20px;
      }
      textarea {
        height: 120px;
        font-size: 14px;
      }
      button, .delete-btn {
        padding: 10px 20px;
        font-size: 14px;
      }
      nav {
        gap: 10px;
        flex-wrap: wrap;
      }
      nav a {
        font-size: 14px;
        padding: 6px 10px;
      }
    }
  </style>
</head>
<body>

<header class="site-header">
  <a href="index.php" class="logo" aria-label="AgriConnect Home">AgriConnect</a>
  <nav>
  
    <a href="agri_officer_dashboard.php">Dashboard</a>
    <a href="agriofficerloginsignup.html">Logout</a>
  </nav>
</header>

<div class="container">
  <h2>Announcement</h2>

  <?php if (!empty($success)): ?>
    <div class="message"><?php echo $success; ?></div>
  <?php endif; ?>
  <?php if (!empty($error)): ?>
    <div class="message error"><?php echo $error; ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <textarea name="message" required placeholder="Enter announcement..."><?php echo htmlspecialchars($announcement); ?></textarea>

    <label style="margin-top:15px; display:block;">Attach PDF (optional):</label>
    <input type="file" name="pdf_file" accept="application/pdf" />

    <div class="actions">
      <button type="submit"><?php echo $announcement ? 'Update Announcement' : 'Post Announcement'; ?></button>
      <?php if ($announcement_id): ?>
        <form method="POST" action="delete_announcement.php?id=<?php echo $announcement_id; ?>" style="display:inline;">
          <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this announcement?');">Delete</button>
        </form>
      <?php endif; ?>
    </div>
  </form>


</body>
</html>
