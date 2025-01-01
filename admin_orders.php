<?php
require 'db.php'; // Include database connection
session_start();

try {
    $filterDate = isset($_GET['filter_date']) ? $_GET['filter_date'] : null;

    if ($filterDate) {
        // Query to fetch orders for a specific date
        $query = "SELECT * FROM orders WHERE DATE(created_at) = :filterDate ORDER BY id DESC";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':filterDate', $filterDate);
    } else {
        // Default query to fetch all orders
        $query = "SELECT * FROM orders ORDER BY id DESC";
        $stmt = $conn->prepare($query);
    }
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch order items as before
    $items_query = $conn->query("SELECT * FROM order_items");
    $items = $items_query->fetchAll(PDO::FETCH_ASSOC);

    // Group items by order_id
    $order_items = [];
    foreach ($items as $item) {
        $order_items[$item['order_id']][] = $item;
    }

    // Calculate total amount for displayed orders
    $totalAmount = 0;
    foreach ($orders as $order) {
        $totalAmount += $order['total'];
    }

    $currentDay = date('l, F j, Y');
} catch (PDOException $e) {
    die("Error fetching orders: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - All Orders</title>
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
            margin-bottom: 20px;
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

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            overflow: auto;
            padding-top: 50px;
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border-radius: 10px;
            width: 60%;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }

        .modal h2 {
            color: #6200ea;
            font-size: 24px;
        }

        .modal ul {
            list-style-type: none;
            padding: 0;
        }

        .modal ul li {
            margin: 10px 0;
        }

        .filter-form {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
        }

        .filter-form input[type="date"] {
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

        .total-amount {
            text-align: center;
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
    </style>
</head>

<body>
<?php include 'sidebar.php'; ?>

    <h1>Admin - All Orders</h1>
    <!-- Date Filter Form -->
    <form method="GET" action="" class="filter-form">
        <label for="filter_date">Filter by Date:</label>
        <input type="date" id="filter_date" name="filter_date" value="<?php echo htmlspecialchars($filterDate ?? ''); ?>">
        <button type="submit">Filter</button>
        <a href="admin_orders.php" style="margin-left: 10px;">Clear Filter</a>
    </form>

    <!-- Orders Table -->
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                    <td><?php echo htmlspecialchars($order['name']); ?></td>
                    <td><?php echo htmlspecialchars($order['phone']); ?></td>
                    <td>
                        <button class="actions-btn" onclick="showModal(<?php echo $order['id']; ?>)">View Details</button>
                    </td>
                </tr>
                <div id="modal-<?php echo $order['id']; ?>" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeModal(<?php echo $order['id']; ?>)">&times;</span>
                        <h2>Order Details (ID: <?php echo htmlspecialchars($order['id']); ?>)</h2>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                        <p><strong>Pickup/Delivery:</strong> <?php echo htmlspecialchars($order['pickup_delivery']); ?></p>
                        <p><strong>Delivery Address:</strong> <?php echo htmlspecialchars($order['address'] ?? 'N/A'); ?></p>
                        <p><strong>Total Amount:</strong> GHS <?php echo number_format($order['total'], 2); ?></p>
                        <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['created_at'] ?? 'N/A'); ?></p>
                        <h3>Items:</h3>
                        <ul>
                            <?php if (isset($order_items[$order['id']])): ?>
                                <?php foreach ($order_items[$order['id']] as $item): ?>
                                    <li>
                                        <strong>Item Name:</strong> <?php echo htmlspecialchars($item['item_name']); ?><br>
                                        <strong>Price (GHS):</strong> <?php echo number_format($item['item_price'], 2); ?><br>
                                        <strong>Quantity:</strong> <?php echo htmlspecialchars($item['quantity']); ?>
                                    </li>
                                    <hr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>No items found for this order.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Total Amount -->
    <div class="total-amount">
        <h3>Total Orders Amount: GHS <?php echo number_format($totalAmount, 2); ?></h3>
    </div>

    <script>
        // Modal functions to view and close details
        function showModal(id) {
            document.getElementById('modal-' + id).style.display = 'block';
        }

        function closeModal(id) {
            document.getElementById('modal-' + id).style.display = 'none';
        }
    </script>
</body>

</html>
