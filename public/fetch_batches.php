<?php
require_once '../config/database.php';

// Attempt MySQL connection
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $lab = $_POST['lab'];
    $item = $_POST['item'];

    // Fetch batches with similar lab_id and item_id and their expiration dates
    $query = "SELECT i.batch_number, i.stock, i.used_stock, i.minimum_stock, i.exp_date, l.lab_name, it.item_name 
              FROM inventory i
              JOIN laboratories l ON i.lab_id = l.lab_id
              JOIN items it ON i.item_id = it.item_id
              WHERE l.lab_name = ? AND it.item_name = ?";
    $stmt = $link->prepare($query);
    $stmt->bind_param("ss", $lab, $item);
    $stmt->execute();
    $result = $stmt->get_result();
    $batches = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Sort batches by expiration date
    usort($batches, function($a, $b) {
        return strtotime($a['exp_date']) - strtotime($b['exp_date']);
    });

    // Identify the nearest batch
    $nearestBatch = !empty($batches) ? $batches[0] : null;

    // Prepare the response
    $response = [
        'batches' => $batches,
        'nearestBatch' => $nearestBatch
    ];

    // Return the response as JSON
    echo json_encode($response);
}

// Close database connection
mysqli_close($link);
?>
