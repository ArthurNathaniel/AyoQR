<?php
require 'db.php'; // Include database connection
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate input
        $name = $_POST['name'] ?? null;
        $phone = $_POST['phone'] ?? null;
        $email = $_POST['email'] ?? null;
        $pickup_delivery = $_POST['pickup_delivery'] ?? null;
        $address = ($pickup_delivery === 'delivery') ? ($_POST['address'] ?? null) : null;

        if (!$name || !$phone || !$email || !$pickup_delivery) {
            throw new Exception("Missing required fields.");
        }

        // Calculate total and validate cart
        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            throw new Exception("Cart is empty.");
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Start transaction
        $conn->beginTransaction();

        // Insert order
        $stmt = $conn->prepare("
            INSERT INTO orders (name, phone, email, pickup_delivery, address, total)
            VALUES (:name, :phone, :email, :pickup_delivery, :address, :total)
        ");
        $stmt->execute([
            ':name' => $name,
            ':phone' => $phone,
            ':email' => $email,
            ':pickup_delivery' => $pickup_delivery,
            ':address' => $address,
            ':total' => $total,
        ]);
        $order_id = $conn->lastInsertId();

        // Insert order items
        $stmt = $conn->prepare("
            INSERT INTO order_items (order_id, item_name, item_price, quantity)
            VALUES (:order_id, :item_name, :item_price, :quantity)
        ");
        foreach ($cart as $item) {
            $stmt->execute([
                ':order_id' => $order_id,
                ':item_name' => $item['name'],
                ':item_price' => $item['price'],
                ':quantity' => $item['quantity'],
            ]);
        }

        // Commit transaction
        $conn->commit();

        // Clear cart session
        unset($_SESSION['cart']);

        echo json_encode(['success' => true, 'order_id' => $order_id]);
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
