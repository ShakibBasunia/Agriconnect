<?php
include 'db_connection.php';
session_start();

// Area removed; filtering only by year
$filter_year = $_GET['year'] ?? null;

// Insert Crop
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_crop'])) {
    $crop_name = $_POST['crop_name'];
    $stmt = $conn->prepare("INSERT INTO crops (crop_name) VALUES (?)");
    $stmt->bind_param("s", $crop_name);
    $stmt->execute();
}

// Delete Crop
if (isset($_POST['delete_crop'])) {
    $crop_id = $_POST['crop_id'];
    $stmt = $conn->prepare("DELETE FROM crops WHERE id=?");
    $stmt->bind_param("i", $crop_id);
    $stmt->execute();
}

// Get all crops
$crops = $conn->query("SELECT * FROM crops")->fetch_all(MYSQLI_ASSOC);

// Farmer summary
$where_clause = $filter_year ? "WHERE fc.year = ?" : "";
$query = "SELECT f.first_name, f.last_name, f.phone, f.area, fc.year, c.crop_name,
          SUM(fc.quantity_kg) AS total_qty, 
          SUM(fc.quantity_kg * fc.sell_price_per_kg) AS total_sales
          FROM farmers f
          JOIN farmer_cultivations fc ON f.id = fc.farmer_id
          JOIN crops c ON c.id = fc.crop_id
          $where_clause
          GROUP BY f.id, fc.crop_id, fc.year";

$stmt = $conn->prepare($query);
if ($filter_year) $stmt->bind_param("i", $filter_year);
$stmt->execute();
$result = $stmt->get_result();
$farmer_data = $result->fetch_all(MYSQLI_ASSOC);

