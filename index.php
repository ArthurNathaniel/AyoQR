<?php
require 'db.php';
session_start();

// Initialize the cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle adding to the cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['menu_id'], $_POST['name'], $_POST['price'], $_POST['image_path'])) {
    $menu_id = intval($_POST['menu_id']);
    $name = htmlspecialchars($_POST['name'], ENT_QUOTES);
    $price = floatval($_POST['price']);
    $image_path = htmlspecialchars($_POST['image_path'], ENT_QUOTES);

    // Check if the item is already in the cart
    if (isset($_SESSION['cart'][$menu_id])) {
        // Item already in the cart, show alert message
        echo "<script>alert('This item is already in your cart!');</script>";
    } else {
        // Add item to cart
        $_SESSION['cart'][$menu_id] = [
            'name' => $name,
            'price' => $price,
            'quantity' => 1,
            'image_path' => $image_path,
        ];
    }
}

// Calculate cart count
$cartCount = array_sum(array_column($_SESSION['cart'], 'quantity'));

// Fetch categories using PDO
$categories_query = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = $categories_query->fetchAll(PDO::FETCH_ASSOC);

// Fetch menu items based on selected category (if any)
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

$query = "SELECT id, image_path, name, price FROM menu";
if ($category_id > 0) {
    $query .= " WHERE category_id = :category_id";
}
$query .= " ORDER BY name ASC";
$menuItems_query = $conn->prepare($query);

if ($category_id > 0) {
    $menuItems_query->bindParam(':category_id', $category_id, PDO::PARAM_INT);
}
$menuItems_query->execute();
$menuItems = $menuItems_query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Menu</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css"> <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/index.css">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="index_all">
        <div class="title">
            <h2>Our Menu</h2>
        </div>

        <!-- Category Filter -->
        <div class="category-filter">
            <form method="GET" action="">
               <div class="forms">
               <label for="category">Select Category:</label>
                <select name="category_id" id="category">
                    <option value="0">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" <?php echo $category_id == $category['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name'], ENT_QUOTES); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
               </div>
                <div class="forms">
                <button type="submit">Filter</button>
                </div>
            </form>
        </div>

        <!-- Menu Items -->
        <div class="menu-container" id="menu-container">
            <?php if (!empty($menuItems)): ?>
                <?php foreach ($menuItems as $item): ?>
                    <div class="card menu-item" data-name="<?php echo strtolower($item['name']); ?>">
                        <div class="card_box">
                            <div class="card_image">
                                <img src="<?php echo htmlspecialchars($item['image_path'], ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($item['name'], ENT_QUOTES); ?>">
                            </div>
                            <div class="card_details">
                                <h3><?php echo htmlspecialchars($item['name'], ENT_QUOTES); ?></h3>
                                <div class="card_price">
                                    <p>GH₵ <?php echo number_format($item['price'], 2); ?></p>
                                </div>
                                <form method="POST" action="">
                                    <input type="hidden" name="menu_id" value="<?php echo $item['id']; ?>">
                                    <input type="hidden" name="name" value="<?php echo htmlspecialchars($item['name'], ENT_QUOTES); ?>">
                                    <input type="hidden" name="price" value="<?php echo $item['price']; ?>">
                                    <input type="hidden" name="image_path" value="<?php echo htmlspecialchars($item['image_path'], ENT_QUOTES); ?>">
                                    <div class="card_button">
                                        <button type="submit" name="add_to_cart"><i class="fa-solid fa-circle-plus"></i></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No food items found.</p>
            <?php endif; ?>
        </div>
    </div>
    <!-- <?php include 'footer.php'; ?> -->
    <script src="./js/swiper.js"></script>
    <script>
        // Toggle search input visibility and swiper images
        document.getElementById("search-link").addEventListener("click", function() {
            const searchBar = document.getElementById("search-bar");
            const swiperImages = document.querySelector(".swiper_images");

            if (searchBar.style.display === "none" || searchBar.style.display === "") {
                searchBar.style.display = "block"; // Show the search bar
                swiperImages.style.display = "none"; // Hide the swiper images
            } else {
                searchBar.style.display = "none"; // Hide the search bar
                swiperImages.style.display = "block"; // Show the swiper images
            }
        });

        // Real-time search functionality
        document.getElementById("search-input").addEventListener("input", function() {
            const searchQuery = this.value.toLowerCase();
            const menuItems = document.querySelectorAll(".menu-item");

            menuItems.forEach(item => {
                const itemName = item.getAttribute("data-name");
                if (itemName.includes(searchQuery)) {
                    item.style.display = "block";
                } else {
                    item.style.display = "none";
                }
            });
        });
    </script>

</body>

</html>
