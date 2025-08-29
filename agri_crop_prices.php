<?php
include 'db_connection.php';

// Handle crop insertion by Agri Officer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_crop_name'])) {
    $new_crop_name = $_POST['new_crop_name'];

    $stmt = $conn->prepare("INSERT INTO crop_prices (crop_name) VALUES (?)");
    $stmt->bind_param("s", $new_crop_name);
    $stmt->execute();
}

// Group all farmer crops
$result = $conn->query("SELECT fc.crop_name, MIN(fc.quantity_kg) as min_q, MAX(fc.quantity_kg) as max_q, COUNT(*) as total, f.first_name, f.last_name, f.area, f.phone 
                        FROM farmer_crops fc
                        JOIN farmers f ON fc.farmer_id = f.id
                        GROUP BY fc.crop_name");

// Handle price update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crop_name']) && !isset($_POST['delete_crop'])) {
    $crop_name = $_POST['crop_name'];
    $min_price = $_POST['min_price'];
    $max_price = $_POST['max_price'];

    $stmt = $conn->prepare("INSERT INTO crop_prices (crop_name, min_price, max_price)
                            VALUES (?, ?, ?)
                            ON DUPLICATE KEY UPDATE min_price=?, max_price=?");
    $stmt->bind_param("sdddd", $crop_name, $min_price, $max_price, $min_price, $max_price);
    $stmt->execute();
}

// Handle crop deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_crop'])) {
    $crop_name = $_POST['delete_crop'];

    // Delete the crop from the crop_prices table
    $stmt = $conn->prepare("DELETE FROM crop_prices WHERE crop_name = ?");
    $stmt->bind_param("s", $crop_name);
    $stmt->execute();
}

// Fetch newly added crops (for the second table)
$new_crops_result = $conn->query("SELECT * FROM crop_prices WHERE min_price IS NULL AND max_price IS NULL");

