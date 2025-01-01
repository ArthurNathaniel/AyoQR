<?php
require 'db.php'; // Include database connection
session_start();

try {
    // Fetch data for the order items today
    $todayQuery = "
        SELECT oi.item_name AS food_name, COUNT(*) AS total
        FROM order_items oi
        INNER JOIN orders o ON o.id = oi.order_id
        WHERE DATE(o.created_at) = CURDATE()
        GROUP BY oi.item_name";
    $stmt = $conn->prepare($todayQuery);
    $stmt->execute();
    $todayData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $todayLabels = array_column($todayData, 'food_name');
    $todayCounts = array_column($todayData, 'total');

    // Fetch data for the order items this month
    $monthQuery = "
        SELECT oi.item_name AS food_name, COUNT(*) AS total
        FROM order_items oi
        INNER JOIN orders o ON o.id = oi.order_id
        WHERE MONTH(o.created_at) = MONTH(CURDATE()) AND YEAR(o.created_at) = YEAR(CURDATE())
        GROUP BY oi.item_name";
    $stmt = $conn->prepare($monthQuery);
    $stmt->execute();
    $monthData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $monthLabels = array_column($monthData, 'food_name');
    $monthCounts = array_column($monthData, 'total');

    // Fetch total amounts for today, this week, this month, and this year
    $queries = [
        'today' => "SELECT SUM(oi.item_price * oi.quantity) AS total 
                    FROM order_items oi
                    INNER JOIN orders o ON o.id = oi.order_id
                    WHERE DATE(o.created_at) = CURDATE()",
        'week' => "SELECT SUM(oi.item_price * oi.quantity) AS total 
                   FROM order_items oi
                   INNER JOIN orders o ON o.id = oi.order_id
                   WHERE YEARWEEK(o.created_at, 1) = YEARWEEK(CURDATE(), 1)",
        'month' => "SELECT SUM(oi.item_price * oi.quantity) AS total 
                    FROM order_items oi
                    INNER JOIN orders o ON o.id = oi.order_id
                    WHERE MONTH(o.created_at) = MONTH(CURDATE()) AND YEAR(o.created_at) = YEAR(CURDATE())",
        'year' => "SELECT SUM(oi.item_price * oi.quantity) AS total 
                   FROM order_items oi
                   INNER JOIN orders o ON o.id = oi.order_id
                   WHERE YEAR(o.created_at) = YEAR(CURDATE())",

    ];

    $totals = [];
    foreach ($queries as $key => $query) {
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $totals[$key] = $stmt->fetchColumn() ?: 0;
    }
    $allTimeQuery = "
    SELECT SUM(oi.item_price * oi.quantity) AS total
    FROM order_items oi
    INNER JOIN orders o ON o.id = oi.order_id";
    $stmt = $conn->prepare($allTimeQuery);
    $stmt->execute();
    $totals['all_time'] = $stmt->fetchColumn() ?: 0;  // Add this line to fetch the all-time total

    // Fetch data for the order items this year
    $yearQuery = "
        SELECT oi.item_name AS food_name, COUNT(*) AS total
        FROM order_items oi
        INNER JOIN orders o ON o.id = oi.order_id
        WHERE YEAR(o.created_at) = YEAR(CURDATE())
        GROUP BY oi.item_name";
    $stmt = $conn->prepare($yearQuery);
    $stmt->execute();
    $yearData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $yearLabels = array_column($yearData, 'food_name');
    $yearCounts = array_column($yearData, 'total');
} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Dashboard</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .cards {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            flex: 1;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        canvas {
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <?php include 'sidebar.php'; ?>
    <div class="cards">
        <div class="card">
            <h3>Today's Total</h3>
            <p>GHS <?php echo number_format($totals['today'], 2); ?></p>
        </div>
        <div class="card">
            <h3>This Week's Total</h3>
            <p>GHS <?php echo number_format($totals['week'], 2); ?></p>
        </div>
        <div class="card">
            <h3>This Month's Total</h3>
            <p>GHS <?php echo number_format($totals['month'], 2); ?></p>
        </div>
        <div class="card">
            <h3>This Year's Total</h3>
            <p>GHS <?php echo number_format($totals['year'], 2); ?></p>
        </div>
        <div class="card">
            <h3>All-Time Total</h3>
            <p>GHS <?php echo number_format($totals['all_time'], 2); ?></p>
        </div>
    </div>

    <h1>Orders Dashboard</h1>

    <h2>Orders Today</h2>
    <canvas id="ordersTodayChart"></canvas>

    <h2>Orders This Month</h2>
    <canvas id="ordersMonthChart"></canvas>

    <h2>Orders This Year</h2>
    <canvas id="ordersYearChart"></canvas>

    <script>
        const colors = [
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(25, 0, 86)',
            'rgb(75, 192, 192)',
            'rgb(153, 102, 255)',
            'rgb(255, 159, 64)',
            'rgb(255, 19, 54)',
            'rgb(255, 99, 132)',
            'rgb(54, 162, 25)',
            'rgb(186, 85, 211)',
            'rgb(255, 69, 0)',
            'rgb(32, 178, 170)',
            'rgb(255, 165, 0)',
            'rgb(0, 255, 255)',
            'rgb(106, 90, 205)',
            'rgb(138, 43, 226)',
            'rgb(255, 105, 180)',
            'rgb(0, 0, 255)',
            'rgb(255, 20, 147)',
            'rgb(0, 128, 128)'
        ];

        function createChart(ctx, type, labels, data) {
            new Chart(ctx, {
                type,
                data: {
                    labels,
                    datasets: [{
                        data,
                        backgroundColor: colors,
                        borderColor: colors.map(c => c.replace('0.2', '1')),
                        borderWidth: 1
                    }]
                }
            });
        }

        createChart(document.getElementById('ordersTodayChart').getContext('2d'), 'doughnut', <?php echo json_encode($todayLabels); ?>, <?php echo json_encode($todayCounts); ?>);
        createChart(document.getElementById('ordersMonthChart').getContext('2d'), 'pie', <?php echo json_encode($monthLabels); ?>, <?php echo json_encode($monthCounts); ?>);
        createChart(document.getElementById('ordersYearChart').getContext('2d'), 'bar', <?php echo json_encode($yearLabels); ?>, <?php echo json_encode($yearCounts); ?>);
    </script>
</body>

</html>