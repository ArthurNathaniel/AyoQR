<?php
require 'db.php';
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $foodName = trim($_POST['food_name']);
    $categoryId = intval($_POST['category_id']);
    $price = floatval($_POST['price']);
    $imagePath = "";

    if (empty($foodName) || $categoryId <= 0 || $price <= 0) {
        $message = "Please fill in all fields correctly.";
    } else {
        // Handle image upload
        if (!empty($_FILES['food_image']['name'])) {
            $uploadDir = 'uploads/';
            $fileExt = strtolower(pathinfo($_FILES['food_image']['name'], PATHINFO_EXTENSION));
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($fileExt, $allowedExts)) {
                $uniqueFileName = uniqid('food_', true) . '.' . $fileExt;
                $uploadFile = $uploadDir . $uniqueFileName;

                if (move_uploaded_file($_FILES['food_image']['tmp_name'], $uploadFile)) {
                    $imagePath = $uploadFile;
                } else {
                    $message = "Error uploading the image.";
                }
            } else {
                $message = "Invalid image file type.";
            }
        } else {
            $message = "Food image is required.";
        }

        if (!empty($imagePath)) {
            // Check for duplicate food name in the same category
            $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM menu WHERE name = :name AND category_id = :category_id");
            $stmt->execute(['name' => $foodName, 'category_id' => $categoryId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row['count'] > 0) {
                $message = "Error: Food item with the same name already exists in this category.";
            } else {
                // Insert new food item
                $stmt = $conn->prepare("INSERT INTO menu (image_path, name, category_id, price) VALUES (:image_path, :name, :category_id, :price)");
                $success = $stmt->execute([
                    'image_path' => $imagePath,
                    'name' => $foodName,
                    'category_id' => $categoryId,
                    'price' => $price,
                ]);

                $message = $success ? "Food item added successfully!" : "Database error: " . $conn->errorInfo()[2];
            }
        }
    }
}

// Fetch categories
$stmt = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Food Menu</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/category.css">
    <script>
        function showAlert(message) {
            if (message) {
                alert(message);
            }
        }
    </script>
</head>
<body onload="showAlert('<?php echo htmlspecialchars($message, ENT_QUOTES); ?>')">
    <?php include 'sidebar.php'; ?>
    <div class="category_all">
        <div class="category_box">
            <div class="category_title">
                <h2>Create Food Menu</h2>
            </div> 

            <form method="POST" enctype="multipart/form-data">
                <div class="forms">
                    <label for="food_image">Food Image:</label>
                    <input type="file" id="food_image" name="food_image" accept="image/*" required>
                </div>
                <div class="forms">
                    <label for="food_name">Food Name:</label>
                    <input type="text" id="food_name" placeholder="Enter your food name" name="food_name" required>
                </div>
                <div class="forms">
                    <label for="category_id">Category:</label>
                    <select id="category_id" name="category_id" required>
                        <option value="" hidden>Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>">
                                <?php echo htmlspecialchars($category['name'], ENT_QUOTES); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="forms">
                    <label for="price">Price:</label>
                    <input type="number" placeholder="Enter your price" id="price" name="price" step="0.01" required>
                </div>
                <div class="forms">
                    <button type="submit">Add Food</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
