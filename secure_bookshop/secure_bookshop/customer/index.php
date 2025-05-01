<?php
session_start();
include '../db.php'; // Database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user data from session
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Home - Online Bookstore</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 20px;
            text-align: center;
        }
        h1 {
            color: #333;
        }
        nav {
            margin-top: 30px;
        }
        nav ul {
            list-style-type: none;
            padding: 0;
        }
        nav ul li {
            display: inline-block;
            margin: 10px 20px;
        }
        nav ul li a {
            text-decoration: none;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border-radius: 8px;
            transition: background-color 0.3s;
        }
        nav ul li a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<h1>Welcome back, <?php echo htmlspecialchars($username); ?>!</h1>
<p>What would you like to do today?</p>

<nav>
    <ul>
        <li><a href="explore.php">📚 Explore Books</a></li>
        <li><a href="profile.php">👤 My Profile</a></li>
        <li><a href="cart.php">🛒 View Cart</a></li>
        <li><a href="client_orders.php">🧾 Order History</a></li>
        <li><a href="wishlist.php">💖 Wish List</a></li>
        <li><a href="contact.php">📩 Contact Us</a></li>
        <li><a href="faq.php">❓ FAQ</a></li>
        <li><a href="logout.php">🚪 Logout</a></li>
    </ul>
</nav>

</body>
</html>
