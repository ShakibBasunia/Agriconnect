<?php
include 'db_connection.php';
session_start();

$farmer_id = $_SESSION['farmer_id'] ?? 0;
if (!$farmer_id) {
    die("You must be logged in to access this page.");
}

// Handle deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM farmer_crops WHERE id = ? AND farmer_id = ?");
    $stmt->bind_param("ii", $delete_id, $farmer_id);
    $stmt->execute();
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// Handle new submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $crop = $_POST['crop_name'];
    $quantity = $_POST['quantity'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("INSERT INTO farmer_crops (farmer_id, crop_name, quantity_kg, address, phone) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isdss", $farmer_id, $crop, $quantity, $address, $phone);
    $stmt->execute();
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// Fetch farmer crops
$crops = $conn->query("SELECT * FROM farmer_crops WHERE farmer_id = $farmer_id ORDER BY submitted_at DESC");

// Get price list
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
<title>Farmer Crop Submission</title>
<style>
  /* Body and font */
  body {
    background-color: #121912;
    color: #b3ffb3;
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 30px 20px;
    min-height: 100vh;
  }

  /* Navbar styling */
  nav {
    background-color: #004d00;
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 3px solid #008000;
  }

  nav .logo {
    color: #b3ffb3;
    font-weight: 700;
    font-size: 1.4rem;
  }

  nav a {
    color: #b3ffb3;
    text-decoration: none;
    margin-left: 20px;
    font-weight: 600;
    transition: color 0.3s ease;
  }

  nav a:hover {
    color: #80e050;
  }

  /* Headings */
  h2 {
    color: #80e050;
    font-weight: 700;
    margin-bottom: 20px;
    user-select: none;
  }

  /* Form */
  form.submit-form {
    max-width: 600px;
    background: #1a2d1a;
    padding: 25px 30px;
    border-radius: 12px;
    box-shadow: 0 0 15px rgba(0, 128, 0, 0.5);
    margin-bottom: 40px;
  }

  select, input[type="number"], input[type="text"], button {
    width: 100%;
    margin: 12px 0;
    padding: 12px 15px;
    border-radius: 8px;
    border: 1.5px solid #80e050;
    background-color: #263d26;
    color: #b3ffb3;
    font-size: 1rem;
    transition: border-color 0.3s ease, background-color 0.3s ease;
  }

  select:focus, input[type="number"]:focus, input[type="text"]:focus {
    outline: none;
    border-color: #aaffaa;
    background-color: #2e4b2e;
  }

  button {
    background-color: #33cc33;
    border: none;
    font-weight: 700;
    cursor: pointer;
    transition: background-color 0.3s ease;
    user-select: none;
  }

  button:hover, button:focus {
    background-color: #28a428;
    outline: none;
  }

  /* Table */
  table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 50px;
    max-width: 1000px;
    user-select: none;
  }

  th, td {
    padding: 15px 20px;
    border: 1px solid #4d704d;
    text-align: left;
    vertical-align: middle;
  }

  th {
    background-color: #004d00;
    color: #b3ffb3;
    font-weight: 700;
  }

  td {
    background-color: #1b3a1b;
    color: #b3ffb3;
  }

  tr:hover td {
    background-color: #2d6b2d;
    transition: background-color 0.25s ease;
  }

  .actions {
    display: flex;
    gap: 8px;
  }

  .btn-delete {
    background-color: #cc3333;
    border: none;
    padding: 6px 14px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.9rem;
    transition: background-color 0.3s ease;
  }

  .btn-delete:hover {
    background-color: #992222;
  }

  /* Responsive */
  @media (max-width: 700px) {
    body {
      padding: 20px 10px;
    }
    form.submit-form, table {
      width: 100%;
      margin: 0 auto 40px auto;
    }
    th, td {
      padding: 10px 8px;
    }
    .actions {
      flex-direction: column;
      gap: 4px;
    }
  }
</style>

<script>
  function confirmDelete(cropId) {
    if (confirm("Are you sure you want to delete this submission?")) {
      window.location.href = "?delete=" + cropId;
    }
  }
</script>

</head>
<body>

<nav>
  <div class="logo">Farmer Portal</div>
  <div>
    <a href="farmer_dashboard.php">Dashboard</a>
    <a href="farmer_login.php">Logout</a>
  </div>
</nav>

<h2>Submit Crop Details</h2>
<form method="post" class="submit-form" aria-label="Crop submission form">
    <select name="crop_name" required aria-required="true" aria-label="Select Crop">
        <option value="" disabled selected>Select Crop</option>
        <?php foreach ($price_map as $crop_name => $data): ?>
            <option value="<?= htmlspecialchars($crop_name) ?>"><?= htmlspecialchars($crop_name) ?></option>
        <?php endforeach; ?>
    </select>
    <input type="number" name="quantity" min="0.01" step="0.01" placeholder="Quantity (kg)" required aria-required="true" aria-label="Quantity in kilograms" />
    <input type="text" name="address" placeholder="Address" required aria-required="true" aria-label="Address" />
    <input type="text" name="phone" placeholder="Phone" required aria-required="true" aria-label="Phone number" />
    <button type="submit">Submit</button>
</form>

<h2>Your Submissions</h2>
<?php if ($crops->num_rows > 0): ?>
<table aria-label="List of your submitted crops">
    <thead>
        <tr><th>Crop</th><th>Quantity (kg)</th><th>Address</th><th>Phone</th><th>Submitted At</th><th>Actions</th></tr>
    </thead>
    <tbody>
        <?php while ($row = $crops->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['crop_name']) ?></td>
            <td><?= htmlspecialchars($row['quantity_kg']) ?></td>
            <td><?= htmlspecialchars($row['address']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['submitted_at']) ?></td>
            <td class="actions">
              <button type="button" class="btn-delete" onclick="confirmDelete(<?= $row['id'] ?>)">Delete</button>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php else: ?>
<p>You have not submitted any crops yet.</p>
<?php endif; ?>

<h2>Price Information</h2>
<table aria-label="Crop price information">
    <thead>
        <tr><th>Crop</th><th>Min Price</th><th>Max Price</th></tr>
    </thead>
    <tbody>
        <?php foreach ($price_map as $crop => $data): ?>
        <tr>
            <td><?= htmlspecialchars($crop) ?></td>
            <td><?= htmlspecialchars($data['min_price']) ?></td>
            <td><?= htmlspecialchars($data['max_price']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
