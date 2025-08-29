<?php
include 'db_connection.php';
session_start();

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $nid = $_POST['nid'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    if ($_POST['password'] !== $_POST['cpassword']) {
        $error = "Passwords do not match.";
    } else {
        $sql = "INSERT INTO wholesellers (name, email, phone, nid, password) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $name, $email, $phone, $nid, $password);

        if ($stmt->execute()) {
            $success = "Registration successful. Please login. <br><a href='wholeseller_login.php'><button type='button'>Login</button></a>";
        } else {
            $error = "Registration failed. Email might be already used.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Wholeseller Registration</title>
    <style>
        :root{
            --bg-1: #07130b;
            --bg-2: #0b2a12;
            --card: rgba(7, 18, 15, 0.9);
            --accent: #80ff80;
            --accent-dark: #2e7d32;
            --muted: #cfead1;
            --glass: rgba(255,255,255,0.03);
        }
        *{box-sizing:border-box;margin:0;padding:0}
        body{
            font-family: 'Poppins', sans-serif;
            color: var(--muted);

            /* Background image with overlay */
            background:
                linear-gradient(rgba(7,19,11,0.8), rgba(11,42,18,0.8)), /* dark overlay */
                url('wholesell.jpg'); /* ← replace with your image path */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
        }
        header.site-header{
            padding:18px 32px;
            display:flex;align-items:center;justify-content:space-between;
            background: linear-gradient(90deg, var(--bg-1), var(--bg-2));
            box-shadow: 0 4px 12px rgba(0,0,0,0.7);
        }
        .logo {color:var(--muted);text-decoration:none;font-weight:700;font-size:1.15rem;}
        nav {display:flex;gap:18px;}
        nav a{
            color:var(--muted);text-decoration:none;padding:8px 12px;border-radius:10px;
            font-weight:600;position: relative;transition: all 0.3s ease;
        }
        nav a::after{
            content: "";position: absolute;left: 0;bottom: -5px;width: 0%;
            height: 3px;background: var(--accent);transition: width 0.3s ease;
        }
        nav a:hover{background:var(--glass);color:var(--accent);}
        nav a:hover::after{width: 100%;}
        .container{
            max-width: 500px;margin: 60px auto;padding: 30px;
            background: var(--card);border-radius: 12px;
            box-shadow: 0 0 15px var(--glass);
        }
        h2{text-align:center;margin-bottom:20px;color:var(--accent);}
        .form-control{
            width: 100%;padding:12px;margin-bottom:15px;border-radius:6px;
            border:1px solid var(--accent-dark);background: var(--glass);
            color: var(--muted);
        }
        .form-control:focus{outline:none;border-color:var(--accent);}
        .btn{
            background: linear-gradient(90deg, var(--accent-dark), #1E90FF);
            color: #06110a;border:none;padding:12px;width:100%;
            border-radius:6px;font-weight:700;cursor:pointer;
            transition: transform 0.3s ease;
        }
        .btn:hover{transform:scale(1.05);}
        .alert-success {
            background: #2e7d32; color: white; padding: 10px; border-radius: 6px; text-align: center; margin-bottom: 15px;
        }
        .alert-danger {
            background:#8B0000;color:white;padding:10px;border-radius:6px;text-align:center; margin-bottom: 15px;
        }
    </style>
</head>
<body>

<header class="site-header" role="banner">
  <a href="wholeseller_login.php" class="logo" aria-label="AgriConnect Home">AgriConnect</a>
  <nav role="navigation" aria-label="Primary Navigation">
    <a href="wholeseller_login.php">Login</a>
    <a href="wholeseller_register.php">Register</a>
  </nav>
</header>

<div class="container">
    <h2>Wholeseller Registration</h2>
    <?php if(isset($success)) echo "<div class='alert-success'>$success</div>"; ?>
    <?php if(isset($error)) echo "<div class='alert-danger'>$error</div>"; ?>
    <form method="POST" class="registration-form" novalidate>
        <input type="text" name="name" class="form-control" placeholder="Name" required>
        <input type="text" name="phone" class="form-control" placeholder="Phone" required>
        <input type="text" name="nid" class="form-control" placeholder="NID" required>
        <input type="email" name="email" class="form-control" placeholder="Email" required>
        <input type="password" name="password" class="form-control" placeholder="Password" required>
        <input type="password" name="cpassword" class="form-control" placeholder="Re-enter Password" required>
        <button type="submit" name="register" class="btn">Register</button>
    </form>
</div>

</body>
</html>
