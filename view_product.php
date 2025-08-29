<?php
include 'db_connection.php';
session_start();

if (!isset($_GET['product_id'])) {
    echo "Product ID not specified.";
    exit();
}

$product_id = $_GET['product_id'];

$sql = "SELECT * FROM farmer_products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "Product not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Product Details - AgriConnect</title>
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
    * {
      box-sizing: border-box;
      margin: 0; padding: 0;
    }
    body {
      font-family: 'Poppins', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      background: radial-gradient(1000px 600px at 10% 10%, rgba(128,255,128,0.05), transparent 10%),
                  linear-gradient(180deg,var(--bg-1), var(--bg-2) 60%);
      color: var(--muted);
      line-height: 1.4;
      min-height: 100vh;
      padding-bottom: 60px;
    }
    header.site-header {
      position: relative; z-index: 10;
      padding: 18px 32px;
      display: flex; align-items: center; justify-content: space-between;
      gap: 12px;
      background: linear-gradient(90deg, var(--bg-1), var(--bg-2));
      box-shadow: 0 4px 12px rgba(0,0,0,0.7);
      user-select:none;
    }
    .logo {
      color: var(--muted);
      font-weight: 700;
      font-size: 1.15rem;
      text-decoration: none;
      user-select:none;
    }
    nav {
      display: flex;
      align-items: center;
      gap: 18px;
    }
    nav a {
      color: var(--muted);
      text-decoration: none;
      padding: 8px 12px;
      border-radius: 10px;
      font-weight: 600;
      position: relative;
      transition: all 0.3s ease;
      user-select:none;
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
      background: var(--glass);
      color: var(--accent);
      outline: none;
      transform: translateY(-2px);
    }
    nav a:hover::after,
    nav a:focus::after {
      width: 100%;
    }

    main.container {
      max-width: 960px;
      margin: 60px auto;
      padding: 20px;
      background: var(--card);
      border-radius: var(--card-radius);
      box-shadow: 0 0 15px var(--glass);
      user-select:none;
    }

    .product-details {
      display: flex;
      flex-wrap: wrap;
      gap: 30px;
      justify-content: center;
    }

    .product-image {
      flex: 1 1 300px;
      max-width: 400px;
      border-radius: var(--card-radius);
      overflow: hidden;
      box-shadow: 0 0 20px var(--accent);
    }
    .product-image img {
      width: 100%;
      height: 400px;
      object-fit: cover;
      display: block;
      user-select:none;
    }

    .product-info {
      flex: 1 1 300px;
      color: var(--muted);
      font-size: 1rem;
    }
    .product-info h2 {
      color: var(--accent);
      font-weight: 700;
      font-size: 2rem;
      margin-bottom: 16px;
      user-select:text;
    }
    .product-info p {
      margin-bottom: 16px;
      line-height: 1.5;
      user-select:text;
    }
    .product-info strong {
      font-weight: 600;
      color: var(--accent);
    }

    .btn-buy,
    .btn-disabled {
      display: inline-block;
      padding: 14px 32px;
      border-radius: 8px;
      font-weight: 700;
      font-size: 1.1rem;
      text-align: center;
      cursor: pointer;
      box-shadow: 0 6px 15px rgba(0,0,0,0.3);
      user-select:none;
      transition: background-color 0.3s ease, transform 0.3s ease;
      border: none;
    }

    .btn-buy {
      background: linear-gradient(90deg, var(--accent-dark), #1E90FF);
      color: #06110a;
      text-decoration: none;
    }
    .btn-buy:hover {
      background-color: #007acc;
      transform: scale(1.05);
      box-shadow: 0 9px 20px rgba(0,0,0,0.5);
      color: #06110a;
    }

    .btn-disabled {
      background-color: #555555;
      color: #ddd;
      cursor: not-allowed;
    }

    @media (max-width: 720px) {
      main.container {
        margin: 40px 12px;
      }
      .product-details {
        flex-direction: column;
        align-items: center;
      }
      .product-info, .product-image {
        max-width: 100%;
      }
      .product-image img {
        height: auto;
      }
    }
  </style>
</head>
<body>

<header class="site-header" role="banner">
  <a href="home.php" class="logo" aria-label="AgriConnect Home">AgriConnect</a>
  <nav role="navigation" aria-label="Primary Navigation">
    <a href="marketplace.php">Dashboard</a>
    <a href="home.php">Logout</a>
  </nav>
</header>

<main class="container" role="main" aria-labelledby="product-title">
  <div class="product-details">
    <div class="product-image" aria-label="Image of <?php echo htmlspecialchars($product['product_name']); ?>">
      <?php if (!empty($product['image']) && file_exists($product['image'])): ?>
          <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?> Image" />
      <?php else: ?>
          <img src="default_product.png" alt="No Image Available" />
      <?php endif; ?>
    </div>
    <div class="product-info">
      <h2 id="product-title"><?php echo htmlspecialchars($product['product_name']); ?></h2>
      <p><strong>Price:</strong> ৳<?php echo number_format($product['price_per_kg'], 2); ?> /kg</p>
      <p><strong>Description:</strong><br><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
      <p><strong>Available Quantity:</strong> 
        <?php echo $product['quantity'] > 0 ? $product['quantity'] . ' kg' : '<span style="color:#ff6666;">Out of Stock</span>'; ?>
      </p>

      <?php if ($product['quantity'] > 0): ?>
        <a href="buy_product.php?product_id=<?php echo $product['id']; ?>" class="btn-buy" role="button" aria-label="Buy <?php echo htmlspecialchars($product['product_name']); ?>">Buy Now</a>
      <?php else: ?>
        <button class="btn-disabled" disabled aria-disabled="true">Out of Stock</button>
      <?php endif; ?>
    </div>
  </div>
</main>

</body>
</html>
