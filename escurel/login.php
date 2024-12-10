<?php
session_start();
include_once 'handleForms.php';

// Check if the user is already logged in
if (isset($_SESSION['user'])) {
    // Redirect the logged-in user to the appropriate dashboard
    header("Location: " . ($_SESSION['user']['role'] == 'HR' ? 'hr_Dashboard.php' : 'applicant_Dashboard.php'));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user = handleLogin($username, $password);
    
    if ($user) {
        $_SESSION['user'] = $user;
        header("Location: " . ($user['role'] == 'HR' ? 'hr_Dashboard.php' : 'applicant_Dashboard.php'));
        exit();
    } else {
        $error = "Invalid credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobConnect - Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="main-container">
        <div class="auth-container">
            <div class="auth-box">
                <div class="auth-content">
                    <div class="brand-section">
                        <h2 class="brand-title">JobConnect</h2>
                        <p class="brand-subtitle">Welcome back! Please login to your account</p>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert-box error">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="auth-form">
                        <div class="input-group">
                            <label for="username" class="input-label">Username</label>
                            <input type="text" 
                                   id="username" 
                                   name="username" 
                                   class="input-field"
                                   placeholder="Enter your username"
                                   autocomplete="username"
                                   required>
                        </div>

                        <div class="input-group">
                            <label for="password" class="input-label">Password</label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="input-field"
                                   placeholder="Enter your password"
                                   autocomplete="current-password"
                                   required>
                        </div>

                        <button type="submit" class="submit-button">
                            LogIn
                        </button>
                    </form>

                    <div class="auth-footer">
                        <p class="redirect-text">
                            New to JobConnect? 
                            <a href="register.php" class="redirect-link">Create an account</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

