<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['farmer_id'])) {
    header('Location: farmer_login.php');
    exit();
}

$farmer_id = $_SESSION['farmer_id'];

// Handle product upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_product'])) {
    if (isset($_POST['product_name'], $_POST['price_per_kg'], $_POST['quantity'])) {
        $product_name = $_POST['product_name'];
        $price_per_kg = $_POST['price_per_kg'];
        $quantity = $_POST['quantity'];
        $description = $_POST['description'] ?? '';

        $file_destination = null;
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $file = $_FILES['file'];
            $file_name = $file['name'];
            $file_tmp = $file['tmp_name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($file_ext, $allowed)) {
                $file_new_name = uniqid('', true) . '.' . $file_ext;
                $file_destination = 'uploads/' . $file_new_name;

                if (!move_uploaded_file($file_tmp, $file_destination)) {
                    echo "<script>alert('File upload failed.');</script>";
                    exit;
                }
            } else {
                echo "<script>alert('Invalid file type.');</script>";
                exit;
            }
        }

        $stmt = $conn->prepare("INSERT INTO farmer_products (farmer_id, product_name, quantity, price_per_kg, image, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isidss", $farmer_id, $product_name, $quantity, $price_per_kg, $file_destination, $description);

        if ($stmt->execute()) {
            echo "<script>alert('Product uploaded successfully!'); window.location.href='farmer_product_upload.php';</script>";
            exit();
        } else {
            echo "<script>alert('Database error: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('All fields are required.');</script>";
    }
}

// Handle product delete
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    // Delete image if exists
    $img_query = $conn->query("SELECT image FROM farmer_products WHERE id = '$delete_id' AND farmer_id = '$farmer_id'");
    if ($img_query && $img_row = $img_query->fetch_assoc()) {
        if ($img_row['image'] && file_exists($img_row['image'])) {
            unlink($img_row['image']);
        }
    }
    $conn->query("DELETE FROM farmer_products WHERE id = '$delete_id' AND farmer_id = '$farmer_id'");
    echo "<script>alert('Product deleted successfully!'); window.location.href='farmer_product_upload.php';</script>";
    exit();
}

// Handle product update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product'])) {
    $update_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $price_per_kg = $_POST['price_per_kg'];
    $quantity = $_POST['quantity'];
    $description = $_POST['description'];

    // Optional image upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $file = $_FILES['file'];
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_ext, $allowed)) {
            $file_new_name = uniqid('', true) . '.' . $file_ext;
            $file_destination = 'uploads/' . $file_new_name;

            if (move_uploaded_file($file_tmp, $file_destination)) {
                $stmt = $conn->prepare("UPDATE farmer_products SET product_name=?, quantity=?, price_per_kg=?, image=?, description=? WHERE id=? AND farmer_id=?");
                $stmt->bind_param("sidssii", $product_name, $quantity, $price_per_kg, $file_destination, $description, $update_id, $farmer_id);
            } else {
                echo "<script>alert('File upload failed.');</script>";
                exit();
            }
        } else {
            echo "<script>alert('Invalid file type.');</script>";
            exit();
        }
    } else {
        $stmt = $conn->prepare("UPDATE farmer_products SET product_name=?, quantity=?, price_per_kg=?, description=? WHERE id=? AND farmer_id=?");
        $stmt->bind_param("sidssi", $product_name, $quantity, $price_per_kg, $description, $update_id, $farmer_id);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Product updated successfully!'); window.location.href='farmer_product_upload.php';</script>";
        exit();
    } else {
        echo "<script>alert('Update failed: " . $conn->error . "');</script>";
    }
}

$query = "SELECT * FROM farmer_products WHERE farmer_id = '$farmer_id' ORDER BY created_at DESC";
$products = $conn->query($query);

