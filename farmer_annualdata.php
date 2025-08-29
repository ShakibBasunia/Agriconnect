<?php
include 'db_connection.php';
session_start();
$farmer_id = $_SESSION['farmer_id'] ?? 1; // fallback for demo/testing

// Insert crop data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_crop'])) {
    $crop_id = $_POST['crop_id'];
    $qty = $_POST['quantity_kg'];
    $price = $_POST['sell_price_per_kg'];
    $year = $_POST['year'];
    $stmt = $conn->prepare("INSERT INTO farmer_cultivations (farmer_id, crop_id, quantity_kg, sell_price_per_kg, year) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiddi", $farmer_id, $crop_id, $qty, $price, $year);
    $stmt->execute();
}

// Delete crop entry
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $conn->query("DELETE FROM farmer_cultivations WHERE id=$id AND farmer_id=$farmer_id");
}

$crops = $conn->query("SELECT * FROM crops")->fetch_all(MYSQLI_ASSOC);
$entries = $conn->query("SELECT fc.*, c.crop_name FROM farmer_cultivations fc JOIN crops c ON c.id = fc.crop_id WHERE farmer_id=$farmer_id")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Farmer Dashboard</title>
    <style>
        body { background-color: #001a00; color: white; font-family: Arial; }
        nav { background: #004d00; padding: 10px; display: flex; justify-content: space-between; align-items: center; }
        nav h2 { margin: 0; }
        form, table { margin: 20px auto; width: 80%; }
        select, input { width: 100%; padding: 10px; margin: 10px 0; }
        .btn { background: #00cc66; padding: 10px 20px; border: none; color: white; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #00994d; }
        table { width: 100%; border-collapse: collapse; background: #003300; }
        th, td { padding: 10px; border: 1px solid #00cc66; }
        th { background: #004d00; }
    </style>
</head>
<body>

<nav>
    <h2>Farmer Dashboard</h2>
</nav>

<form method="POST">
    <select name="crop_id" required>
        <option value="">Select Crop</option>
        <?php foreach ($crops as $crop): ?>
            <option value="<?= $crop['id'] ?>"><?= $crop['crop_name'] ?></option>
        <?php endforeach; ?>
    </select>
    <input type="number" name="quantity_kg" placeholder="Quantity (kg)" step="0.1" required>
    <input type="number" name="sell_price_per_kg" placeholder="Sell Price per kg" step="0.1" required>
    <input type="number" name="year" placeholder="Year" required>
    <button type="submit" name="submit_crop" class="btn">Submit</button>
</form>

<table>
    <tr><th>Crop</th><th>Qty (kg)</th><th>Price/kg</th><th>Year</th><th>Action</th></tr>
    <?php foreach ($entries as $entry): ?>
    <tr>
        <td><?= $entry['crop_name'] ?></td>
        <td><?= $entry['quantity_kg'] ?></td>
        <td>৳<?= $entry['sell_price_per_kg'] ?></td>
        <td><?= $entry['year'] ?></td>
        <td><a href="?delete_id=<?= $entry['id'] ?>" class="btn">Delete</a></td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
