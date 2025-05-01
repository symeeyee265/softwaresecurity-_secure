<?php
include '../db.php';
session_start();

// Verify database connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Check authentication
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize variables
$search = '';
$books = [];
$error = '';

try {
    // Process search with prepared statement
    if (isset($_GET['search'])) {
        $search = trim($_GET['search']);
        
        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, title, author, price FROM books 
                              WHERE title LIKE CONCAT('%', ?, '%') 
                              OR author LIKE CONCAT('%', ?, '%')");
        $stmt->bind_param("ss", $search, $search);
        $stmt->execute();
        $books = $stmt->get_result();
    } else {
        // Get all books with explicit column selection
        $books = $conn->query("SELECT id, title, author, price FROM books");
    }
} catch (Exception $e) {
    $error = "Error loading books. Please try again.";
    error_log("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Explore Books</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        .search-form {
            margin: 20px 0;
            display: flex;
            gap: 10px;
        }
        .search-form input {
            padding: 8px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .search-form button {
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .error-message {
            color: #d9534f;
            padding: 10px;
            background-color: #f2dede;
            border-radius: 4px;
            margin: 10px 0;
        }
        .btn {
            display: inline-block;
            padding: 8px 12px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .home-btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>📚 Explore Books</h2>

        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="get" class="search-form">
            <input type="text" name="search" placeholder="Search by title or author..." 
                   value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($books && $books->num_rows > 0): ?>
                    <?php while ($row = $books->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['author'] ?? 'Unknown'); ?></td>
                            <td>$<?php echo number_format($row['price'], 2); ?></td>
                            <td>
                                <a href="book_detail.php?id=<?php echo (int)$row['id']; ?>" class="btn">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No books found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="index.php" class="home-btn">🏠 Go Back to Home</a>
    </div>
</body>
</html>
<?php
// Close database connection if using prepared statement
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
?>