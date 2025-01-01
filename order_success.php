<?php
require 'db.php'; // Include database connection
session_start();
// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
// Calculate cart count
$cartCount = array_sum(array_column($_SESSION['cart'], 'quantity'));
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
    <title>Order Receipt</title>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/receipt.css">
</head>
<body>
    <div class="receipt">
        <div class="logo"></div>
        <!-- <div class="receipt-header">
            <h2>Order Receipt</h2>
            <p><strong>Thank you for your order!</strong></p>
        </div> -->

        <!-- Order Details -->
        <div class="receipt-section">
            <div class="title">
            <h3>Order Information</h3>
            </div>
           
            <p><strong>Name:</strong> <?php echo htmlspecialchars($order['name'], ENT_QUOTES); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone'], ENT_QUOTES); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email'], ENT_QUOTES); ?></p>
            <p><strong>Pickup/Delivery:</strong> <?php echo htmlspecialchars($order['pickup_delivery'], ENT_QUOTES); ?></p>
            <?php if ($order['pickup_delivery'] === 'delivery'): ?>
                <p><strong>Delivery Address:</strong> <?php echo htmlspecialchars($order['address'], ENT_QUOTES); ?></p>
            <?php endif; ?>
        </div>

        <!-- Order Items Table -->
        <div class="receipt-section">
           
            <table class="receipt-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['item_name'], ENT_QUOTES); ?></td>
                            <td>GH₵ <?php echo number_format($item['item_price'], 2); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>GH₵ <?php echo number_format($item['item_price'] * $item['quantity'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
       <div class="total">
       Total: <strong> GH₵ <?php echo number_format($order['total'], 2); ?></strong>
       </div>
        <div class="order_id">
        <h1>
        <strong>Order ID:</strong> <?php echo htmlspecialchars($order['id'], ENT_QUOTES); ?>
        </h1>
        </div>
        </div>
    </div>
</body>
</html>

