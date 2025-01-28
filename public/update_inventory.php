<?php
session_start();

require_once "../config/database.php";

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate input
    if (!isset($input['inventoryId'], $input['labId'], $input['itemId'], $input['unitMeasurement'], $input['batchNumber'], $input['minimumStock'], $input['stock'], $input['expDate'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
        exit;
    }

    // Sanitize input
    $inventoryId = mysqli_real_escape_string($link, $input['inventoryId']);
    $labId = mysqli_real_escape_string($link, $input['labId']);
    $itemId = mysqli_real_escape_string($link, $input['itemId']);
    $unitMeasurement = mysqli_real_escape_string($link, $input['unitMeasurement']);
    $batchNumber = mysqli_real_escape_string($link, $input['batchNumber']);
    $minimumStock = mysqli_real_escape_string($link, $input['minimumStock']);
    $stock = mysqli_real_escape_string($link, $input['stock']);
    $expDate = mysqli_real_escape_string($link, $input['expDate']);
    $username = $_SESSION['username'];

    // Attempt connection to MySQL DB
    $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // Check for connection errors
    if ($link === false) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Could not connect to database']);
        exit;
    }

    // Get current details before update
    $currentSql = "SELECT lab_id, item_id, batch_number FROM inventory WHERE inventory_id = ?";
    if ($currentStmt = mysqli_prepare($link, $currentSql)) {
        mysqli_stmt_bind_param($currentStmt, "i", $inventoryId);
        mysqli_stmt_execute($currentStmt);
        mysqli_stmt_bind_result($currentStmt, $currentLabId, $currentItemId, $currentBatchNumber);
        mysqli_stmt_fetch($currentStmt);
        mysqli_stmt_close($currentStmt);
    }

    // Update inventory record
    $updateSql = "UPDATE inventory SET lab_id=?, item_id=?, unit_measurement=?, batch_number=?, minimum_stock=?, stock=?, exp_date=? WHERE inventory_id=?";
    if ($stmt = mysqli_prepare($link, $updateSql)) {
        mysqli_stmt_bind_param($stmt, "sssssssi", $labId, $itemId, $unitMeasurement, $batchNumber, $minimumStock, $stock, $expDate, $inventoryId);
        if (mysqli_stmt_execute($stmt)) {
            // Insert data into inventory_logs
            $historySql = "INSERT INTO inventory_logs (username, laboratory, item, batch_number, date, action) VALUES (?, ?, ?, ?, NOW(), 'Edited')";
            if ($historyStmt = mysqli_prepare($link, $historySql)) {
                mysqli_stmt_bind_param($historyStmt, "ssss", $username, $currentLabId, $currentItemId, $currentBatchNumber);
                mysqli_stmt_execute($historyStmt);
                mysqli_stmt_close($historyStmt);
            }
            mysqli_stmt_close($stmt);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . mysqli_error($link)]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . mysqli_error($link)]);
    }
    mysqli_close($link);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
