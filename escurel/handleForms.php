<?php
include_once 'models.php';

function handleLogin($username, $password) {
    global $pdo;
    $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username, $password]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function handleRegistration($username, $password, $role) {
    global $pdo;
    try {
        $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $password, $role]);
        return true;
    } catch (PDOException $e) {
        if ($e->getCode() == '23000') {
            // Username already exists
            return "Username already exists. Please choose a different username.";
        }
        throw $e; // Re-throw other database errors
    }
}

function handleApplication($applicantId, $jobPostId, $coverLetter, $resume) {
    // Define the upload directory for resumes
    $uploadDir = 'uploads/';
    
    // Get the file's extension and ensure it's a PDF
    $fileExtension = pathinfo($resume['name'], PATHINFO_EXTENSION);
    
    if ($fileExtension !== 'pdf') {
        echo "Error: Only PDF files are allowed.";
        return;
    }
    
    // Define the full file path
    $resumePath = $uploadDir . basename($resume['name']);
    
    // Attempt to move the uploaded file to the destination directory
    if (move_uploaded_file($resume['tmp_name'], $resumePath)) {
        // If the upload is successful, call the applyToJob function from models.php
        applyToJob($applicantId, $jobPostId, $coverLetter, $resumePath);
    } else {
        echo "Error: Failed to upload the resume.";
    }
}

function handleMessage($senderId, $receiverId, $messageContent) {
    global $pdo;
    $sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$senderId, $receiverId, $messageContent]);
}
?>
