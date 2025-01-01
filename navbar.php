<div class="navbar_all">
    <div class="logo"></div>
    <div class="nav_links">
        <a href="index.php"><i class="fa-solid fa-house"></i> Home</a>
        <a href="javascript:void(0);" id="search-link"><i class="fa-solid fa-magnifying-glass"></i> Search</a>
        <a href="cart.php" class="cart-link">
            <i class="fa-solid fa-cart-shopping"></i> Cart
            <span class="cart-badge"><?php echo $cartCount; ?></span>
        </a>
    </div>
    <div class="contact_us">
        <a href="#"><i class="fa-solid fa-phone-volume"></i> Call</a>
    </div>
</div>

<!-- Search Input -->
<div class="search-bar" id="search-bar" style="display: none;">
    <input type="text" id="search-input" placeholder="Search food...">
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const navLinks = document.querySelectorAll(".nav_links a"); // Select all navigation links
    const currentURL = window.location.href; // Get the current URL

    navLinks.forEach(link => {
        if (currentURL.includes(link.getAttribute("href"))) {
            link.style.fontWeight = "bold"; // Make the active link bold
            link.style.color = "#000"; // Add a custom color for the active link
        } else {
            link.style.fontWeight = "normal"; // Reset others
            link.style.color = ""; // Reset color
        }
    });
});


</script>