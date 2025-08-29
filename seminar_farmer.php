<?php
session_start();
include 'db_connection.php';

// Fetch seminars
$seminars = $conn->query("SELECT * FROM seminars ORDER BY seminar_date ASC, seminar_time ASC");

// Get current date and time
date_default_timezone_set('Asia/Dhaka');
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Seminars</title>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #0d0d0d;
      color: #d4f5c3;
      margin: 0;
      padding: 0;
    }

    nav {
      background-color: #004d00;
      padding: 15px 30px;
      color: #fff;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    nav .logo {
      font-size: 24px;
      font-weight: bold;
    }

    nav a {
      color: #fff;
      margin-left: 20px;
      text-decoration: none;
      transition: color 0.3s;
    }

    nav a:hover {
      color: #80ff80;
    }

    .container {
      max-width: 900px;
      margin: 30px auto;
      background-color: #1a1a1a;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 20px #004d00;
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #80ff80;
    }

    table {
      width: 100%;
      margin-top: 30px;
      border-collapse: collapse;
      background-color: #1a1a1a;
      color: #fff;
    }

    th, td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #004d00;
    }

    tr:hover {
      background-color: #003300;
    }

    .join-btn {
      background-color: #004d00;
      color: #fff;
      padding: 8px 16px;
      border: none;
      border-radius: 5px;
      text-decoration: none;
      transition: background-color 0.3s;
    }

    .join-btn:hover {
      background-color: #006600;
    }

    .disabled-btn {
      background-color: #555;
      color: #ccc;
      padding: 8px 16px;
      border: none;
      border-radius: 5px;
      text-decoration: none;
      cursor: not-allowed;
    }
  </style>
</head>
<body>

  <nav>
    <div class="logo">AgriConnect</div>
    <div>
      <a href="farmer_dashboard.php">Dashboard</a>
      <a href="logout.php">Logout</a>
    </div>
  </nav>

  <div class="container">
    <h2>Upcoming Seminars</h2>
    <table>
      <tr>
        <th>Title</th>
        <th>Date</th>
        <th>Time</th>
        <th>Action</th>
      </tr>
      <?php while($row = $seminars->fetch_assoc()): ?>
        <tr>
          <td><?php echo htmlspecialchars($row['title']); ?></td>
          <td><?php echo $row['seminar_date']; ?></td>
          <td><?php echo date("g:i A", strtotime($row['seminar_time'])); ?></td>
          <td>
            <?php
              $seminarDateTime = strtotime($row['seminar_date'] . ' ' . $row['seminar_time']);
              $currentDateTime = strtotime($currentDate . ' ' . $currentTime);
              if ($currentDateTime >= $seminarDateTime) {
                echo '<a class="join-btn" href="' . $row['video_link'] . '" target="_blank">Join Now</a>';
              } else {
                echo '<span class="disabled-btn">Not Started</span>';
              }
            ?>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>

</body>
</html>
