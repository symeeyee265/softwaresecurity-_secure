<?php
include '../db.php';
session_start();

$user_id = $_SESSION['user_id']; // No validation for session

// Fetch all orders for the user
$orders = mysqli_query($conn, "SELECT * FROM orders WHERE user_id = '$user_id'");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Orders</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h2>Your Orders</h2>

    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Order ID</th>
            <th>Total</th>
            <th>Status</th>
            <th>View</th>
        </tr>

        <?php
        while ($row = mysqli_fetch_assoc($orders)) {
            echo "<tr>";
            echo "<td>".$row['id']."</td>";
            echo "<td>$".$row['total']."</td>";
            echo "<td>".$row['status']."</td>";
            echo "<td><a href='view_order.php?id=".$row['id']."'>View Details</a>
    " . ($row['status'] == 'Pending' ? " | <a href='make_payment.php?id=".$row['id']."'>Pay Now</a>" : "") . "</td>"; // IDOR again!
            echo "</tr>";
        }
        ?>
    </table>

    <p><a href="explore.php">Continue Shopping</a></p>
</body>
</html>
