<?php
require 'db.php';
session_start();

// Assuming that Paystack provides the transaction reference
if (isset($_POST['reference'])) {
    $reference = $_POST['reference'];

    // Verify the payment with Paystack API (this is just an example; ensure proper security)
    $url = "https://api.paystack.co/transaction/verify/" . $reference;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer sk_test_97c287a5af8e3865efb3630634fe7e3bcf2dd523" // Replace with your secret key
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if ($data['status'] === 'success') {
        // Payment is successful, update order status to 'Completed'
        $order_id = $data['data']['order_id'];
        $stmt = $pdo->prepare("UPDATE orders SET order_status = 'Completed' WHERE order_id = ?");
        $stmt->execute([$order_id]);

        // Redirect to success page
        header("Location: order_success.php?order_id=" . $order_id);
        exit;
    } else {
        // Payment failed, update order status to 'Cancelled'
        $order_id = $data['data']['order_id'];
        $stmt = $pdo->prepare("UPDATE orders SET order_status = 'Cancelled' WHERE order_id = ?");
        $stmt->execute([$order_id]);

        // Redirect to failure page
        header("Location: order_failure.php?order_id=" . $order_id);
        exit;
    }
}
?>
