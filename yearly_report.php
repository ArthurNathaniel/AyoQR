<?php
require 'db.php'; // Include database connection
session_start();

// Default year is the current year
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Initialize the total amount for each month
$monthlyTotals = [
    'January' => 0, 'February' => 0, 'March' => 0, 'April' => 0, 'May' => 0, 'June' => 0,
    'July' => 0, 'August' => 0, 'September' => 0, 'October' => 0, 'November' => 0, 'December' => 0
];

try {
    // Query to fetch total orders amount for each month in the selected year
    $query = "
        SELECT MONTH(created_at) AS month, SUM(total) AS total_amount 
        FROM orders 
        WHERE YEAR(created_at) = :year 
        GROUP BY MONTH(created_at) 
        ORDER BY month
    ";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':year', $year);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fill monthly totals array with the results
    foreach ($results as $row) {
        $month = (int) $row['month'];
        $monthName = date('F', mktime(0, 0, 0, $month, 10)); // Convert month number to month name
        $monthlyTotals[$monthName] = $row['total_amount'];
    }
} catch (PDOException $e) {
    die("Error fetching monthly totals: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yearly Orders Report</title>
    <?php include 'cdn.php'; ?>

    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/admin_orders.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #6200ea;
            color: #fff;
            font-size: 16px;
        }

        td {
            font-size: 14px;
        }

        .actions-btn {
            padding: 8px 12px;
            background-color: #6200ea;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .actions-btn:hover {
            background-color: #4500a3;
        }

        .filter-form {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
        }

        .filter-form select {
            padding: 5px;
            margin-right: 10px;
        }

        .filter-form button {
            padding: 6px 12px;
            background-color: #6200ea;
            color: white;
            border: none;
            border-radius: 4px;
        }

        .filter-form button:hover {
            background-color: #4500a3;
        }
    </style>
</head>

<body>
<?php include 'sidebar.php'; ?>

    <h1>Yearly Orders Report</h1>

    <!-- Year Selection Form -->
    <form method="GET" action="" class="filter-form">
        <label for="year">Select Year:</label>
        <select name="year" id="year">
        <?php for ($i = 2020; $i <= date('Y') + 1; $i++): ?>
    <option value="<?php echo $i; ?>" <?php echo ($i == $year) ? 'selected' : ''; ?>><?php echo $i; ?></option>
<?php endfor; ?>

        </select>
        <button type="submit">View Report</button>
    </form>

    <!-- Monthly Totals Table -->
    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th>Total Amount (GHS)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($monthlyTotals as $month => $totalAmount): ?>
                <tr>
                    <td><?php echo $month; ?></td>
                    <td>GHS <?php echo number_format($totalAmount, 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>
