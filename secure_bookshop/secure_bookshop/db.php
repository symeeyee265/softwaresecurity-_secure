<?php
$host = "localhost";
$user = "root"; // Change this if your MySQL username is different
$pass = "";     // Change this if you have a MySQL password
$dbname = "secure_bookstore";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
