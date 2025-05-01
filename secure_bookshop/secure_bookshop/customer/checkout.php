<?php
include '../db.php';
session_start();

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['checkout'])) {
    // Re-fetch cart items securely
    $cart_items = mysqli_query($conn, "SELECT cart.*, books.price 
                                       FROM cart 
                                       JOIN books ON cart.book_id = books.id 
                                       WHERE cart.user_id = '$user_id'");

    $order_total = 0;
    $order_data = [];

    while ($item = mysqli_fetch_assoc($cart_items)) {
        $quantity = $item['quantity'];
        $price = $item['price'];
        $book_id = $item['book_id'];
        $order_total += $price * $quantity;
        $order_data[] = ['book_id' => $book_id, 'quantity' => $quantity];
    }

    // Create Order
    mysqli_query($conn, "INSERT INTO orders (user_id, total, status) VALUES ('$user_id', '$order_total', 'Pending')");

    $order_id = mysqli_insert_id($conn);

    // Insert into order_items
    foreach ($order_data as $item) {
        $book_id = $item['book_id'];
        $quantity = $item['quantity'];
        mysqli_query($conn, "INSERT INTO order_items (order_id, book_id, quantity) VALUES ('$order_id', '$book_id', '$quantity')");
    }

    // Clear cart
    mysqli_query($conn, "DELETE FROM cart WHERE user_id = '$user_id'");

    echo "<script>alert('Checkout complete!'); window.location='payment.php?order_id=$order_id';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h2>Checkout</h2>

    <form method="post">
        <button type="submit" name="checkout">✅ Place Order</button>
    </form>

    <p><a href="cart.php">← Back to Cart</a></p>
</body>
</html>
