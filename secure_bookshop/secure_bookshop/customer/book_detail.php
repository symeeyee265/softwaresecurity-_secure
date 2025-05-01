<?php
include '../db.php';
session_start();

// Create a CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Search for books functionality
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
if ($search_query) {
    $search_stmt = mysqli_prepare($conn, "SELECT * FROM books WHERE title LIKE? OR author LIKE?");
    $search_param = "%$search_query%";
    mysqli_stmt_bind_param($search_stmt, "ss", $search_param, $search_param);
    mysqli_stmt_execute($search_stmt);
    $search_result = mysqli_stmt_get_result($search_stmt);
    $search_books = mysqli_fetch_all($search_result, MYSQLI_ASSOC);
    mysqli_stmt_close($search_stmt);
}

// Validate and sanitize input
$book_id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;
if ($book_id === false || $book_id === null) {
    echo "<script>alert('Invalid book ID!'); window.location='explore.php';</script>";
    exit;
}

$book = null;
$book_data = [];
if ($book_id) {
    // Use prepared statements to prevent SQL injection
    $stmt = mysqli_prepare($conn, "SELECT * FROM books WHERE id =?");
    mysqli_stmt_bind_param($stmt, "i", $book_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $book_data = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

// Handle the request to add to the cart
if (isset($_POST['add_to_cart']) && $book_id) {
    // Validate the CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token']!== $_SESSION['csrf_token']) {
        echo "<script>alert('Invalid CSRF token!'); window.location='book_detail.php?id=$book_id';</script>";
        exit;
    }

    // Validate if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Please log in to add items to the cart!'); window.location='login.php';</script>";
        exit;
    }
    $user_id = $_SESSION['user_id'];

    // Validate and sanitize the quantity input
    $quantity = isset($_POST['quantity']) ? filter_var($_POST['quantity'], FILTER_VALIDATE_INT) : 1;
    if ($quantity === false || $quantity < 1) {
        echo "<script>alert('Invalid quantity!'); window.location='book_detail.php?id=$book_id';</script>";
        exit;
    }

    // Use prepared statements to insert data into the cart
    $stmt = mysqli_prepare($conn, "INSERT INTO cart (user_id, book_id, quantity) VALUES (?,?,?)");
    mysqli_stmt_bind_param($stmt, "iii", $user_id, $book_id, $quantity);
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Added to cart!'); window.location='cart_unsecure.php';</script>";
    } else {
        echo "<script>alert('Error adding to cart: ".mysqli_error($conn)."'); window.location='book_detail.php?id=$book_id';</script>";
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Book Details</title>
    <style>
        form {
            background: white;
            padding: 20px;
            width: 300px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        form input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        form button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }

        form button:hover {
            background-color: #0056b3;
        }

        .add-to-cart-form {
            margin: 0;
            width: auto;
            box-shadow: none;
            padding: 0;
        }
    </style>
</head>

<body>
    <!-- Only show the search form when not viewing book details -->
    <?php if (!$book_id): ?>
        <form action="book_detail.php" method="get">
            <input type="text" name="search" placeholder="Search books by title or author">
            <button type="submit" class="search-button">Search</button>
        </form>
    <?php endif; ?>

    <?php if (isset($search_books) && $search_books): ?>
        <h2>Search Results</h2>
        <ul>
            <?php foreach ($search_books as $search_book): ?>
                <li>
                    <a href="book_detail.php?id=<?php echo $search_book['id']; ?>">
                        <?php echo htmlspecialchars($search_book['title'], ENT_QUOTES, 'UTF-8'); ?> by <?php echo htmlspecialchars($search_book['author'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <h2>Book Details</h2>

    <?php if (isset($book_data) && $book_data): ?>
        <h3><?php echo htmlspecialchars($book_data['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
        <p><strong>Author:</strong> <?php echo htmlspecialchars($book_data['author'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Price:</strong> $<?php echo htmlspecialchars($book_data['price'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($book_data['description'], ENT_QUOTES, 'UTF-8'); ?></p>

        <form method="post" class="add-to-cart-form">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            Quantity: <input type="number" name="quantity" value="1" min="1" required>
            <button type="submit" name="add_to_cart" class="add-to-cart-button">Add to Cart</button>
        </form>
    <?php else: ?>
        <p>Book not found!</p>
    <?php endif; ?>

    <p><a href="explore.php">Back to Explore</a></p>
    <a href="add_to_wishlist.php?book_id=<?php echo $book_id; ?>">❤️ Add to Wishlist</a>
</body>

</html>    
