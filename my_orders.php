<?php
include 'db_connection.php';
session_start();

// Check wholeseller login
if (!isset($_SESSION['wholeseller_id'])) {
    header('Location: wholeseller_login.php');
    exit();
}
$wholeseller_id = $_SESSION['wholeseller_id'];

// Enable exceptions for MySQLi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$error = '';
$success = '';

if (isset($_GET['product_id'])) {
    // --- BUY PRODUCT PAGE ---
    $product_id = intval($_GET['product_id']);

    // Fetch product info
    $stmt = $conn->prepare("SELECT * FROM farmer_products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    if (!$product) {
        echo "<p style='color:red;font-weight:bold;'>Product not found. <a href='marketplace.php'>Go back to Marketplace</a></p>";
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $quantity = floatval($_POST['quantity']);

        if ($quantity <= 0) {
            $error = "Please enter a valid quantity.";
        } elseif ($quantity > $product['quantity']) {
            $error = "Only {$product['quantity']} kg available in stock.";
        } else {
            $total_amount = $quantity * $product['price_per_kg'];

            // Begin transaction
            $conn->begin_transaction();

            try {
                // Insert order
                $insertStmt = $conn->prepare(
                    "INSERT INTO orders (wholeseller_id, product_id, quantity, total_amount, status) 
                     VALUES (?, ?, ?, ?, 'pending')"
                );
                $insertStmt->bind_param("iidd", $wholeseller_id, $product_id, $quantity, $total_amount);
                $insertStmt->execute();

                // Update product quantity
                $new_quantity = $product['quantity'] - $quantity;
                $updateStmt = $conn->prepare("UPDATE farmer_products SET quantity = ? WHERE id = ?");
                $updateStmt->bind_param("di", $new_quantity, $product_id);
                $updateStmt->execute();

                $conn->commit();
                $success = "Order placed successfully! Total: ৳" . number_format($total_amount, 2);
                $product['quantity'] = $new_quantity; // Update local variable for display
            } catch (Exception $e) {
                $conn->rollback();
                $error = "Order failed: " . $e->getMessage();
            }
        }
    }

} else {
    // --- PRODUCT LIST PAGE ---
    $products_result = $conn->query("SELECT * FROM farmer_products ORDER BY id DESC");
    $products_list = $products_result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Marketplace - AgriConnect</title>
<style>
:root{
  --bg-1: #07130b;
  --bg-2: #0b2a12;
  --card: #07120f;
  --accent: #80ff80;
  --accent-dark: #2e7d32;
  --muted: #cfead1;
  --glass: rgba(255,255,255,0.03);
  --card-radius: 16px;
}
*{box-sizing:border-box;margin:0;padding:0}
body {
  font-family: 'Poppins', sans-serif;
  background: radial-gradient(1000px 600px at 10% 10%, rgba(128,255,128,0.05), transparent 10%),
              linear-gradient(180deg,var(--bg-1), var(--bg-2) 60%);
  color: var(--muted);
  min-height: 100vh;
  padding-bottom: 60px;
}
header.site-header {
  padding: 18px 32px;
  display: flex; align-items: center; justify-content: space-between;
  background: linear-gradient(90deg, var(--bg-1), var(--bg-2));
  box-shadow: 0 4px 12px rgba(0,0,0,0.7);
}
.logo { color: var(--muted); font-weight: 700; font-size: 1.15rem; text-decoration: none; }
nav { display: flex; align-items: center; gap: 18px; }
nav a { color: var(--muted); text-decoration: none; padding: 8px 12px; border-radius: 10px; font-weight: 600; transition: all 0.3s ease; }
nav a:hover { background: var(--glass); color: var(--accent); }
main.container { max-width: 900px; margin: 60px auto; padding: 20px; background: var(--card); border-radius: var(--card-radius); box-shadow: 0 0 15px var(--glass); }
h1 { margin-bottom: 24px; color: var(--accent); text-align: center; }
.product-img { width: 100%; max-height: 200px; object-fit: cover; border-radius: 12px; margin-bottom: 10px; }
.product-card { display: inline-block; width: 280px; margin: 10px; padding: 12px; background: rgba(15,40,20,0.85); border-radius: var(--card-radius); box-shadow: 0 6px 18px var(--glass); vertical-align: top; }
.product-card h3 { margin-bottom: 6px; }
.product-card p { font-size: 0.9rem; margin-bottom: 6px; }
.product-card a { display: inline-block; padding: 8px 12px; background: linear-gradient(90deg, var(--accent-dark), #1E90FF); color: #06110a; border-radius: 6px; font-weight: 600; text-decoration: none; transition: all 0.3s ease; }
.product-card a:hover { transform: scale(1.05); }
.message { padding: 12px; margin-bottom: 20px; border-radius: 8px; font-weight: 600; text-align: center; }
.error { background-color: #e53935; color: white; }
.success { background-color: #4caf50; color: white; }
</style>
</head>
<body>

<header class="site-header">
  <a href="wholeseller_dashboard.php" class="logo">AgriConnect</a>
  <nav>
    <a href="wholeseller_login.php">Logout</a>
      <a href="home.php">Home</a>
  </nav>
</header>

<main class="container">
<?php if (isset($product)): ?>
  <!-- Buy Product Page -->
  <h1>Buy Product</h1>
  <?php if ($error): ?><div class="message error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="message success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
  <?php if (!empty($product['image']) && file_exists($product['image'])): ?>
    <img src="<?= htmlspecialchars($product['image']) ?>" alt="Product Image" class="product-img">
  <?php else: ?>
    <img src="default_product.png" alt="No Image Available" class="product-img">
  <?php endif; ?>
  <p><strong>Name:</strong> <?= htmlspecialchars($product['product_name']) ?></p>
  <p><strong>Price:</strong> ৳<?= number_format($product['price_per_kg'],2) ?>/kg</p>
  <p><strong>Available:</strong> <?= $product['quantity'] ?> kg</p>
  <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($product['description'])) ?></p>

  <?php if ($product['quantity'] > 0): ?>

  <?php else: ?>
    <p style="color:#e53935;font-weight:700;">Out of Stock</p>
  <?php endif; ?>

<?php else: ?>
  <!-- Product List Page -->
  <h1>Marketplace</h1>
  <?php if (empty($products_list)): ?>
    <p>No products available at the moment.</p>
  <?php else: ?>
    <?php foreach ($products_list as $prod): ?>
      <div class="product-card">
        <?php if (!empty($prod['image']) && file_exists($prod['image'])): ?>
          <img src="<?= htmlspecialchars($prod['image']) ?>" alt="Product Image" class="product-img">
        <?php else: ?>
          <img src="default_product.png" alt="No Image" class="product-img">
        <?php endif; ?>
        <h3><?= htmlspecialchars($prod['product_name']) ?></h3>
        <p>Quantity:<?= number_format($prod['price_per_kg'],2) ?>kg</p>
       
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
<?php endif; ?>
</main>

</body>
</html>
