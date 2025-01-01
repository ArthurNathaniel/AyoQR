<?php
require 'db.php';
session_start();

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle adding item to the cart
if (isset($_POST['add_to_cart'])) {
    $menuId = intval($_POST['menu_id']);
    $menuName = $_POST['menu_name'];
    $menuPrice = floatval($_POST['menu_price']);
    $menuImage = $_POST['menu_image'];

    // Check if the item already exists in the cart
    if (isset($_SESSION['cart'][$menuId])) {
        // Increase quantity if item exists
        $_SESSION['cart'][$menuId]['quantity']++;
    } else {
        // Add new item to the cart
        $_SESSION['cart'][$menuId] = [
            'id' => $menuId,
            'name' => $menuName,
            'price' => $menuPrice,
            'image_path' => $menuImage,
            'quantity' => 1
        ];
    }
    header("Location: cart.php");
    exit;
}

// Handle deleting an item from the cart
if (isset($_POST['delete_item'])) {
    $menuId = intval($_POST['menu_id']);
    unset($_SESSION['cart'][$menuId]);
    header("Location: cart.php");
    exit;
}

// Handle clearing the cart
if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = [];
    header("Location: cart.php");
    exit;
}

// Handle increasing quantity
if (isset($_POST['increase_quantity'])) {
    $menuId = intval($_POST['menu_id']);
    if (isset($_SESSION['cart'][$menuId])) {
        $_SESSION['cart'][$menuId]['quantity']++;
    }
    header("Location: cart.php");
    exit;
}

// Handle decreasing quantity
if (isset($_POST['decrease_quantity'])) {
    $menuId = intval($_POST['menu_id']);
    if (isset($_SESSION['cart'][$menuId]) && $_SESSION['cart'][$menuId]['quantity'] > 1) {
        $_SESSION['cart'][$menuId]['quantity']--;
    }
    header("Location: cart.php");
    exit;
}

// Handle checkout
if (isset($_POST['checkout'])) {
    echo "<p>Checkout successful! Thank you for your order.</p>";
    $_SESSION['cart'] = []; // Clear the cart after successful checkout
    exit;
}

// Calculate cart count
$cartCount = array_sum(array_column($_SESSION['cart'], 'quantity'));
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/cart.css">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="checkout_all">
        <div class="checkout_box">
            <div class="cart_all">
                <h3>My Cart</h3>
                <div class="clear_all">
                    <form method="POST">
                        <button type="submit" name="clear_cart">Clear all</button>
                    </form>
                </div>
            </div>

            <?php if (empty($_SESSION['cart'])): ?>
                <p>Your cart is empty.</p>
                <a href="index.php">Go back to shopping</a>
            <?php else: ?>
                <form method="POST" action="">
                    <div class="cart-items">
                        <?php $total = 0; ?>
                        <?php foreach ($_SESSION['cart'] as $menuId => $item): ?>
                            <?php $subtotal = $item['price'] * $item['quantity']; ?>
                            <div class="cart-item">
                                <div class="food_image">
                                    <img src="<?php echo htmlspecialchars($item['image_path'], ENT_QUOTES); ?>" alt="Food Image">
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="menu_id" value="<?php echo $menuId; ?>">
                                        <button type="submit" name="delete_item" class="delete"><i class="fa-regular fa-trash-can"></i></button>
                                    </form>
                                </div>

                                <div class="food_name">
                                    <h3><?php echo htmlspecialchars($item['name'], ENT_QUOTES); ?></h3>
                                    <!-- <p>Price: GHS <?php echo number_format($item['price'], 2); ?></p> -->
                                    <p class="price"> GH₵ <?php echo number_format($subtotal, 2); ?></p>
                                </div>
                                <div class="quantity">

                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="menu_id" value="<?php echo $menuId; ?>">
                                        <button type="submit" name="increase_quantity" style="border:none; background:none;">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </form>
                                    <?php echo $item['quantity']; ?>

                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="menu_id" value="<?php echo $menuId; ?>">
                                        <button type="submit" name="decrease_quantity" style="border:none; background:none;">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </form>
                                </div>
                                <!-- <p>Total: GHS <?php echo number_format($subtotal, 2); ?></p> -->


                            </div>
                            <?php $total += $subtotal; ?>
                        <?php endforeach; ?>
                    </div>



        </div>
        <div class="total_all">
            <p>SubTotal GH₵ <?php echo number_format($total, 2); ?></strong></p>
            <div class="line"></div>
            <p><strong>Total: GH₵ <?php echo number_format($total, 2); ?></strong></p>
            <div class="checkout_btn">
                <a href="checkout.php" class="btn">
                <button>Proceed to checkout</button>    
                </a>
            </div>

            </form>
        </div>

      <div class="continue_shopping">
      <a href="index.php"> <i class="fa-solid fa-arrow-left-long"></i> Continue Shopping</a>
      </div>
    <?php endif; ?>
    </div>
</body>

</html>
<style>