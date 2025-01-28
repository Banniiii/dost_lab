<?php
session_start();
require_once '../config/database.php';

// Attempt MySQL connection
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($link === false) {
    echo json_encode(['error' => 'Could not connect to database']);
    exit;
}

// Assuming your login script sets the username in the session
if (!isset($_SESSION['username'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$currentUser = $_SESSION['username'];

// Always return the current user in the response
$response = ["status" => "success", "currentUser" => $currentUser];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lab = $_POST['lab'] ?? '';
    $item = $_POST['item'] ?? '';
    $batch = $_POST['batch'] ?? '';
    $useStock = $_POST['use_stock'] ?? 0;

    // Validate input
    if (empty($lab) || empty($item) || empty($batch) || empty($useStock)) {
        $response['error'] = 'Invalid input';
        echo json_encode($response);
        exit;
    }

    // Fetch the current used stock and batch number from the database
    $query = "SELECT used_stock, stock FROM inventory WHERE lab_id = (SELECT lab_id FROM laboratories WHERE lab_name = ?) AND item_id = (SELECT item_id FROM items WHERE item_name = ?) AND batch_number = ?";
    $stmt = $link->prepare($query);
    $stmt->bind_param("sss", $lab, $item, $batch);
    $stmt->execute();
    $stmt->bind_result($currentUsedStock, $currentTotalStock);
    $stmt->fetch();
    $stmt->close();

    // Calculate the new used stock value
    $newUsedStock = $currentUsedStock + $useStock; 
    $newTotalStock = $currentTotalStock - $useStock;

    // Update the used stock and total stock values in the database
    $updateQuery = "UPDATE inventory SET used_stock = ?, stock = ? WHERE lab_id = (SELECT lab_id FROM laboratories WHERE lab_name = ?) AND item_id = (SELECT item_id FROM items WHERE item_name = ?) AND batch_number = ?";
    $updateStmt = $link->prepare($updateQuery);
    $updateStmt->bind_param("iisss", $newUsedStock, $newTotalStock, $lab, $item, $batch);
    $updateStmt->execute();
    $updateStmt->close();

    // Add stock information to the response
    $response["newUsedStock"] = $newUsedStock;
    $response["newTotalStock"] = $newTotalStock;
}

echo json_encode($response);
?>
