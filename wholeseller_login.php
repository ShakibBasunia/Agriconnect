<?php
include 'db_connection.php';
session_start();

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM wholesellers WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $wholeseller = $stmt->get_result()->fetch_assoc();

    if ($wholeseller && password_verify($password, $wholeseller['password'])) {
        $_SESSION['wholeseller_id'] = $wholeseller['id'];
        header('Location: marketplace.php');
        exit();
    } else {
        $error = "Invalid login credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wholeseller Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
                linear-gradient(rgba(7, 19, 11, 0.85), rgba(11, 42, 18, 0.85)), /* overlay */
                url('farmersmarket.JPG'); /* ← change to your image path */
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
        }
        .btn:hover{transform:scale(1.05);}
        .alert{background:#8B0000;color:white;padding:10px;border-radius:6px;text-align:center;}
    </style>
</head>
<body>

<header class="site-header">
    <a href="#" class="logo">AgriConnect</a>
    <nav>
        <a href="home.php">Home</a>
        <a href="wholeseller_register.php">Register</a>
    </nav>
</header>

<div class="container">
    <h2>Wholeseller Login</h2>
    <?php if(isset($error)) echo "<div class='alert'>$error</div>"; ?>
    <form method="POST">
        <input type="email" name="email" class="form-control" placeholder="Email" required>
        <input type="password" name="password" class="form-control" placeholder="Password" required>
        <button type="submit" name="login" class="btn">Login</button>
    </form>
</div>

</body>
</html>
