<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['farmer_id'])) {
    header("Location: farmer_login.php");
    exit();
}

$farmer_id = $_SESSION['farmer_id'];

$sql = "SELECT t.*, f.product_name, f.price_per_kg, w.name AS wholeseller_name, w.phone AS wholeseller_phone, w.email AS wholeseller_email
        FROM transactions t
        JOIN farmer_products f ON t.product_id = f.id
        JOIN wholesellers w ON t.wholeseller_id = w.id
        WHERE t.farmer_id = ?
        ORDER BY t.transaction_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $farmer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Farmer - Transaction History</title>
<style>
  :root {
    --bg-1: #07130b;
    --bg-2: #0b2a12;
    --card: #07120f;
    --accent: #80ff80;
    --accent-dark: #2e7d32;
    --muted: #cfead1;
    --glass: rgba(255, 255, 255, 0.03);
    --glass-2: rgba(255, 255, 255, 0.02);
    --card-radius: 16px;
    --gap: 28px;

    --badge-intransit: #ffc107;
    --badge-shipped: #0d6efd;
    --badge-received: #198754;
  }

  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }

  body {
    font-family: 'Poppins', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    background: radial-gradient(1000px 600px at 10% 10%, rgba(128, 255, 128, 0.05), transparent 10%),
                linear-gradient(180deg, var(--bg-1), var(--bg-2) 60%);
    color: var(--muted);
    min-height: 100vh;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
  }

  /* Navbar */
  nav.navbar {
    background-color: #145214;
    padding: 18px 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
  }

  nav.navbar .navbar-brand {
    color: #ffffff;
    font-weight: 700;
    font-size: 1.15rem;
    text-decoration: none;
  }

  nav.navbar .navbar-nav {
    display: flex;
    gap: 18px;
    list-style: none;
  }

  nav.navbar .nav-link {
    color: #ffffff;
    text-decoration: none;
    padding: 8px 12px;
    border-radius: 10px;
    font-weight: 600;
    transition: color 0.2s ease, background-color 0.2s ease;
  }

  nav.navbar .nav-link:hover,
  nav.navbar .nav-link:focus {
    background: rgba(255, 255, 255, 0.1);
    color: #a8ff8a;
    outline: none;
    transform: translateY(-2px);
  }

  .container {
    max-width: 1000px;
    margin: 40px auto;
    background: var(--card);
    padding: 32px;
    border-radius: var(--card-radius);
    box-shadow: 0 8px 24px rgba(46, 125, 50, 0.4);
  }

  h2 {
    color: var(--accent);
    margin-bottom: 24px;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    background: var(--card);
    border-radius: var(--card-radius);
    overflow: hidden;
    box-shadow: 0 6px 16px rgba(0,0,0,0.3);
  }

  thead tr {
    background-color: var(--accent-dark);
  }

  th, td {
    padding: 14px 16px;
    text-align: center;
    border-bottom: 1px solid var(--glass);
    font-size: 0.9rem;
  }

  th {
    color: var(--muted);
    font-weight: 700;
  }

  tbody tr {
    transition: background-color 0.3s ease;
  }

  tbody tr:nth-child(even) {
    background-color: var(--bg-1);
  }

  tbody tr:hover {
    background-color: #2e7d3244;
  }

  .badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.85rem;
    color: #fff;
    user-select: none;
  }

  .badge-warning {
    background-color: var(--badge-intransit);
    color: #212529;
  }

  .badge-primary {
    background-color: var(--badge-shipped);
  }

  .badge-success {
    background-color: var(--badge-received);
  }

  p {
    font-size: 1.1rem;
    color: var(--muted);
    text-align: center;
  }

  @media (max-width: 800px) {
    table, thead, tbody, th, td, tr {
      display: block;
    }
    thead tr {
      position: absolute;
      top: -9999px;
      left: -9999px;
    }
    tbody tr {
      margin-bottom: 20px;
      background: var(--bg-1);
      border-radius: var(--card-radius);
      padding: 16px;
    }
    tbody td {
      border: none;
      position: relative;
      padding-left: 50%;
      text-align: left;
      font-size: 0.9rem;
    }
    tbody td::before {
      position: absolute;
      left: 16px;
      top: 14px;
      white-space: nowrap;
      font-weight: 700;
      color: var(--accent);
      content: attr(data-label);
    }
  }
</style>
</head>
<body>

<nav class="navbar">
  <a class="navbar-brand" href="#">AgriConnect - Farmer</a>
  <ul class="navbar-nav">
    <li><a href="farmer_dashboard.php" class="nav-link">Dashboard</a></li>
    <li><a href="farmer_product_upload.php" class="nav-link">Upload Products</a></li>

  </ul>
</nav>

<div class="container">
  <h2>Transaction History</h2>

  <?php if ($result->num_rows > 0): ?>
  <table role="table" aria-label="Transaction history table">
    <thead>
      <tr>
        <th scope="col">Product Name</th>
        <th scope="col">Quantity (kg)</th>
        <th scope="col">Amount</th>
        <th scope="col">Wholeseller Name</th>
        <th scope="col">Wholeseller Contact</th>
        <th scope="col">Shipment Status</th>
        <th scope="col">Transaction Date</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td data-label="Product Name"><?= htmlspecialchars($row['product_name']) ?></td>
        <td data-label="Quantity (kg)">
          <?php 
            $quantity_bought = $row['amount'] / $row['price_per_kg']; 
            echo number_format($quantity_bought, 2);
          ?> kg
        </td>
        <td data-label="Amount"><?= htmlspecialchars(number_format($row['amount'], 2)) ?> TK</td>
        <td data-label="Wholeseller Name"><?= htmlspecialchars($row['wholeseller_name']) ?></td>
        <td data-label="Wholeseller Contact">
          <?= htmlspecialchars($row['wholeseller_phone']) ?><br>
          <?= htmlspecialchars($row['wholeseller_email']) ?>
        </td>
        <td data-label="Shipment Status">
          <?php
            if ($row['shipment_status'] == 'In Transit') {
              echo "<span class='badge badge-warning'>In Transit</span>";
            } elseif ($row['shipment_status'] == 'Shipped') {
              echo "<span class='badge badge-primary'>Shipped</span>";
            } elseif ($row['shipment_status'] == 'Received') {
              echo "<span class='badge badge-success'>Received</span>";
            } else {
              echo htmlspecialchars($row['shipment_status']);
            }
          ?>
        </td>
        <td data-label="Transaction Date"><?= htmlspecialchars(date('d M Y, h:i A', strtotime($row['transaction_date']))) ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  <?php else: ?>
  <p>No transactions found.</p>
  <?php endif; ?>
</div>

</body>
</html>