$edit_product = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $edit_query = $conn->query("SELECT * FROM farmer_products WHERE id='$edit_id' AND farmer_id='$farmer_id'");
    if ($edit_query && $edit_query->num_rows > 0) {
        $edit_product = $edit_query->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Farmer - Manage Products</title>
<style>
  /* Base CSS variables and reset from your theme */
  :root {
    --bg-1: #07130b;
    --bg-2: #0b2a12;
    --card: #07120f;
    --accent: #80ff80;
    --accent-dark: #2e7d32;
    --muted: #cfead1;
    --glass: rgba(255, 255, 255, 0.03);
    --glass-2: rgba(255, 255, 255, 0.02);
    --card-radius: 16px;
    --gap: 28px;
  }

  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }

  html, body {
    height: 100%;
  }

  body {
    font-family: 'Poppins', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    color: var(--muted);
    background: radial-gradient(1000px 600px at 10% 10%, rgba(128, 255, 128, 0.05), transparent 10%),
                linear-gradient(180deg, var(--bg-1), var(--bg-2) 60%);
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    line-height: 1.4;
    padding-bottom: 60px;
    min-height: 100vh;
  }

  /* Navbar */
  nav {
    position: sticky;
    top: 0;
    background: var(--bg-1);
    border-bottom: 1px solid var(--glass);
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 32px;
    z-index: 10;
  }

  nav .logo {
    color: var(--muted);
    font-weight: 700;
    font-size: 1.25rem;
    text-decoration: none;
  }

  nav ul {
    list-style: none;
    display: flex;
    gap: 20px;
  }

  nav ul li a {
    color: var(--muted);
    text-decoration: none;
    padding: 8px 12px;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.18s ease;
  }

  nav ul li a:hover,
  nav ul li a:focus {
    background: var(--glass);
    color: var(--accent);
    outline: none;
    transform: translateY(-2px);
  }

  /* Container */
  .container {
    max-width: 900px;
    margin: 40px auto;
    background: var(--card);
    padding: 32px;
    border-radius: var(--card-radius);
    box-shadow: 0 8px 24px rgba(46, 125, 50, 0.4);
  }

  h2 {
    color: var(--accent);
    margin-bottom: 20px;
  }

  form .form-group {
    margin-bottom: 20px;
  }

  label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: var(--muted);
  }

  input[type="text"],
  input[type="number"],
  input[type="file"],
  textarea {
    width: 100%;
    padding: 10px 14px;
    border-radius: 12px;
    background: var(--bg-2);
    border: 1px solid var(--glass);
    color: var(--muted);
    font-size: 1rem;
    transition: border-color 0.3s ease;
  }

  input[type="text"]:focus,
  input[type="number"]:focus,
  input[type="file"]:focus,
  textarea:focus {
    outline: none;
    border-color: var(--accent);
    background: var(--bg-1);
  }

  button {
    background: linear-gradient(90deg, var(--accent-dark), #1E90FF);
    color: #06110a;
    border: none;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.45);
    transition: transform 0.18s ease, box-shadow 0.18s ease;
  }

  button:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.6);
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 32px;
    color: var(--muted);
  }

  th, td {
    padding: 14px 12px;
    border: 1px solid var(--glass);
    text-align: left;
  }

  th {
    background: var(--bg-2);
    font-weight: 600;
    color: var(--accent);
  }

  tr:nth-child(even) {
    background: var(--bg-1);
  }

  tr:hover {
    background: var(--bg-2);
  }

  img {
    border-radius: 12px;
    max-width: 80px;
    height: auto;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
  }

  .btn-edit,
  .btn-delete {
    padding: 6px 12px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    transition: background-color 0.3s ease;
    user-select: none;
  }

  .btn-edit {
    background: #ffa500;
    color: #1b1b00;
  }

  .btn-edit:hover {
    background: #cc8400;
    color: #fff;
  }

  .btn-delete {
    background: #dc3545;
    color: #fff;
  }

  .btn-delete:hover {
    background: #a0262f;
  }

  /* Responsive */
  @media (max-width: 600px) {
    nav {
      flex-direction: column;
      align-items: flex-start;
      gap: 10px;
    }
    nav ul {
      flex-direction: column;
      gap: 10px;
    }
    .container {
      margin: 20px;
      padding: 20px;
    }
    table, th, td {
      font-size: 0.9rem;
    }
    button {
      width: 100%;
    }
  }
</style>
</head>
<body>

<nav>
  <a href="#" class="logo">AgriConnect - Farmer</a>
  <ul>
    <li><a href="farmer_transactions.php">View Transactions</a></li>
    <li><a href="farmer_login.php">Logout</a></li>
  </ul>
</nav>

<div class="container">

<?php if ($edit_product): ?>
    <h2>Edit Product</h2>
    <form action="farmer_product_upload.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="product_id" value="<?= $edit_product['id'] ?>">
        <div class="form-group">
            <label>Product Name</label>
            <input type="text" name="product_name" value="<?= htmlspecialchars($edit_product['product_name']) ?>" required>
        </div>
        <div class="form-group">
            <label>Quantity (kg)</label>
            <input type="number" name="quantity" value="<?= $edit_product['quantity'] ?>" required>
        </div>
        <div class="form-group">
            <label>Price per Kg</label>
            <input type="number" step="0.01" name="price_per_kg" value="<?= $edit_product['price_per_kg'] ?>" required>
        </div>
        <div class="form-group">
            <label>Change Image (optional)</label>
            <input type="file" name="file">
        </div>
        <div class="form-group">
            <label>Product Description</label>
            <textarea name="description" rows="3" required><?= htmlspecialchars($edit_product['description']) ?></textarea>
        </div>
        <button type="submit" name="update_product">Update Product</button>
        <a href="farmer_product_upload.php" style="margin-left: 15px; color: var(--accent); font-weight:600; text-decoration:none;">Cancel</a>
    </form>

<?php else: ?>
    <h2>Upload New Product</h2>
    <form action="farmer_product_upload.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>Product Name</label>
            <input type="text" name="product_name" required>
        </div>
        <div class="form-group">
            <label>Quantity (kg)</label>
            <input type="number" name="quantity" required>
        </div>
        <div class="form-group">
            <label>Price per Kg</label>
            <input type="number" step="0.01" name="price_per_kg" required>
        </div>
        <div class="form-group">
            <label>Upload Image</label>
            <input type="file" name="file" required>
        </div>
        <div class="form-group">
            <label>Product Description</label>
            <textarea name="description" rows="3" required></textarea>
        </div>
        <button type="submit" name="upload_product">Upload Product</button>
    </form>
<?php endif; ?>

<hr>

<h2>My Uploaded Products</h2>
<table>
    <thead>
        <tr>
            <th>Image</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Price per Kg</th>
            <th>Uploaded At</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $products->fetch_assoc()): ?>
        <tr>
            <td>
                <?php if ($row['image'] && file_exists($row['image'])): ?>
                    <img src="<?= htmlspecialchars($row['image']) ?>" alt="Product Image">
                <?php else: ?>
                    No Image
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($row['product_name']) ?></td>
            <td><?= $row['quantity'] ?> kg</td>
            <td>Taka <?= $row['price_per_kg'] ?></td>
            <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
            <td>
                <a href="farmer_product_upload.php?edit_id=<?= $row['id'] ?>" class="btn-edit" tabindex="0">Edit</a>
                <a href="farmer_product_upload.php?delete_id=<?= $row['id'] ?>" onclick="return confirm('Are you sure to delete this product?')" class="btn-delete" tabindex="0">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</div>

</body>
</html>
