<?php
require 'db.php';
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch food items
$query = "SELECT id, name, price FROM menu ORDER BY name ASC";
$stmt = $conn->prepare($query);
$stmt->execute();
$foodItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $food_id = $_POST['food_id'];
    $new_price = $_POST['price'];

    if (is_numeric($new_price) && $new_price > 0) {
        // Update food price
        $stmt = $conn->prepare("UPDATE menu SET price = ? WHERE id = ?");
        if ($stmt->execute([$new_price, $food_id])) {
            echo "<script>alert('Price updated successfully!'); window.location.href = 'update_price.php';</script>";
        } else {
            echo "<script>alert('Error updating price.');</script>";
        }
    } else {
        echo "<script>alert('Please enter a valid price.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Food Price</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/menu.css">
</head>

<body>
<?php include 'sidebar.php'; ?>
   <div class="menu_alls">
   <div class="menu_box">
   <h1>Update Food Price</h1>
    <form method="POST">
        <div class="forms">
            <label for="food_id">Select Food Item:</label>
            <select id="food_id" name="food_id" required>
                <option value="" hidden>Select Food</option>
                <?php foreach ($foodItems as $row): ?>
                    <option value="<?php echo $row['id']; ?>">
                        <?php echo htmlspecialchars($row['name'], ENT_QUOTES); ?> - GHS <?php echo number_format($row['price'], 2); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="forms">
            <label for="price">New Price (GHS):</label>
            <input type="number" step="0.01" id="price" name="price" required>
        </div>

        <div class="forms">
            <button type="submit">Update Price</button>
        </div>
    </form>
    </div>
   </div>

</body>

</html>
