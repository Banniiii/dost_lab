<?php
include_once "../config/database.php";

// Attempt MySQL connection
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Get the data from the POST request
$data = json_decode(file_get_contents('php://input'), true);

$user = mysqli_real_escape_string($link, $data['user']);
$laboratory = mysqli_real_escape_string($link, $data['laboratory']);
$item = mysqli_real_escape_string($link, $data['item']);
$total_used_stock = mysqli_real_escape_string($link, $data['total_used_stock']);
$batch_details = mysqli_real_escape_string($link, $data['batch_details']);
$remarks = mysqli_real_escape_string($link, $data['remarks']); // Add this line

// Insert the data into the database
$sql = "INSERT INTO receipts (user, laboratory, item, total_used_stock, batch_details, remarks)
        VALUES ('$user', '$laboratory', '$item', '$total_used_stock', '$batch_details', '$remarks')"; 

if (mysqli_query($link, $sql)) {
    echo "Receipt data saved successfully";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($link);
}

mysqli_close($link);
?>
