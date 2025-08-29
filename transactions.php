<?php
include 'db_connection.php';
session_start();

if (!isset($_SESSION['wholeseller_id'])) {
    header('Location: wholeseller_login.php');
    exit();
}

$wholeseller_id = $_SESSION['wholeseller_id'];

// Fetch the transactions for the wholeseller
$sql = "SELECT t.*, f.product_name, f.price_per_kg FROM transactions t 
        JOIN farmer_products f ON t.product_id = f.id 
        WHERE t.wholeseller_id = ? ORDER BY t.transaction_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $wholeseller_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Transaction History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #1a1a2e; color: white; }
        .navbar { background-color: #6f42c1; }
        .nav-link, .navbar-brand { color: white !important; }
        .nav-link:hover { color: #ff66cc !important; text-decoration: underline; }
    </style>
</head>
<body>

<?php include 'wholeseller_navbar.php'; ?>

<div class="container mt-5">
    <h2>Transaction History</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Shipment Status</th>
                <th>Transaction Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                    <td><?php 
                        // Assuming quantity is calculated based on amount and price per kg
                        $quantity_bought = $row['amount'] / $row['price_per_kg']; 
                        echo number_format($quantity_bought, 2); 
                    ?> kg</td>
                    <td>Taka <?php echo number_format($row['amount'], 2); ?></td>
                    <td><?php echo htmlspecialchars($row['shipment_status']); ?></td>
                    <td><?php echo htmlspecialchars($row['transaction_date']); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>
