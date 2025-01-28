<?php
include_once "side_bar.php";
require_once "../config/database.php";
include_once 'auto_check.php';

// Attempt connection to MySQL DB
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check for connection errors
if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Default query
$reportSql = "SELECT i.inventory_id, l.lab_name, it.item_name, i.batch_number, i.unit_measurement, i.exp_date, i.stock, i.minimum_stock 
              FROM inventory i
              JOIN laboratories l ON i.lab_id = l.lab_id
              JOIN items it ON i.item_id = it.item_id
              WHERE 1=1"; // 1=1 is a placeholder to easily append conditions

// Apply filters based on the button clicked
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
if ($filter == 'minimum_stock') {
    $reportSql .= " AND i.stock <= i.minimum_stock";
} elseif ($filter == 'no_stock') {
    $reportSql .= " AND i.stock = 0";
} elseif ($filter == 'expired') {
    $reportSql .= " AND i.exp_date <= CURDATE()";
}

// Execute the query
$reportResult = mysqli_query($link, $reportSql);

// Count total number of laboratories
$sqlLaboratories = "SELECT COUNT(*) AS total_labs FROM laboratories";
$resultLaboratories = $link->query($sqlLaboratories);
$totalLaboratories = ($resultLaboratories->num_rows > 0) ? $resultLaboratories->fetch_assoc()['total_labs'] : 0;

// Count total number of items (optional, if not already counted)
$sqlItems = "SELECT COUNT(*) AS total_items FROM items";
$resultItems = $link->query($sqlItems);
$totalItems = ($resultItems->num_rows > 0) ? $resultItems->fetch_assoc()['total_items'] : 0;

// Total number of categories (assuming fixed 5 categories)
$totalCategories = 5;

// Count items in each category
$sqlMinStock = "SELECT COUNT(*) AS min_stock_count FROM inventory WHERE stock <= minimum_stock";
$resultMinStock = $link->query($sqlMinStock);
$minStockCount = ($resultMinStock->num_rows > 0) ? $resultMinStock->fetch_assoc()['min_stock_count'] : 0;

$sqlNoStock = "SELECT COUNT(*) AS no_stock_count FROM inventory WHERE stock = 0";
$resultNoStock = $link->query($sqlNoStock);
$noStockCount = ($resultNoStock->num_rows > 0) ? $resultNoStock->fetch_assoc()['no_stock_count'] : 0;

$sqlExpired = "SELECT COUNT(*) AS expired_count FROM inventory WHERE exp_date <= CURDATE()";
$resultExpired = $link->query($sqlExpired);
$expiredCount = ($resultExpired->num_rows > 0) ? $resultExpired->fetch_assoc()['expired_count'] : 0;

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.4.0/remixicon.css" crossorigin="">
    <link rel="stylesheet" href="styles/report.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Report</title>
</head>
<body>
    <div class="main-container">
        <div class="report-container">
            <div class="report-header">
                <h1>Reports</h1>
                <div class="filter-buttons">
                    <button class="r-button" onclick="filterReports('minimum_stock')">Minimum Stock</button>
                    <button class="r-button" onclick="filterReports('no_stock')">No Stock</button>
                    <button class="r-button" onclick="filterReports('expired')">Expired</button>
                    <button class="r-button" id="downloadButton">Export Excel</button>
                </div>
            </div>

            <div class="table-container">
                <table class="table table-striped report-table">
                    <thead>
                        <tr>
                            <th>Inventory ID</th>
                            <th>Laboratory</th>
                            <th>Item</th>
                            <th>Batch Number</th>
                            <th>Unit of Measurement</th>
                            <th>Minimum Stock</th>
                            <th>Stock</th>
                            <th>Expiration Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($reportResult && mysqli_num_rows($reportResult) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($reportResult)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['inventory_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['lab_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['batch_number']); ?></td>
                                    <td><?php echo htmlspecialchars($row['unit_measurement']); ?></td>
                                    <td><?php echo htmlspecialchars($row['minimum_stock']); ?></td>
                                    <td><?php echo htmlspecialchars($row['stock']); ?></td>
                                    <td><?php echo htmlspecialchars($row['exp_date']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8">No inventory items match the selected filter criteria</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="graph-container">
            <div class="chart-container">
                <canvas id="statsChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        function filterReports(filter) {
            window.location.href = `report.php?filter=${filter}`;
        }

        document.addEventListener('DOMContentLoaded', function () { 
            var ctxBar = document.getElementById('statsChart').getContext('2d');
            var statsChart = new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: ['Minimum Stock', 'No Stock', 'Expired'],
                    datasets: [{
                        label: 'Total Count',
                        data: [
                            <?php echo $minStockCount; ?>,
                            <?php echo $noStockCount; ?>,
                            <?php echo $expiredCount; ?>
                        ], 
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Inventory Statistics',
                            color: '#000',  
                            font: {
                                size: 16
                            }
                        }
                    }
                }
            });
        });

        document.getElementById('downloadButton').addEventListener('click', function () {
            var wb = XLSX.utils.book_new();
            var ws = XLSX.utils.table_to_sheet(document.querySelector('.report-table'));
                
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
        
            XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
        
            var wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'array' });
        
            var blob = new Blob([wbout], { type: 'application/octet-stream' });
            var url = window.URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'inventory_report.xlsx';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        });
    </script>
    <script src="scripts/search.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
</body>
</html>
