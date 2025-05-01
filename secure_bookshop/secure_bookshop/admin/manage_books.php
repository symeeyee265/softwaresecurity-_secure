<?php
include '../db.php';
session_start();

// Vulnerable code preserved
if (isset($_POST['add'])) {
    $title = $_POST['title'];
    $author = $_POST['author']; // This was missing in your table structure
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category'];

    // Vulnerable SQL injection preserved
    $sql = "INSERT INTO books (title, author, description, price, category_id) 
            VALUES ('$title', '$author', '$description', '$price', '$category_id')";
    
    if(mysqli_query($conn, $sql)) {
        echo "<script>alert('Book added successfully. ID: ".mysqli_insert_id($conn)."');</script>";
    } else {
        echo "<script>alert('Error: ".mysqli_error($conn)."');</script>";
    }
}

// Rest of your vulnerable code remains the same...
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Books</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h2>Manage Books</h2>
    <form method="post">
        Title: <input type="text" name="title" required><br><br>
        Author: <input type="text" name="author" required><br><br>
        Description: <textarea name="description" required></textarea><br><br>
        Price: <input type="number" name="price" step="0.01" required><br><br>
        Category:
        <select name="category" required>
            <?php
            $categories = mysqli_query($conn, "SELECT * FROM categories");
            while ($row = mysqli_fetch_assoc($categories)) {
                echo "<option value='".$row['id']."'>".$row['name']."</option>";
            }
            ?>
        </select><br><br>
        <button type="submit" name="add">Add Book</button>
    </form>

    <h3>Existing Books:</h3>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Author</th>
            <th>Price</th>
            <th>Category</th>
            <th>Action</th>
        </tr>
        <?php
        $books = mysqli_query($conn, "SELECT books.*, categories.name AS category_name 
                                    FROM books 
                                    LEFT JOIN categories ON books.category_id = categories.id");
        while ($row = mysqli_fetch_assoc($books)) {
            echo "<tr>
                <td>".$row['id']."</td>
                <td>".$row['title']."</td>
                <td>".$row['author']."</td>
                <td>".$row['price']."</td>
                <td>".$row['category_name']."</td>
                <td><a href='?delete=".$row['id']."'>Delete</a></td>
              </tr>";
        }
        ?>
    </table>
</body>
</html>