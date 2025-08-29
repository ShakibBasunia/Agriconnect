<?php
session_start();
include('db_connection.php'); // Include DB connection

$message = ""; // store login messages

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM farmers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['role'] = 'farmer';
            $_SESSION['farmer_id'] = $row['id'];
            $_SESSION['farmer_name'] = $row['first_name'] . ' ' . $row['last_name'];
            $_SESSION['farmer_image'] = $row['image'];
            header("Location: farmer_dashboard.php");
            exit();
        } else {
            $message = "<div class='error'>❌ Invalid login credentials!</div>";
        }
    } else {
        $message = "<div class='error'>❌ No user found with this email.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Farmer Login</title>
<style>
:root{
  --accent: #80ff80;
  --accent-dark: #2e7d32;
  --muted: #cfead1;
  --card: rgba(28,28,28,0.85);
  --glass: rgba(255,255,255,0.03);
}

/* Reset and full height */
*{box-sizing:border-box;margin:0;padding:0}
html, body{height:100%;}

/* Body with background image and overlay */
body {
  font-family: 'Poppins', sans-serif;
  color: var(--muted);
  background:
    linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
    url('istockphoto-491151340-612x612.jpg'); /* replace with your image path */
  background-size: cover;
  background-position: center;
  background-attachment: fixed;
  display: flex;
  flex-direction: column;
}

/* Navbar */
header.site-header{
  padding:18px 32px;
  display:flex; justify-content:space-between; align-items:center;
  background: rgba(0,0,0,0.7);
  box-shadow: 0 4px 12px rgba(0,0,0,0.7);
}

.logo {color: var(--muted); text-decoration:none; font-weight:700; font-size:1.15rem;}
nav {display:flex; gap:18px;}
nav a{color: var(--muted); text-decoration:none; padding:8px 12px; border-radius:10px; font-weight:600; position:relative; transition: all 0.3s;}
nav a::after {content:""; position:absolute; left:0; bottom:-5px; width:0%; height:3px; background: var(--accent); transition: width 0.3s;}
nav a:hover, nav a:focus {background: var(--glass); color: var(--accent); transform:translateY(-2px);}
nav a:hover::after, nav a:focus::after {width:100%;}

/* Form styling */
form {
  max-width:420px;
  margin:100px auto;
  background-color: var(--card);
  padding:30px;
  border-radius:12px;
  box-shadow: 0 6px 30px rgba(0,0,0,0.4);
  backdrop-filter: blur(6px);
  animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {from {opacity:0; transform:translateY(10px);} to {opacity:1; transform:translateY(0);}}

h2 {text-align:center; margin-bottom:20px; color: var(--accent); font-weight:700;}

input {
  width:100%; padding:12px; margin:10px 0; border-radius:6px; border:none; background-color:#262626; color:#fff; font-size:15px;
}
input:focus {outline:none; border:2px solid var(--accent); background-color:#333;}

button {
  width:100%; padding:12px; background: linear-gradient(90deg, var(--accent-dark), #1E90FF); color:#06110a;
  border:none; border-radius:6px; cursor:pointer; font-size:16px; font-weight:700; transition: transform 0.2s ease;
}
button:hover {transform: scale(1.05); box-shadow:0 9px 20px rgba(0,0,0,0.5);}

.error, .success {
  text-align:center; padding:12px; border-radius:5px; margin:15px auto; max-width:420px; font-weight:600;
}
.error {background-color: #ff4d4d33; color: #ff4d4d;}
.success {background-color: #4CAF5033; color: #4CAF50;}

a {color: var(--accent); text-decoration:none; font-weight:600; display:block; text-align:center; margin-top:15px;}
a:hover{color:#00bfff;}

@media(max-width:640px){form{margin:50px 15px; padding:25px;}}
</style>
</head>
<body>

<header class="site-header">
  <a href="home.php" class="logo">AgriConnect</a>
  <nav>
    <a href="home.php">Home</a>
    <a href="about.php">About</a>
    <a href="faq.php">FAQ</a>
    <a href="farmer_registration.php">Register</a>
  </nav>
</header>

<?php if(!empty($message)) echo $message; ?>

<form method="POST" action="farmer_login.php">
  <h2>Farmer Login</h2>
  <input type="email" name="email" placeholder="Email" required autocomplete="email" />
  <input type="password" name="password" placeholder="Password" required autocomplete="current-password" />
  <button type="submit">Login</button>
  <p>Don't have an account? <a href="farmer_registration.php">Click here to register</a></p>
</form>

</body>
</html>
