<?php
session_start();
if ($_SESSION['user']['role'] != 'Applicant') {
    header("Location: index.php");
    exit();
}
include_once 'models.php';
$jobPosts = getJobPosts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="app-container">
        <div class="sidebar">
            <div class="brand">
                <div class="brand-logo">JH</div>
                <div class="brand-name">JobHub</div>
            </div>
            <div class="sidebar-menu">
                <div class="menu-item active">Dashboard</div>
                <a href="messaging.php" class="menu-item">Messages</a>
                <a href="logout.php" class="menu-item logout">Logout</a>
            </div>
        </div>

        <div class="main-content">
            <div class="content-header">
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user']['username']); ?></h1>
            </div>

            <div class="content-body">
                <div class="card job-listings">
                    <div class="card-header">
                        <h2>Available Positions</h2>
                    </div>

                    <?php if (empty($jobPosts)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">🔍</div>
                            <p class="empty-title">No Positions Available</p>
                            <p class="empty-desc">Check back later for new opportunities</p>
                        </div>
                    <?php else: ?>
                        <div class="job-grid">
                            <?php foreach ($jobPosts as $post): ?>
                                <div class="job-card">
                                    <h3 class="job-title"><?= htmlspecialchars($post['title']) ?></h3>
                                    <a href="show_JobPost.php?id=<?= $post['id'] ?>" class="btn-view">
                                        View Details
                                    </a>
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

