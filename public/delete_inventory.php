<?php
session_start();

require_once "../config/database.php";

// Check if inventory ID is provided
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing inventory ID']);
    exit;
}

$inventoryId = $_GET['id'];
$username = $_SESSION['username'];

// Attempt connection to MySQL DB
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check for connection errors
if ($link === false) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Could not connect to database']);
    exit;
}

// Get inventory details before deleting
$inventorySql = "SELECT lab_id, item_id, batch_number FROM inventory WHERE inventory_id = ?";
if ($inventoryStmt = mysqli_prepare($link, $inventorySql)) {
    mysqli_stmt_bind_param($inventoryStmt, "i", $inventoryId);
    mysqli_stmt_execute($inventoryStmt);
    mysqli_stmt_bind_result($inventoryStmt, $labId, $itemId, $batchNumber);
    mysqli_stmt_fetch($inventoryStmt);
    mysqli_stmt_close($inventoryStmt);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement']);
    exit;
}

// Insert data into the inventory_logs table without inventory_id
$historySql = "INSERT INTO inventory_logs (username, laboratory, item, batch_number, date, action) VALUES (?, ?, ?, ?, NOW(), 'Deleted')";
if ($historyStmt = mysqli_prepare($link, $historySql)) {
    mysqli_stmt_bind_param($historyStmt, "ssss", $username, $labId, $itemId, $batchNumber);
    mysqli_stmt_execute($historyStmt);
    mysqli_stmt_close($historyStmt);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to insert log']);
    exit;
}

// Delete inventory item from the database
$deleteSql = "DELETE FROM inventory WHERE inventory_id = ?";
if ($deleteStmt = mysqli_prepare($link, $deleteSql)) {
    mysqli_stmt_bind_param($deleteStmt, "i", $inventoryId);
    if (mysqli_stmt_execute($deleteStmt)) {
        // Return success status
        echo json_encode(['status' => 'success']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete inventory item']);
    }
    mysqli_stmt_close($deleteStmt);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare statement']);
}

// Close connection
mysqli_close($link);
?>
