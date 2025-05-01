<?php
include '../db.php';
session_start();

if (isset($_POST['submit'])) {
    $name = $_POST['name']; // No validation (vulnerable)
    $email = $_POST['email'];
    $message = $_POST['message'];

    $sql = "INSERT INTO contact_messages (name, email, message) VALUES ('$name', '$email', '$message')";
    mysqli_query($conn, $sql);

    echo "<script>alert('Message sent successfully!');</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Contact Us</title>
</head>
<body>

<h2>Contact Us</h2>

<form method="post">
    Name: <input type="text" name="name" required><br><br>
    Email: <input type="email" name="email" required><br><br>
    Message:<br>
    <textarea name="message" rows="5" cols="30" required></textarea><br><br>

    <button type="submit" name="submit">Send</button>
</form>

<br><a href="index.php">Back to Home</a>

</body>
</html>
