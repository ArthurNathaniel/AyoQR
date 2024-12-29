<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Retrieve admin email from session
$adminEmail = $_SESSION['email'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        header {
            background-color: #007BFF;
            color: #fff;
            padding: 1rem;
            text-align: center;
        }
        nav {
            margin: 2rem;
        }
        nav a {
            display: block;
            margin: 0.5rem 0;
            color: #007BFF;
            text-decoration: none;
        }
        nav a:hover {
            text-decoration: underline;
        }
        .logout {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome, Admin!</h1>
        <p>You are logged in as: <?php echo htmlspecialchars($adminEmail, ENT_QUOTES); ?></p>
    </header>
    <nav>
        <h2>Dashboard Options</h2>
        <a href="manage_orders.php">Manage Orders</a>
        <a href="manage_tables.php">Manage Tables</a>
        <a href="view_reports.php">View Reports</a>
        <a href="settings.php">Settings</a>
        <a href="logout.php" class="logout">Logout</a>
    </nav>
</body>
</html>
