<?php
session_start();
include '../db.php'; // Database connection

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get wishlist items
$result = mysqli_query($conn, "SELECT wishlist.id AS wishlist_id, books.* 
                               FROM wishlist 
                               JOIN books ON wishlist.book_id = books.id 
                               WHERE wishlist.user_id = '$user_id'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Wishlist</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="container">
    <h1>📚 My Wishlist</h1>

    <p><a href="index.php">← Back to Home</a></p>

    <table border="1" cellpadding="10" cellspacing="0" class="wishlist-table">
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Price</th>
            <th>Action</th>
        </tr>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['author']); ?></td>
                    <td>$<?php echo htmlspecialchars($row['price']); ?></td>
                    <td>
                        <a href="move_to_cart.php?id=<?php echo $row['wishlist_id']; ?>">🛒 Move to Cart</a> | 
                        <a href="remove_wishlist.php?id=<?php echo $row['wishlist_id']; ?>">❌ Remove</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">Your wishlist is empty.</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>
