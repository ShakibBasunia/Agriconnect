<?php
session_start();
if (!isset($_SESSION['farmer_id'])) {
  header("Location: farmer_login.php");
  exit;
}
$farmerName = $_SESSION['farmer_name'] ?? 'Farmer Name';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Farmer Dashboard</title>
  <style>
    :root{
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
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Poppins', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      color: var(--muted);
    }

  




    
    body{
      background-image: url(images/x.jpg);
    }

    body
    /* Navbar */
    nav {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 60px;
      background: linear-gradient(90deg, var(--bg-1), var(--bg-2));
      box-shadow: 0 4px 12px rgba(0,0,0,0.7);
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 32px;
      z-index: 1000;
    }
    nav .logo {
      font-weight: 700;
      font-size: 1.3rem;
      color: var(--accent);
      user-select: none;
    }
    nav .nav-links {
      display: flex;
      gap: 20px;
    }
    nav .nav-links a {
      color: var(--muted);
      text-decoration: none;
      font-weight: 600;
      padding: 8px 14px;
      border-radius: 10px;
      position: relative;
      transition: all 0.3s ease;
    }
    nav .nav-links a::after {
      content: "";
      position: absolute;
      left: 0;
      bottom: -5px;
      width: 0%;
      height: 3px;
      background: var(--accent);
      border-radius: 3px;
      transition: width 0.3s ease;
    }
    nav .nav-links a:hover,
    nav .nav-links a:focus {
      background: var(--glass);
      color: var(--accent);
      transform: translateY(-2px);
      outline: none;
    }
    nav .nav-links a:hover::after,
    nav .nav-links a:focus::after {
      width: 100%;
    }

    /* Sidebar */
    .sidebar {
      position: fixed;
      top: 60px;
      left: 0;
      width: 250px;
      height: calc(100vh - 60px);
      background: var(--card);
      box-shadow: 0 8px 26px rgba(2,6,2,0.6);
      border-radius: 0 0 var(--card-radius) 0;
      padding: 30px 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .sidebar h3 {
      font-weight: 700;
      color: var(--accent);
      margin-bottom: 30px;
      text-align: center;
      font-size: 1.25rem;
      user-select: none;
    }
    .sidebar ul {
      list-style: none;
      width: 100%;
      display: flex;
      flex-direction: column;
      gap: 15px;
    }
    .sidebar ul li a {
      display: block;
      padding: 14px 20px;
      background: var(--glass-2);
      color: var(--muted);
      text-decoration: none;
      font-weight: 600;
      border-radius: var(--card-radius);
      text-align: center;
      transition: background 0.3s, color 0.3s, transform 0.3s;
      user-select: none;
    }
    .sidebar ul li a:hover,
    .sidebar ul li a:focus {
      background: var(--accent-dark);
      color: var(--bg-1);
      transform: translateY(-5px);
      outline: none;
    }

    /* Main Content */
    .main-content {
      margin-left: 250px;
      margin-top: 60px;
      padding: 40px 30px;
      flex-grow: 1;
      min-height: calc(100vh - 60px);
    }

    /* Bulletin */
    .bulletin {
      background: var(--accent-dark);
      padding: 14px 24px;
      border-radius: var(--card-radius);
      margin-bottom: 40px;
      overflow: hidden;
      position: relative;
      box-shadow: 0 0 20px var(--accent);
    }
    .bulletin p {
      white-space: nowrap;
      animation: scroll 15s linear infinite;
      font-weight: 700;
      color: var(--muted);
      user-select: none;
    }
    @keyframes scroll {
      0% { transform: translateX(100%); }
      100% { transform: translateX(-100%); }
    }

    /* Sections Grid */
    .sections {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: var(--gap);
    }
    .section-box {
      background: var(--card);
      padding: 28px 24px;
      border-radius: var(--card-radius);
      box-shadow: 0 0 15px var(--glass);
      text-align: center;
      cursor: pointer;
      transition: transform 0.3s cubic-bezier(.2,.9,.25,1), box-shadow 0.3s ease;
      user-select: none;
    }
    .section-box:hover,
    .section-box:focus {
      transform: translateY(-8px) scale(1.03);
      box-shadow: 0 20px 40px var(--accent);
      color: var(--accent);
      outline: none;
    }
    .section-box h3 {
      font-weight: 700;
      font-size: 1.3rem;
      margin: 0;
    }

    /* Chat Button */
    #chat-button {
      position: fixed;
      bottom: 20px;
      right: 20px;
      padding: 16px;
      border-radius: 50%;
      background-color: var(--accent);
      color: var(--bg-1);
      font-size: 24px;
      border: none;
      cursor: pointer;
      box-shadow: 0 6px 15px rgba(0,0,0,0.3);
      transition: box-shadow 0.3s ease, transform 0.3s ease;
      user-select: none;
    }
    #chat-button:hover,
    #chat-button:focus {
      box-shadow: 0 10px 25px var(--accent);
      transform: scale(1.1);
      outline: none;
    }

    /* Seminar popup */
    .seminar-popup {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background: var(--accent-dark);
      color: var(--muted);
      padding: 18px 24px;
      border-radius: var(--card-radius);
      box-shadow: 0 0 25px var(--accent);
      display: flex;
      align-items: center;
      gap: 15px;
      z-index: 1100;
      animation: slideUp 0.5s ease forwards;
      user-select: none;
    }
    .seminar-popup strong {
      margin-right: 6px;
      font-weight: 700;
    }
    .popup-btn {
      background-color: var(--accent);
      padding: 8px 16px;
      border-radius: 8px;
      color: var(--bg-1);
      font-weight: 700;
      text-decoration: none;
      transition: background-color 0.3s ease;
    }
    .popup-btn:hover,
    .popup-btn:focus {
      background-color: #00cc00;
      outline: none;
    }
    .close-btn {
      font-weight: 700;
      cursor: pointer;
      color: var(--muted);
      margin-left: auto;
      font-size: 1.3rem;
      line-height: 1;
      user-select: none;
    }

    @keyframes slideUp {
      from { transform: translateY(100px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }

    /* Responsive */
    @media (max-width: 768px) {
      .sidebar {
        position: relative;
        width: 100%;
        height: auto;
        flex-direction: row;
        padding: 10px;
        justify-content: center;
        flex-wrap: wrap;
        gap: 12px;
      }
      .sidebar h3 {
        margin-bottom: 0;
        font-size: 1rem;
      }
      .sidebar ul {
        flex-direction: row;
        width: auto;
        gap: 12px;
      }
      .sidebar ul li a {
        padding: 8px 12px;
        font-size: 0.9rem;
      }
      .main-content {
        margin-left: 0;
        margin-top: 20px;
        padding: 20px 15px;
      }
      .sections {
        grid-template-columns: 1fr 1fr;
      }
    }
    @media (max-width: 480px) {
      .sections {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>

<nav>
  <div class="logo">AgriConnect</div>
  <div class="nav-links">
    <a href="home.php">Home</a>
    <a href="farmerlogout.php">Logout</a>
  </div>
</nav>

<div class="sidebar">
  <h3><?php echo htmlspecialchars($farmerName); ?></h3>
  <ul>
    <li><a href="#">Dashboard</a></li>
    <li><a href="announcement.php">Announcement</a></li>
    <li><a href="seminar_farmer.php">Seminar</a></li>
    <li><a href="farmer_post.php">Problems & Solutions</a></li>
    <li><a href="farmer_tutorial.php">Tutorials</a></li>
    <li><a href="farmer_crop_submission.php">Prices</a></li>
    <li><a href="farmer_annualdata.php">Insert Annual Data</a></li>
    <li><a href="rent.html">Rent Equipments</a></li>
    <li><a href="farmer_product_upload.php">WholeSale</a></li>
  </ul>
</div>

<div class="main-content">
  <?php
  include 'db_connection.php';
  $bulletinText = '';
  $res = $conn->query("SELECT message FROM announcements ORDER BY id DESC LIMIT 1");
  if ($res->num_rows > 0) {
      $bulletinText = $res->fetch_assoc()['message'];
  }
  ?>
  <div class="bulletin">
    <p><?php echo htmlspecialchars($bulletinText ?: '🔔 No announcements available at the moment.'); ?></p>
  </div>

  <div class="sections">
    <div class="section-box" onclick="window.location.href='seminar_farmer.php'" tabindex="0" role="button" aria-label="Go to Seminar page"><h3>Seminar</h3></div>
    <div class="section-box" onclick="window.location.href='farmer_post.php'" tabindex="0" role="button" aria-label="Go to Problems & Solutions page"><h3>Problems & Solutions</h3></div>
    <div class="section-box" onclick="window.location.href='farmer_tutorial.php'" tabindex="0" role="button" aria-label="Go to Tutorials page"><h3>Tutorials</h3></div>
    <div class="section-box" onclick="window.location.href='farmer_crop_submission.php'" tabindex="0" role="button" aria-label="Go to Prices page"><h3>Prices</h3></div>
    <div class="section-box" onclick="window.location.href='farmer_product_upload.php'" tabindex="0" role="button" aria-label="Go to WholeSale page"><h3>WholeSale</h3></div>
    <div class="section-box" onclick="window.location.href='rent.html'" tabindex="0" role="button" aria-label="Go to Rent Equipments page"><h3>Rent Equipments</h3></div>
    <div class="section-box" onclick="window.location.href='farmer_annualdata.php'" tabindex="0" role="button" aria-label="Go to Insert Annual Data page"><h3>Insert Annual Data</h3></div>
  </div>

  <!-- Chat button -->
  <button id="chat-button" onclick="window.location.href='farmchat.html'" aria-label="Open chat">💬</button>
</div>

<?php
include 'db_connection.php';

// Get the latest seminar
$seminarQuery = $conn->query("SELECT * FROM seminars ORDER BY id DESC LIMIT 1");

if ($seminarQuery && $seminarQuery->num_rows > 0) {
  $seminar = $seminarQuery->fetch_assoc();
  $latestSeminarId = $seminar['id'];

  // Check if session already saw this seminar
  if (!isset($_SESSION['last_seen_seminar_id']) || $_SESSION['last_seen_seminar_id'] != $latestSeminarId) {
    $_SESSION['last_seen_seminar_id'] = $latestSeminarId;
    ?>
    <div class="seminar-popup" id="seminarPopup" role="alert" aria-live="assertive">
      <span class="close-btn" onclick="closePopup()" aria-label="Close popup">×</span>
      📢 <strong>New Seminar:</strong> <?php echo htmlspecialchars($seminar['title']); ?>
      <a href="seminar_farmer.php" class="popup-btn">Go to Seminar Page</a>
    </div>
    <script>
      function closePopup() {
        document.getElementById('seminarPopup').style.display = 'none';
      }
    </script>
    <?php
  }
}
?>

</body>
</html>
