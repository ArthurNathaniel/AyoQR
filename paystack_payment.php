<?php
require 'db.php';
session_start();

// Fetch order details
$order_id = $_GET['order_id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

// If order is not found, redirect to checkout page
if (!$order) {
    header("Location: checkout.php");
    exit;
}

$total_amount = $order['total_amount'] * 100; // Amount in kobo

// Paystack public key
$paystackPublicKey = 'pk_test_112a19f8ae988db1be016b0323b0e4fe95783fe8';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paystack Payment</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/checkout.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="checkout_all">
        <div class="checkout_box">
            <h3>Paystack Payment</h3>
            <form action="process_payment.php" method="POST" id="payment-form">
                <!-- Customer Information (pre-filled from order) -->
                <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
                <p><strong>Total Amount: GHS <?php echo number_format($order['total_amount'], 2); ?></strong></p>

                <!-- Paystack Payment -->
                <div class="paystack">
                    <button type="button" id="paystack-button" class="paystack-button">
                        Pay with Paystack (GHS <?php echo number_format($order['total_amount'], 2); ?>)
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('paystack-button').addEventListener('click', function() {
            var handler = PaystackPop.setup({
                key: '<?php echo $paystackPublicKey; ?>',
                email: '<?php echo $order['customer_email']; ?>',
                amount: <?php echo $total_amount; ?>, // Amount in kobo
                currency: "GHS",
                ref: '' + Math.floor((Math.random() * 1000000000) + 1), // Random reference number
                callback: function(response) {
                    alert('Payment successful! Transaction reference: ' + response.reference);
                    // You may also update the order status here or trigger some other actions
                    window.location.href = "order_success.php?order_id=" + <?php echo $order_id; ?>;
                },
                onClose: function() {
                    alert('Transaction was not completed.');
                }
            });
            handler.openIframe();
        });
    </script>

    <script src="https://js.paystack.co/v1/inline.js"></script>
</body>
</html>
