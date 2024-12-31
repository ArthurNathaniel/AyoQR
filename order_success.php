<?php
require 'db.php'; // Include database connection
session_start();

// Check if order_id is provided in the query string
if (!isset($_GET['order_id'])) {
    die("Invalid request. No order ID provided.");
}

// Retrieve the order_id from the query string
$order_id = intval($_GET['order_id']);

try {
    // Fetch order details
    $order_query = $conn->prepare("SELECT * FROM orders WHERE id = :order_id");
    $order_query->execute([':order_id' => $order_id]);
    $order = $order_query->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        die("Order not found.");
    }

    // Fetch order items
    $items_query = $conn->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
    $items_query->execute([':order_id' => $order_id]);
    $items = $items_query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching order details: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success</title>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/order_success.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="order-success">
        <h2>Thank You for Your Order!</h2>
        <p>Your order has been placed successfully. Below are the details:</p>

        <!-- Order Details -->
        <div class="order-details">
            <h3>Order Information</h3>
            <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['id'], ENT_QUOTES); ?></p>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($order['name'], ENT_QUOTES); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone'], ENT_QUOTES); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email'], ENT_QUOTES); ?></p>
            <p><strong>Pickup/Delivery:</strong> <?php echo htmlspecialchars($order['pickup_delivery'], ENT_QUOTES); ?></p>
            <?php if ($order['pickup_delivery'] === 'delivery'): ?>
                <p><strong>Delivery Address:</strong> <?php echo htmlspecialchars($order['address'], ENT_QUOTES); ?></p>
            <?php endif; ?>
            <p><strong>Total Amount:</strong> GHS <?php echo number_format($order['total'], 2); ?></p>
        </div>

        <!-- Order Items -->
        <div class="order-items">
            <h3>Items Ordered</h3>
            <ul>
                <?php foreach ($items as $item): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($item['item_name'], ENT_QUOTES); ?></strong>
                        - GHS <?php echo number_format($item['item_price'], 2); ?>
                        x <?php echo $item['quantity']; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <p>If you have any questions or concerns, please contact our support team.</p>
        <a href="index.php" class="btn">Return to Homepage</a>
    </div>
</body>
</html>
