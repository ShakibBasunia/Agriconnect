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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Farmer Portal - Tutorials</title>
  <style>
    /* Root variables */
    :root {
      --bg-dark: #1a1a1a;
      --bg-secondary: #2d2d2d;
      --green-light: #80ff80;
      --green-dark: #004d00;
      --green-hover: #4caf50;
      --text-light: #d4f5c3;
      --text-muted: #b0b0b0;
      --border-radius: 12px;
      --box-shadow: 0 4px 12px rgba(0, 77, 0, 0.6);
      --font-family: 'Poppins', sans-serif;
      --gap: 20px;
    }

    /* Reset and base */
    * {
      box-sizing: border-box;
    }
    body {
      margin: 0;
      font-family: var(--font-family);
      background-color: var(--bg-dark);
      color: var(--text-light);
      min-height: 100vh;
      line-height: 1.5;
    }

    /* Navbar */
    nav {
      background-color: var(--green-dark);
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 18px 40px;
      box-shadow: var(--box-shadow);
      position: sticky;
      top: 0;
      z-index: 1000;
    }
    nav div:first-child {
      font-weight: 700;
      font-size: 1.6rem;
      user-select: none;
      color: var(--green-light);
    }
    nav div:last-child a {
      color: var(--text-light);
      font-weight: 600;
      text-decoration: none;
      padding: 10px 15px;
      border-radius: 8px;
      transition: background-color 0.3s ease;
      user-select: none;
    }
    nav div:last-child a:hover,
    nav div:last-child a:focus {
      background-color: var(--green-hover);
      outline: none;
      color: #fff;
    }

    /* Tutorials list container */
    .tutorials-list {
      max-width: 1200px;
      margin: 40px auto;
      display: flex;
      flex-wrap: wrap;
      gap: var(--gap);
      padding: 0 20px;
      justify-content: center;
    }

    /* Each tutorial box */
    .tutorial-box {
      background-color: var(--bg-secondary);
      width: 260px;
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow);
      cursor: pointer;
      display: flex;
      flex-direction: column;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      user-select: none;
    }
    .tutorial-box:hover,
    .tutorial-box:focus-within {
      transform: translateY(-6px);
      box-shadow: 0 8px 24px rgba(0, 77, 0, 0.8);
      outline: none;
    }

    /* Media preview */
    .tutorial-preview {
      height: 150px;
      overflow: hidden;
      border-top-left-radius: var(--border-radius);
      border-top-right-radius: var(--border-radius);
      background-color: #000;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .tutorial-preview img,
    .tutorial-preview video {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-top-left-radius: var(--border-radius);
      border-top-right-radius: var(--border-radius);
    }

    /* Text content inside box */
    .tutorial-box h3 {
      color: var(--green-light);
      font-size: 1.2rem;
      margin: 15px 15px 10px;
      font-weight: 700;
      user-select: text;
    }
    .tutorial-box p {
      color: var(--text-muted);
      font-size: 0.9rem;
      margin: 0 15px 15px;
      user-select: text;
    }
    .tutorial-box a {
      display: inline-block;
      color: var(--green-hover);
      font-weight: 700;
      margin: 0 15px 20px;
      text-decoration: none;
      font-size: 0.95rem;
      user-select: text;
      transition: color 0.3s ease;
    }
    .tutorial-box a:hover,
    .tutorial-box a:focus {
      color: var(--green-light);
      outline: none;
      text-decoration: underline;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .tutorial-box {
        width: 45%;
      }
    }
    @media (max-width: 480px) {
      nav {
        flex-direction: column;
        gap: 10px;
      }
      .tutorial-box {
        width: 90%;
      }
    }
  </style>
</head>
<body>

<nav>
  <div>Farmer Portal</div>
  <div><a href="farmer_login.php" tabindex="0">Logout</a>
  <a href="farmer_dashboard.php">Dashboard</a>
</div>
  
</nav>

<div class="tutorials-list" role="list">
  <?php if ($result->num_rows > 0): ?>
    <?php while ($tutorial = $result->fetch_assoc()): ?>
      <div class="tutorial-box" role="listitem" tabindex="0" onclick="window.location.href='tutorial_details.php?id=<?= htmlspecialchars($tutorial['id']) ?>'" onkeypress="if(event.key === 'Enter'){window.location.href='tutorial_details.php?id=<?= htmlspecialchars($tutorial['id']) ?>'}">
        <div class="tutorial-preview">
          <?php if (!empty($tutorial['media'])): ?>
            <?php if (strpos($tutorial['media'], '.mp4') !== false): ?>
              <video src="<?= htmlspecialchars($tutorial['media']) ?>" controls muted preload="metadata"></video>
            <?php else: ?>
              <img src="<?= htmlspecialchars($tutorial['media']) ?>" alt="Tutorial Preview">
            <?php endif; ?>
          <?php else: ?>
            <img src="placeholder-image.png" alt="No preview available">
          <?php endif; ?>
        </div>
        <h3><?= htmlspecialchars($tutorial['title']) ?></h3>
        <p><?= htmlspecialchars(mb_strimwidth($tutorial['description'], 0, 80, '...')) ?></p>
        <a href="tutorial_details.php?id=<?= htmlspecialchars($tutorial['id']) ?>" tabindex="0">View Details</a>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p style="text-align:center; margin-top: 40px; font-size: 1.2rem; color: var(--text-muted); user-select: none;">No tutorials available yet.</p>
  <?php endif; ?>
</div>

</body>
</html>
