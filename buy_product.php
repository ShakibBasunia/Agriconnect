<?php
include 'db_connection.php';
session_start();

if (!isset($_SESSION['wholeseller_id'])) {
    header('Location: wholeseller_login.php');
    exit();
}

if (!isset($_GET['product_id'])) {
    echo "Invalid product.";
    exit();
}

$product_id = intval($_GET['product_id']);

// Fetch product details
$sql = "SELECT * FROM farmer_products WHERE id = $product_id";
$result = $conn->query($sql);
if ($result->num_rows != 1) {
    echo "Product not found.";
    exit();
}
$product = $result->fetch_assoc();

// Handle purchase form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quantity = intval($_POST['quantity']);
    $mobile = $conn->real_escape_string($_POST['mobile']);
    $address = $conn->real_escape_string($_POST['address']);

    // Bangladeshi mobile number validation (starts with 8801)
    if (!preg_match('/^8801[3-9][0-9]{8}$/', $mobile)) {
        echo "<script>alert('দয়া করে সঠিক Bangladeshi মোবাইল নম্বর (8801XXXXXXXX) লিখুন।'); window.history.back();</script>";
        exit();
    }

    if ($quantity <= 0 || $quantity > $product['quantity']) {
        echo "<script>alert('Invalid quantity selected.'); window.history.back();</script>";
        exit();
    }

    $amount = $quantity * $product['price_per_kg'];
    $wholeseller_id = $_SESSION['wholeseller_id'];
    $farmer_id = $product['farmer_id'];
    $product_name = $product['product_name'];

    // Insert into transaction table
    $stmt = $conn->prepare("INSERT INTO transactions (farmer_id, wholeseller_id, product_name, amount, shipment_status, product_id) VALUES (?, ?, ?, ?, 'In Transit', ?)");
    $stmt->bind_param("iisdi", $farmer_id, $wholeseller_id, $product_name, $amount, $product_id);

    if ($stmt->execute()) {
        // Update product quantity
        $new_quantity = $product['quantity'] - $quantity;
        $update_product = "UPDATE farmer_products SET quantity = $new_quantity WHERE id = $product_id";
        $conn->query($update_product);

        echo "<script>alert('Purchase successful! Mobile: $mobile, Address: $address'); window.location.href='marketplace.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="bn">
<head>
    <title>Buy Product</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
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
            font-family: 'Poppins', system-ui, sans-serif;
            color:var(--muted);
            background: radial-gradient(1000px 600px at 10% 10%, rgba(128,255,128,0.05), transparent 10%),
                        linear-gradient(180deg,var(--bg-1), var(--bg-2) 60%);
            line-height:1.4;
            padding-bottom:60px;
        }
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
        }
        nav {display:flex; align-items:center; gap:18px;}
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
        nav a:hover { background:var(--glass); color:var(--accent); transform:translateY(-2px);}
        nav a:hover::after { width: 100%; }

        .container {
            max-width: 700px;
            margin: 100px auto;
            background: var(--card);
            padding: 40px 30px;
            border-radius: var(--card-radius);
            box-shadow: 0 0 20px var(--glass);
        }
        h2 {
            color: var(--accent);
            margin-bottom: 28px;
            font-weight: 700;
            font-size: 1.8rem;
        }
        label { display:block; margin-bottom:8px; font-weight:600; }
        input, textarea {
            width: 100%;
            padding: 12px;
            background: var(--glass-2);
            border: 1px solid var(--glass);
            border-radius: 8px;
            color: var(--muted);
            margin-bottom: 18px;
            font-size: 1rem;
        }
        input:focus, textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 10px var(--accent);
        }
        button {
            background: linear-gradient(90deg, var(--accent-dark), #1E90FF);
            color: #06110a;
            border: none;
            border-radius: 6px;
            padding: 14px 30px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }
        button:hover {
            transform: scale(1.05);
            box-shadow: 0 9px 20px rgba(0,0,0,0.5);
        }
        @media (max-width:640px) {
            .container {margin: 60px 12px; padding: 30px 20px;}
            h2 { font-size: 1.5rem; }
        }
    </style>
</head>
<body>

<header class="site-header">
    <a href="#" class="logo">AgriConnect</a>
    <nav>
        <a href="marketplace.php">Marketplace</a>
        <a href="wholeseller_orders.php">My Orders</a>
    </nav>
</header>

<div class="container">
    <h2>Buy <?php echo htmlspecialchars($product['product_name']); ?></h2>
    <form action="" method="post">
        <label for="quantity">Quantity to Buy (Max: <?php echo $product['quantity']; ?> kg)</label>
        <input type="number" name="quantity" id="quantity" required min="1" max="<?php echo $product['quantity']; ?>">

        <label for="mobile">Mobile Number</label>
        <input type="text" name="mobile" id="mobile" required pattern="8801[3-9][0-9]{8}" title="8801 দিয়ে শুরু হওয়া সঠিক মোবাইল নম্বর লিখুন">

        <label for="address">Delivery Address</label>
        <textarea name="address" id="address" rows="3" required></textarea>

        <button type="submit">Confirm Purchase</button>
    </form>
</div>

</body>
</html>
