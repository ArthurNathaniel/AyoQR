<!-- HTML -->
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
  <div class="cart">
  <a href="cart.php" class="cart-link">
            <i class="fa-solid fa-cart-shopping"></i> Cart
            <span class="cart-badge"><?php echo $cartCount; ?></span>
        </a>
  </div>
    <button id="mobile-button">
        <i class="fa-solid fa-bars-staggered"></i>
    </button>
    <div class="mobile_links" id="mobile-links">
        <a href="index.php"><i class="fa-solid fa-house"></i> Home</a>
        <a href="javascript:void(0);" id="mobile-search-link"><i class="fa-solid fa-magnifying-glass"></i> Search</a>
        <a href="cart.php" class="cart-link">
            <i class="fa-solid fa-cart-shopping"></i> Cart
            <span class="cart-badge"><?php echo $cartCount; ?></span>
        </a>
        <a href="#"><i class="fa-solid fa-phone-volume"></i> Call</a>
    </div>
</div>

<!-- Search Input -->
<div class="search-bar" id="search-bar" style="display: none;">
<button id="close-search-bar"><i class="fa-solid fa-xmark"></i></button>
    <input type="text" id="search-input" placeholder="Search food...">
    
</div>

<!-- JavaScript -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Highlight active link in nav_links
    const navLinks = document.querySelectorAll(".nav_links a");
    const currentURL = window.location.href;

    navLinks.forEach(link => {
        if (currentURL.includes(link.getAttribute("href"))) {
            link.style.fontWeight = "bold";
            link.style.color = "#000";
        } else {
            link.style.fontWeight = "normal";
            link.style.color = "";
        }
    });

    // Mobile menu toggle
    const button = document.getElementById('mobile-button');
    const mobileLinks = document.getElementById('mobile-links');
    const searchBar = document.getElementById('search-bar');
    const searchLinks = document.querySelectorAll('#search-link, #mobile-search-link');
    const closeSearchBar = document.getElementById('close-search-bar');

    button.addEventListener('click', () => {
        mobileLinks.classList.toggle('active');

        const icon = button.querySelector('i');
        if (mobileLinks.classList.contains('active')) {
            icon.classList.replace('fa-bars-staggered', 'fa-xmark');
        } else {
            icon.classList.replace('fa-xmark', 'fa-bars-staggered');
        }
    });

    // Search bar toggle
    searchLinks.forEach(link => {
        link.addEventListener('click', () => {
            searchBar.style.display = "block";
        });
    });

    // Close search bar using the button
    closeSearchBar.addEventListener('click', () => {
        searchBar.style.display = "none";
    });
});
</script>
