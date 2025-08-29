<?php
include 'db_connection.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $nid = $_POST['nid'];
    $area = $_POST['area'];
    $password = $_POST['password'];
    $re_password = $_POST['re_password'];

    if ($password !== $re_password) {
        $message = "❌ Passwords do not match!";
    } else {
        $image_name = "";
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) mkdir($target_dir);
            $image_name = $target_dir . basename($_FILES["image"]["name"]);
            move_uploaded_file($_FILES["image"]["tmp_name"], $image_name);
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO farmers (first_name, last_name, email, phone, nid, area, image, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $first_name, $last_name, $email, $phone, $nid, $area, $image_name, $hashed_password);

        if ($stmt->execute()) {
            $message = "✅ Registration successful! <a href='farmer_login.php'>Login now</a>";
        } else {
            $message = "❌ Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Farmer Registration</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet"/>
<style>
body {
  font-family: 'Poppins', sans-serif;
  height: 100%;
  margin: 0;
  color: #d4f5c3;

  /* Background image with dark overlay */
  background:
    linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
    url('IMG-20250816-WA0016.jpg'); /* Replace with your image path */
  background-size: cover;
  background-position: center;
  background-attachment: fixed;
  display: flex;
  flex-direction: column;
}

/* Navbar */
nav {
  background-color: rgba(0, 77, 0, 0.85);
  padding: 15px 40px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

nav .logo {
  font-size: 1.8rem;
  font-weight: bold;
  color: #d4f5c3;
}

nav ul {
  list-style: none;
  display: flex;
  gap: 25px;
}

nav ul li a {
  text-decoration: none;
  color: #d4f5c3;
  font-weight: 500;
  transition: color 0.3s;
}

nav ul li a:hover {
  color: #80ff80;
}

/* Registration form container */
.container {
  max-width: 500px;
  margin: 60px auto;
  padding: 30px;
  background-color: rgba(26, 26, 26, 0.85);
  border-radius: 15px;
  box-shadow: 0 0 15px #004d00;
  backdrop-filter: blur(6px); /* subtle glass effect */
}

.container h2 {
  text-align: center;
  margin-bottom: 20px;
  color: #80ff80;
}

.container input {
  width: 100%;
  padding: 10px;
  margin: 10px 0;
  background: #333;
  color: #d4f5c3;
  border: none;
  border-radius: 5px;
}

.container button {
  width: 100%;
  padding: 10px;
  background: #008000;
  color: white;
  font-weight: bold;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background 0.3s;
}

.container button:hover {
  background: #00cc44;
}

.message {
  margin-top: 10px;
  text-align: center;
  color: #f77;
}
</style>
</head>
<body>

<nav>
  <div class="logo">AgriConnect</div>
  <ul>
    <li><a href="home.php">Home</a></li>
    <li><a href="#">About</a></li>
  </ul>
</nav>

<div class="container">
  <h2>Farmer Registration</h2>
  <form method="POST" enctype="multipart/form-data">
    <input type="text" name="first_name" placeholder="First Name" required />
    <input type="text" name="last_name" placeholder="Last Name" required />
    <input type="email" name="email" placeholder="Email" required />
    <input type="text" name="phone" placeholder="Bangladeshi Phone Number" required />
    <input type="text" name="nid" placeholder="NID (Optional)" />
    <input type="text" name="area" placeholder="Area" required />
    <input type="file" name="image" required />
    <input type="password" name="password" placeholder="Password" required />
    <input type="password" name="re_password" placeholder="Re-enter Password" required />
    <button type="submit">Register</button>
    <div class="message"><?= $message ?></div>
  </form>
</div>

</body>
</html>
