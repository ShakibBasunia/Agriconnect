<?php
session_start();
include 'db_connection.php';

$me     = $_SESSION['farmer_id']   ?? 1;
$meName = $_SESSION['farmer_name'] ?? 'Farmer X';

/* ----------  Book  ---------- */
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['book_equipment'])) {
    $rid  = intval($_POST['rental_id']);
    $qty  = intval($_POST['qty']);
    $loc  = $_POST['location'];
    $dt   = $_POST['date'];
    $slot = $_POST['time'];
    $ph   = $_POST['contact_phone'];

    $item = $conn->query("SELECT * FROM equipment_rentals WHERE id=$rid")->fetch_assoc();

    if ($item['farmer_id']==$me)          $error="You can’t book your own listing.";
    elseif ($item['qty_available']<$qty)  $error="Only {$item['qty_available']} left.";
    else {
        $chk=$conn->prepare("SELECT id FROM rental_bookings WHERE rental_id=? AND date=?");
        $chk->bind_param("is",$rid,$dt); $chk->execute();
        if($chk->get_result()->num_rows)  $error="Already booked for that date.";
        else{
            $ins = $conn->prepare("INSERT INTO rental_bookings
            (rental_id, booked_by_id, customer_name, location, date, time_slot, contact_number, qty, status)
            VALUES (?,?,?,?,?,?,?,?,'Booked')");
        $ins->bind_param("iisssssi", $rid, $me, $meName, $loc, $dt, $slot, $ph, $qty);
        $ins->execute();
        
        
            $conn->query("UPDATE equipment_rentals SET qty_available=qty_available-$qty WHERE id=$rid");
            $success="Booked!";
        }
    }
}

/* ----------  Complete  ---------- */
if (isset($_POST['mark_received'])) {
    $bid=intval($_POST['booking_id']);
    $row=$conn->query("SELECT rental_id,qty FROM rental_bookings WHERE id=$bid")->fetch_assoc();
    $conn->query("UPDATE rental_bookings SET status='Completed' WHERE id=$bid");
    $conn->query("UPDATE equipment_rentals SET qty_available=qty_available+{$row['qty']}
                  WHERE id={$row['rental_id']}");
}

/* ----------  Fetch data  ---------- */
$listings=$conn->query("SELECT e.*,f.first_name,f.last_name
                        FROM equipment_rentals e JOIN farmers f ON f.id=e.farmer_id");

$cur = $conn->query("SELECT b.*,e.name equip
                     FROM rental_bookings b JOIN equipment_rentals e ON e.id=b.rental_id
                     WHERE b.booked_by_id=$me AND b.status='Booked'
                     ORDER BY b.created_at DESC");

$hist= $conn->query("SELECT b.*,e.name equip,e.price unit_price,f.first_name,f.last_name
                     FROM rental_bookings b
                     JOIN equipment_rentals e ON e.id=b.rental_id
                     JOIN farmers f ON f.id=e.farmer_id
                     WHERE b.booked_by_id=$me AND b.status='Completed'
                     ORDER BY b.created_at DESC");
?>
<!DOCTYPE html><html lang="en"><head><meta charset="utf-8">
<title>Rent Equipment</title>
<style>
 body{margin:0;background:#0d0d0d;color:#d4f5c3;font-family:Poppins}
 nav{background:#004d00;padding:15px 30px;display:flex;justify-content:space-between}
 nav a{color:#d4f5c3;text-decoration:none;margin-left:20px}nav a:hover{color:#80ff80}
 .container{padding:30px}
 .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:20px}
 .card{background:#1a1a1a;padding:15px;border-radius:10px;box-shadow:0 0 10px #004d00;cursor:pointer;transition:.3s}
 .card:hover{transform:translateY(-5px);box-shadow:0 0 15px #80ff80}
 .card.disabled{opacity:.35;pointer-events:none}
 .card img,.card video{width:100%;height:150px;object-fit:cover;border-radius:6px}
 .full{color:#ff5050;font-weight:bold}
 .badge{background:#ff5050;color:#fff;font-size:11px;border-radius:4px;padding:2px 6px;margin-left:6px}
 /* modal */
 .modal{display:none;position:fixed;inset:0;background:#000a;align-items:center;justify-content:center;z-index:999}
 .box{background:#1a1a1a;padding:25px;border-radius:10px;width:380px;max-height:90vh;overflow:auto;box-shadow:0 0 10px #004d00}
 .close{float:right;font-weight:bold;color:#fff;cursor:pointer}
 input,select{width:100%;padding:10px;margin:8px 0;border:none;border-radius:5px;background:#333;color:#fff}
 input[type=submit]{background:#80ff80;color:#000;cursor:pointer}input[type=submit]:hover{background:#00cc00}
 table {
  width: 100%;
  margin-top: 40px;
  border-collapse: collapse;
  background: #1a1a1a;
  border: 1px solid #333; /* Border around the table */
}

th, td {
  padding: 10px;
  text-align: center; /* Center-aligns the text */
  border: 1px solid #333; /* Border around the cells */
  color: #fff;
}

th {
  background: #004d00;
}

</style></head>
<body>
<nav>
  <div><b>AgriConnect</b></div>
  <div><a href="give_rent_equipment.php">Give Rent</a><a href="farmerlogout.php">Logout</a></div>
</nav>

<div class="container">
 <h2>Equipment & Services for Rent</h2>
 <?php if(isset($error))   echo "<p style='color:#ff8080'>$error</p>"; ?>
 <?php if(isset($success)) echo "<p style='color:#80ff80'>$success</p>"; ?>

 <div class="grid">
 <?php while($r=$listings->fetch_assoc()):
   $mine=$r['farmer_id']==$me; $out=$r['qty_available']<=0;
   $media = preg_match('/\.(mp4|webm)$/i',$r['media'])
            ? "<video src='{$r['media']}'></video>" : "<img src='{$r['media']}'>";
 ?>
  <div class="card <?=($mine||$out)?'disabled':''?>" <?php if(!$mine&& !$out):?>onclick="openM(<?=$r['id']?>)"<?php endif;?>>
    <?=$media?>
    <h4><?=$r['name']?></h4>
    <small>Service by <?=$r['first_name']?> <?=$r['last_name']?></small>
    <?php if($mine):?><span class="badge">Your listing</span><?php endif;?>
    <?php if($out):?><div class="full">Fully booked</div><?php endif;?>
  </div>

  <!-- Modal -->
  <?php if(!$mine):?>
  <div class="modal" id="m<?=$r['id']?>">
   <div class="box">
    <span class="close" onclick="closeM(<?=$r['id']?>)">×</span>
    <h3><?=$r['name']?></h3>
    <?=$media?>
    <p><?=$r['description']?></p>
    <p><b>Price:</b> ৳<?=$r['price']?> /unit/24h</p>
    <p><b>Available:</b> <?=$r['qty_available']?></p>
    <form method="post">
      <input type="hidden" name="rental_id" value="<?=$r['id']?>">
      <input type="hidden" name="customer_name" value="<?=$meName?>">
      <input type="text" name="location" placeholder="Location" required>
      <input type="date"  name="date" required>
      <select name="time" required><option>Morning</option><option>Afternoon</option><option>Evening</option></select>
      <input type="number" name="qty" id="q<?=$r['id']?>" min="1" max="<?=$r['qty_available']?>" value="1"
             oninput="total(<?=$r['id']?>,<?=$r['price']?>)">
      <input type="text" id="tot<?=$r['id']?>" value="Total: ৳<?=$r['price']?>" disabled>
      <input type="text" name="contact_phone" placeholder="Phone" required>
      <input type="submit" name="book_equipment" value="Book">
    </form>
   </div>
  </div>
  <?php endif;?>
 <?php endwhile;?>
 </div>

 <!-- current bookings -->
 <h2>Your Current Bookings</h2>
 <table>
  <tr><th>Equipment</th><th>Date</th><th>Time</th><th>Qty</th><th>Status</th><th>Action</th></tr>
  <?php while($b=$cur->fetch_assoc()):?>
   <tr>
     <td><?=$b['equip']?></td><td><?=$b['date']?></td><td><?=$b['time_slot']?></td><td><?=$b['qty']?></td><td><?=$b['status']?></td>
     <td>
       <form method="post" style="display:inline">
         <input type="hidden" name="booking_id" value="<?=$b['id']?>">
         <button name="mark_received">Mark Received</button>
       </form>
     </td>
   </tr>
  <?php endwhile;?>
 </table>

 <!-- history -->
 <h2>Service History</h2>
 <table>
  <tr><th>Equipment</th><th>Provider</th><th>Date</th><th>Time</th><th>Qty</th><th>Unit Price</th><th>Total Paid</th><th>Contact</th></tr>
  <?php while($h=$hist->fetch_assoc()):
        $total=$h['unit_price']*$h['qty']; ?>
   <tr>
     <td><?=$h['equip']?></td>
     <td><?=$h['first_name'].' '.$h['last_name']?></td>
     <td><?=$h['date']?></td><td><?=$h['time_slot']?></td><td><?=$h['qty']?></td>
     <td>৳<?=$h['unit_price']?></td><td>৳<?=$total?></td>
     <td><?=$h['contact_number']?></td>
   </tr>
  <?php endwhile;?>
 </table>
</div>

<script>
function openM(id){document.getElementById('m'+id).style.display='flex';}
function closeM(id){document.getElementById('m'+id).style.display='none';}
function total(id,p){const q=document.getElementById('q'+id).value||1;document.getElementById('tot'+id).value='Total: ৳'+q*p;}
</script>
</body></html>
