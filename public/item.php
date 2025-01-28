<?php
include_once 'side_bar.php';
require_once "../config/database.php";
require_once "../includes/functions.php";
include_once 'auto_check.php';

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Get user role
$user_role = $_SESSION['role'] ?? '';

if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Handle form submission for adding an item
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['itemName'])) {
    $itemName = $_POST['itemName'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $unitMeasurement = $_POST['unitMeasurement'];
    $username = $_SESSION['username']; 

    // Insert data into the items table
    $sql = "INSERT INTO items (item_name, category, description, unit_measurement) VALUES (?, ?, ?, ?)";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssss", $itemName, $category, $description, $unitMeasurement);
        if (mysqli_stmt_execute($stmt)) {
            // Insert data into the history table
            $historySql = "INSERT INTO history (username, item, action) VALUES (?, ?, 'Added')";
            if ($historyStmt = mysqli_prepare($link, $historySql)) {
                mysqli_stmt_bind_param($historyStmt, "ss", $username, $itemName);
                mysqli_stmt_execute($historyStmt);
                mysqli_stmt_close($historyStmt);
            }
        } else {
            echo "Error: " . mysqli_error($link);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error: " . mysqli_error($link);
    }
}

// Handle form submission for updating an item
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updateItemId'])) {
    $updateItemId = $_POST['updateItemId'];
    $updateItemName = $_POST['updateItemName'];
    $updateCategory = $_POST['updateCategory'];
    $updateDescription = $_POST['updateDescription'];
    $updateUnitMeasurement = $_POST['updateUnitMeasurement'];
    $username = $_SESSION['username']; 

    // Update data in the items table
    $updateSql = "UPDATE items SET item_name=?, category=?, description=?, unit_measurement=? WHERE item_id=?";
    if ($stmt = mysqli_prepare($link, $updateSql)) {
        mysqli_stmt_bind_param($stmt, "ssssi", $updateItemName, $updateCategory, $updateDescription, $updateUnitMeasurement, $updateItemId);
        if (mysqli_stmt_execute($stmt)) {
            // Insert data into the history table
            $historySql = "INSERT INTO history (username, item, action) VALUES (?, ?, 'Edited')";
            if ($historyStmt = mysqli_prepare($link, $historySql)) {
                mysqli_stmt_bind_param($historyStmt, "ss", $username, $updateItemName);
                mysqli_stmt_execute($historyStmt);
                mysqli_stmt_close($historyStmt);
            }
            header("Location: item.php"); 
            exit();
        } else {
            echo "Error updating record: " . mysqli_error($link);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error: " . mysqli_error($link);
    }
}

// Handle item deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteItemId'])) {
    $deleteItemId = $_POST['deleteItemId'];
    $username = $_SESSION['username']; 

    // Get item name before deletion
    $itemSql = "SELECT item_name FROM items WHERE item_id = ?";
    if ($itemStmt = mysqli_prepare($link, $itemSql)) {
        mysqli_stmt_bind_param($itemStmt, "i", $deleteItemId);
        mysqli_stmt_execute($itemStmt);
        mysqli_stmt_bind_result($itemStmt, $itemName);
        mysqli_stmt_fetch($itemStmt);
        mysqli_stmt_close($itemStmt);
    }

    // SQL to delete a record
    $deleteSql = "DELETE FROM items WHERE item_id = ?";
    if ($stmt = mysqli_prepare($link, $deleteSql)) {
        mysqli_stmt_bind_param($stmt, "i", $deleteItemId);
        if (mysqli_stmt_execute($stmt)) {
            // Insert data into the history table
            $historySql = "INSERT INTO history (username, item, action) VALUES (?, ?, 'Deleted')";
            if ($historyStmt = mysqli_prepare($link, $historySql)) {
                mysqli_stmt_bind_param($historyStmt, "ss", $username, $itemName);
                mysqli_stmt_execute($historyStmt);
                mysqli_stmt_close($historyStmt);
            }
        } else {
            echo "Error deleting item: " . mysqli_error($link);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error: " . mysqli_error($link);
    }
}

// Fetch history data from the database
$historySql = "SELECT * FROM history ORDER BY date DESC";
$historyResult = mysqli_query($link, $historySql);

// Fetch data from the database
$sql = "SELECT * FROM items";
$result = mysqli_query($link, $sql);

// Fetch the count of items per category
$countSql = "SELECT category, COUNT(*) as count FROM items GROUP BY category";
$countResult = mysqli_query($link, $countSql);
$categoryCounts = [];
if ($countResult) {
    while ($row = mysqli_fetch_assoc($countResult)) {
        $categoryCounts[$row['category']] = $row['count'];
    }
}

$categoryCountsJson = json_encode($categoryCounts);

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.4.0/remixicon.css" crossorigin="">
    <link rel="stylesheet" href="styles/item.css">
    <title>Item</title>
</head>
<body>
    <div class="item-container">
        <div class="item-header">
            <h1>Items</h1>
            <?php if (can_manage_item()): ?>
                <button class="btn btn-primary" id="addItemButton">Add Item</button>
            <?php endif; ?>
        </div>
        <div class="table-container">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Unit of Measurement</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr id="item-<?php echo $row['item_id']; ?>">
                                <td><?php echo $row['item_id']; ?></td>
                                <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['category']); ?></td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td><?php echo htmlspecialchars($row['unit_measurement']); ?></td>
                                <td>
                                    <?php if (can_manage_item()): ?>
                                        <button class="action-btn edit-btn" onclick="updateItem(<?php echo $row['item_id']; ?>, '<?php echo htmlspecialchars($row['item_name']); ?>', '<?php echo htmlspecialchars($row['category']); ?>', '<?php echo htmlspecialchars($row['description']); ?>', '<?php echo htmlspecialchars($row['unit_measurement']); ?>')">
                                            <i class="ri-edit-box-line"></i>
                                        </button>
                                        <button class="action-btn delete-btn" onclick="deleteItem(<?php echo $row['item_id']; ?>, '<?php echo addslashes($row['item_name']); ?>')">
                                            <i class="ri-delete-bin-2-line"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No items found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Item Modal -->
    <div class="custom-modal" id="addItemModal">
        <div class="custom-modal-content">
            <div class="custom-modal-header">
                <h3 class="custom-modal-title">Add Item</h3>
                <span class="close" data-close>&times;</span>
            </div>
            <div class="custom-modal-body">
                <form id="addItemForm" method="POST">
                    <div class="form-group">
                        <label for="itemName">Item Name</label>
                        <input type="text" class="form-control" id="itemName" name="itemName" required>
                    </div>
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select class="form-control" id="category" name="category" required>
                            <option value="chemical/reagent">Chemical/Reagent</option>
                            <option value="glassware">Glassware</option>
                            <option value="equipment">Equipment</option>
                            <option value="consumable">Consumable</option>
                            <option value="culture media">Culture Media</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" style="font-family: 'Ubuntu', sans-serif; font-size: 16px;" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="unitMeasurement">Unit of Measurement</label>
                        <input type="text" class="form-control" id="unitMeasurement" name="unitMeasurement" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Item</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Item Modal -->
    <div class="custom-modal" id="updateItemModal">
        <div class="custom-modal-content">
            <div class="custom-modal-header">
                <h3 class="custom-modal-title">Update Item</h3>
                <span class="close" data-close>&times;</span>
            </div>
            <div class="custom-modal-body">
                <form id="updateItemForm" method="POST">
                    <input type="hidden" id="updateItemId" name="updateItemId">
                    <div class="form-group">
                        <label for="updateItemName">Item Name</label>
                        <input type="text" class="form-control" id="updateItemName" name="updateItemName" required>
                    </div>
                    <div class="form-group">
                        <label for="updateCategory">Category</label>
                        <select class="form-control" id="updateCategory" name="updateCategory" required>
                            <option value="chemical/reagent">Chemical/Reagent</option>
                            <option value="glassware">Glassware</option>
                            <option value="equipment">Equipment</option>
                            <option value="consumable">Consumable</option>
                            <option value="culture media">Culture Media</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="updateDescription">Description</label>
                        <textarea class="form-control" id="updateDescription" name="updateDescription" style="font-family: 'Ubuntu', sans-serif; font-size: 16px;" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="updateUnitMeasurement">Unit of Measurement</label>
                        <input type="text" class="form-control" id="updateUnitMeasurement" name="updateUnitMeasurement" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Item</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal for Items -->
    <div id="deleteItemConfirmationModal" class="delete-custom-modal">
        <div class="delete-custom-modal-content">
            <div class="delete-custom-modal-header">
                <h3>Delete Item</h3>
            </div>
            <div class="delete-custom-modal-body">
                <p id="deleteItemModalText"></p>
                <button class="btns confirm-btn" onclick="confirmDeleteItem()">Okay</button>
                <button class="btns cancel-btn" onclick="closeItemModal()">Cancel</button>
            </div>
        </div>
    </div>


    <script>
        // Handle modal opening and closing
        document.getElementById('addItemButton').addEventListener('click', function () {
            document.getElementById('addItemModal').style.display = 'block';
        });

        document.querySelectorAll('[data-close]').forEach(function (element) {
            element.addEventListener('click', function () {
                element.closest('.custom-modal').style.display = 'none';
            });
        });

        // Close the modal if the user clicks anywhere outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('addItemModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }

        // Handle update item
        function updateItem(id, name, category, description, unitMeasurement) {
            document.getElementById('updateItemId').value = id;
            document.getElementById('updateItemName').value = name;
            document.getElementById('updateDescription').value = description;
            document.getElementById('updateUnitMeasurement').value = unitMeasurement;
        
            var categorySelect = document.getElementById('updateCategory');
            // Set the selected option based on the category
            for (var i = 0; i < categorySelect.options.length; i++) {
                if (categorySelect.options[i].value === category) {
                    categorySelect.options[i].selected = true;
                    break;
                }
            }
        
            document.getElementById('updateItemModal').style.display = 'block';

            // Close the modal if the user clicks anywhere outside of it
            window.onclick = function(event) {
                const modal = document.getElementById('updateItemModal');
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            }
        }

        // Handle delete item
        let itemIdToDelete = null;

        function deleteItem(id, itemName) {
            itemIdToDelete = id;
            document.getElementById('deleteItemModalText').textContent = `Are you sure you want to delete this item: ${itemName}?`;
            document.getElementById('deleteItemConfirmationModal').style.display = 'block';
        }

        function closeItemModal() {
            document.getElementById('deleteItemConfirmationModal').style.display = 'none';
            itemIdToDelete = null;
        }

        function confirmDeleteItem() {
            if (itemIdToDelete) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
            
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'deleteItemId';
                input.value = itemIdToDelete;
            
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
    <script src="scripts/search.js"></script>
</body>
</html>
