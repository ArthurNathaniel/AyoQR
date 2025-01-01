<?php
require 'db.php';
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryName = trim($_POST['category_name']);

    if (!empty($categoryName)) {
        try {
            // Insert the category into the database
            $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (:name)");
            $stmt->bindValue(':name', $categoryName, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $message = "Category added successfully!";
            } else {
                $errorInfo = $stmt->errorInfo();
                if ($errorInfo[1] == 1062) { // Duplicate entry error code
                    $message = "Category already exists.";
                } else {
                    $message = "Error: " . $errorInfo[2];
                }
            }
        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
        }
    } else {
        $message = "Category name cannot be empty.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Food Category</title>
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
                <h2>Create Food Category</h2>
            </div>
            <form method="POST">
                <div class="forms">
                    <label for="category_name">Category Name:</label>
                    <input type="text" id="category_name" placeholder="Enter your category name" name="category_name" required>
                </div>
                <div class="forms">
                    <button type="submit">Add Category</button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>
