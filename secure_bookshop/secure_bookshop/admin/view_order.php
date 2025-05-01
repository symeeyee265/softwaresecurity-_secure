<?php
include '../db.php';
session_start();

$order_id = $_GET['id']; // No validation or authorization check (IDOR)

?>

<!DOCTYPE html>
<html>
<head>
    <title>View Order Details</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h2>Order Details (Order ID: <?php echo $order_id; ?>)</h2>

    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Book Title</th>
            <th>Quantity</th>
        </tr>

        <?php
        $items = mysqli_query($conn, "SELECT books.title, order_items.quantity 
                                      FROM order_items 
                                      JOIN books ON order_items.book_id = books.id 
                                      WHERE order_items.order_id = '$order_id'");

        while ($row = mysqli_fetch_assoc($items)) {
            echo "<tr>";
            echo "<td>".$row['title']."</td>"; // Possible XSS if book title has script
            echo "<td>".$row['quantity']."</td>";
            echo "</tr>";
        }
        ?>
    </table>

</body>
</html>
