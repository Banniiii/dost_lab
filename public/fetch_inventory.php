<?php
require "../config/database.php";

// Attempt MySQL connection
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

$query = "SELECT i.inventory_id, l.lab_name, it.item_name, i.unit_measurement, i.batch_number, i.minimum_stock, i.stock, i.used_stock, i.exp_date
          FROM inventory i
          JOIN laboratories l ON i.lab_id = l.lab_id
          JOIN items it ON i.item_id = it.item_id";
$result = mysqli_query($link, $query);

$inventory = [];
while ($row = mysqli_fetch_assoc($result)) {
    $inventory[] = $row;
}

header('Content-Type: application/json');
echo json_encode($inventory);
?>
