<?php
include '../db.php';
session_start();

// No session validation (vulnerable to unauthorized access)
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Orders</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h2>Manage Orders</h2>

    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Order ID</th>
            <th>User ID</th>
            <th>Total Price</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>

        <?php
        // Vulnerable SQL query preserved
        $orders = mysqli_query($conn, "SELECT * FROM orders");

        while ($row = mysqli_fetch_assoc($orders)) {
            echo "<tr>";
            echo "<td>".$row['id']."</td>"; // XSS vulnerability
            echo "<td>".$row['user_id']."</td>"; // IDOR vulnerability
            echo "<td>$".number_format($row['total'], 2)."</td>"; // Changed to 'total' and formatted
            echo "<td>".$row['created_at']."</td>"; // XSS vulnerability
            echo "<td><a href='view_order.php?id=".$row['id']."'>View Details</a></td>"; // CSRF/IDOR vulnerability
            echo "</tr>";
        }
        ?>
    </table>

</body>
</html>