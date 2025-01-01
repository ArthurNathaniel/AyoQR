<?php
// checkout.php
require 'db.php';
session_start();
// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
// Calculate cart count
$cartCount = array_sum(array_column($_SESSION['cart'], 'quantity'));

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
    <?php include 'cdn.php'; ?>
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
                    <div class="forms">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="forms">
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" required>
                    </div>

                    <div class="forms">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="forms">
                        <label for="pickup_delivery">Pick up / Delivery</label>
                        <select name="pickup_delivery" id="pickup_delivery" required onchange="toggleAddressInput()">
                            <option value="" hidden>Select delivery mode</option>
                            <option value="pickup">Pickup</option>
                            <option value="delivery">Delivery</option>
                        </select>
                    </div>

                    <div class="forms">
                        <!-- Address input shows when delivery is selected -->
                        <div id="address-input" style="display:none;">
                            <label for="address">Delivery Address</label>
                            <input type="text" id="address" name="address" placeholder="Enter delivery address">
                        </div>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="cart-summary">
                 <div class="forms">
                 <h3>Order Summary</h3>
                 </div>
                    <?php if (!empty($_SESSION['cart'])): ?>
                        <ul>
                            <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                                <div class="forms">
                                    <li>
                                        <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                        - GHS <?php echo number_format($item['price'], 2); ?>
                                        x <?php echo $item['quantity']; ?>
                                    </li>
                                </div>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>Your cart is empty.</p>
                    <?php endif; ?>
                    <br>
                    <hr>

                    <div class="forms">
                        <p><strong>Total: GHS <?php echo number_format($total, 2); ?></strong></p>
                    </div>
                </div>

                <!-- Paystack Payment -->
                <div class="paystack forms">
                    <button type="button" id="paystack-button" class="paystack-button">
                        Proceed to make payment </button>
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