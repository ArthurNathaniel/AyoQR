<?php
require 'db.php'; // Include database connection
session_start();

try {
    // Fetch all orders
    $orders_query = $conn->query("SELECT * FROM orders ORDER BY id DESC");
    $orders = $orders_query->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all order items grouped by order ID
    $items_query = $conn->query("SELECT * FROM order_items");
    $items = $items_query->fetchAll(PDO::FETCH_ASSOC);

    // Group items by order_id for easy access
    $order_items = [];
    foreach ($items as $item) {
        $order_items[$item['order_id']][] = $item;
    }
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
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/admin_orders.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .order-row {
            background-color: #f9f9f9;
        }
        .items-table {
            margin-top: 10px;
            margin-left: 20px;
            width: 90%;
        }
    </style>
</head>
<body>
    <h1>Admin - All Orders</h1>
    <p>Below is a list of all orders and their associated items:</p>

    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Pickup/Delivery</th>
                <th>Delivery Address</th>
                <th>Total Amount (GHS)</th>
                <th>Order Date</th>
                <th>Items</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr class="order-row">
                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                    <td><?php echo htmlspecialchars($order['name']); ?></td>
                    <td><?php echo htmlspecialchars($order['phone']); ?></td>
                    <td><?php echo htmlspecialchars($order['email']); ?></td>
                    <td><?php echo htmlspecialchars($order['pickup_delivery']); ?></td>
                    <td><?php echo htmlspecialchars($order['address'] ?? 'N/A'); ?></td>
                    <td><?php echo number_format($order['total'], 2); ?></td>
                    <td><?php echo htmlspecialchars($order['created_at'] ?? 'N/A'); ?></td>
                    <td>
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th>Item Name</th>
                                    <th>Price (GHS)</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($order_items[$order['id']])): ?>
                                    <?php foreach ($order_items[$order['id']] as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                                            <td><?php echo number_format($item['item_price'], 2); ?></td>
                                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3">No items found for this order.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
