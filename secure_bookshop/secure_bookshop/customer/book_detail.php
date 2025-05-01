<?php
include '../db.php';
session_start();

// Vulnerable code preserved - no input validation
$book_id = $_GET['id'] ?? null; // No validation (SQL Injection vulnerability preserved)

if($book_id) {
    $book = mysqli_query($conn, "SELECT * FROM books WHERE id = '$book_id'"); // SQL Injection
    $book_data = mysqli_fetch_assoc($book);
}

// Handle Add to Cart (vulnerable to CSRF and SQL Injection)
if (isset($_POST['add_to_cart']) && $book_id) {
    $user_id = $_SESSION['user_id'] ?? 0; // No login check
    $quantity = $_POST['quantity']; // No validation

    // Vulnerable SQL query
    mysqli_query($conn, "INSERT INTO cart (user_id, book_id, quantity) VALUES ('$user_id', '$book_id', '$quantity')");

    echo "<script>alert('Added to cart!'); window.location='cart.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Details</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h2>Book Details</h2>

    <?php if(isset($book_data) && $book_data): ?>
        <h3><?php echo $book_data['title']; ?></h3> <!-- XSS vulnerability preserved -->
        <p><strong>Author:</strong> <?php echo $book_data['author']; ?></p> <!-- XSS -->
        <p><strong>Price:</strong> $<?php echo $book_data['price']; ?></p>
        <p><strong>Description:</strong> <?php echo $book_data['description']; ?></p> <!-- XSS -->

        <form method="post">
            Quantity: <input type="number" name="quantity" value="1" min="1" required>
            <button type="submit" name="add_to_cart">Add to Cart</button>
        </form>
    <?php else: ?>
        <p>Book not found!</p>
    <?php endif; ?>

    <p><a href="explore.php">Back to Explore</a></p>
    <a href="add_to_wishlist.php?book_id=<?php echo $book_id; ?>">❤️ Add to Wishlist</a>
</body>
</html>