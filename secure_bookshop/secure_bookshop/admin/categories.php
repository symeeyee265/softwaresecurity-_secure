<?php
include '../db.php';
session_start();

// No CSRF protection here (CSRF vulnerable)

if (isset($_POST['add'])) {
    $name = $_POST['name'];

    // No input sanitization (vulnerable to XSS)
    $sql = "INSERT INTO categories (name) VALUES ('$name')";
    mysqli_query($conn, $sql);

    echo "<script>alert('Category added');</script>";
}

// Deletion without confirmation (IDOR possible)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // First delete books in this category to avoid foreign key constraint violation
    mysqli_query($conn, "DELETE FROM books WHERE category_id=$id");
    
    // Then delete the category
    if(mysqli_query($conn, "DELETE FROM categories WHERE id=$id")) {
        echo "<script>alert('Category deleted');</script>";
    } else {
        echo "<script>alert('Error deleting category: ".mysqli_error($conn)."');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Categories</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h2>Manage Categories</h2>
    <form method="post">
        Category Name: <input type="text" name="name" required><br><br>
        <button type="submit" name="add">Add Category</button>
    </form>

    <h3>Existing Categories:</h3>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Action</th>
        </tr>

        <?php
        $result = mysqli_query($conn, "SELECT * FROM categories");
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>".$row['id']."</td>";
            echo "<td>".$row['name']."</td>"; // vulnerable to XSS
            echo "<td><a href='?delete=".$row['id']."' onclick='return confirm(\"Delete this category?\")'>Delete</a></td>"; // IDOR/CSRF vulnerable
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>