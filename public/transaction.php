<?php
include_once "side_bar.php";
require_once "../config/database.php";
require_once "../includes/functions.php";
include_once 'auto_check.php';

// Attempt connection to MySQL DB
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check for connection errors
if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Fetch transaction data
$sql = "SELECT * FROM receipts";
$transactionResult = mysqli_query($link, $sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.4.0/remixicon.css" crossorigin="">
    <link rel="stylesheet" href="styles/transaction.css">
    <title>Transaction</title>
</head>
<body>
    <div class="button-container">
        <div class="item-button">
            <a href="item_history.php"><button>Items History</button></a>
        </div>
        <div class="inventory-button">
            <a href="inventory_history.php"><button>Inventory History</button></a>
        </div>
        <div class="receipt-button">
            <a href="transaction.php"><button>Receipt History</button></a>
        </div>
    </div>

    <div class="main-container">
        <div class="transaction-container">
            <div class="transaction-header">
                <h1>Receipt History</h1>
                <button id="exportButton" class="export_button">Export to Excel</button>
            </div>

            <div class="table-container">
                <table class="table table-striped transaction-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Laboratory</th>
                            <th>Item</th>
                            <th>Batch Details</th>
                            <th>Total Used Stock</th>
                            <th>Remarks</th>
                            <th>Transaction Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($transactionResult && mysqli_num_rows($transactionResult) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($transactionResult)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['user']); ?></td>
                                    <td><?php echo htmlspecialchars($row['laboratory']); ?></td>
                                    <td><?php echo htmlspecialchars($row['item']); ?></td>
                                    <td>
                                        <?php
                                            $batchDetails = json_decode($row['batch_details'], true);
                                            foreach ($batchDetails as $batch) {
                                                echo 'Batch: ' . htmlspecialchars($batch['batch_number']) . ', Used Stock: ' . htmlspecialchars($batch['used_stock']) . '<br>';
                                            }
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['total_used_stock']); ?></td>
                                    <td><?php echo htmlspecialchars($row['remarks']); ?></td>
                                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">No history found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="scripts/search.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
    <script>
        document.getElementById('exportButton').addEventListener('click', function () {
            var wb = XLSX.utils.book_new();
            var ws = XLSX.utils.table_to_sheet(document.querySelector('.transaction-table'));

            // Adjust column widths
            var wscols = [];
            var range = XLSX.utils.decode_range(ws['!ref']); // Get the range of the table
            for (var C = range.s.c; C <= range.e.c; ++C) {
                var maxWidth = 10; // Default width
                for (var R = range.s.r; R <= range.e.r; ++R) {
                    var cell = ws[XLSX.utils.encode_cell({ r: R, c: C })];
                    if (cell && cell.v) {
                        maxWidth = Math.max(maxWidth, (cell.v.toString().length + 2)); // Add extra space for padding
                    }
                }
                wscols.push({ wpx: maxWidth * 8 }); // Multiply by 8 for pixel width
            }
            ws['!cols'] = wscols; // Apply column widths

            XLSX.utils.book_append_sheet(wb, ws, 'Receipts');

            var wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });

            var blob = new Blob([wbout], { type: 'application/octet-stream' });
            var url = window.URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'receipt_history.xlsx';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        });
    </script>
</body>
</html>

<?php
// Close connection
mysqli_close($link);
?>
