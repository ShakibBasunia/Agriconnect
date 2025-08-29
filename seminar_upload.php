<?php
include 'db_connection.php';

// Handle seminar insert
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['title'])) {
    $title = $_POST['title'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $link = $_POST['link'];

    $stmt = $conn->prepare("INSERT INTO seminars (title, seminar_date, seminar_time, video_link) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $date, $time, $link);
    $stmt->execute();
    $stmt->close();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM seminars WHERE id = $id");
}

// Fetch seminars
$seminars = $conn->query("SELECT * FROM seminars ORDER BY seminar_date ASC, seminar_time ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Agri Officer Seminar Panel</title>
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

    .container {
      max-width: 900px;
      margin: 30px auto;
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

    form input, form button {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border-radius: 12px;
      border: none;
      font-size: 16px;
      font-weight: 600;
      transition: background-color 0.3s ease;
    }

    form input {
      background-color: rgba(255,255,255,0.05);
      color: var(--muted);
      box-shadow: inset 0 0 8px rgba(0,0,0,0.3);
    }

    form input::placeholder {
      color: var(--muted);
      opacity: 0.7;
    }

    form input:focus {
      outline: none;
      background-color: var(--glass);
      border: 2px solid var(--accent);
      color: var(--accent);
    }

    form button {
      background: linear-gradient(90deg, var(--accent-dark), #1E90FF);
      color: #06110a;
      cursor: pointer;
      box-shadow: 0 6px 18px rgba(0,0,0,0.45);
      font-weight: 700;
      border-radius: 12px;
    }

    form button:hover {
      transform: translateY(-4px) scale(1.02);
      box-shadow: 0 12px 30px rgba(0,0,0,0.6);
    }

    table {
      width: 100%;
      margin-top: 30px;
      border-collapse: collapse;
      background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
      border-radius: var(--card-radius);
      overflow: hidden;
      box-shadow: 0 8px 26px rgba(2,6,2,0.6);
      border: 1px solid rgba(255,255,255,0.03);
    }

    th, td {
      padding: 16px;
      text-align: left;
      color: var(--muted);
      border-bottom: 1px solid rgba(255,255,255,0.05);
      font-weight: 600;
    }

    tr:hover {
      background-color: var(--glass);
      color: var(--accent);
      cursor: pointer;
    }

    a {
      color: var(--accent);
      font-weight: 700;
      text-decoration: none;
      transition: color 0.3s ease;
    }
    a:hover, a:focus {
      color: var(--accent-dark);
      text-decoration: underline;
    }

    .action-btns a {
      text-decoration: none;
      padding: 6px 12px;
      border-radius: 12px;
      font-weight: 700;
      color: var(--muted);
      margin-right: 10px;
      background-color: rgba(255,255,255,0.03);
      transition: background-color 0.3s ease, color 0.3s ease;
      display: inline-block;
    }
    .action-btns a.delete {
      background-color: #cc000033;
      color: #ff6666;
    }
    .action-btns a.delete:hover {
      background-color: #ff6666;
      color: #610000;
    }

    @media (max-width:640px){
      header.site-header{
        padding: 12px 18px;
        flex-wrap: wrap;
        gap: 12px;
      }
      nav {
        flex-wrap: wrap;
        gap: 12px;
      }
      .container {
        margin: 20px 10px;
        padding: 20px;
      }
      table, th, td {
        font-size: 14px;
      }
      form input, form button {
        font-size: 14px;
        padding: 10px;
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
  <h2>Upload Seminar</h2>
  <form method="POST">
    <input type="text" name="title" placeholder="Seminar Title" required />
    <input type="date" name="date" required />
    <input type="time" name="time" required />
    <input type="url" name="link" placeholder="Video Call Link" required />
    <button type="submit">Upload Seminar</button>
  </form>

  <h2>Seminar List</h2>
  <table>
    <tr>
      <th>Title</th>
      <th>Date</th>
      <th>Time</th>
      <th>Link</th>
      <th>Action</th>
    </tr>
    <?php while($row = $seminars->fetch_assoc()): ?>
    <tr>
      <td><?php echo htmlspecialchars($row['title']); ?></td>
      <td><?php echo $row['seminar_date']; ?></td>
      <td><?php echo date("g:i A", strtotime($row['seminar_time'])); ?></td>
      <td><a href="<?php echo htmlspecialchars($row['video_link']); ?>" target="_blank" rel="noopener noreferrer">Join Link</a></td>
      <td class="action-btns">
        <a class="delete" href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this seminar?')">Delete</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>

</body>
</html>