// Pie chart data
$pstmt = $conn->prepare("SELECT c.crop_name, SUM(fc.quantity_kg) AS total_qty
                        FROM farmer_cultivations fc
                        JOIN crops c ON fc.crop_id = c.id
                        " . ($filter_year ? "WHERE fc.year = ?" : "") . "
                        GROUP BY fc.crop_id");
if ($filter_year) $pstmt->bind_param("i", $filter_year);
$pstmt->execute();
$pie_result = $pstmt->get_result();
$pie_data = [];
while ($row = $pie_result->fetch_assoc()) {
    $pie_data[] = $row;
}

// Totals
$total_sales_all = 0;
$total_farmers = [];
foreach ($farmer_data as $data) {
    $total_sales_all += $data['total_sales'];
    $total_farmers[$data['phone']] = true;
}

// Crop summary
$crop_summary_result = $conn->query("SELECT c.crop_name, 
    SUM(fc.quantity_kg) AS total_qty, 
    SUM(fc.quantity_kg * fc.sell_price_per_kg) AS total_sales
    FROM farmer_cultivations fc 
    JOIN crops c ON c.id = fc.crop_id
    " . ($filter_year ? "WHERE fc.year = $filter_year" : "") . "
    GROUP BY fc.crop_id");
$crop_summary = $crop_summary_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>AgriConnect</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
   :root {
  --bg-dark: #c31717ff;
  --bg-gradient: linear-gradient(180deg, #07130b, #0b2a12 60%);

  --accent: #a85252ff;
  --accent-dark: #2e7d32;
  --text-light: #e1e1e1ff;
  --text-muted: #29ba33ff;
  --glass: rgba(251, 14, 14, 0.74);
  --border-radius: 16px;
  --gap: 20px;
  --shadow: rgba(157, 61, 61, 0.4);
  --transition: 0.3s ease;
}

body {
  font-family: 'Poppins', system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
  min-height: 100vh;
  margin: 0;
  padding: 2rem;
  color: var(--text-light);
  background: radial-gradient(1000px 600px at 10% 10%, rgba(128,255,128,0.08), transparent 10%), var(--bg-gradient);
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

/* Navigation */
nav {
  background: var(--card-bg);
  backdrop-filter: blur(8px);
  border-radius: var(--border-radius);
  padding: 1rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 6px 20px var(--shadow);
  margin-bottom: 2rem;
  transition: background var(--transition), box-shadow var(--transition);
}

nav h2 {
  color: var(--accent);
  font-weight: 800;
  letter-spacing: 2px;
  font-size: 1.9rem;
}

nav div a,
nav div button {
  color: var(--text-light);
  background: linear-gradient(135deg, var(--accent-dark), #1E90FF);
  padding: 0.5rem 1.2rem;
  border-radius: var(--border-radius);
  font-weight: 600;
  border: none;
  cursor: pointer;
  margin-left: 1rem;
  box-shadow: 0 4px 12px rgba(223, 19, 19, 0.75);
  transition: all var(--transition);
  text-decoration: none;
}

nav div a:hover,
nav div button:hover {
  transform: translateY(-3px) scale(1.02);
  box-shadow: 0 6px 18px rgba(215, 51, 51, 0.6);
  background: linear-gradient(135deg, #62c462, var(--accent));
}

/* Forms */
form {
  max-width: 360px;
  margin: 0 auto 2rem;
  display: flex;
  justify-content: center;
  gap: 0.5rem;
}

form input[type="number"],
#cropModal input[type="text"],
#cropModal select {
  flex-grow: 1;
  padding: 0.7rem 1rem;
  border-radius: var(--border-radius);
  border: 1px solid var(--glass);
  background: rgba(190, 43, 43, 0.05);
  color: var(--text-light);
  font-weight: 600;
  font-size: 1rem;
  transition: border-color var(--transition), box-shadow var(--transition);
}

form input:focus,
#cropModal input:focus,
#cropModal select:focus {
  outline: none;
  border-color: var(--accent);
  box-shadow: 0 0 12px var(--accent);
}

/* Buttons */
form button,
#cropModal button {
  padding: 0.6rem 1.2rem;
  border-radius: var(--border-radius);
  background: linear-gradient(135deg, var(--accent-dark), #99bfe5ff);
  color: #cdda22ff;
  font-weight: 700;
  border: none;
  cursor: pointer;
  box-shadow: 0 6px 16px rgba(234, 51, 51, 0.5);
  transition: all var(--transition);
}

form button:hover,
#cropModal button:hover {
  transform: translateY(-2px) scale(1.03);
  box-shadow: 0 8px 22px rgba(217, 38, 38, 0.7);
}

/* Tables */
table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  background: var(--card-bg);
  border-radius: var(--border-radius);
  box-shadow: 0 8px 28px var(--shadow);
  overflow: hidden;
  margin-bottom: 3rem;
}

th, td {
  padding: 14px 16px;
  text-align: center;
  color: var(--text-light);
}

thead th {
  background: linear-gradient(135deg, var(--accent-dark), #1E90FF);
  font-weight: 700;
  font-size: 1rem;
  letter-spacing: 1px;
  text-transform: uppercase;
}

tbody tr:nth-child(even) {
  background-color: rgba(128,255,128,0.08);
}

tbody tr:hover {
  background: var(--accent);
  color: #f53131ff;
  font-weight: 700;
  cursor: pointer;
  transform: scale(1.01);
  transition: background 0.3s ease, transform 0.3s ease;
}

/* Chart container */
.chart-container {
  max-width: 480px;
  margin: 0 auto 3rem;
  background: var(--card-bg);
  backdrop-filter: blur(8px);
  padding: 1.2rem;
  border-radius: var(--border-radius);
  box-shadow: 0 10px 28px var(--shadow);
}

/* Modal */
#cropModal {
  display: none;
  position: fixed;
  top: 50%;
  left: 50%;
  width: 90%;
  max-width: 480px;
  background: var(--card-bg);
  border-radius: var(--border-radius);
  padding: 2rem;
  box-shadow: 0 14px 40px var(--shadow);
  transform: translate(-50%, -50%);
  z-index: 100;
  backdrop-filter: blur(10px);
  transition: opacity var(--transition), transform var(--transition);
}

#modalOverlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.65);
  z-index: 90;
  transition: opacity var(--transition);
}

/* Responsive tweaks */
@media (max-width: 600px) {
  nav {
    flex-direction: column;
    gap: 1rem;
  }
  table th, table td {
    padding: 10px 8px;
    font-size: 0.9rem;
  }
  .chart-container {
    max-width: 100%;
  }
}

    </style>
</head>
<body>

<nav>
    <h2>AgriConnect</h2>
    <div>
        <a href="#" onclick="showModal()">Manage Crops</a>
        <a href="agri_officer_dashboard.php">Dashboard</a>
        <button onclick="window.print()">Print</button>
    </div>
</nav>

<form method="GET" aria-label="Filter by year">
    <input type="number" name="year" placeholder="Enter Year to Filter (e.g., 2024)" value="<?= htmlspecialchars($filter_year) ?>" min="2015" max="2100" />
    <button type="submit">Filter</button>
</form>

<h3>Total Amount Sold: ৳<?= number_format($total_sales_all, 2) ?> | Total Farmers: <?= count($total_farmers) ?></h3>

<table aria-label="Farmer sales summary">
    <thead>
        <tr><th>Farmer</th><th>Phone</th><th>Location</th><th>Crop</th><th>Qty (kg)</th><th>Total Sales</th><th>Year</th></tr>
    </thead>
    <tbody>
    <?php foreach ($farmer_data as $row): ?>
    <tr>
        <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
        <td><?= htmlspecialchars($row['phone']) ?></td>
        <td><?= htmlspecialchars($row['area']) ?></td>
        <td><?= htmlspecialchars($row['crop_name']) ?></td>
        <td><?= number_format($row['total_qty'], 2) ?></td>
        <td>৳<?= number_format($row['total_sales'], 2) ?></td>
        <td><?= htmlspecialchars($row['year']) ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h3>Crop-wise Summary</h3>
<table aria-label="Crop-wise summary">
    <thead>
        <tr><th>Crop</th><th>Total Quantity (kg)</th><th>Total Sales (৳)</th><th>Suggested Price (৳/kg)</th></tr>
    </thead>
    <tbody>
    <?php foreach ($crop_summary as $row):
        $suggested_price = $row['total_qty'] > 0 ? $row['total_sales'] / $row['total_qty'] : 0;
    ?>
    <tr>
        <td><?= htmlspecialchars($row['crop_name']) ?></td>
        <td><?= number_format($row['total_qty'], 2) ?></td>
        <td>৳<?= number_format($row['total_sales'], 2) ?></td>
        <td>৳<?= number_format($suggested_price, 2) ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="chart-container" aria-label="Pie chart of crop quantities">
    <canvas id="cropPie"></canvas>
</div>

<!-- Modal Overlay -->
<div id="modalOverlay" onclick="hideModal()"></div>

<!-- Crop Modal -->
<div id="cropModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle" tabindex="-1">
    <h3 id="modalTitle">Manage Crops</h3>
    <form method="POST" aria-label="Add new crop">
        <input type="text" name="crop_name" placeholder="New Crop Name" required autocomplete="off" />
        <button type="submit" name="add_crop">Add</button>
    </form>
    <form method="POST" aria-label="Delete existing crop">
        <select name="crop_id" required>
            <option value="">Select Crop to Delete</option>
            <?php foreach ($crops as $crop): ?>
                <option value="<?= $crop['id'] ?>"><?= htmlspecialchars($crop['crop_name']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="delete_crop">Delete</button>
    </form>
    <button onclick="hideModal()">Close</button>
</div>

<script>
  function showModal() {
    document.getElementById('cropModal').style.display = 'block';
    document.getElementById('modalOverlay').style.display = 'block';
    document.getElementById('cropModal').focus();
  }
  function hideModal() {
    document.getElementById('cropModal').style.display = 'none';
    document.getElementById('modalOverlay').style.display = 'none';
  }

  const ctx = document.getElementById('cropPie').getContext('2d');
  new Chart(ctx, {
    type: 'pie',
    data: {
      labels: <?= json_encode(array_column($pie_data, 'crop_name')) ?>,
      datasets: [{
        label: 'Top Sold Crops',
        data: <?= json_encode(array_column($pie_data, 'total_qty')) ?>,
        backgroundColor: ['#415641ff', '#346234ff', '#acbbadff', '#a3d9a5', '#4caf50', '#99e699', '#407f41']
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'bottom',
          labels: { color: 'var(--text-light)' }
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              return context.label + ': ' + context.parsed.toLocaleString() + ' kg';
            }
          }
        }
      }
    }
  });
</script>

</body>
</html>
