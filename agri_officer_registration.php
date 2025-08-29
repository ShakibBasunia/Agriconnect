<?php 
include('db_connection.php'); // Include DB connection

$message = ""; // To store success or error message

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = $_POST['first_name'];
    $lastName  = $_POST['last_name'];
    $email     = $_POST['email'];
    $password  = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO agri_officers (first_name, last_name, email, password) 
            VALUES ('$firstName', '$lastName', '$email', '$password')";

    if (mysqli_query($conn, $sql)) {
        $message = "<div class='success'>✅ Registration successful! Please <a href='agri_officer_login.php'>Login</a></div>";
    } else {
        $message = "<div class='error'>❌ Error: " . mysqli_error($conn) . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Agri Officer Registration</title>
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

  /* Background image with dark overlay + fallback gradient */
  background:
    linear-gradient(rgba(7, 19, 11, 0.85), rgba(11, 42, 18, 0.85)), /* overlay */
    url('Agrioffficer.jpg'), /* change this path to your image */
    radial-gradient(1000px 600px at 10% 10%, rgba(128,255,128,0.05), transparent 10%),
    linear-gradient(180deg,var(--bg-1), var(--bg-2) 60%);
  background-size: cover;
  background-position: center;
  background-attachment: fixed;

  -webkit-font-smoothing:antialiased;
  -moz-osx-font-smoothing:grayscale;
  line-height:1.4;
  padding-bottom:60px;
}

/* Navbar */
header.site-header{
  position:relative;
  z-index:10;
  padding:18px 32px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:12px;
  background: linear-gradient(90deg, var(--bg-1), var(--bg-2));
  box-shadow: 0 4px 12px rgba(0,0,0,0.7);
}
.logo {
  display:flex;
  align-items:center;
  gap:12px;
  text-decoration:none;
  color:var(--muted);
  font-weight:700;
  font-size:1.15rem;
}
nav { display:flex; align-items:center; gap:18px; }
nav a{
  color:var(--muted);
  text-decoration:none;
  padding:8px 12px;
  border-radius:10px;
  font-weight:600;
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
nav a:hover, nav a:focus {
  background:var(--glass);
  color:var(--accent);
  outline: none;
  transform:translateY(-2px);
}
nav a:hover::after, nav a:focus::after { width: 100%; }

form {
  max-width: 420px;
  margin: 80px auto;
  background-color: rgba(28, 28, 28, 0.9);
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 6px 30px rgba(0, 0, 0, 0.4);
  animation: fadeIn 0.5s ease-in-out;
}
@keyframes fadeIn {
  from {opacity: 0; transform: translateY(10px);}
  to {opacity: 1; transform: translateY(0);}
}
h2 {
  text-align: center;
  margin-bottom: 20px;
  color: var(--accent);
  font-weight: 700;
}
input {
  width: 100%;
  padding: 12px;
  margin: 10px 0;
  border-radius: 6px;
  border: none;
  background-color: #262626;
  color: #fff;
  font-size: 15px;
  transition: background-color 0.3s ease, border 0.3s ease;
}
input:focus {
  outline: none;
  border: 2px solid var(--accent);
  background-color: #333;
}
button {
  width: 100%;
  padding: 12px;
  background: linear-gradient(90deg, var(--accent-dark), #1E90FF);
  color: #06110a;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 16px;
  font-weight: 700;
  transition: transform 0.2s ease;
  box-shadow: 0 6px 15px rgba(0,0,0,0.3);
}
button:hover {
  transform: scale(1.05);
  box-shadow: 0 9px 20px rgba(0,0,0,0.5);
}
.error, .success {
  text-align: center;
  padding: 12px;
  border-radius: 5px;
  margin: 15px auto;
  max-width: 420px;
  animation: fadeIn 0.3s ease-in-out;
  font-weight: 600;
}
.error { background-color: #ff4d4d33; color: #ff4d4d; }
.success { background-color: #4CAF5033; color: #4CAF50; }
a { color: var(--accent); text-decoration: none; font-weight: 600; }
a:hover { color: #00bfff; text-decoration: underline; }

/* Responsive tweaks */
@media (max-width:640px) {
  header.site-header { padding: 12px 18px; }
  form { margin: 40px 15px; padding: 25px; }
}
</style>
</head>
<body>
<header class="site-header">
  <a href="index.php" class="logo">AgriConnect</a>
  <nav>
    <a href="home.php">Home</a>
    <a href="about.php">About</a>
    <a href="faq.php">FAQ</a>
    <a href="contact.php">Contact</a>
    <a href="agri_officer_login.php">Login</a>
    <a href="agri_officer_registration.php">Register</a>
  </nav>
</header>

<?php if (!empty($message)) echo $message; ?>

<form method="POST" action="agri_officer_registration.php">
  <h2>Agri Officer Registration</h2>
  <input type="text" name="first_name" placeholder="First Name" required />
  <input type="text" name="last_name" placeholder="Last Name" required />
  <input type="email" name="email" placeholder="Email" required />
  <input type="password" name="password" placeholder="Password" required />
  <button type="submit">Register</button>
</form>
</body>
</html>
