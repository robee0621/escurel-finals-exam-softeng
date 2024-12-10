<?php
session_start();
include_once 'handleForms.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    handleRegistration($username, $password, $role);
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindHire - Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="main-wrapper">
        <div class="auth-container">
            <div class="auth-box">
                <div class="auth-content">
                    <div class="text-center mb-4">
                        <h2 class="auth-title">Join Us Today</h2>
                        <p class="auth-subtitle">Create your account to get started</p>
                    </div>

                    <form action="register.php" method="POST" class="auth-form" id="registerForm">
                        <div class="form-control">
                            <label for="username" class="input-label">Username</label>
                            <input type="text" 
                                   id="username" 
                                   name="username" 
                                   class="input-field"
                                   placeholder="Choose a username" 
                                   required>
                        </div>
                        
                        <div class="form-control">
                            <label for="password" class="input-label">Password</label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="input-field"
                                   placeholder="Create a strong password" 
                                   required>
                        </div>
                        
                        <div class="form-control">
                            <label for="role" class="input-label">Account Type</label>
                            <select id="role" 
                                    name="role" 
                                    class="select-field"
                                    required>
                                <option value="" disabled selected>Select your role</option>
                                <option value="HR">HR Representative</option>
                                <option value="Applicant">Job Seeker</option>
                            </select>
                        </div>

                        <button type="submit" class="submit-btn">Create Account</button>
                    </form>

                    <div class="auth-footer">
                        <p class="switch-auth">
                            Already registered? 
                            <a href="login.php" class="auth-link">Login here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

