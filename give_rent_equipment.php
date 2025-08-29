<?php
session_start();
include 'db_connection.php';


$farmerId   = $_SESSION['farmer_id']   ?? 1;
$farmerName = isset($_SESSION['farmer_name']) ? $_SESSION['farmer_name'] : 'Farmer X';

$message="";
$editing = false;

/* -------------------- DELETE -------------------- */
if (isset($_GET['del'])) {
    $id = intval($_GET['del']);
    $conn->query("DELETE FROM equipment_rentals WHERE id=$id AND farmer_id=$farmerId");
    header("Location: give_rent_equipment.php"); exit;
}

/* -------------------- EDIT (fetch row) ---------- */
$editRow=null;
if (isset($_GET['edit'])) {
    $editing = true;
    $eid     = intval($_GET['edit']);
    $editRow = $conn->query("SELECT * FROM equipment_rentals WHERE id=$eid AND farmer_id=$farmerId")->fetch_assoc();
}

/* -------------------- INSERT / UPDATE ----------- */
if ($_SERVER["REQUEST_METHOD"]==="POST" && isset($_POST['save_equipment'])) {

    $name  = $_POST['equipment_name'];
    $desc  = $_POST['description'];
    $price = $_POST['price'];
    $phone = $_POST['contact_phone'];
    $qty   = $_POST['qty_available'];
    $mediaPath = '';

    if ($_FILES['media']['error'] === 0) {
        $dir="uploads/";
        $mediaPath = $dir.time().'_'.basename($_FILES['media']['name']);
        move_uploaded_file($_FILES['media']['tmp_name'],$mediaPath);
    }

    /* -------- UPDATE -------- */
    if (!empty($_POST['equipment_id'])) {
        $id = intval($_POST['equipment_id']);
        if ($mediaPath){
            $sql="UPDATE equipment_rentals
                    SET name=?,description=?,price=?,contact_phone=?,qty_available=?,media=?
                  WHERE id=? AND farmer_id=?";
            $stmt=$conn->prepare($sql);
            $stmt->bind_param("ssdsssis",
                $name,$desc,$price,$phone,$qty,$mediaPath,$id,$farmerId);
        } else {
            $sql="UPDATE equipment_rentals
                    SET name=?,description=?,price=?,contact_phone=?,qty_available=?
                  WHERE id=? AND farmer_id=?";
            $stmt=$conn->prepare($sql);
            $stmt->bind_param("ssdssis",
                $name,$desc,$price,$phone,$qty,$id,$farmerId);
        }
        $stmt->execute();
        header("Location: give_rent_equipment.php"); exit;

    /* -------- INSERT -------- */
    } else {
        $sql="INSERT INTO equipment_rentals
             (farmer_id,name,description,price,contact_phone,media,qty_available)
             VALUES (?,?,?,?,?,?,?)";
        $stmt=$conn->prepare($sql);
        $stmt->bind_param("issdssi",
            $farmerId,$name,$desc,$price,$phone,$mediaPath,$qty);
        $stmt->execute();
        $message="Uploaded!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Give Equipment for Rent</title>
<style>
 body{margin:0;background:#0d0d0d;color:#d4f5c3;font-family:Poppins}
 nav{background:#004d00;padding:15px 30px;display:flex;justify-content:space-between}
 nav a{color:#d4f5c3;margin-left:20px;text-decoration:none}nav a:hover{color:#80ff80}
 .container{padding:30px}
 .section{background:#1a1a1a;padding:20px;border-radius:10px;margin-bottom:30px;box-shadow:0 0 10px #004d00}
 input,textarea{width:100%;padding:10px;margin:10px 0;border:none;border-radius:5px;background:#333;color:#fff}
 input[type=submit]{background:#80ff80;color:#000;cursor:pointer}input[type=submit]:hover{background:#00cc00}
 table {
  width: 100%;
  border-collapse: collapse;
  background: #2b2b2b;
  margin-top: 20px;
  border: 1px solid #444; /* Border around the table */
}

th, td {
  padding: 12px;
  text-align: center; /* Center-aligns the text */
  border: 1px solid #444; /* Border around the cells */
  color: #fff;
}

th {
  background: #004d00;
}

 img{width:80px;border-radius:4px}
 .action a{color:#80ff80;text-decoration:none;margin:0 4px}.action a:hover{color:#ff8080}
 .title{font-size:24px;color:#80ff80;margin-bottom:20px}
</style>
</head>
<body>
<nav>
  <div><strong>AgriConnect</strong></div>
  <div><a href="farmer_dashboard.php">Dashboard</a><a href="farmerlogout.php">Logout</a></div>
</nav>

<div class="container">
 <h2 class="title"><?= $editing?'Edit':'Upload' ?> Equipment</h2>

 <form class="section" method="POST" enctype="multipart/form-data">
  <input type="hidden" name="equipment_id" value="<?= $editRow['id'] ?? '' ?>">
  <label>Name</label>
  <input type="text" name="equipment_name" value="<?= $editRow['name'] ?? '' ?>" required>
  <label>Description</label>
  <textarea name="description" required><?= $editRow['description'] ?? '' ?></textarea>
  <label>Price (per day)</label>
  <input type="number" name="price" step="0.01" value="<?= $editRow['price'] ?? '' ?>" required>
  <label>Quantity Available</label>
  <input type="number" name="qty_available" min="1" value="<?= $editRow['qty_available'] ?? 1 ?>" required>
  <label>Contact Phone</label>
  <input type="text" name="contact_phone" value="<?= $editRow['contact_phone'] ?? '' ?>" required>
  <label>Media (leave blank to keep existing)</label>
  <input type="file" name="media" accept="image/*,video/*">
  <input type="submit" name="save_equipment" value="<?= $editing?'Update':'Upload' ?>">
  <p><?= $message ?></p>
 </form>

 <!-- My listings -->
 <div class="section">
  <h3>My Uploaded Equipment</h3>
  <table>
    <tr><th>Media</th><th>Name</th><th>Description</th><th>Price</th><th>Qty Left</th><th>Action</th></tr>
    <?php
      $res=$conn->query("SELECT * FROM equipment_rentals WHERE farmer_id=$farmerId");
      while($e=$res->fetch_assoc()){
        echo "<tr>
          <td><img src='{$e['media']}'></td>
          <td>{$e['name']}</td>
          <td>{$e['description']}</td>
          <td>৳{$e['price']}</td>
          <td>{$e['qty_available']}</td>
          <td class='action'>
             <a href='give_rent_equipment.php?edit={$e['id']}'>Edit</a>|
             <a href='give_rent_equipment.php?del={$e['id']}'
                onclick=\"return confirm('Delete this listing?');\">Delete</a>
          </td>
        </tr>";
      }
    ?>
  </table>
 </div>

 <!-- Booked sessions -->
 <div class="section">
  <h3>Booked Sessions</h3>
  <table>
   <tr><th>Qty</th><th>Contact</th><th>Date</th><th>Time</th><th>Status</th></tr>
   <?php
     $q="SELECT b.*, e.name equip FROM rental_bookings b
         JOIN equipment_rentals e ON e.id=b.rental_id
         WHERE e.farmer_id=$farmerId AND b.status='Booked'";
     $r=$conn->query($q);
     while($b=$r->fetch_assoc()){
       echo "<tr>
         <td>{$b['qty']}</td>
         <td>{$b['contact_number']}</td>
         <td>{$b['date']}</td>
         <td>{$b['time_slot']}</td>
         <td>{$b['status']}</td>
       </tr>";
     }
   ?>
  </table>
 </div>

 <!-- Completed -->
 <div class="section">
  <h3>Completed Services</h3>
  <table>
   <tr><th>Qty</th><th>Contact</th><th>Date</th><th>Time</th></tr>
   <?php
     $q="SELECT b.* FROM rental_bookings b
         JOIN equipment_rentals e ON e.id=b.rental_id
         WHERE e.farmer_id=$farmerId AND b.status='Completed'";
     $r=$conn->query($q);
     while($b=$r->fetch_assoc()){
       echo "<tr>
         <td>{$b['qty']}</td>
         <td>{$b['contact_number']}</td>
         <td>{$b['date']}</td>
         <td>{$b['time_slot']}</td>
       </tr>";
     }
   ?>
  </table>
 </div>
</div>
</body>
</html>
