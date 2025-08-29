<?php
session_start();
include('db_connection.php');

// Check if the wholesaler is logged in
if (!isset($_SESSION['wholeseller_id'])) {
    header('Location: wholeseller_login.php');
    exit();
}

// Fetch products with 'In Transit' status for the current wholesaler
$wholeseller_id = $_SESSION['wholeseller_id'];
$sql = "SELECT t.*, f.product_name, f.price_per_kg, f.quantity 
        FROM transactions t 
        JOIN farmer_products f ON t.product_id = f.id 
        WHERE t.wholeseller_id = ? AND t.shipment_status = 'In Transit'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $wholeseller_id);
$stmt->execute();
$result = $stmt->get_result();

// Mark product as received
if (isset($_GET['mark_received'])) {
    $transaction_id = $_GET['mark_received'];

    $update_sql = "UPDATE transactions SET shipment_status = 'Received' WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("i", $transaction_id);
    $update_stmt->execute();

    // Redirect to the same page after the update
    header('Location: receive_product.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wholesaler - Receive Products</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<?php include('wholeseller_navbar.php'); ?> <!-- Wholeseller-specific Navbar -->

<div class="container mt-5">
    <h2>Wholesaler - Received Products</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Farmer</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['product_name']; ?></td>
                <td>$<?php echo $row['price_per_kg']; ?></td>
                <td><?php echo $row['quantity']; ?> kg</td>
                <td><?php echo $row['farmer_id']; ?> <!-- Add farmer name if you wish by joining farmer table --></td>
                <td>
                    <a href="receive_product.php?mark_received=<?php echo $row['id']; ?>" class="btn btn-success">Mark as Received</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
