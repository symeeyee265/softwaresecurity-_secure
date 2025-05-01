<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = $_GET['id'] ?? null;

if ($order_id) {
    // Check if this order belongs to the logged-in user and is still pending
    $check = mysqli_query($conn, "SELECT * FROM orders WHERE id = '$order_id' AND user_id = '$user_id' AND status = 'Pending'");

    if (mysqli_num_rows($check) === 1) {
        // Update to Paid
        mysqli_query($conn, "UPDATE orders SET status = 'Paid' WHERE id = '$order_id'");

        echo "<script>alert('Payment successful!'); window.location='client_orders.php';</script>";
    } else {
        echo "<script>alert('Invalid order or already paid.'); window.location='client_orders.php';</script>";
    }
} else {
    echo "<script>alert('Order ID missing.'); window.location='client_orders.php';</script>";
}
?>
