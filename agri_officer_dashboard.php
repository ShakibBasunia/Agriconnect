<?php
session_start();
if (!isset($_SESSION['agri_officer_id'])) {
    header("Location: agri_officer_login.php");
    exit;
}

$agriOfficerName = $_SESSION['agri_officer_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Agri Officer Dashboard</title>
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

    *{box-sizing:border-box;margin:0;padding:0}
    html,body{height:100%}
   
    body{
      font-family: 'Poppins', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      color: var(--muted);
      background: radial-gradient(1000px 600px at 10% 10%, rgba(128,255,128,0.05), transparent 10%),
                  linear-gradient(180deg,var(--bg-1), var(--bg-2) 60%);
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
      line-height:1.4;
      padding-bottom:60px;
    }

    header.site-header{
      position:relative; z-index:10;
      padding:18px 32px;
      display:flex; align-items:center; justify-content:space-between;
      gap:12px;
      background: linear-gradient(90deg, var(--bg-1), var(--bg-2));
      box-shadow: 0 4px 12px rgba(0,0,0,0.7);
    }

    .logo {
      color: var(--muted);
      font-weight: 700;
      font-size: 1.15rem;
      text-decoration: none;
    }

    nav {
      display:flex; align-items:center; gap:18px;
    }
    nav a{
      color: var(--muted);
      text-decoration:none; padding:8px 12px; border-radius:10px; font-weight:600;
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

    .welcome {
      margin: 50px auto 40px;
      text-align: center;
      max-width: 900px;
    }
    .welcome h1 {
      font-size: clamp(1.8rem, 4vw, 3rem);
      color: var(--accent);
      text-shadow: 0 6px 26px rgba(0,0,0,0.6);
      margin-bottom: 12px;
    }
    .welcome p {
      font-size: 1.1rem;
      color: #d9efd8;
      opacity: 0.9;
      max-width: 600px;
      margin: 0 auto;
    }

    .sections {
      max-width: 1100px;
      margin: 0 auto 60px;
      display: grid;
      grid-template-columns: repeat(auto-fit,minmax(280px,1fr));
      gap: var(--gap);
      padding: 0 22px;
    }

    .section {
      background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
      border-radius: var(--card-radius);
      padding: 22px;
      min-height: 180px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      text-align: center;
      color: var(--muted);
      cursor: pointer;
      box-shadow: 0 8px 26px rgba(2,6,2,0.6);
      border: 1px solid rgba(255,255,255,0.03);
      transition: transform 0.34s cubic-bezier(.2,.9,.25,1), box-shadow 0.34s;
    }

    .section:hover {
      transform: translateY(-10px) scale(1.02);
      box-shadow: 0 24px 44px rgba(2,6,2,0.65);
      color: var(--accent);
      background: linear-gradient(180deg, rgba(128,255,128,0.05), rgba(128,255,128,0.02));
    }

    .section h3 {
      margin-bottom: 10px;
      font-size: 1.25rem;
      font-weight: 700;
    }
    .section p {
      font-size: 1rem;
      opacity: 0.85;
    }

    @media (max-width:640px){
      header.site-header{padding:12px 18px;}
      .sections {
        grid-template-columns: 1fr;
        padding: 0 12px;
      }
      .section {
        min-height: 150px;
        padding: 16px;
      }
    }
  </style>
</head>
<body>

<header class="site-header">
  <a href="index.php" class="logo" aria-label="AgriConnect Home">AgriConnect</a>
  <nav>
    <a href="home.php">Home</a> 
      <a href="about.php">About</a>
    <a href="faq.php">FAQ</a>
    <a href="agriofficerloginsignup.html">Logout</a>
  </nav>
</header>

<div class="welcome">
  <h1>Welcome, <?php echo htmlspecialchars($agriOfficerName); ?></h1>
  <p>Here you can manage farm-related tasks, announcements, seminars, etc.</p>
</div>

<div class="sections">
  <div class="section" onclick="window.location.href='seminar_upload.php'">
    <h3>Manage Seminars</h3>
    <p>Manage upcoming seminars for farmers.</p>
  </div>
  <div class="section" onclick="window.location.href='upload_announcement.php'">
    <h3>Create Announcements</h3>
    <p>Post announcements for farmers.</p>
  </div>
  <div class="section" onclick="window.location.href='agriofficer_tutorials.php'">
    <h3>Tutorials</h3>
    <p>Post tutorials for farmers.</p>
  </div>
  <div class="section" onclick="window.location.href='agri_crop_prices.php'">
    <h3>Crop Prices</h3>
    <p>Crop Prices Management</p>
  </div>
  <div class="section" onclick="window.location.href='agri_all_farmers.php'">
    <h3>All Farmers</h3>
    <p>See the most harvested and total amount of crops.</p>
  </div>
  <div class="section" onclick="window.location.href='agri_post.php'">
    <h3>Problems and Solutions</h3>
    <p>The post platform.</p>
  </div>
</div>

</body>
</html>
