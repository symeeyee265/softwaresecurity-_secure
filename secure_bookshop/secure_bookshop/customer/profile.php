<?php
include '../db.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch current user's profile
$user_id = $_SESSION['user_id'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($query);

// Handle profile update
if (isset($_POST['update_profile'])) {
    $new_username = mysqli_real_escape_string($conn, $_POST['username']);
    $new_email = mysqli_real_escape_string($conn, $_POST['email']);

    $update = mysqli_query($conn, "UPDATE users SET username='$new_username', email='$new_email' WHERE id='$user_id'");

    if ($update) {
        $_SESSION['username'] = $new_username; // Update session username
        echo "<script>alert('Profile updated successfully!'); window.location='profile.php';</script>";
    } else {
        echo "<script>alert('Failed to update profile.');</script>";
    }
}

// Handle password change
if (isset($_POST['change_password'])) {
    $current_password = mysqli_real_escape_string($conn, $_POST['current_password']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);

    // Check if current password matches
    $check = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id' AND password='$current_password'");

    if (mysqli_num_rows($check) > 0) {
        $update_pass = mysqli_query($conn, "UPDATE users SET password='$new_password' WHERE id='$user_id'");
        if ($update_pass) {
            echo "<script>alert('Password changed successfully!'); window.location='profile.php';</script>";
        } else {
            echo "<script>alert('Failed to change password.');</script>";
        }
    } else {
        echo "<script>alert('Current password incorrect!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<h2>My Profile</h2>

<!-- Profile Update Form -->
<form method="post">
    <label>Username:</label><br>
    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"><br><br>

    <button type="submit" name="update_profile">Update Profile</button>
</form>

<hr>

<!-- Change Password Form -->
<h3>Change Password</h3>
<form method="post">
    <label>Current Password:</label><br>
    <input type="password" name="current_password" required><br><br>

    <label>New Password:</label><br>
    <input type="password" name="new_password" required><br><br>

    <button type="submit" name="change_password">Change Password</button>
</form>

<hr>

<br>
<!-- Go Back to Home -->
<a href="index.php" style="display: inline-block; padding: 10px 15px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;">🏠 Go Back to Home</a>

</body>
</html>
