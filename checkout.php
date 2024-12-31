<?php
// checkout.php
require 'db.php';
session_start();

// Sample cart items for demonstration (only for testing, replace with dynamic cart data in production)
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [
        ['name' => 'Item 1', 'price' => 50, 'quantity' => 2],
        ['name' => 'Item 2', 'price' => 30, 'quantity' => 1],
        ['name' => 'Item 3', 'price' => 20, 'quantity' => 3],
    ];
}

// Calculate total price from the cart
$total = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/checkout.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="checkout_all">
        <div class="checkout_box">
            <h3>Checkout</h3>
            <form id="checkout-form">
                <!-- Customer Information -->
                <div class="customer-info">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required>

                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" required>

                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>

                    <label for="pickup_delivery">Pick up / Delivery</label>
                    <select name="pickup_delivery" id="pickup_delivery" required onchange="toggleAddressInput()">
                        <option value="pickup">Pickup</option>
                        <option value="delivery">Delivery</option>
                    </select>

                    <!-- Address input shows when delivery is selected -->
                    <div id="address-input" style="display:none;">
                        <label for="address">Delivery Address</label>
                        <input type="text" id="address" name="address" placeholder="Enter delivery address">
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="cart-summary">
                    <h4>Order Summary</h4>
                    <?php if (!empty($_SESSION['cart'])): ?>
                        <ul>
                            <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                                <li>
                                    <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                    - GHS <?php echo number_format($item['price'], 2); ?>
                                    x <?php echo $item['quantity']; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>Your cart is empty.</p>
                    <?php endif; ?>
                    <hr>
                    <p><strong>Total: GHS <?php echo number_format($total, 2); ?></strong></p>
                </div>

                <!-- Paystack Payment -->
                <div class="paystack">
                    <button type="button" id="paystack-button" class="paystack-button">
                        Pay with Paystack (GHS <?php echo number_format($total, 2); ?>)
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Toggle the address input field
        function toggleAddressInput() {
            const pickupDelivery = document.getElementById('pickup_delivery').value;
            const addressInput = document.getElementById('address-input');
            addressInput.style.display = (pickupDelivery === 'delivery') ? 'block' : 'none';
        }

        // Paystack integration
        document.getElementById('paystack-button').addEventListener('click', function() {
            const totalAmount = <?php echo $total; ?> * 100; // Convert GHS to Kobo
            const handler = PaystackPop.setup({
                key: 'pk_test_112a19f8ae988db1be016b0323b0e4fe95783fe8', // Replace with your Paystack test key
                email: document.getElementById('email').value,
                amount: totalAmount,
                currency: "GHS",
                ref: '' + Math.floor((Math.random() * 1000000000) + 1),
                callback: function(response) {
                    alert('Payment successful! Reference: ' + response.reference);
                    submitCheckoutForm(); // Call to submit the form after payment
                },
                onClose: function() {
                    alert('Transaction not completed.');
                }
            });
            handler.openIframe();
        });

        // Submit the checkout form via AJAX
        function submitCheckoutForm() {
            const formData = new FormData(document.getElementById('checkout-form'));

            fetch('process_checkout.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Order placed successfully!');
                    window.location.href = 'order_success.php?order_id=' + data.order_id;
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to place order.');
            });
        }
    </script>

    <script src="https://js.paystack.co/v1/inline.js"></script>
</body>
</html>
