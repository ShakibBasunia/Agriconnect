<?php
include 'db_connection.php';
session_start();

if (!isset($_SESSION['wholeseller_id'])) {
    header('Location: wholeseller_login.php');
    exit();
}

$sql = "SELECT * FROM farmer_products ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
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
      --glass-2: rgba(255,255,255,0.02);
      --card-radius: 16px;
      --gap: 28px;
    }

    *{box-sizing:border-box;margin:0;padding:0}
    html,body{height:100%}
    body{
      font-family: 'Poppins', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      color:var(--muted);
      background: radial-gradient(1000px 600px at 10% 10%, rgba(128,255,128,0.05), transparent 10%),
                  linear-gradient(180deg,var(--bg-1), var(--bg-2) 60%);
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
      line-height:1.4;
      padding-bottom:60px;
    }

    /* Navbar */
    header.site-header{
      position:relative; z-index:10;
      padding:18px 32px;
      display:flex; align-items:center; justify-content:space-between;
      gap:12px;
      background: linear-gradient(90deg, var(--bg-1), var(--bg-2));
      box-shadow: 0 4px 12px rgba(0,0,0,0.7);
    }
    .logo {
      display:flex; align-items:center; gap:12px; text-decoration:none;
      color:var(--muted);
      font-weight:700; font-size:1.15rem;
      user-select:none;
    }
    nav {
      display:flex; align-items:center; gap:18px;
    }
    nav a{
      color:var(--muted);
      text-decoration:none; padding:8px 12px; border-radius:10px; font-weight:600;
      position: relative;
      transition: all 0.3s ease;
    }
    nav a::after {
      content: "";
      position: absolute;
      left: 0;
      bottom: -5px;
      width: 0%;
      height: 3px;
      background: var(--accent);
      transition: width 0.3s ease;
      border-radius: 3px;
    }
    nav a:hover,
    nav a:focus {
      background:var(--glass);
      color:var(--accent);
      outline: none;
      transform:translateY(-2px);
    }
    nav a:hover::after,
    nav a:focus::after {
      width: 100%;
    }

    /* Product Cards */
    .product-container {
      max-width: 1200px;
      margin: 60px auto;
      padding: 20px;
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: var(--gap);
    }
    .card {
      background-color: var(--card);
      border-radius: var(--card-radius);
      width: 300px;
      overflow: hidden;
      box-shadow: 0 0 15px var(--glass);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
      transform: translateY(-6px);
      box-shadow: 0 0 30px var(--accent);
    }
    .card-img-top {
      width: 100%;
      height: 200px;
      object-fit: cover;
      background-color: var(--glass);
    }
    .card-body {
      padding: 20px;
    }
    .card-title {
      color: var(--accent);
      font-weight: 700;
      margin-bottom: 12px;
    }
    .btn {
      display: inline-block;
      padding: 8px 14px;
      border-radius: 6px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s ease;
    }
    .btn-success {
      background: linear-gradient(90deg, var(--accent-dark), #1E90FF);
      color: #06110a;
    }
    .btn-success:hover {
      transform: scale(1.05);
    }
    .btn-outline {
      background: transparent;
      border: 1px solid var(--accent);
      color: var(--accent);
    }
    .btn-outline:hover {
      background: var(--accent);
      color: #06110a;
    }
  </style>
</head>
<body>

<header class="site-header">
  <a href="home.php" class="logo">AgriConnect</a>
  <nav>
   
    <a href="wholeseller_login.php">Logout</a>
 <a href="my_orders.php">My Orders</a>
  </nav>
</header>

<main class="product-container">
  <?php if ($result->num_rows > 0): ?>
      <?php while ($product = $result->fetch_assoc()): ?>
          <div class="card">
              <?php if (!empty($product['image']) && file_exists($product['image'])): ?>
                  <img src="<?php echo $product['image']; ?>" class="card-img-top" alt="Product Image">
              <?php else: ?>
                  <img src="default_product.png" class="card-img-top" alt="No Image Available">
              <?php endif; ?>
              <div class="card-body">
                  <h5 class="card-title"><?php echo htmlspecialchars($product['product_name']); ?></h5>
                  <p>Price: ৳<?php echo number_format($product['price_per_kg'], 2); ?> /kg</p>
                  <?php if ($product['quantity'] > 0): ?>
                      <p>Available: <?php echo $product['quantity']; ?> kg</p>
                      <a href="buy_product.php?product_id=<?php echo $product['id']; ?>" class="btn btn-success btn-sm">Buy Now</a>
                  <?php else: ?>
                      <p style="color:red;">Out of Stock</p>
                  <?php endif; ?>
                  <a href="view_product.php?product_id=<?php echo $product['id']; ?>" class="btn btn-outline btn-sm">View Details</a>
              </div>
          </div>
      <?php endwhile; ?>
  <?php else: ?>
      <p>No products available at the moment.</p>
  <?php endif; ?>
</main>

</body>
</html>
