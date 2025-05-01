<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>FAQ - Bookstore</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<h1>❓ Frequently Asked Questions</h1>
<a href="index.php">← Back to Home</a>

<div class="faq">
    <h3>📦 How do I place an order?</h3>
    <p>Simply browse books, add to your cart, and go to checkout.</p>

    <h3>🔐 Is my personal data safe?</h3>
    <p>This site is intentionally vulnerable for demo purposes 😅</p>

    <h3>💳 What payment methods are supported?</h3>
    <p>Currently, we accept demo card inputs for testing.</p>

    <h3>📬 Can I contact customer support?</h3>
    <p>Yes, visit the <a href="contact.php">Contact Page</a> and send your message.</p>
</div>

</body>
</html>
