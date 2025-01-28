<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if ID parameter is provided
if (!isset($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing ID parameter']);
    exit;
}

// Sanitize ID input
$inventoryId = mysqli_real_escape_string($link, $_GET['id']);

// Fetch inventory details
$sql = "SELECT * FROM inventory WHERE inventory_id='$inventoryId'";
$result = mysqli_query($link, $sql);

if (!$result) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . mysqli_error($link)]);
    exit;
}

$data = mysqli_fetch_assoc($result);
echo json_encode($data);

// Close connection
mysqli_close($link);
?>
