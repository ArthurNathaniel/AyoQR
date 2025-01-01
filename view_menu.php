<?php
require 'db.php';
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle deletion
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM menu WHERE id = ?");
    if ($stmt->execute([$deleteId])) {
        header("Location: view_menu.php?message=Food item deleted successfully!");
        exit();
    } else {
        $error = "Error deleting food item: " . $conn->errorInfo()[2];
    }
}

// Fetch menu items
$query = "
    SELECT menu.id, menu.image_path, menu.name, categories.name AS category_name, menu.price 
    FROM menu
    INNER JOIN categories ON menu.category_id = categories.id
    ORDER BY menu.name ASC
";
$stmt = $conn->prepare($query);
$stmt->execute();
$menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Food Menu</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/menu.css">
    <style>
        img {
            width: 100px;
            height: auto;
        }
        .delete-btn {
           color: red;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            background-color: transparent;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }
    </style>
</head>

<body>
    <?php include 'sidebar.php'; ?>
    <div class="menu_all">
        <div class="menu_box">
            <h1>View Food Menu</h1>
            <?php if (isset($_GET['message'])): ?>
                <p style="color: green;"><?php echo htmlspecialchars($_GET['message'], ENT_QUOTES); ?></p>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error, ENT_QUOTES); ?></p>
            <?php endif; ?>
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price (GHS)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($menuItems)): ?>
                        <?php foreach ($menuItems as $row): ?>
                            <tr>
                                <td>
                                    <img src="<?php echo htmlspecialchars($row['image_path'], ENT_QUOTES); ?>" 
                                         alt="<?php echo htmlspecialchars($row['name'], ENT_QUOTES); ?>">
                                </td>
                                <td><?php echo htmlspecialchars($row['name'], ENT_QUOTES); ?></td>
                                <td><?php echo htmlspecialchars($row['category_name'], ENT_QUOTES); ?></td>
                                <td><?php echo number_format($row['price'], 2); ?></td>
                                <td>
                                    <a href="view_menu.php?delete_id=<?php echo $row['id']; ?>" 
                                       onclick="return confirm('Are you sure you want to delete this food item?');">
                                       <button class="delete-btn"><i class="fa-solid fa-trash"></i></button>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No food menu items found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
