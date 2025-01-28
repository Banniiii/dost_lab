<?php
require_once '../config/database.php';

// Attempt MySQL connection
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get batch number, stock, and used stock from POST request
    $batchNumber = $_POST['batch_number'];
    $stock = $_POST['stock'];
    $usedStock = $_POST['used_stock'];

    // Check if batch number, stock, and used stock are set and not empty
    if (isset($batchNumber) && isset($stock) && isset($usedStock)) {
        // Prepare SQL query to update stock and used stock
        $query = "UPDATE inventory SET stock = ?, used_stock = ? WHERE batch_number = ?";

        // Initialize prepared statement
        $stmt = $link->prepare($query);

        // Bind parameters
        $stmt->bind_param("iis", $stock, $usedStock, $batchNumber);

        // Execute query
        if ($stmt->execute()) {
            // Send success response
            echo json_encode(["success" => true]);
        } else {
            // Send error response
            echo json_encode(["success" => false, "error" => "Failed to update batch stock"]);
        }

        // Close statement
        $stmt->close();
    } else {
        // Send error response
        echo json_encode(["success" => false, "error" => "Invalid input"]);
    }
} else {
    // Send error response for invalid request method
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
}

// Close database connection
mysqli_close($link);
?>
