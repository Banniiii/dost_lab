<?php
include_once "side_bar.php";
require_once "../config/database.php";
require_once "../includes/functions.php";
include_once 'auto_check.php';

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Fetch data from the database with JOINs
$sql = "
    SELECT 
        i.inventory_id,
        l.lab_name AS lab_name,
        it.item_name AS item_name,
        i.unit_measurement,
        i.batch_number,
        i.minimum_stock,
        i.stock,
        i.used_stock,
        i.exp_date
    FROM 
        inventory i
    JOIN 
        laboratories l ON i.lab_id = l.lab_id
    JOIN 
        items it ON i.item_id = it.item_id
";

$result = mysqli_query($link, $sql);

$role = $_SESSION['role'] ?? '';
$labName = '';

if (is_lab_manager()) {
    if ($role === 'Microbiology Lab Manager') {
        $labName = 'Microbiology';
    } elseif ($role === 'Chemical Lab Manager') {
        $labName = 'Chemical';
    } elseif ($role === 'Metrology Lab Manager') {
        $labName = 'Metrology';
    }
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.4.0/remixicon.css" crossorigin="">
    <link rel="stylesheet" href="styles/inventory.css">
    <title>Inventory</title>
</head>
<body>
    <div class="header-container">
        <div class="card card-stock-notice">
            <div class="label">
                <span class="out-of-stock-icon">🚫</span> Stock Notice
            </div>
        </div>
        <div class="card card-expiry-warning">
            <div class="label">
                <span class="warning-icon">⚠️</span> Expiry Warning
            </div>
        </div>
        <div class="card card-out-of-stock">
            <div class="label">
                <span class="out-of-stock-icon">🚫</span> Out of Stock
            </div>
        </div>
        <div class="card card-expired-batch">
            <div class="label">
                <span class="warning-icon">⚠️</span> Expired Batch
            </div>
        </div>
    </div>

    <div class="main-container">
        <div class="inventory-container">
            <div class="inventory-header">
                <h1>Inventory</h1>
                <?php if (can_add_inventory()): ?>
                    <button class="add-button" id="addInventoryButton">Add Inventory</button>
                <?php endif; ?>
            </div>

            <div class="table-container">
                <table class="table table-striped inventory-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Laboratory</th>
                            <th>Item</th>
                            <th>Unit of Measurement</th>
                            <th>Batch Number</th>
                            <th>Minimum Stock</th>
                            <th>Available Stock</th>
                            <th>Used Stock</th>
                            <th>Expiration Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="inventoryTableBody">
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <?php
                                    $today = date('Y-m-d');
                                    $stock = intval($row['stock']);
                                    $minStock = intval($row['minimum_stock']);
                                    $expDate = $row['exp_date'];
                            
                                    // Default row style
                                    $rowStyle = '';
                            
                                    // Check if stock is below minimum or expiration date has passed
                                    if ($stock <= $minStock || $expDate <= $today) {
                                        $rowStyle = 'background-color: rgba(238, 123, 9, 0.213);'; 

                                        // Additional styling based on specific conditions
                                        if ($stock <= $minStock) {
                                            $stockText = $stock . ' 🚫';
                                            if ($stock == 0) {
                                                $rowStyle = 'background-color: rgba(255, 0, 0, 0.213);'; // Critical warning color
                                            }
                                        } else {
                                            $stockText = $stock;
                                        }
                                    
                                        if ($expDate <= $today) {
                                            $expDateText = $expDate . ' ⚠️';
                                            $rowStyle = 'background-color: rgba(255, 0, 0, 0.213);'; // Critical warning color
                                        } else {
                                            $expDateText = $expDate;
                                        }
                                    } else {
                                        $stockText = $stock;
                                        $expDateText = $expDate;
                                    }
                                ?>
                                <tr style="<?php echo htmlspecialchars($rowStyle); ?>">
                                    <td><?php echo htmlspecialchars($row['inventory_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['lab_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['unit_measurement']); ?></td>
                                    <td><?php echo htmlspecialchars($row['batch_number']); ?></td>
                                    <td><?php echo htmlspecialchars($row['minimum_stock']); ?></td>
                                    <td><?php echo htmlspecialchars($stockText); ?></td>
                                    <td><?php echo htmlspecialchars($row['used_stock']); ?></td>
                                    <td><?php echo htmlspecialchars($expDateText); ?></td>
                                    <td>
                                        <?php if (can_use_inventory($row['lab_name'])): ?>
                                            <button class="action-btn use-btn" onclick="openUseStockModal('<?php echo addslashes($row['lab_name']); ?>', '<?php echo addslashes($row['item_name']); ?>', '<?php echo addslashes($row['batch_number']); ?>', <?php echo $row['minimum_stock']; ?>, <?php echo $row['stock']; ?>, <?php echo $row['used_stock']; ?>, '<?php echo addslashes($row['exp_date']); ?>')">
                                            Use
                                        <?php endif; ?>
                                        <?php if (can_editdelete_inventory($row['lab_name'])): ?>
                                            </button>
                                            <button class="action-btn edit-btn" onclick="openEditModal(<?php echo $row['inventory_id']; ?>)">
                                                <i class="ri-edit-box-line"></i>
                                            </button>
                                            <button class="action-btn delete-btn" onclick="deleteInventory(<?php echo $row['inventory_id']; ?>, '<?php echo addslashes($row['batch_number']); ?>')">
                                                <i class="ri-delete-bin-2-line"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10">No inventory records found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- add inventory modal-->
    <div id="addInventoryModal" class="custom-modal">
        <div class="custom-modal-content">
            <div class="custom-modal-header">
                <h3>Add Inventory</h3>
                <span class="close" onclick="closeAddInventoryModal()">&times;</span>
            </div>
            <div class="custom-modal-body">
                <form id="addInventoryForm">
                    <div class="form-group">
                        <label for="labSelect">Laboratory:</label>
                        <select id="labSelect" class="form-control" name="labId" required>
                            <!-- Laboratory options will be populated dynamically -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="itemSelect">Item:</label>
                        <select id="itemSelect" class="form-control" name="itemId" required>
                            <!-- Item options will be populated dynamically -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="unitMeasurement">Unit of Measurement:</label>
                        <input type="text" id="unitMeasurement" class="form-control" name="unitMeasurement" readonly required>
                    </div>
                    <div class="form-group">
                        <label for="batchNumber">Batch Number:</label>
                        <input type="text" id="batchNumber" class="form-control" name="batchNumber" required>
                    </div>
                    <div class="form-group">
                        <label for="minimumStock">Minimum Stock:</label>
                        <input type="number" id="minimumStock" class="form-control" name="minimumStock" required>
                    </div>
                    <div class="form-group">
                        <label for="stock">Available Stock:</label>
                        <input type="number" id="stock" class="form-control" name="stock" required>
                    </div>
                    <div class="form-group">
                        <label for="expDate">Expiration Date:</label>
                        <input type="date" id="expDate" class="form-control" name="expDate" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Add Inventory</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- update inventory modal-->
    <div id="updateInventoryModal" class="custom-modal">
        <div class="custom-modal-content">
            <div class="custom-modal-header">
                <h3>Edit Inventory</h3>
                <span class="close" onclick="closeUpdateInventoryModal()">&times;</span>
            </div>
            <div class="custom-modal-body">
                <form id="updateInventoryForm">
                    <input type="hidden" id="updateInventoryId" name="inventoryId">
                    <div class="form-group">
                        <label for="updateLabSelect">Laboratory:</label>
                        <select id="updateLabSelect" class="form-control" name="labId" required>
                            <!-- Laboratory options will be populated dynamically -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="updateItemSelect">Item:</label>
                        <select id="updateItemSelect" class="form-control" name="itemId" required>
                            <!-- Item options will be populated dynamically -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="updateUnitMeasurement">Unit of Measurement:</label>
                        <input type="text" id="updateUnitMeasurement" class="form-control readonly-field" name="unitMeasurement" readonly required>
                    </div>
                    <div class="form-group">
                        <label for="updateBatchNumber">Batch Number:</label>
                        <input type="text" id="updateBatchNumber" class="form-control" name="batchNumber" required>
                    </div>
                    <div class="form-group">
                        <label for="updateMinimumStock">Minimum Stock:</label>
                        <input type="number" id="updateMinimumStock" class="form-control" name="minimumStock" required>
                    </div>
                    <div class="form-group">
                        <label for="updateStock">Available Stock:</label>
                        <input type="number" id="updateStock" class="form-control" name="stock" required>
                    </div>
                    <div class="form-group">
                        <label for="updateUsedStock">Used Stock:</label>
                        <input type="number" id="updateUsedStock" class="form-control" name="usedStock" readonly>
                    </div>
                    <div class="form-group">
                        <label for="updateExpDate">Expiration Date:</label>
                        <input type="date" id="updateExpDate" class="form-control" name="expDate" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update Inventory</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Use Stock Modal -->
    <div id="useStockModal" class="custom-modal">
        <div class="custom-modal-content">
            <div class="custom-modal-header">
                <h3>Stock</h3>
                <span class="close" onclick="closeUseStockModal()">&times;</span>
            </div>
            <div class="custom-modal-body">
                <form id="useStockForm">
                    <div class="form-group">
                        <label for="useStockLab">Laboratory:</label>
                        <input type="text" id="useStockLab" name="lab" class="form-control readonly-field" readonly>
                    </div>
                    <div class="form-group">
                        <label for="useStockItem">Item:</label>
                        <input type="text" id="useStockItem" name="item" class="form-control readonly-field" readonly>
                    </div>
                    <div class="form-group">
                        <label for="useStockBatch">Batch Number:</label>
                        <input type="text" id="useStockBatch" name="batch" class="form-control readonly-field" readonly>
                    </div>
                    <div class="form-group">
                        <label for="useStockMin">Minimum Stock:</label>
                        <input type="number" id="useStockMin" name="min_stock" class="form-control readonly-field" readonly>
                    </div>
                    <div class="form-group">
                        <label for="useStockTotal">Available Stock:</label>
                        <input type="number" id="useStockTotal" name="total_stock" class="form-control readonly-field" readonly>
                    </div>
                    <div class="form-group">
                        <label for="useStockUse">Add Stock to Use:</label>
                        <input type="number" id="useStockUse" name="use_stock" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Use Stock</button>
                    </div>
                    <div class="form-group">
                        <label for="useStockUsed">Used Stock Total:</label>
                        <input type="number" id="useStockUsed" name="used_stock" class="form-control readonly-field" readonly>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Insufficient Stock Modal -->
    <div id="insufficientStockModal" class="insufficient-custom-modal">
        <div class="insufficient-modal-content">
            <div class="insufficient-modal-header">
                <h3>Insufficient Stock</h3>
            </div>
            <div class="insufficient-modal-body">
                <p>Insufficient balance of stock.</p>
                <div class="form-group">
                    <button class="btn insufficient-btn-primary" onclick="closeInsufficientStockModal()">Okay</button>
                </div>
            </div>
            <span class="warning-symbol">🚫</span>
        </div>
    </div>

    <!-- Expired Stock Modal -->
    <div id="expiredStockModal" class="expired-custom-modal">
        <div class="expired-modal-content">
            <div class="expired-modal-header">
                <h3>Expired Stock</h3>
            </div>
            <div class="expired-modal-body">
                <p>The selected batch has expired.</p>
                <div class="form-group">
                    <button class="btn expired-btn-primary" onclick="closeExpiredStockModal()">Okay</button>
                </div>
            </div>
            <span class="warning-symbol">⚠️</span>
        </div>
    </div>

    <!-- Insufficient and Expired Stock Modal -->
    <div id="insufficientExpiredStockModal" class="insufficientexpired-custom-modal">
        <div class="insufficientexpired-modal-content">
            <div class="expired-modal-header">
                <h3>Insufficient stock and Expired batch</h3>
            </div>
            <div class="insufficientexpired-modal-body">
                <p>The selected batch has insufficient stock and has expired.</p>
                <div class="form-group">
                    <button class="btn insufficientexpired-btn-primary" onclick="closeInsufficientExpiredStockModal()">Okay</button>
                </div>
            </div>
            <span class="warnings-symbol">🚫⚠️</span>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div id="receiptModal" class="receipt-custom-modal">
        <div class="receipt-modal-content">
            <div class="receipt-modal-header">
                <h3>Your Transaction</h3>
            </div>
            <div class="receipt-modal-body">
                <p><strong>User:</strong> <span id="receiptUser"></span></p>
                <p><strong>Laboratory:</strong> <span id="receiptLab"></span></p>
                <p><strong>Item:</strong> <span id="receiptItem"></span></p>
                <p><strong>Batch Number:</strong> <span id="receiptBatch"></span></p>
                <p><strong>Total Stock Used:</strong> <span id="receiptUsedStock"></span></p>
                <p><strong>Remarks:</strong> <span id="receiptRemarks"></span></p>
                <div class="receipt-form-group"> 
                    <button class="btn receipt-btn-primary" onclick="proceedTransaction('receiptModal')">Okay</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Decision Modal -->
    <div id="decisionModal" class="decision-custom-modal">
        <div class="decision-modal-content">
            <h2>Confirm which Batch</h2>
            <p>The selected batch is not the nearest to expiration. Which one do you want to utilize?</p>
            <div class="decision-modal-buttons">
                <button id="useNearestBatch">Nearest Expiration Batch</button>
                <button id="useSelectedBatch">Currently Selected Batch</button>
            </div>
        </div>
    </div>

    <!-- Insufficient Balance Modal -->
    <div id="insufficientBalanceModal" class="insufficient-balance-custom-modal">
        <div class="insufficient-balance-modal-content">
            <div class="insufficient-balance-modal-header">
                <h3>Insufficient Balance</h3>
            </div>
            <div class="insufficient-balance-modal-body">
                <p>The used stock exceeds the available stock.</p>
                <div class="form-group">
                    <button class="btn insufficient-balance-btn-primary" onclick="closeInsufficientBalanceModal()">Okay</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Insufficient Balance Decision Modal -->
    <div id="insufficientBalanceDecisionModal" class="insufficient-decision-custom-modal">
        <div class="insufficient-decision-modal-content">
            <div class="insufficient-decision-modal-header">
                <h3>Insufficient Balance</h3>
            </div>
            <div class="insufficient-decision-modal-body">
                <p>The used stock exceeds the available stock. Do you want to use other batches to get the remaining stocks needed?</p>
                <div class="form-group">
                    <button class="btn insufficient-decision-btn-primary" onclick="useOtherBatches()">Yes</button>
                    <button class="btn insufficient-decision-btn-secondary" onclick="closeInsufficientBalanceDecisionModal()">No</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Receipt Modal -->
    <div id="ReceiptModal" class="Receipt-custom-modal">
        <div class="Receipt-modal-content">
            <div class="Receipt-modal-header">
                <h3>Transaction Receipt</h3>
            </div>
            <div class="Receipt-modal-body">
                <p><strong>User:</strong> <span id="ReceiptUser"></span></p>
                <p><strong>Laboratory:</strong> <span id="ReceiptLab"></span></p>
                <p><strong>Item:</strong> <span id="ReceiptItem"></span></p>
                <table class="receipt-table">
                    <thead>
                        <tr>
                            <th>Batch Number</th>
                            <th>Used Stock</th>
                        </tr>
                    </thead>
                    <tbody id="receiptBatches"></tbody>
                </table>
                <p><strong>Total Used Stock:</strong> <span id="receiptTotalUsed"></span></p>
                <p><strong>Remarks:</strong> <span id="ReceiptRemarks"></span></p>
                <div class="Receipt-form-group">
                    <button class="receipt-btn-primary" onclick="proceedTransaction('ReceiptModal')">Okay</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmationModal" class="delete-custom-modal">
        <div class="delete-custom-modal-content">
            <div class="delete-custom-modal-header">
                <h3>Delete</h3>
            </div>
            <div class="delete-custom-modal-body"></div>
                <p id="deleteModalText"></p>
                <button class="btns confirm-btn" onclick="confirmDelete()">Okay</button>
                <button class="btns cancel-btn" onclick="closeModal()">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Remarks Modal -->
    <div id="remarksModal" class="remarks-custom-modal">
        <div class="remarks-modal-content">
            <div class="remarks-modal-header">
                <h3>Enter Remarks</h3>
            </div>
            <div class="remarks-modal-body">
                <form id="remarksForm">
                    <div class="remarks-form-group">
                        <textarea id="remarks" name="remarks" rows="4" cols="40" placeholder="Enter remarks for this batch" style="font-family: 'Ubuntu', sans-serif; font-size: 16px;"></textarea>
                    </div>
                    <div class="remarks-form-group">
                        <button type="button" id="saveRemarksBtn" class="remarks-btn-primary">Save Remarks</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Function to open a modal by ID
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.style.display = 'block';
        }

        // Function to close a modal by ID
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.style.display = 'none';
        }

        document.addEventListener("DOMContentLoaded", function() {
            // Add modal handling for add inventory modal
            const modalBtn = document.getElementById('addInventoryButton');
            const closeAddBtn = document.querySelector('#addInventoryModal .close');
            const addForm = document.getElementById('addInventoryForm');
            const tbody = document.getElementById('inventoryTableBody');

            // Show add inventory modal
            modalBtn.addEventListener('click', function() {
                openModal('addInventoryModal');
            });

            // Close add inventory modal
            closeAddBtn.addEventListener('click', function() {
                closeModal('addInventoryModal');
            });

            // Fetch and populate dropdowns for add inventory modal
            fetchLaboratories();
            fetchItems();           

            let userRole = "<?php echo $role; ?>";
            let userLab = "<?php echo $labName; ?>";

            // Function to fetch laboratories from the database
            function fetchLaboratories() {
                fetch('fetch_laboratories.php')
                    .then(response => response.json())
                    .then(data => {
                        laboratories = data;
                        const labSelect = document.getElementById('labSelect');
                        labSelect.innerHTML = '';
                    
                        // Create and append options
                        data.forEach(lab => {
                            const option = document.createElement('option');
                            option.value = lab.lab_id;
                            option.textContent = lab.lab_name;
                            labSelect.appendChild(option);
                        });
                    
                        // Automatically select the lab if the user is a lab manager
                        if (userRole.includes('Lab Manager') && userLab) {
                            const labOption = Array.from(labSelect.options).find(option => option.textContent === userLab);
                            if (labOption) {
                                labSelect.value = labOption.value; // Set the value to the matching option
                            }
                            labSelect.disabled = true; // Disable the dropdown to prevent changes
                        }
                    })
                    .catch(error => console.error('Error fetching laboratories:', error));
            }
      
            // Function to fetch items from database and set unit of measurement
            function fetchItems() {
                fetch('fetch_items.php')
                    .then(response => response.json())
                    .then(data => {
                        items = data;
                        const itemSelect = document.getElementById('itemSelect');
                        itemSelect.innerHTML = '';
                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.item_id;
                            option.textContent = item.item_name;
                            itemSelect.appendChild(option);
                        });         

                        // Automatically set unit of measurement for the first item when modal opens
                        if (items.length > 0) {
                            itemSelect.value = items[0].item_id; // Set the first item as selected
                            document.getElementById('unitMeasurement').value = items[0].unit_measurement;
                        }           

                        // Set unit of measurement dynamically when user changes item
                        itemSelect.addEventListener('change', function() {
                            const selectedItem = items.find(item => item.item_id == itemSelect.value);
                            if (selectedItem) {
                                document.getElementById('unitMeasurement').value = selectedItem.unit_measurement;
                            }
                        });
                    })
                    .catch(error => console.error('Error fetching items:', error));
            }

            // Fetch and display existing inventory data
            fetch('fetch_inventory.php')
                .then(response => response.json())
                .then(data => {
                    //updateRowStyles();
                })
            .catch(error => console.error('Error fetching inventory:', error));

            // Handle form submission for add inventory form
            addForm.addEventListener('submit', function(event) {
                event.preventDefault();

                // Collect form data
                const formData = {
                    labId: document.getElementById('labSelect').value,
                    itemId: document.getElementById('itemSelect').value,
                    unitMeasurement: document.getElementById('unitMeasurement').value,
                    batchNumber: document.getElementById('batchNumber').value,
                    minimumStock: document.getElementById('minimumStock').value,
                    stock: document.getElementById('stock').value,
                    expDate: document.getElementById('expDate').value
                };

                // Post form data to server
                fetch('add_inventory.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData),
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        // Find the names of the laboratory and item
                        const labName = laboratories.find(lab => lab.lab_id == formData.labId).lab_name;
                        const itemName = items.find(item => item.item_id == formData.itemId).item_name;

                        // Update the table with the new inventory item
                        const newRow = document.createElement('tr');
                        newRow.innerHTML = `
                            <td>${data.inventory_id}</td>
                            <td>${labName}</td>
                            <td>${itemName}</td>
                            <td>${formData.unitMeasurement}</td>
                            <td>${formData.batchNumber}</td>
                            <td>${formData.minimumStock}</td>
                            <td>${formData.stock}</td>
                            <td>${formData.expDate}</td>
                            <td>
                                <button class="action-btn use-btn" onclick="openUseStockModal('${item.lab_name}', '${item.item_name}', '${item.batch_number}', ${item.minimum_stock}, ${item.stock}, ${item.used_stock}, '${item.exp_date}')">
                                    Use
                                </button>
                                <button class="action-btn edit-btn" onclick="openEditModal(${data.inventory_id})">
                                    <i class="ri-edit-box-line"></i>
                                </button>
                                <button class="action-btn delete-btn" onclick="deleteInventory(${item.inventory_id}, '${item.batch_number}')">
                                    <i class="ri-delete-bin-2-line"></i>
                                </button>
                            </td>
                        `;
                        tbody.appendChild(newRow);

                        // Hide the add inventory modal
                        closeModal('addInventoryModal');
                        addForm.reset();
                        //updateRowStyles(); // Update row styles after adding the new inventory item
                    } else {
                        console.error('Error adding inventory:', data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
                //reload the page
                location.reload();
            });
        });

        // Function to open edit inventory modal
        function openEditModal(inventoryId) {
            const modal = document.getElementById('updateInventoryModal');
            modal.style.display = 'block';

            // Fetch inventory details for given inventoryId
            fetch(`fetch_inventory_details.php?id=${inventoryId}`)
                .then(response => response.json())
                .then(data => {
                    // Populate fields with fetched data
                    document.getElementById('updateInventoryId').value = data.inventory_id;
                    document.getElementById('updateUnitMeasurement').value = data.unit_measurement;
                    document.getElementById('updateBatchNumber').value = data.batch_number;
                    document.getElementById('updateMinimumStock').value = data.minimum_stock;
                    document.getElementById('updateStock').value = data.stock;
                    document.getElementById('updateUsedStock').value = data.used_stock;
                    document.getElementById('updateExpDate').value = data.exp_date;
                
                    // Fetch laboratory options and populate the select dropdown
                    fetch('fetch_laboratories.php')
                        .then(response => response.json())
                        .then(labs => {
                            const labSelect = document.getElementById('updateLabSelect');
                            labSelect.innerHTML = labs.map(lab => 
                                `<option value="${lab.lab_id}">${lab.lab_name}</option>`
                            ).join('');

                            // Set the selected value
                            labSelect.value = data.lab_id;

                            // Check user role and update the dropdown if needed
                            const userRole = "<?php echo $role; ?>"; // PHP to JS
                            const userLab = "<?php echo $labName; ?>"; // PHP to JS

                            if (userRole.includes('Lab Manager')) {
                                labSelect.value = data.lab_id; // Set the lab to the user's lab if needed
                                labSelect.disabled = true; // Disable the dropdown
                            }
                        })
                    .catch(error => console.error('Error fetching laboratories:', error));
                    
                    // Fetch item options and populate the select dropdown
                    fetch('fetch_items.php')
                        .then(response => response.json())
                        .then(items => {
                            const itemSelect = document.getElementById('updateItemSelect');
                            itemSelect.innerHTML = items.map(item => 
                                `<option value="${item.item_id}">${item.item_name}</option>`
                            ).join('');
                            itemSelect.value = data.item_id; // Set the selected value
                        
                            // Update unit measurement based on selected item
                            itemSelect.addEventListener('change', function() {
                                const selectedItem = items.find(item => item.item_id == this.value);
                                document.getElementById('updateUnitMeasurement').value = selectedItem.unit_measurement;
                            });
                        })
                    .catch(error => console.error('Error fetching items:', error));
                })
            .catch(error => console.error('Error fetching inventory details:', error));
        }

        // Handle form submission for update inventory form
        const updateForm = document.getElementById('updateInventoryForm');
        updateForm.addEventListener('submit', function(event) {
            event.preventDefault();
        
            // Collect form data
            const formData = {
                inventoryId: document.getElementById('updateInventoryId').value,
                labId: document.getElementById('updateLabSelect').value,
                itemId: document.getElementById('updateItemSelect').value,
                unitMeasurement: document.getElementById('updateUnitMeasurement').value,
                batchNumber: document.getElementById('updateBatchNumber').value,
                minimumStock: document.getElementById('updateMinimumStock').value,
                stock: document.getElementById('updateStock').value,
                usedStock: document.getElementById('updateUsedStock').value,
                expDate: document.getElementById('updateExpDate').value
            };
        
            // Post form data to server
            fetch('update_inventory.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData),
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    //reload the page
                    location.reload();
                    // Update the table with the edited inventory item
                    const editedRow = document.querySelector(`#inventoryTableBody tr[data-id="${formData.inventoryId}"]`);
                    if (editedRow) {
                        editedRow.innerHTML = `
                            <td>${formData.inventoryId}</td>
                            <td>${formData.labId}</td>
                            <td>${formData.itemId}</td>
                            <td>${formData.unitMeasurement}</td>
                            <td>${formData.batchNumber}</td>
                            <td>${formData.minimumStock}</td>
                            <td>${formData.stock}</td>
                            <td>${formData.usedStock}</td>
                            <td>${formData.expDate}</td>
                            <td>
                                <button class="action-btn use-btn" onclick="openUseStockModal('${item.lab_name}', '${item.item_name}', '${item.batch_number}', ${item.minimum_stock}, ${item.stock}, ${item.used_stock}, '${item.exp_date}')">
                                    Use
                                </button>
                                <button class="action-btn edit-btn" onclick="openEditModal(${formData.inventoryId})">
                                    <i class="ri-edit-box-line"></i>
                                </button>
                                <button class="action-btn delete-btn" onclick="deleteInventory(${item.inventory_id}, '${item.batch_number}')">
                                    <i class="ri-delete-bin-2-line"></i>
                                </button>
                            </td>
                        `;
                    }
                
                    // Hide the update inventory modal
                    closeModal('updateInventoryModal');
                
                    // Reset the form
                    updateForm.reset();
                } else {
                    console.error('Error updating inventory:', data.message);
                }
                //reload the page
                location.reload();
            })
            .catch(error => console.error('Error:', error));
        });

        // Function to delete inventory item
        let inventoryIdToDelete = null;

        function deleteInventory(inventoryId, batchNumber) {
            inventoryIdToDelete = inventoryId;
            document.getElementById('deleteModalText').textContent = `Are you sure you want to delete this batch: ${batchNumber}?`;
            document.getElementById('deleteConfirmationModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('deleteConfirmationModal').style.display = 'none';
            inventoryIdToDelete = null;
        }

        function confirmDelete() {
            if (inventoryIdToDelete) {
                fetch(`delete_inventory.php?id=${inventoryIdToDelete}`, {
                    method: 'DELETE',
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        // Remove the row from the table
                        const deletedRow = document.querySelector(`#inventoryTableBody tr[data-id="${inventoryIdToDelete}"]`);
                        if (deletedRow) {
                            deletedRow.remove();
                        }
                        closeModal();
                        location.reload();
                    } else {
                        console.error('Error deleting inventory:', data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }

        function openReceiptModal(user, labName, itemName, batchNumber, usedStock) {
            document.getElementById('receiptUser').textContent = user;
            document.getElementById('receiptLab').textContent = labName;
            document.getElementById('receiptItem').textContent = itemName;
            document.getElementById('receiptBatch').textContent = batchNumber;
            document.getElementById('receiptUsedStock').textContent = usedStock;

            // Get the remarks from localStorage and display it
            var remarks = localStorage.getItem('remarks');
            document.getElementById('receiptRemarks').textContent = remarks ? remarks : "Used";

            document.getElementById('receiptModal').style.display = 'flex';
        }

        // Function to proceed with the transaction and store receipt data
        function proceedTransaction(modalId) {
            let user, laboratory, item, totalUsedStock, batchDetails, remarks;
        
            if (modalId === 'receiptModal') {
                // Existing logic for the first type of receipt modal
                user = document.getElementById('receiptUser').textContent;
                laboratory = document.getElementById('receiptLab').textContent;
                item = document.getElementById('receiptItem').textContent;
                const batchNumber = document.getElementById('receiptBatch').textContent;
                const usedStock = document.getElementById('receiptUsedStock').textContent;
                remarks = document.getElementById('receiptRemarks').textContent;
            
                totalUsedStock = usedStock;
                batchDetails = JSON.stringify([{ batch_number: batchNumber, used_stock: usedStock }]);
            
                saveReceiptData(user, laboratory, item, totalUsedStock, batchDetails, remarks);
            
            } else if (modalId === 'ReceiptModal') {
                // Logic for the second type of receipt modal
                user = document.getElementById('ReceiptUser').textContent;
                laboratory = document.getElementById('ReceiptLab').textContent;
                item = document.getElementById('ReceiptItem').textContent;
                totalUsedStock = document.getElementById('receiptTotalUsed').textContent;
                remarks = document.getElementById('ReceiptRemarks').textContent;
            
                const batchRows = document.querySelectorAll('#receiptBatches tr');
                const batchData = [];
            
                batchRows.forEach(row => {
                    const batchNumber = row.cells[0].textContent;
                    const usedStock = row.cells[1].textContent;
                    batchData.push({
                        batch_number: batchNumber,
                        used_stock: usedStock
                    });
                });
            
                batchDetails = JSON.stringify(batchData);
            
                saveReceiptData(user, laboratory, item, totalUsedStock, batchDetails, remarks);
            }
        }

        // Function to save the receipt data via AJAX
        function saveReceiptData(user, laboratory, item, totalUsedStock, batchDetails, remarks) {
            const receiptData = {
                user: user,
                laboratory: laboratory,
                item: item,
                total_used_stock: totalUsedStock,
                batch_details: batchDetails,
                remarks: remarks 
            };
        
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'save_receipt.php', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    console.log('Receipt data saved successfully');
                    location.reload();
                } else {
                    console.error('Failed to save receipt data');
                    console.error(xhr.responseText);
                }
            };
            xhr.send(JSON.stringify(receiptData));
        }

        // Fetch batches from the backend
        function fetchBatches(labId, itemId, callback) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'fetch_batches.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    let response;
                    try {
                        response = JSON.parse(xhr.responseText);
                    } catch (e) {
                        console.error('Failed to parse JSON response:', e);
                        return;
                    }
                
                    if (response.batches && Array.isArray(response.batches)) {
                        callback(response);
                    } else {
                        console.error('Expected an array in response.batches but got:', response.batches);
                    }
                } else {
                    console.error('Request failed with status:', xhr.status);
                }
            };
            xhr.send(`lab=${encodeURIComponent(labId)}&item=${encodeURIComponent(itemId)}`);
        }

        function openUseStockModal(labName, itemName, batchNumber, minStock, totalStock, usedStock, expDate) {
            fetchBatches(labName, itemName, function(response) {
                console.log('Response:', response); // Debugging line       

                const batches = response.batches;       

                // Sort batches by expiration date
                batches.sort((a, b) => new Date(a.exp_date) - new Date(b.exp_date));        

                // Filter out batches with 0 balance and expired
                const validBatches = batches.filter(batch => batch.stock > 0 && new Date(batch.exp_date) >= new Date());        

                // Determine nearest batch that can be used
                let nearestBatch = validBatches.length > 0 ? validBatches[0] : null;        

                // Debugging line
                if (nearestBatch) {
                    console.log('Nearest Valid Batch:', nearestBatch);
                } else {
                    console.error('No valid batch found');
                }       

                const currentDate = new Date();
                const expirationDate = new Date(expDate);       

                let selectedBatch = { labName, itemName, batchNumber, minStock, totalStock, usedStock };        

                if (totalStock <= 0 && expirationDate < currentDate) {
                    openInsufficientExpiredStockModal();
                } else if (totalStock <= 0) {
                    openInsufficientStockModal();
                } else if (expirationDate < currentDate) {
                    openExpiredStockModal();
                } else if (nearestBatch && nearestBatch.batch_number !== batchNumber) {
                    openDecisionModal(selectedBatch, nearestBatch);
                } else {
                    openUseStockModalWithValues(labName, itemName, batchNumber, minStock, totalStock, usedStock);
                    document.getElementById('useStockUse').focus();
                }
            });
        }

        function closeUseStockModal() {
            document.getElementById('useStockModal').style.display = 'none';
        }

        // Close the modal if the user clicks anywhere outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('useStockModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }

        // Function for opening the use stock modal with values
        function openUseStockModalWithValues(labName, itemName, batchNumber, minStock, totalStock, usedStock) {
            document.getElementById('useStockLab').value = labName;
            document.getElementById('useStockItem').value = itemName;
            document.getElementById('useStockBatch').value = batchNumber;
            document.getElementById('useStockMin').value = minStock;
            document.getElementById('useStockTotal').value = totalStock;
            document.getElementById('useStockUsed').value = usedStock;
            document.getElementById('useStockModal').style.display = 'block';
            document.getElementById('useStockUse').focus();
        }

        // Modify the form submission event listener to check stock balance
        document.getElementById('useStockForm').addEventListener('submit', function(event) {
            event.preventDefault(); 
        
            // Get form data
            const labName = document.getElementById('useStockLab').value;
            const itemName = document.getElementById('useStockItem').value;
            const batchNumber = document.getElementById('useStockBatch').value;
            const useStock = parseInt(document.getElementById('useStockUse').value);
            const totalStock = parseInt(document.getElementById('useStockTotal').value);
        
            // Check if the used stock exceeds the total stock
            if (useStock > totalStock) {
                // Fetch batches with similar labName and itemName
                fetchBatches(labName, itemName, function(response) {
                    const batches = response.batches.filter(batch => batch.batch_number !== batchNumber);
                    if (batches.length > 0) {
                        openInsufficientBalanceDecisionModal();
                    } else {
                        openInsufficientBalanceModal();
                    }
                });
            } else {
                // Optionally close the use stock modal
                closeUseStockModal();

                // Open the remarks modal before proceeding with the AJAX request
                openRemarksModal();
            
                // When the save button in the remarks modal is clicked
                document.getElementById('saveRemarksBtn').onclick = function() {
                    var remarks = document.getElementById("remarks").value;
                    localStorage.setItem('remarks', remarks);
                
                    // Proceed with the AJAX request if stock is sufficient
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', 'used_stock.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                
                    // Handle the response
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            const response = JSON.parse(xhr.responseText);
                            const newUsedStock = response.newUsedStock;
                            const newTotalStock = response.newTotalStock;
                            const user = response.currentUser;
                        
                            // Update the UI with the new used stock and total stock values
                            document.getElementById('useStockUsed').value = newUsedStock;
                            document.getElementById('useStockTotal').value = newTotalStock;
                        
                            // Open the receipt modal with the appropriate details
                            openReceiptModal(user, labName, itemName, batchNumber, useStock);
                        
                            // Update the specific row in the inventory table (example)
                            updateInventoryRow(labName, itemName, batchNumber, newUsedStock, newTotalStock);
                        } else {
                            const errorResponse = JSON.parse(xhr.responseText);
                            console.error('Error:', errorResponse.error);
                            openInsufficientBalanceModal();
                        }
                    };
                
                    // Send the request with form data
                    xhr.send(`lab=${encodeURIComponent(labName)}&item=${encodeURIComponent(itemName)}&batch=${encodeURIComponent(batchNumber)}&use_stock=${encodeURIComponent(useStock)}`);

                    // Close the remarks modal
                    closeRemarksModal();
                };
            }
        });

        function updateInventoryRow(labName, itemName, batchNumber, newUsedStock, newTotalStock) {
            const inventoryRows = document.querySelectorAll('.inventory-table tbody tr');
            inventoryRows.forEach(row => {
                const rowLab = row.querySelector('.lab_name')?.textContent.trim();
                const rowItem = row.querySelector('.item_name')?.textContent.trim();
                const rowBatch = row.querySelector('.batch_number')?.textContent.trim();
            
                // Ensure the element is found and contains text
                if (rowLab && rowItem && rowBatch &&
                    rowLab === labName && rowItem === itemName && rowBatch === batchNumber) {
                    
                    const usedStockCell = row.querySelector('.used_stock');
                    const totalStockCell = row.querySelector('.total_stock');
                    
                    if (usedStockCell && totalStockCell) {
                        usedStockCell.textContent = newUsedStock;
                        totalStockCell.textContent = newTotalStock;
                    } else {
                        console.error('Error: Used stock or total stock cell not found.');
                    }
                }
            });
        }

        // Functions for opening and closing modals
        function closeAddInventoryModal() {
            document.getElementById('addInventoryModal').style.display = 'none';
        }

        function closeUpdateInventoryModal() {
            document.getElementById('updateInventoryModal').style.display = 'none';
        }

        function openInsufficientStockModal() {
            document.getElementById('insufficientStockModal').style.display = 'block';
        }

        function closeInsufficientStockModal() {
            document.getElementById('insufficientStockModal').style.display = 'none';
        }

        function openExpiredStockModal() {
            document.getElementById('expiredStockModal').style.display = 'block';
        }

        function closeExpiredStockModal() {
            document.getElementById('expiredStockModal').style.display = 'none';
        }

        function openInsufficientExpiredStockModal() {
            document.getElementById('insufficientExpiredStockModal').style.display = 'block';
        }

        function closeInsufficientExpiredStockModal() {
            document.getElementById('insufficientExpiredStockModal').style.display = 'none';
        }

        function openInsufficientBalanceModal() {
            document.getElementById('insufficientBalanceModal').style.display = 'block';
        }

        function closeInsufficientBalanceModal() {
            document.getElementById('insufficientBalanceModal').style.display = 'none';
        }

        function openInsufficientBalanceDecisionModal() {
            document.getElementById('insufficientBalanceDecisionModal').style.display = 'block';
        }

        function closeInsufficientBalanceDecisionModal() {
            document.getElementById('insufficientBalanceDecisionModal').style.display = 'none';
        }

        function useOtherBatches() {
            closeInsufficientBalanceDecisionModal();
            gatherOtherBatchesStocks();
        }

        // Function to close the remarks modal
        function closeRemarksModal() {
        document.getElementById('remarksModal').style.display = 'none';
        }

        var saveBtn = document.getElementById('saveRemarksBtn');        

        saveBtn.onclick = function() {
            var remarks = document.getElementById("remarks").value;
            localStorage.setItem('remarks', remarks);
            document.getElementById('remarksModal').style.display = "none";
        
            openReceiptModal('User', 'Laboratory', 'Item', 'BatchNumber', 'UsedStock');
            displayReceiptModal('User', 'Laboratory', 'Item', [{ batch_number: 'BatchNumber', used_stock: 'UsedStock' }], 'UsedStock');
        };
        
        function displayReceiptModal(user, labName, itemName, batches, totalUsedStock) {
            document.getElementById('ReceiptUser').textContent = user;
            document.getElementById('ReceiptLab').textContent = labName;
            document.getElementById('ReceiptItem').textContent = itemName;
        
            const receiptBatches = document.getElementById('receiptBatches');
            receiptBatches.innerHTML = '';
        
            batches.forEach(batch => {
                const row = document.createElement('tr');
                const batchCell = document.createElement('td');
                batchCell.innerText = batch.batch_number;
                const usedStockCell = document.createElement('td');
                usedStockCell.innerText = batch.used_stock;
                row.appendChild(batchCell);
                row.appendChild(usedStockCell);
                receiptBatches.appendChild(row);
            });
        
            document.getElementById('receiptTotalUsed').innerText = totalUsedStock;
        
            // Get the remarks from localStorage and display it
            var remarks = localStorage.getItem('remarks');
            document.getElementById('ReceiptRemarks').textContent = remarks ? remarks : "Used";
        
            // Ensure the modal is shown
            const receiptModal = document.getElementById('ReceiptModal');
            receiptModal.style.display = 'flex';
        }

        // Fetch the current user and proceed with the stock handling logic
        function fetchCurrentUserAndProceed(callback) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'used_stock.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    console.log('Raw response:', xhr.responseText); 
                    let response;
                    try {
                        response = JSON.parse(xhr.responseText);
                    } catch (e) {
                        console.error('Failed to parse JSON response:', e);
                        return;
                    }
                
                    if (response.status === 'success') {
                        callback(response.currentUser);
                    } else {
                        console.error('Failed to fetch user information:', response.error);
                    }
                } else {
                    console.error('Request failed with status:', xhr.status);
                }
            };
            xhr.send();
        }

        // Function to open the remarks modal with a callback
        function openRemarksModal(callback) {
            document.getElementById('remarksModal').style.display = 'flex';
            var saveBtn = document.getElementById('saveRemarksBtn');
        
            // Save the remarks and close the modal
            saveBtn.onclick = function() {
                var remarks = document.getElementById("remarks").value;
                localStorage.setItem('remarks', remarks);
                console.log('Remarks saved:', remarks); // Log the saved remarks
                document.getElementById('remarksModal').style.display = "none";
            
                // Execute the callback function after closing the remarks modal
                if (callback) {
                    console.log('Executing callback'); // Log callback execution
                    callback();
                }
            };
        }

        // Updated function to handle using other batches
        function gatherOtherBatchesStocks() {
            const labName = document.getElementById('useStockLab').value;
            const itemName = document.getElementById('useStockItem').value;
            const selectedBatchNumber = document.getElementById('useStockBatch').value;
            const useStock = parseInt(document.getElementById('useStockUse').value);
        
            fetchCurrentUserAndProceed(function(userName) {
                fetchBatches(labName, itemName, function(response) {
                    const batches = response.batches;
                    const selectedBatch = batches.find(batch => batch.batch_number === selectedBatchNumber);
                
                    if (selectedBatch) {
                        const currentTotalStock = parseInt(selectedBatch.stock);
                        const neededStock = useStock - currentTotalStock;
                    
                        if (neededStock <= 0) {
                            // If current batch has enough stock, proceed with the transaction
                            selectedBatch.stock -= useStock;
                            selectedBatch.used_stock += useStock;
                        
                            // Close the use stock modal and open the remarks modal
                            closeUseStockModal();
                            openRemarksModal(function() {
                                // Update the batch stock and open the receipt modal only after remarks are saved
                                updateBatchStock(selectedBatch);
                                displayReceiptModal(userName, labName, itemName, [{
                                    batch_number: selectedBatch.batch_number,
                                    used_stock: useStock
                                }], useStock);
                            });
                        } else {
                            // Calculate total available stock from other batches
                            const otherBatches = batches
                                .filter(batch => batch.batch_number !== selectedBatchNumber && new Date(batch.exp_date) >= new Date())
                                .sort((a, b) => new Date(a.exp_date) - new Date(b.exp_date));
                        
                            const totalAvailableStock = otherBatches.reduce((total, batch) => total + parseInt(batch.stock), 0);
                        
                            if (totalAvailableStock >= neededStock) {
                                // Update selected batch stock
                                selectedBatch.stock = 0;
                                selectedBatch.used_stock += currentTotalStock;
                            
                                let remainingStock = neededStock;
                                const usedBatches = [{
                                    batch_number: selectedBatch.batch_number,
                                    used_stock: currentTotalStock
                                }];
                            
                                // Use other batches to fulfill the remaining stock
                                otherBatches.forEach(batch => {
                                    if (remainingStock <= 0) return;
                                
                                    let usedStockFromBatch = 0;
                                    if (batch.stock >= remainingStock) {
                                        usedStockFromBatch = remainingStock;
                                        batch.stock -= remainingStock;
                                        batch.used_stock += remainingStock;
                                        remainingStock = 0;
                                    } else {
                                        usedStockFromBatch = batch.stock;
                                        remainingStock -= batch.stock;
                                        batch.used_stock += batch.stock;
                                        batch.stock = 0;
                                    }
                                
                                    usedBatches.push({
                                        batch_number: batch.batch_number,
                                        used_stock: usedStockFromBatch
                                    });
                                });
                            
                                if (remainingStock > 0) {
                                    // If after updating other batches, stock is still insufficient
                                    openInsufficientBalanceModal();
                                } else {
                                    // Close the use stock modal and open the remarks modal
                                    closeUseStockModal();
                                    openRemarksModal(function() {
                                        // Update the stock of other batches and open the receipt modal after remarks are saved
                                        otherBatches.forEach(batch => updateBatchStock(batch));
                                        updateBatchStock(selectedBatch);
                                        displayReceiptModal(userName, labName, itemName, usedBatches, useStock);
                                    });
                                }
                            } else {
                                // Not enough stock available in all other batches
                                openInsufficientBalanceModal();
                            }
                        }
                    }
                });
            });
        }

        // Function to update the batch stock in the backend
        function updateBatchStock(batch) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_batch_stock.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status !== 200) {
                    console.error('Failed to update batch stock');
                } else {
                    console.log('Batch stock updated successfully');
                }
            };
            xhr.send(`batch_number=${encodeURIComponent(batch.batch_number)}&stock=${encodeURIComponent(batch.stock)}&used_stock=${encodeURIComponent(batch.used_stock)}`);
        }

        function openDecisionModal(selectedBatch, nearestBatch) {
            document.getElementById('decisionModal').style.display = 'block';
        
            document.getElementById('useNearestBatch').onclick = function() {
                closeDecisionModal();
                if (nearestBatch) {
                    openUseStockModalWithValues(
                        nearestBatch.lab_name,
                        nearestBatch.item_name,
                        nearestBatch.batch_number,
                        nearestBatch.minimum_stock,
                        nearestBatch.stock,
                        nearestBatch.used_stock

                    );
                }
            };
        
            document.getElementById('useSelectedBatch').onclick = function() {
                closeDecisionModal();
                openUseStockModalWithValues(
                    selectedBatch.labName,
                    selectedBatch.itemName,
                    selectedBatch.batchNumber,
                    selectedBatch.minStock,
                    selectedBatch.totalStock,
                    selectedBatch.usedStock
                );
            };
        }

        function closeDecisionModal() {
            document.getElementById('decisionModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('decisionModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>
    <script src="scripts/search.js"></script>
</body>
</html>