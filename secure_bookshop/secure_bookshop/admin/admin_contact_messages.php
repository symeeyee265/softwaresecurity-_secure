<?php
include '../db.php';
session_start();

// (Optional: Admin login session check here)

$messages = mysqli_query($conn, "SELECT * FROM contact_messages ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Contact Messages</title>
</head>
<body>

<h2>Contact Messages</h2>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Sender Name</th>
        <th>Email</th>
        <th>Message</th>
        <th>Date Sent</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($messages)) { ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['name']); ?></td>
        <td><?php echo htmlspecialchars($row['email']); ?></td>
        <td><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
        <td><?php echo $row['created_at']; ?></td>
    </tr>
    <?php } ?>
</table>

<br><a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
