<?php
session_start();
if ($_SESSION['user']['role'] != 'HR') {
    header("Location: index.php");
    exit();
}

include_once 'models.php';

// Fetch job posts
$jobPosts = getJobPosts();

// Handle the delete request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_job_post'])) {
    $jobPostId = $_POST['job_post_id'];
    deleteJobPost($jobPostId);
    header('Location: hr_Dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobConnect - HR Portal</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="app-container">
        <div class="sidebar">
            <div class="brand">
                <div class="brand-logo">JC</div>
                <div class="brand-name">JobConnect</div>
            </div>
            <div class="sidebar-menu">
                <div class="menu-item active">Dashboard</div>
                <a href="logout.php" class="menu-item logout">Logout</a>
            </div>
        </div>

        <div class="main-content">
            <div class="content-header">
                <h1>HR Dashboard</h1>
                <div class="action-button">
                    <form action="add_JobPost.php" method="GET">
                        <button type="submit" class="btn-create">
                            <span class="icon">+</span> New Job Post
                        </button>
                    </form>
                </div>
            </div>

            <div class="content-body">
                <div class="card job-listings">
                    <div class="card-header">
                        <h2>Active Job Listings</h2>
                    </div>

                    <?php if (empty($jobPosts)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">📝</div>
                            <p class="empty-title">No Job Posts Yet</p>
                            <p class="empty-desc">Create your first job post to start hiring</p>
                        </div>
                    <?php else: ?>
                        <div class="job-list">
                            <?php foreach ($jobPosts as $post): ?>
                                <div class="job-item" id="job-<?= $post['id'] ?>">
                                    <div class="job-info">
                                        <h3 class="job-title"><?= htmlspecialchars($post['title']) ?></h3>
                                    </div>
                                    <div class="job-actions">
                                        <a href="show_JobPost.php?id=<?= $post['id'] ?>" class="btn-action view">
                                            View Details
                                        </a>
                                        <a href="messaging.php?job_id=<?= $post['id'] ?>" class="btn-action message">
                                            Messages
                                        </a>
                                        <form action="hr_Dashboard.php" method="POST" class="delete-form">
                                            <input type="hidden" name="job_post_id" value="<?= $post['id'] ?>">
                                            <button type="submit" name="delete_job_post" 
                                                    class="btn-action delete"
                                                    onclick="return confirm('Are you sure you want to delete this job post?');">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

