<?php
// announcement.php
session_start();
include 'db_connection.php';

// Fetch announcements
$sql = "SELECT id, message, created_at, pdf_path FROM announcements ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>ঘোষণা তালিকা</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            /* ব্যাকগ্রাউন্ড ইমেজ */
            background-image: url('wind-power-1357419_640.jpg'); /* এখানে আপনার ইমেজ path দিন */
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: center;
        }

        /* ব্যাকগ্রাউন্ড overlay effect */
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.5); /* অন্ধকার করার জন্য */
            z-index: -1;
        }

        .container {
            width: 90%;
            margin: auto;
            background: rgba(20, 102, 143, 0.9); /* হালকা সাদা ব্যাকগ্রাউন্ড */
            padding: 20px;
            margin-top: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        h2 {
            text-align: center;
            color: #222;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #336618ff;
            padding: 10px;
            text-align: center;
        }
        th {
            background: #4CAF50;
            color: white;
        }
        tr:nth-child(even) { background-color: #f9f9f9; }
        a.button {
            display: inline-block;
            padding: 6px 12px;
            background: #2196F3;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        a.button:hover {
            background: #0b7dda;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>📢 সর্বশেষ ঘোষণা</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>বার্তা</th>
            <th>তারিখ</th>
            <th>PDF দেখুন</th>
            <th>ডাউনলোড</th>
        </tr>
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['message']}</td>
                        <td>{$row['created_at']}</td>
                        <td><a class='button' href='{$row['pdf_path']}' target='_blank'>দেখুন</a></td>
                        <td><a class='button' href='{$row['pdf_path']}' download>ডাউনলোড</a></td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>কোনো ঘোষণা পাওয়া যায়নি</td></tr>";
        }
        ?>
    </table>
</div>
</body>
</html>
