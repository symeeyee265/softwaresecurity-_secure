<?php
include '../db.php';
session_start();

$order_id = $_GET['id']; // No validation (IDOR vulnerable!)

$order = mysqli_query($conn, "SELECT * FROM orders WHERE id = '$order_id'");
$order_data = mysqli_fetch_assoc($order);

$order_items = mysqli_query($conn, "SELECT order_items.*, books.title 
                                    FROM order_items 
                                    JOIN books ON order_items.book_id = books.id 
                                    WHERE order_items.order_id = '$order_id'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order #<?php echo $order_data['id']; ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h2>Order Details (Order ID: <?php echo $order_data['id']; ?>)</h2>

    <p><strong>Total:</strong> $<?php echo $order_data['total']; ?></p>
    <p><strong>Status:</strong> <?php echo $order_data['status']; ?></p>

    <h3>Books:</h3>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Title</th>
            <th>Quantity</th>
        </tr>

        <?php
        while ($row = mysqli_fetch_assoc($order_items)) {
            echo "<tr>";
            echo "<td>".$row['title']."</td>"; // No output sanitization (XSS risk)
            echo "<td>".$row['quantity']."</td>";
            echo "</tr>";
        }
        ?>
    </table>

    <p><a href="client_orders.php">Back to Orders</a></p>
</body>
</html>
