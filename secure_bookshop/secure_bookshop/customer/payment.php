<?php
include '../db.php';
session_start();

$order_id = $_GET['order_id']; // No validation (IDOR!)

$order = mysqli_query($conn, "SELECT * FROM orders WHERE id = '$order_id'");
$order_data = mysqli_fetch_assoc($order);

// Handle Payment
if (isset($_POST['pay'])) {
    // Payment status update with no authentication
    mysqli_query($conn, "UPDATE orders SET status = 'Paid' WHERE id = '$order_id'");

    echo "<script>alert('Payment Successful!'); window.location='client_orders.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h2>Payment</h2>

    <p><strong>Order ID:</strong> <?php echo $order_data['id']; ?></p>
    <p><strong>Total Amount:</strong> $<?php echo $order_data['total']; ?></p>
    <p><strong>Status:</strong> <?php echo $order_data['status']; ?></p>

    <form method="post">
        <button type="submit" name="pay">Pay Now</button>
    </form>

    <p><a href="explore.php">Back to Home</a></p>
</body>
</html>
