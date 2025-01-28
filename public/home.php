<?php 
include_once 'side_bar.php'; 
require_once "../config/database.php";
require_once "../includes/functions.php";
include_once 'auto_check.php';

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Fetch expiration dates and item names
$sql = "SELECT i.item_name, inv.exp_date 
        FROM inventory inv
        JOIN items i ON inv.item_id = i.item_id 
        WHERE inv.exp_date IS NOT NULL";
$result = $link->query($sql);

$events = array();
$currentDate = new DateTime();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $expDate = new DateTime($row["exp_date"]);
        $interval = $currentDate->diff($expDate);
        $daysToExpiration = $interval->days;
        $backgroundColor = '';

        if ($expDate < $currentDate || $daysToExpiration <= 3) {
            $backgroundColor = 'rgba(255, 0, 0, 0.2)'; 
        } elseif ($daysToExpiration <= 7) {
            $backgroundColor = 'rgba(255, 165, 0, 0.2)'; 
        } else {
            $backgroundColor = 'rgba(0, 128, 0, 0.2)'; 
        }

        $events[] = array(
            'title' => $row["item_name"],
            'start' => $row["exp_date"],
            'backgroundColor' => $backgroundColor,
            'borderColor' => $backgroundColor,
            'textColor' => 'black' 
        );
    }
}

// Count total number of users
$sqlUsers = "SELECT COUNT(*) AS total_users FROM users";
$resultUsers = $link->query($sqlUsers);
$totalUsers = ($resultUsers->num_rows > 0) ? $resultUsers->fetch_assoc()['total_users'] : 0;

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

// Fetch the count of items per category
$countSql = "SELECT category, COUNT(*) as count FROM items GROUP BY category";
$countResult = mysqli_query($link, $countSql);
$categoryCounts = [];
if ($countResult) {
    while ($row = mysqli_fetch_assoc($countResult)) {
        $categoryCounts[$row['category']] = $row['count'];
    }
}

$categoryCountsJson = json_encode(array_values($categoryCounts));

$link->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="styles/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.4.0/remixicon.css" crossorigin="">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="calendar-container">
         <div id="calendar"></div>
    </div>

    <div class="horizontal-container">
        <div class="lab-container">
            <div class="lab-content">
                <a href="lab.php"><img src="image/lab.png" alt="lab-logo"></a>
                <h3>Laboratory</h3>
                <p>Manage and monitor the laboratory operations and data efficiently</p>
                <button>
                    <a href="lab.php"><i class="ri-arrow-right-s-line"></i></a>
                </button>
            </div>
        </div>
        <div class="lab-container">
            <div class="lab-content">
                <a href="item.php"><img src="image/items.png" alt="lab-logo"></a>
                <h3>Items</h3>
                <p>Keep track of all laboratory items and equipment inside the inventory</p>
                <button>
                    <a href="item.php"><i class="ri-arrow-right-s-line"></i></a>
                </button>
            </div>
        </div>
        <div class="lab-container">
            <div class="lab-content">
                <a href="inventory.php"><img src="image/inventory.png" alt="lab-logo"></a>
                <h3>Inventory</h3>
                <p>Organize and control stock levels of lab supplies and reagents</p>
                <button>
                    <a href="inventory.php"><i class="ri-arrow-right-s-line"></i></a>
                </button>
            </div>
        </div>
        <div class="lab-container">
            <div class="lab-content">
                <a href="report.php"><img src="image/reports.png" alt="lab-logo"></a>
                <h3>Reports</h3>
                <p>Generate and review detailed laboratory performance and compliance reports</p>
                <button>
                    <a href="report.php"><i class="ri-arrow-right-s-line"></i></a>
                </button>
            </div>
        </div>
    </div>
    
    <div class="graph-container">
        <div class="chart-container">
            <canvas id="statsChart"></canvas>
        </div>

        <div class="pie-chart-container">
            <canvas id="categoryChart"></canvas>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var events = <?php echo json_encode($events); ?>;
            
            if (calendarEl) {
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    events: events
                });
                calendar.render();
            } else {
                console.error('Calendar element not found');
            }

            var ctxBar = document.getElementById('statsChart').getContext('2d');
            var statsChart = new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: ['Users', 'Labs', 'Items', 'Categories'],
                    datasets: [{
                        label: 'Total Count',
                        data: [
                            <?php echo $totalUsers; ?>,
                            <?php echo $totalLaboratories; ?>,
                            <?php echo $totalItems; ?>,
                            <?php echo $totalCategories; ?>
                        ], 
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)'
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
                            text: 'Laboratory Statistics',
                            color: '#000',  
                            font: {
                                size: 16
                            }
                        }
                    }
                }
            });

            var ctxPie = document.getElementById('categoryChart').getContext('2d');
            var categoryChart = new Chart(ctxPie, {
                type: 'pie',
                data: {
                    labels: <?php echo json_encode(array_keys($categoryCounts)); ?>,
                    datasets: [{
                        label: 'Items each Category',
                        data: <?php echo $categoryCountsJson; ?>,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.6)',
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(255, 206, 86, 0.6)',
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(153, 102, 255, 0.6)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        title: {
                            display: true,
                            text: 'Items each category',
                            color: '#000',  
                            font: {
                                size: 16
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
