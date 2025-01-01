<?php
require 'db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the email exists
    $stmt = $conn->prepare("SELECT id, password FROM admins WHERE email = :email");
    $stmt->bindValue(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        $adminId = $admin['id'];
        $hashedPassword = $admin['password'];

        // Verify the password
        if (password_verify($password, $hashedPassword)) {
            // Start session and store admin details
            session_start();
            $_SESSION['admin_id'] = $adminId;
            $_SESSION['email'] = $email;

            // Redirect to admin dashboard
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $message = "Incorrect password. Please try again.";
        }
    } else {
        $message = "Email not found. Please check your email or sign up.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/login.css">
    <script>
        function showAlert(message) {
            if (message) {
                alert(message);
            }
        }
    </script>
</head>

<body onload="showAlert('<?php echo htmlspecialchars($message, ENT_QUOTES); ?>')">
    <div class="login_all">
        <div class="login_box">
            <div class="login_title">
                <h2><span>Ayo</span>QR - Login</h2>
            </div>

            <form method="POST">
                <div class="forms">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="forms">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="forms">
                    <button type="submit">Login</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
