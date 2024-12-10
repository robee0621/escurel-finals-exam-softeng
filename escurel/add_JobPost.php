<?php
session_start();
if ($_SESSION['user']['role'] != 'HR') {
    header("Location: index.php");
    exit();
}
include_once 'handleForms.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    createJobPost($title, $description);
    header("Location: hr_Dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Job Post</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="app-container">
        <div class="content-wrapper">
            <div class="top-bar">
                <div class="page-info">
                    <h1 class="section-title">Create New Job Post</h1>
                </div>
                <div class="nav-actions">
                    <a href="hr_Dashboard.php" class="nav-link">
                        <span class="icon">←</span> Back to Dashboard
                    </a>
                </div>
            </div>

            <div class="form-container">
                <div class="card">
                    <form action="add_JobPost.php" method="POST" class="job-form">
                        <div class="input-group">
                            <label for="title" class="input-label">Position Title</label>
                            <input 
                                type="text" 
                                id="title" 
                                name="title" 
                                class="text-input" 
                                placeholder="Enter job position title"
                                required
                            >
                        </div>
                        
                        <div class="input-group">
                            <label for="description" class="input-label">Job Description</label>
                            <textarea 
                                id="description" 
                                name="description" 
                                class="text-area" 
                                placeholder="Enter detailed job description"
                                rows="6"
                                required
                            ></textarea>
                        </div>
                        
                        <div class="button-group">
                            <button type="submit" class="btn btn-primary">
                                Create Position
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>


