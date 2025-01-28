<?php
include_once "../config/database.php";

// Attempt MySQL connection
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Query to fetch laboratories
$query = "SELECT lab_id, lab_name FROM laboratories";
$result = mysqli_query($link, $query);

if ($result) {
    $labs = mysqli_fetch_all($result, MYSQLI_ASSOC);
    echo json_encode($labs);
} else {
    echo json_encode(['error' => 'Unable to fetch laboratories']);
}

mysqli_close($link);
?>
