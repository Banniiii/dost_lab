<?php
require_once "../config/database.php";

// Attempt connection to MySQL DB
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check for connection errors
if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Fetch user details using a prepared statement
$sql = "SELECT username, role, profile_image FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

$username = $user['username'];
$role = $user['role'];
$profile_image = $user['profile_image'];

// Set a default image if the user has no profile image
$profile_image_path = !empty($profile_image) ? "uploads/$profile_image" : "image/default-profile.png";

// Close the statement
mysqli_stmt_close($stmt);
?>
?>