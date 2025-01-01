<div class="sidebar">
    <div class="logo_name">
        <h1><span>Ayo</span>QR</h1>
    </div>
    <div class="links">
    <div class="dashed"></div>
    <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard </a>
    <div class="dashed"></div>
    <a href="create_category.php"><i class="fas fa-plus-circle"></i> Categories </a>
    <div class="dashed"></div>
    <a href="view_categories.php"><i class="fas fa-eye"></i> View Categories </a>
    <div class="dashed"></div>
    <a href="create_menu.php"><i class="fas fa-utensils"></i> Food Menu </a>
    <div class="dashed"></div>
    <a href="view_menu.php"><i class="fas fa-list"></i> View Menu </a>
    <div class="dashed"></div>
    <a href="update_price.php"><i class="fas fa-pencil-alt"></i> Update Menu Price </a>
    <div class="dashed"></div>
    <a href="admin_orders.php"><i class="fas fa-box-open"></i> Daily Orders </a>
    <div class="dashed"></div>
    <a href="yearly_report.php"><i class="fas fa-calendar-alt"></i> Yearly Orders </a>
    <div class="dashed"></div>
</div>

    <a href="logout.php">
        <div class="logout">
            <i class="fas fa-power-off"></i> Logout
        </div>
    </a>
</div>

<div class="toggle_btn">
    <p><i class="fas fa-bars"></i></p>
</div>

<script>
    const sidebar = document.querySelector('.sidebar');
    const toggleBtn = document.querySelector('.toggle_btn');
    const toggleIcon = toggleBtn.querySelector('i');

    // Toggle sidebar visibility
    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('hidden');
        toggleBtn.classList.toggle('collapsed');

        if (sidebar.classList.contains('hidden')) {
            toggleIcon.classList.replace('fa-bars', 'fa-xmark');
        } else {
            toggleIcon.classList.replace('fa-xmark', 'fa-bars');
        }
    });

    // Highlight the active link based on the current page
    const currentPage = window.location.pathname.split("/").pop();
    const links = document.querySelectorAll(".links a");

    links.forEach(link => {
        if (link.getAttribute("href") === currentPage) {
            link.classList.add("active");
        }
    });
</script>