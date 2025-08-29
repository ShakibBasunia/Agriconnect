<?php
session_start();
include('db_connection.php'); // Database connection

// Fetch farmer's uploaded products and their shipment status
$farmer_id = $_SESSION['farmer_id']; // Assuming farmer is logged in and their ID is stored in session
$sql = "SELECT p.*, t.shipment_status 
        FROM farmer_products p 
        LEFT JOIN transactions t ON p.id = t.product_id 
        WHERE p.farmer_id = '$farmer_id'";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer - Product Upload & Shipment Status</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include('farmer_navbar.php'); ?> <!-- Farmer-specific Navbar -->

    <div class="container mt-5">
        <h2>Farmer - My Products</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Shipment Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['product_name']; ?></td>
                    <td><?php echo $row['price']; ?></td>
                    <td><?php echo $row['shipment_status']; ?></td>
                    <td>
                        <!-- Allow the farmer to edit/delete product -->
                        <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">Edit</a>
                        <a href="delete_product.php?id=<?php echo $row['id']; ?>" class="btn btn-danger">Delete</a>
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
