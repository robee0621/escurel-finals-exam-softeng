<?php
// Start session and check if user is already logged in
session_start();

// If user is logged in, redirect to appropriate dashboard
if (isset($_SESSION['user'])) {
    header("Location: " . ($_SESSION['user']['role'] == 'HR' ? 'hr_Dashboard.php' : 'applicant_Dashboard.php'));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobConnect - Homepage</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="main-container">
        <div class="welcome-container">
            <div class="welcome-content">
                <div class="brand-section">
                    <h1 class="brand-title">JobConnect</h1>
                    <p class="brand-subtitle">Connecting talent with opportunity</p>
                </div>
                
                <div class="action-section">
                    <div class="action-buttons">
                        <a href="login.php" class="action-button login-btn">
                            LogIn
                        </a>
                        <a href="register.php" class="action-button register-btn">
                            Create Account
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

