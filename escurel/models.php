<?php
include_once 'dbConfig.php';

// Create a job post
function createJobPost($title, $description) {
    global $pdo;
    $sql = "INSERT INTO job_posts (title, description) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$title, $description]);
}

// Get all job posts
function getJobPosts() {
    global $pdo;
    $sql = "SELECT * FROM job_posts";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get a job post by ID
function getJobPostsById($jobPostId) {
    global $pdo;
    $sql = "SELECT * FROM job_posts WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$jobPostId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Apply to a job post
function applyToJob($applicantId, $jobPostId, $coverLetter, $resumePath) {
    global $pdo;
    $sql = "INSERT INTO applications (applicant_id, job_post_id, cover_letter, resume_path) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$applicantId, $jobPostId, $coverLetter, $resumePath]);
}

// Get applications for a specific job post
function getApplications($jobPostId) {
    global $pdo;
    $sql = "SELECT * FROM applications WHERE job_post_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$jobPostId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Update the status of an application (hire or reject)
function updateApplicationStatus($applicationId, $status) {
    global $pdo;
    $sql = "UPDATE applications SET status = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$status, $applicationId]);
}

// Get messages for a user
function getMessagesForUser($userId) {
    global $pdo;
    $sql = "SELECT * FROM messages WHERE sender_id = ? OR receiver_id = ? ORDER BY timestamp DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Delete a job post and its related applications by ID
function deleteJobPost($jobPostId) {
    global $pdo;

    // Delete related applications first
    $sql = "DELETE FROM applications WHERE job_post_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$jobPostId]);

    // Delete the job post
    $sql = "DELETE FROM job_posts WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$jobPostId]);
}

// Fetch applicants for a specific job post
function getApplicantsForJobPost($jobPostId) {
    global $pdo;
    $sql = "SELECT a.id AS application_id, u.id AS applicant_id, u.username, a.cover_letter, a.resume_path, a.status
            FROM applications a
            JOIN users u ON a.applicant_id = u.id
            WHERE a.job_post_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$jobPostId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to hire an applicant (update status to 'Accepted')
function hireApplicant($applicationId) {
    $sql = "UPDATE applications SET status = 'Accepted' WHERE id = ?";
    $stmt = $GLOBALS['pdo']->prepare($sql);
    $stmt->execute([$applicationId]);
}

// Function to reject an applicant (update status to 'Rejected')
function rejectApplicant($applicationId) {
    $sql = "UPDATE applications SET status = 'Rejected' WHERE id = ?";
    $stmt = $GLOBALS['pdo']->prepare($sql);
    $stmt->execute([$applicationId]);
}

// Function to get an applicant's application for a specific job post
function getApplicationByApplicantAndJobPost($applicantId, $jobPostId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM applications WHERE applicant_id = :applicant_id AND job_post_id = :job_post_id");
    $stmt->execute(['applicant_id' => $applicantId, 'job_post_id' => $jobPostId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to send a message between users
function sendMessage($senderId, $receiverId, $message) {
    global $pdo;
    $sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$senderId, $receiverId, $message]);
}

// Function to get messages between two users
function getMessagesBetweenUsers($userId1, $userId2) {
    global $pdo;
    $sql = "SELECT * FROM messages 
            WHERE (sender_id = ? AND receiver_id = ?) 
            OR (sender_id = ? AND receiver_id = ?)
            ORDER BY timestamp ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId1, $userId2, $userId2, $userId1]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get all conversations for a user
function getConversations($userId) {
    global $pdo;
    $sql = "SELECT DISTINCT u.id, u.username 
            FROM users u
            INNER JOIN messages m 
            ON (m.sender_id = u.id OR m.receiver_id = u.id)
            WHERE (m.sender_id = :userId OR m.receiver_id = :userId)
            AND u.id != :userId";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['userId' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get user by ID
function getUserById($userId) {
    global $pdo;
    $sql = "SELECT id, username, role FROM users WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to get all HR users
function getAllHRUsers() {
    global $pdo;
    $sql = "SELECT id, username FROM users WHERE role = 'HR'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>
