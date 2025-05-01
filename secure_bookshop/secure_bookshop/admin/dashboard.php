<?php
include '../db.php';
session_start();

// No security check for session (vulnerability preserved)
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h2>Welcome Admin!</h2>
    <div class="admin-menu">
        <p><a href="categories.php">Manage Categories</a></p> <!-- CSRF vulnerability preserved -->
        <p><a href="manage_books.php">Manage Books</a></p> <!-- CSRF vulnerability preserved -->
        <p><a href="orders.php">Manage Orders</a></p> <!-- CSRF vulnerability preserved -->
        <p><a href="sales_report.php">Sales Report</a></p> <!-- New vulnerable link added -->
        <p><a href="logout.php">Logout</a></p> <!-- CSRF vulnerability preserved -->
    </div>
</body>
</html>