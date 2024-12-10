<?php
session_start();
include_once 'models.php';
include_once 'handleForms.php';

$jobPostId = $_GET['id'];

// Fetch job post details
$jobPost = getJobPostsById($jobPostId);

// If the job post doesn't exist, redirect to another page or show an error
if (!$jobPost) {
    header('Location: index.php'); // Or show an error page
    exit;
}

// Fetch applicants for the job post (only for HR view)
$applicants = [];
if ($_SESSION['user']['role'] == 'HR') {
    $applicants = getApplicantsForJobPost($jobPostId);
}

// If the user is an Applicant
if ($_SESSION['user']['role'] == 'Applicant') {
    $applicantId = $_SESSION['user']['id'];
    
    // Fetch the applicant's application details for the job post
    $application = getApplicationByApplicantAndJobPost($applicantId, $jobPostId);

    // Handle job application submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['resume'])) {
        handleApplication($applicantId, $jobPostId, $_POST['cover_letter'], $_FILES['resume']);
        $applicationStatus = 'Application submitted successfully!';
        // Re-fetch the application status after submission
        $application = getApplicationByApplicantAndJobPost($applicantId, $jobPostId);
    }
}

// Handle hiring or rejecting applicants for HR role
if ($_SESSION['user']['role'] == 'HR' && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $applicationId = $_POST['application_id'];
    $action = $_POST['action'];

    if ($action == 'hire') {
        hireApplicant($applicationId);
    } elseif ($action == 'reject') {
        rejectApplicant($applicationId);
    }

    // Redirect to refresh the page after action
    header("Location: show_JobPost.php?id=$jobPostId");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Job Post</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="main-container">
        <div class="nav-container">
            <div class="logo-section">
                <h2><?= htmlspecialchars($jobPost['title']) ?></h2>
            </div>
            <div class="nav-links">
                <a href="messaging.php" class="nav-item">Messages</a>
                <a href="<?php echo ($_SESSION['user']['role'] == 'Applicant') ? 'applicant_Dashboard.php' : 'hr_Dashboard.php'; ?>" class="nav-item">Dashboard</a>
                <a href="logout.php" class="nav-item logout">Logout</a>
            </div>
        </div>

        <div class="content-container">
            <div class="job-card">
                <div class="job-content">
                    <p class="description"><?= htmlspecialchars($jobPost['description']) ?></p>

                    <?php if (isset($applicationStatus)) { ?>
                        <div class="alert success"><?= $applicationStatus ?></div>
                    <?php } ?>

                    <?php if ($_SESSION['user']['role'] == 'Applicant') { ?>
                        <?php if ($application) { ?>
                            <div class="application-status-card">
                                <h3>Application Status</h3>
                                <div class="status-details">
                                    <p><strong>Cover Letter:</strong> <?= htmlspecialchars($application['cover_letter']) ?></p>
                                    <p><strong>Status:</strong> <span class="status-badge"><?= htmlspecialchars($application['status']) ?></span></p>
                                    <p><strong>Resume:</strong> <a href="<?= htmlspecialchars($application['resume_path']) ?>" target="_blank" class="doc-link">View Resume</a></p>
                                </div>
                                <a href="messaging.php" class="action-button">Message HR</a>
                            </div>
                        <?php } else { ?>
                            <div class="application-form-container">
                                <form action="show_JobPost.php?id=<?= $jobPostId ?>" method="POST" enctype="multipart/form-data">
                                    <div class="input-group">
                                        <label for="cover_letter">Cover Letter</label>
                                        <textarea id="cover_letter" name="cover_letter" placeholder="Write your cover letter here..." required></textarea>
                                    </div>
                                    <div class="input-group">
                                        <label>Resume (PDF Only)</label>
                                        <input type="file" name="resume" accept=".pdf" required>
                                    </div>
                                    <button type="submit" class="action-button">Submit Application</button>
                                </form>
                            </div>
                        <?php } ?>
                    <?php } elseif ($_SESSION['user']['role'] == 'HR') { ?>
                        <div class="applicants-container">
                            <h3>Applications</h3>
                            <?php if (empty($applicants)) { ?>
                                <div class="empty-state">No applications received yet</div>
                            <?php } else { ?>
                                <div class="applicants-grid">
                                    <?php foreach ($applicants as $applicant) { ?>
                                        <div class="applicant-tile">
                                            <div class="applicant-header">
                                                <h4><?= htmlspecialchars($applicant['username']) ?></h4>
                                                <span class="status-indicator"><?= htmlspecialchars($applicant['status']) ?></span>
                                            </div>
                                            <div class="applicant-details">
                                                <p><?= htmlspecialchars($applicant['cover_letter']) ?></p>
                                                <a href="<?= htmlspecialchars($applicant['resume_path']) ?>" target="_blank" class="doc-link">View Resume</a>
                                            </div>
                                            <div class="action-buttons">
                                                <form action="show_JobPost.php?id=<?= $jobPostId ?>" method="POST">
                                                    <input type="hidden" name="application_id" value="<?= $applicant['application_id'] ?>">
                                                    <button type="submit" name="action" value="hire" class="action-button success">Hire</button>
                                                    <button type="submit" name="action" value="reject" class="action-button danger">Reject</button>
                                                </form>
                                                <a href="messaging.php?receiver_id=<?= $applicant['applicant_id'] ?>" class="action-button">Message</a>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>

                    <a href="<?php echo ($_SESSION['user']['role'] == 'Applicant') ? 'applicant_Dashboard.php' : 'hr_Dashboard.php'; ?>" class="back-button">Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>


