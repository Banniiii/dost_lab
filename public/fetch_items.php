<?php
// fetch_items.php
include_once "../config/database.php";

// Attempt MySQL connection
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Query to fetch items
$query = "SELECT item_id, item_name, unit_measurement FROM items";
$result = mysqli_query($link, $query);

if ($result) {
    $items = mysqli_fetch_all($result, MYSQLI_ASSOC);
    echo json_encode($items);
} else {
    echo json_encode(['error' => 'Unable to fetch items']);
}

mysqli_close($link);
?>
