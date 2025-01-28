<?php
session_start();

require_once "../config/database.php";

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate input
    if (!isset($input['labId'], $input['itemId'], $input['unitMeasurement'], $input['batchNumber'], $input['minimumStock'], $input['stock'], $input['expDate'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
        exit;
    }

    // Sanitize input
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

    // Insert new inventory item
    $insertSql = "INSERT INTO inventory (lab_id, item_id, unit_measurement, batch_number, minimum_stock, stock, exp_date) VALUES (?, ?, ?, ?, ?, ?, ?)";
    if ($stmt = mysqli_prepare($link, $insertSql)) {
        mysqli_stmt_bind_param($stmt, "sssssss", $labId, $itemId, $unitMeasurement, $batchNumber, $minimumStock, $stock, $expDate);
        if (mysqli_stmt_execute($stmt)) {
            // Insert data into inventory_logs
            $historySql = "INSERT INTO inventory_logs (username, laboratory, item, batch_number, date, action) VALUES (?, ?, ?, ?, NOW(), 'Added')";
            if ($historyStmt = mysqli_prepare($link, $historySql)) {
                mysqli_stmt_bind_param($historyStmt, "ssss", $username, $labId, $itemId, $batchNumber);
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
