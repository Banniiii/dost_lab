<?php
include_once 'side_bar.php';
require_once "../config/database.php";
require_once "../includes/functions.php";
include_once 'auto_check.php';

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Handle delete request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['labId'])) {
    $labId = $_POST['labId'];

    // SQL to delete a record
    $deleteSql = "DELETE FROM laboratories WHERE lab_id = ?";
    if ($stmt = mysqli_prepare($link, $deleteSql)) {
        mysqli_stmt_bind_param($stmt, "i", $labId);
        if (mysqli_stmt_execute($stmt)) {
            // Deletion successful
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($link)]);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($link)]);
    }

    mysqli_close($link);
    exit(); 
}

// Handle form submission for adding a laboratory
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['labName'])) {
    // Process form data
    $labName = $_POST['labName'];
    $description = $_POST['description'];
    $contactInfo = $_POST['contactInfo'];
    $location = $_POST['location'];

    // Insert data into database
    $insertSql = "INSERT INTO laboratories (lab_name, description, contact_info, location) VALUES (?, ?, ?, ?)";
    if ($stmt = mysqli_prepare($link, $insertSql)) {
        mysqli_stmt_bind_param($stmt, "ssss", $labName, $description, $contactInfo, $location);
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($link)]);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($link)]);
    }
}

// Handle form submission for updating a laboratory
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updateLabId'])) {
    $updateLabId = $_POST['updateLabId'];
    $updateLabName = $_POST['updateLabName'];
    $updateDescription = $_POST['updateDescription'];
    $updateContactInfo = $_POST['updateContactInfo'];
    $updateLocation = $_POST['updateLocation'];

    // SQL to update a record
    $updateSql = "UPDATE laboratories SET lab_name=?, description=?, contact_info=?, location=? WHERE lab_id=?";
    if ($stmt = mysqli_prepare($link, $updateSql)) {
        mysqli_stmt_bind_param($stmt, "ssssi", $updateLabName, $updateDescription, $updateContactInfo, $updateLocation, $updateLabId);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: lab.php");
            exit();
        } else {
            echo "Error updating record: " . mysqli_error($link);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error: " . mysqli_error($link);
    }
}

$sql = "SELECT * FROM laboratories";
$result = mysqli_query($link, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.4.0/remixicon.css" crossorigin="">
    <link rel="stylesheet" href="styles/lab.css">
    <title>Laboratory</title>
</head>
<body>
    <div class="lab-container">
        <h1>Laboratory</h1>
        <?php if (can_add_lab()): ?>
            <button class="btn btn-primary" id="addLabButton">Add Laboratory</button>
        <?php endif; ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Laboratory Name</th>
                    <th>Description</th>
                    <th>Contact Information</th>
                    <th>Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr id="lab-<?php echo $row['lab_id']; ?>">
                            <td><?php echo htmlspecialchars($row['lab_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td><?php echo htmlspecialchars($row['contact_info']); ?></td>
                            <td><?php echo htmlspecialchars($row['location']); ?></td>
                            <td>
                            <?php if (can_manage_lab($row['lab_name'])): ?>
                                <button class="action-btn edit-btn" onclick="updateLab(<?php echo $row['lab_id']; ?>, '<?php echo htmlspecialchars($row['lab_name']); ?>', '<?php echo htmlspecialchars($row['description']); ?>', '<?php echo htmlspecialchars($row['contact_info']); ?>', '<?php echo htmlspecialchars($row['location']); ?>')">
                                    <i class="ri-edit-box-line"></i>
                                </button>
                                <button class="action-btn delete-btn" onclick="deleteLab(<?php echo $row['lab_id']; ?>, '<?php echo addslashes($row['lab_name']); ?>')">
                                    <i class="ri-delete-bin-2-line"></i>
                                </button>
                            <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No laboratories found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Add Lab Modal -->
    <div class="custom-modal" id="addLabModal">
        <div class="custom-modal-content">
            <div class="custom-modal-header">
                <h3 class="custom-modal-title">Add Laboratory</h3>
                <span class="close" data-close>&times;</span>
            </div>
            <div class="custom-modal-body">
                <form id="addLabForm" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="labName">Laboratory Name</label>
                        <input type="text" class="form-control" id="labName" name="labName" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="contactInfo">Contact Information</label>
                        <input type="text" class="form-control" id="contactInfo" name="contactInfo" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" class="form-control" id="location" name="location" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Lab</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Lab Modal -->
    <div class="custom-modal" id="updateLabModal">
        <div class="custom-modal-content">
            <div class="custom-modal-header">
                <h3 class="custom-modal-title">Update Laboratory</h3>
                <span class="close" data-close>&times;</span>
            </div>
            <div class="custom-modal-body">
                <form id="updateLabForm" method="POST" enctype="multipart/form-data">
                    <input type="hidden" id="updateLabId" name="updateLabId">
                    <div class="form-group">
                        <label for="updateLabName">Laboratory Name</label>
                        <input type="text" class="form-control" id="updateLabName" name="updateLabName">
                    </div>
                    <div class="form-group">
                        <label for="updateDescription">Description</label>
                        <textarea class="form-control" id="updateDescription" name="updateDescription"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="updateContactInfo">Contact Information</label>
                        <input type="text" class="form-control" id="updateContactInfo" name="updateContactInfo">
                    </div>
                    <div class="form-group">
                        <label for="updateLocation">Location</label>
                        <input type="text" class="form-control" id="updateLocation" name="updateLocation">
                    </div>
                    <button type="submit" class="btn btn-primary">Update Lab</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal for Labs -->
    <div id="deleteLabConfirmationModal" class="delete-custom-modal">
        <div class="delete-custom-modal-content">
            <div class="delete-custom-modal-header">
                <h3>Delete Laboratory</h3>
            </div>
            <div class="delete-custom-modal-body">
                <p id="deleteLabModalText"></p>
                <button class="btns confirm-btn" onclick="confirmDeleteLab()">Okay</button>
                <button class="btns cancel-btn" onclick="closeLabModal()">Cancel</button>
            </div>
        </div>
    </div>
    <script src="scripts/lab.js"></script>
    <script src="scripts/search.js"></script>
</body>
</html>