<?php
include '../db.php';
if (!$conn) {
    die("Database connection failed: ". mysqli_connect_error());
}
session_start();

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$user_id = $_SESSION['user_id']; // No validation if not logged in

// Delete cart item
if (isset($_GET['delete']) && isset($_GET['csrf_token']) && $_GET['csrf_token'] === $_SESSION['csrf_token']) {
    $cart_id = $_GET['delete'];
    $stmt = mysqli_prepare($conn, "DELETE FROM cart WHERE id =? AND user_id =?");
    if (!$stmt) {
        echo "<script>alert('Failed to prepare delete statement: ". mysqli_error($conn). "'); window.location='cart.php';</script>";
    } else {
        mysqli_stmt_bind_param($stmt, "ii", $cart_id, $user_id);
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo "<script>alert('Item removed!'); window.location='cart.php';</script>";
        } else {
            echo "<script>alert('Failed to remove item!'); window.location='cart.php';</script>";
        }
        mysqli_stmt_close($stmt);
    }
}

// Update cart quantity
if (isset($_GET['update']) && isset($_GET['quantity']) && isset($_GET['csrf_token']) && $_GET['csrf_token'] === $_SESSION['csrf_token']) {
    $cart_id = $_GET['update'];
    $quantity = $_GET['quantity'];
    if ($quantity < 1) {
        echo "<script>alert('Please enter a minimum quantity of 1!'); window.location='cart.php';</script>";
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE cart SET quantity =? WHERE id =? AND user_id =?");
        if (!$stmt) {
            echo "<script>alert('Failed to prepare update statement: ". mysqli_error($conn). "'); window.location='cart.php';</script>";
        } else {
            mysqli_stmt_bind_param($stmt, "iii", $quantity, $cart_id, $user_id);
            mysqli_stmt_execute($stmt);
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                echo "<script>alert('Quantity updated!'); window.location='cart.php';</script>";
            } else {
                echo "<script>alert('Failed to update quantity!'); window.location='cart.php';</script>";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Simulate receiving a message and inserting it into the database
if (isset($_POST['message']) && isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
    $message = $_POST['message'];
    // Input filtering on the server side, only allow letters, numbers, !, ., ,
    if (preg_match('/[^a-zA-Z0-9!., ]/', $message)) {
        echo "<script>alert('Please enter only letters, numbers, !, ., ,'); window.location='cart.php';</script>";
    } else {
        // Check for XSS script
        if (preg_match('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i', $message)) {
            echo "<script>alert('You got an XSS injection!'); window.location='cart.php';</script>";
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO seller_messages (user_id, message) VALUES (?,?)");
            if (!$stmt) {
                echo "<script>alert('Failed to prepare insert statement: ". mysqli_error($conn). "'); window.location='cart.php';</script>";
            } else {
                mysqli_stmt_bind_param($stmt, "is", $user_id, $message);
                if (mysqli_stmt_execute($stmt)) {
                    echo "<script>alert('Message sent!'); window.location='cart.php';</script>";
                } else {
                    echo "<script>alert('Failed to send message: ". mysqli_error($conn). "'); window.location='cart.php';</script>";
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
}

// Fetch user's cart
$stmt = mysqli_prepare($conn, "SELECT cart.*, books.title, books.price 
                               FROM cart 
                               JOIN books ON cart.book_id = books.id 
                               WHERE cart.user_id =?");
if (!$stmt) {
    echo "<script>alert('Failed to prepare select statement: ". mysqli_error($conn). "'); window.location='cart.php';</script>";
} else {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $cart_items = mysqli_stmt_get_result($stmt);

    // Calculate total
    $total = 0;
    $itemCount = 0;
    $cartData = [];
    while ($row = mysqli_fetch_assoc($cart_items)) {
        $subtotal = $row['price'] * $row['quantity'];
        $total += $subtotal;
        $itemCount++;
        $cartData[] = $row;
    }
    // Reset result pointer to start
    mysqli_data_seek($cart_items, 0);
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }

       .header1 {
            text-align: center;
            margin-bottom: 20px;
            background-color: #C5C5C5;
        }

       .main-content {
            display: flex;
            gap: 20px;
        }

       .cart-items {
            flex: 2;
        }

       .summary {
            flex: 1;
            background-color: #f0f0f0;
            padding: 20px;
        }

       .cart-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }

       .cart-item-info {
            flex: 1;
        }

       .quantity-controls {
            display: flex;
            align-items: center;
            gap: 5px;
        }

       .quantity-controls button {
            padding: 5px 10px;
            background-color: #eee;
            border: 1px solid #ddd;
            cursor: pointer;
        }

       .quantity-controls input {
            width: 40px;
            text-align: center;
        }

       .cart-item-price {
            font-weight: bold;
        }

       .delete-btn {
            color: #ff0000;
            cursor: pointer;
        }

       .summary-item {
            margin-bottom: 15px;
        }

       .summary-item label {
            display: block;
            margin-bottom: 5px;
        }

       .summary-item input,
       .summary-item textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
        }

       .checkout-btn {
            width: 100%;
            padding: 10px;
            background-color: #000;
            color: #fff;
            border: none;
            cursor: pointer;
            margin-top: 20px;
        }

       .clickjacking-container {
            position: relative;
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
        }

       .clickjacking-image {
            width: 300px;
            height: auto;
        }

       .clickjacking-overlay {
            display: none; /* Hide clickjacking overlay */
        }
    </style>
</head>

<body>
    <div class="header1">
        <h2>🛒Your Cart 🛒</h2>
    </div>
    <div class="main-content">
        <div class="cart-items">
            <div class="item-count">
                <?php echo "$itemCount items"; ?>
            </div>
            <?php
            if (isset($cartData)) {
                foreach ($cartData as $index => $row) {
                    $subtotal = $row['price'] * $row['quantity'];
            ?>
                <div class="cart-item">
                    <div class="cart-item-info">
                        <h4><?php echo $row['title']; ?></h4>
                        <div class="quantity-controls">
                            <button onclick="changeQuantity(<?php echo $index; ?>, -1)"><i class="fas fa-minus"></i></button>
                            <input type="number" value="<?php echo $row['quantity']; ?>" onchange="updateQuantity(<?php echo $index; ?>, this.value)">
                            <button onclick="changeQuantity(<?php echo $index; ?>, 1)"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>
                    <div class="cart-item-price">€ <span id="subtotal-<?php echo $index; ?>"><?php echo number_format($subtotal, 2); ?></span></div>
                    <div class="delete-btn" onclick="deleteItem(<?php echo $row['id']; ?>, '<?php echo $_SESSION['csrf_token']; ?>')"><i class="fas fa-times"></i></div>
                </div>
            <?php
                }
            }
            ?>
            <a href="explore.php" class="back-to-shop">← Back to shop</a>
        </div>
        <div class="summary">
            <h3>Summary</h3>
            <div class="summary-item">
                <label>ITEMS</label>
                <span id="item-count"><?php echo $itemCount; ?></span>
                <span id="item-total">€ <?php echo number_format($total, 2); ?></span>
            </div>
            <div class="summary-item">
                <label>MESSAGE TO SELLER</label>
                <form action="cart.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <textarea name="message" placeholder="Type your message here" oninput="sanitizeInput(this)"></textarea>
                    <input type="submit" value="Send Message">
                </form>
            </div>
            <div class="summary-item">
                <label>TOTAL PRICE</label>
                <span id="total-price">€ <?php echo number_format($total, 2); ?></span>
            </div>
            <button class="checkout-btn" onclick="window.location.href='checkout.php'">CHECKOUT</button>
        </div>
    </div>
    <div class="clickjacking-container">
        <img class="clickjacking-image" src="https://picsum.photos/300/400" alt="Random Image">
        <!-- Remove clickjacking overlay link -->
    </div>

    <script>
        const cartData = <?php echo json_encode($cartData?? []); ?>;

        function changeQuantity(index, delta) {
            const input = document.querySelector(`input[onchange*='updateQuantity(${index},']`);
            let quantity = parseInt(input.value);
            quantity += delta;
            if (quantity < 1) {
                alert('Please enter a minimum quantity of 1');
                return;
            }
            input.value = quantity;
            updateQuantity(index, quantity);
        }

        function updateQuantity(index, quantity) {
            const price = cartData[index].price;
            const subtotal = price * quantity;
            const subtotalElement = document.getElementById(`subtotal-${index}`);
            subtotalElement.textContent = subtotal.toFixed(2);

            let newTotal = 0;
            const quantityInputs = document.querySelectorAll('.quantity-controls input');
            quantityInputs.forEach((input, i) => {
                const itemQuantity = parseInt(input.value);
                const itemPrice = cartData[i].price;
                newTotal += itemPrice * itemQuantity;
            });

            const itemTotalElement = document.getElementById('item-total');
            const totalPriceElement = document.getElementById('total-price');
            itemTotalElement.textContent = `€ ${newTotal.toFixed(2)}`;
            totalPriceElement.textContent = `€ ${newTotal.toFixed(2)}`;

            // Send a request to update the database
            const cartId = cartData[index].id;
            const csrfToken = '<?php echo $_SESSION['csrf_token']; ?>';
            window.location.href = `cart.php?update=${cartId}&quantity=${quantity}&csrf_token=${csrfToken}`;
        }

        function deleteItem(itemId, csrfToken) {
            if (confirm('Are you sure you want to remove this item?')) {
                window.location.href = `cart.php?delete=${itemId}&csrf_token=${csrfToken}`;
            }
        }

        function sanitizeInput(input) {
            // Front-end input filtering, only allow letters, numbers, !, ., ,
            const regex = /[^a-zA-Z0-9!., ]/g;
            if (regex.test(input.value)) {
                alert('Please enter only letters, numbers, !, ., ,');
                input.value = input.value.replace(regex, '');
            }
        }

        // Test for XSS
        document.addEventListener('DOMContentLoaded', function() {
            const messages = document.querySelectorAll('.summary-item textarea[name="message"]');
            messages.forEach(function(message) {
                const value = message.value;
                if (/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i.test(value)) {
                    alert('You got an XSS injection!');
                }
            });
        });
    </script>
</body>

</html>    
