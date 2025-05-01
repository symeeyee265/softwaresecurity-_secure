<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$wishlist_id = $_GET['id'];

// Delete wishlist entry
mysqli_query($conn, "DELETE FROM wishlist WHERE id = '$wishlist_id' AND user_id = '$user_id'");

header("Location: wishlist.php");
exit();
