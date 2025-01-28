<?php
// DB Configuration
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // DB username
define('DB_PASSWORD', '');     // DB password
define('DB_NAME', 'dost_lab'); // DB name

// Attempt connection to MySQL DB
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Checking for connection
if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>