$prices = $conn->query("SELECT * FROM crop_prices");
$price_map = [];
while ($row = $prices->fetch_assoc()) {
    $price_map[$row['crop_name']] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Agri Officer - Crop Pricing</title>
<style>
  :root {
    --bg-1: #07130b;
    --bg-2: #0b2a12;
    --card: #07120f;
    --accent: #80ff80;
    --accent-dark: #2e7d32;
    --muted: #cfead1;
    --glass: rgba(255, 255, 255, 0.03);
    --card-radius: 16px;
    --gap: 28px;
    --nav-height: 60px;
  }

  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }

  body {
    background: var(--bg-1);
    background: linear-gradient(180deg, var(--bg-1), var(--bg-2) 60%);
    font-family: 'Poppins', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    color: var(--muted);
    padding: 24px;
    padding-top: calc(var(--nav-height) + 24px);
    line-height: 1.5;
  }

  /* Navbar */
  nav {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: var(--nav-height);
    background: var(--card);
    box-shadow: 0 2px 10px rgba(0,0,0,0.6);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 28px;
    z-index: 1000;
    border-bottom-left-radius: 12px;
    border-bottom-right-radius: 12px;
  }

  nav .nav-left,
  nav .nav-right {
    display: flex;
    align-items: center;
  }

  nav a, nav button {
    color: var(--accent);
    font-weight: 600;
    text-decoration: none;
    font-size: 1.1rem;
    padding: 10px 16px;
    border-radius: 8px;
    background: transparent;
    border: none;
    cursor: pointer;
    transition: background-color 0.25s ease;
  }

  nav a:hover, nav button:hover {
    background: var(--accent-dark);
    color: #e0f2e9;
  }

  nav button {
    background: var(--accent-dark);
  }

  h2, h3 {
    color: var(--accent);
    margin-bottom: 16px;
    font-weight: 700;
    text-align: center;
  }

  form {
    background: var(--card);
    padding: 20px 24px;
    border-radius: var(--card-radius);
    margin-bottom: var(--gap);
    box-shadow: 0 6px 30px rgba(0, 0, 0, 0.5);
  }

  input[type="text"],
  input[type="number"],
  input[type="url"],
  textarea {
    width: 100%;
    padding: 12px 14px;
    margin-top: 10px;
    border-radius: 8px;
    background-color: #1a2a19;
    border: 1px solid #234422;
    color: var(--muted);
    font-size: 1rem;
    transition: border-color 0.3s ease;
  }

  input[type="text"]:focus,
  input[type="number"]:focus,
  input[type="url"]:focus,
  textarea:focus {
    outline: none;
    border-color: var(--accent);
    background-color: #253726;
  }

  button, .delete-btn {
    background: linear-gradient(90deg, var(--accent-dark), #1E90FF);
    color: #06110a;
    font-weight: 700;
    border: none;
    border-radius: 10px;
    padding: 12px 24px;
    cursor: pointer;
    transition: box-shadow 0.3s ease, transform 0.2s ease;
    box-shadow: 0 6px 15px rgba(0,0,0,0.4);
    margin-top: 12px;
  }

  button:hover, .delete-btn:hover {
    box-shadow: 0 9px 20px rgba(0,0,0,0.7);
    transform: scale(1.05);
  }

  .delete-btn {
    background: #ff4d4d;
    color: white;
    padding: 8px 18px;
    font-weight: 600;
    margin: 0;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    background: var(--card);
    border-radius: var(--card-radius);
    overflow: hidden;
    box-shadow: 0 6px 20px rgba(0,0,0,0.5);
  }

  th, td {
    padding: 12px 16px;
    text-align: left;
  }

  th {
    background: linear-gradient(90deg, #0b2a12, #07130b);
    color: var(--accent);
    font-weight: 700;
  }

  td {
    border-top: 1px solid #234422;
    vertical-align: middle;
    color: var(--muted);
    font-size: 0.95rem;
  }

  tr:hover td {
    background: var(--glass);
  }

  form > td > input[type="number"] {
    width: 90px;
  }

  .actions {
    margin-top: 28px;
    text-align: center;
  }

  .actions button {
    margin: 0 12px;
    padding: 12px 28px;
    font-size: 1rem;
  }

  @media (max-width: 768px) {
    table, tbody, tr, th, td {
      display: block;
    }
    tr {
      margin-bottom: 24px;
      background: var(--card);
      border-radius: var(--card-radius);
      padding: 12px 16px;
    }
    th {
      display: none;
    }
    td {
      padding-left: 50%;
      position: relative;
      border: none;
      border-bottom: 1px solid #234422;
      margin-bottom: 8px;
    }
    td::before {
      content: attr(data-label);
      position: absolute;
      left: 16px;
      top: 12px;
      font-weight: 600;
      color: var(--accent);
      font-size: 0.9rem;
      white-space: nowrap;
    }
    form > td > input[type="number"] {
      width: 100%;
    }
  }
</style>

<script>
  function printTable() {
      window.print();
  }

  function downloadCSV() {
      const rows = document.querySelectorAll("table tr");
      let csv = [];
      for (let row of rows) {
          let cols = row.querySelectorAll("td, th");
          let data = Array.from(cols).map(col => `"${col.innerText.trim().replace(/"/g, '""')}"`);
          csv.push(data.join(","));
      }
      const blob = new Blob([csv.join("\n")], { type: "text/csv" });
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.setAttribute("hidden", "");
      a.setAttribute("href", url);
      a.setAttribute("download", "crop_prices.csv");
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
  }
</script>
</head>
<body>

<nav>
  <div class="nav-left">
    <a href="agri_officer_dashboard.php">Dashboard</a>
  </div>
  <div class="nav-right">
    <form action="agriofficerloginsignup.html" method="post" style="margin:0;">
      <button type="submit" title="Logout">Logout</button>
    </form>
  </div>
</nav>

<h2>Crop Pricing Management</h2>

<!-- Crop Insertion Form -->
<form method="post" novalidate>
    <h3>Add New Crop</h3>
    <input type="text" name="new_crop_name" placeholder="Enter new crop name" required />
    <button type="submit">Add Crop</button>
</form>

<h3>Farmer Crop Information and Price Management</h3>
<table>
    <thead>
        <tr>
            <th>Farmer Name</th>
            <th>Location</th>
            <th>Phone</th>
            <th>Crop Name</th>
            <th>Total Quantity</th>
            <th>Min Price</th>
            <th>Max Price</th>
            <th>Update Price</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <form method="post" novalidate>
                <td data-label="Farmer Name"><?= htmlspecialchars($row['first_name']) ?> <?= htmlspecialchars($row['last_name']) ?></td>
                <td data-label="Location"><?= htmlspecialchars($row['area']) ?></td>
                <td data-label="Phone"><?= htmlspecialchars($row['phone']) ?></td>
                <td data-label="Crop Name">
                    <?= htmlspecialchars($row['crop_name']) ?>
                    <input type="hidden" name="crop_name" value="<?= htmlspecialchars($row['crop_name']) ?>">
                </td>
                <td data-label="Total Quantity"><?= htmlspecialchars($row['total']) ?></td>
                <td data-label="Min Price">
                    <input type="number" step="0.01" name="min_price" value="<?= isset($price_map[$row['crop_name']]['min_price']) ? htmlspecialchars($price_map[$row['crop_name']]['min_price']) : '' ?>" required>
                </td>
                <td data-label="Max Price">
                    <input type="number" step="0.01" name="max_price" value="<?= isset($price_map[$row['crop_name']]['max_price']) ? htmlspecialchars($price_map[$row['crop_name']]['max_price']) : '' ?>" required>
                </td>
                <td data-label="Update Price"><button type="submit">Save</button></td>
            </form>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<h3>Newly Added Crops (No Prices Set)</h3>
<table>
    <thead>
        <tr>
            <th>Crop Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $new_crops_result->fetch_assoc()): ?>
        <tr>
            <td data-label="Crop Name"><?= htmlspecialchars($row['crop_name']) ?></td>
            <td data-label="Actions">
                <form method="post" style="display:inline;" novalidate>
                    <input type="hidden" name="delete_crop" value="<?= htmlspecialchars($row['crop_name']) ?>">
                    <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this crop?')">Delete</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<div class="actions">
    <button onclick="printTable()">🖨️ Print Table</button>
    <button onclick="downloadCSV()">📥 Download CSV</button>
</div>

</body>
</html>
