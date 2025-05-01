<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    echo "<script>alert('Invalid item!'); window.location='wishlist.php';</script>";
    exit();
}

$wishlist_id = $_GET['id'];

// Get the book_id from wishlist
$result = mysqli_query($conn, "SELECT book_id FROM wishlist WHERE id = '$wishlist_id' AND user_id = '$user_id'");
if ($row = mysqli_fetch_assoc($result)) {
    $book_id = $row['book_id'];

    // Optional: check if already in cart
    $check = mysqli_query($conn, "SELECT * FROM cart WHERE user_id = '$user_id' AND book_id = '$book_id'");
    if (mysqli_num_rows($check) == 0) {
        // ✅ Insert with default quantity 1
        mysqli_query($conn, "INSERT INTO cart (user_id, book_id, quantity) VALUES ('$user_id', '$book_id', 1)");
    }

    // Delete from wishlist
    mysqli_query($conn, "DELETE FROM wishlist WHERE id = '$wishlist_id'");
    
    echo "<script>alert('Moved to cart!'); window.location='cart.php';</script>";
} else {
    echo "<script>alert('Item not found.'); window.location='wishlist.php';</script>";
}
?>
