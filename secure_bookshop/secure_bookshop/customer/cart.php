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

$user_id = $_SESSION['user_id'];

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Remove item from cart (secured)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete'])) {
    // Verify CSRF token
    if (!isset($_GET['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_GET['csrf_token'])) {
        die("Security error: Invalid CSRF token");
    }

    $cart_id = (int)$_GET['delete']; // Force integer type
    
    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cart_id, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['cart_message'] = "Item removed successfully";
    } else {
        $_SESSION['cart_message'] = "Error removing item";
    }
    
    $stmt->close();
    header("Location: cart.php");
    exit();
}

// Fetch cart items with prepared statement
$stmt = $conn->prepare("SELECT cart.id, cart.quantity, books.title, books.price 
                        FROM cart 
                        JOIN books ON cart.book_id = books.id 
                        WHERE cart.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_items = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
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
        .checkout-btn {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .checkout-btn:hover {
            background-color: #45a049;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success {
            background-color: #dff0d8;
            color: #3c763d;
        }
        .error {
            background-color: #f2dede;
            color: #a94442;
        }
    </style>
</head>
<body>
    <h2>🛒 Your Shopping Cart</h2>

    <?php if (isset($_SESSION['cart_message'])): ?>
        <div class="message <?php echo strpos($_SESSION['cart_message'], 'Error') !== false ? 'error' : 'success'; ?>">
            <?php echo htmlspecialchars($_SESSION['cart_message']); ?>
        </div>
        <?php unset($_SESSION['cart_message']); ?>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Quantity</th>
                <th>Price (Each)</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total = 0;
            if ($cart_items->num_rows > 0) {
                while ($row = $cart_items->fetch_assoc()) {
                    $quantity = $row['quantity'];
                    $price = $row['price'];
                    $subtotal = $price * $quantity;
                    $total += $subtotal;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo $quantity; ?></td>
                        <td>$<?php echo number_format($price, 2); ?></td>
                        <td>$<?php echo number_format($subtotal, 2); ?></td>
                        <td>
                            <a href="cart.php?delete=<?php echo $row['id']; ?>&csrf_token=<?php echo urlencode($_SESSION['csrf_token']); ?>" 
                               onclick="return confirm('Are you sure you want to remove this item?')">
                                Remove
                            </a>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo '<tr><td colspan="5">Your cart is empty</td></tr>';
            }
            ?>
        </tbody>
    </table>

    <?php if ($cart_items->num_rows > 0): ?>
    <div class="cart-summary">
        <h3>Total: $<?php echo number_format($total, 2); ?></h3>
        <form action="checkout.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <button type="submit" class="checkout-btn" name="checkout">Proceed to Checkout</button>
        </form>
    </div>
    <?php endif; ?>

    <p><a href="explore.php">← Continue Shopping</a></p>

    <script>
        // Confirm before removing items
        document.querySelectorAll('a[href*="delete"]').forEach(link => {
            link.addEventListener('click', (e) => {
                if (!confirm('Are you sure you want to remove this item?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>