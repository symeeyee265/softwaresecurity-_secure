<?php
session_start();
include '../db.php'; // Connect to DB

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get book ID from URL
if (!isset($_GET['book_id'])) {
    echo "<script>alert('Book not specified!'); window.location='explore.php';</script>";
    exit();
}

$book_id = $_GET['book_id'];

// Optional: Prevent duplicate entries
$check = mysqli_query($conn, "SELECT * FROM wishlist WHERE user_id = '$user_id' AND book_id = '$book_id'");
if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "INSERT INTO wishlist (user_id, book_id) VALUES ('$user_id', '$book_id')");
    echo "<script>alert('Book added to wishlist!'); window.location='wishlist.php';</script>";
} else {
    echo "<script>alert('Book already in wishlist!'); window.location='wishlist.php';</script>";
}
?>
