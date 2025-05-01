<?php
include '../db.php';
session_start();

// No session or admin validation - security vulnerability preserved
$sales = mysqli_query($conn, "SELECT * FROM orders"); // SQL injection vulnerability preserved

$total_sales = 0;
$total_orders = 0;

while ($row = mysqli_fetch_assoc($sales)) {
    $total_sales += $row['total'];
    $total_orders++;
}

$formatted_total = number_format($total_sales, 2);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales Report</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h2>Sales Report</h2>

    <p><strong>Total Orders:</strong> <?php echo $total_orders; ?></p>
    <p><strong>Total Sales:</strong> $<?php echo $formatted_total; ?></p>

    <h3>All Orders:</h3>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Order ID</th>
            <th>User ID</th>
            <th>Total</th>
            <th>Status</th>
        </tr>

        <?php
        $sales = mysqli_query($conn, "SELECT * FROM orders");
        while ($row = mysqli_fetch_assoc($sales)) {
            echo "<tr>";
            echo "<td>".$row['id']."</td>"; // XSS vulnerability
            echo "<td>".$row['user_id']."</td>"; // Data leak vulnerability
            echo "<td>$".number_format($row['total'], 2)."</td>";
            echo "<td>".$row['status']."</td>"; // XSS vulnerability
            echo "</tr>";
        }
        ?>
    </table>

    <!-- Fixed dashboard link while preserving vulnerabilities -->
    <p><a href="/bookshop/admin/dashboard.php">Back to Dashboard</a></p>
    <!-- Or if that doesn't work, try: -->
    <!-- <p><a href="../admin_dashboard.php">Back to Dashboard</a></p> -->
</body>
</html>