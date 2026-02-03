<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$course_id = $_GET['id'] ?? 0;

// Verify Access & Payment
$stmt = $pdo->prepare("SELECT * FROM enrollments WHERE student_id = ? AND course_id = ? AND payment_status = 'paid'");
$stmt->execute([$user_id, $course_id]);
if ($stmt->rowCount() == 0) {
    die("Access Denied: You are not enrolled or payment is pending.");
}

// Fetch Course Details
$courseStmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$courseStmt->execute([$course_id]);
$course = $courseStmt->fetch();

// Fetch Resources (In a real app, these would be in a resources table)
// For this MVP, we will simulate resources or fetch if you want a separate table.
// I created a resources table, so let's use it.
$resStmt = $pdo->prepare("SELECT * FROM resources WHERE course_id = ? ORDER BY uploaded_at DESC");
$resStmt->execute([$course_id]);
$resources = $resStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course: <?php echo htmlspecialchars($course['title']); ?> - ClassTrack</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container nav-flex">
            <a href="dashboard.php" class="logo">ClassTrack Student</a>
            <nav>
                <a href="dashboard.php" class="btn btn-outline">Back to Dashboard</a>
            </nav>
        </div>
    </header>

    <main class="container" style="padding-top: 40px;">
        <div class="card mb-4" style="border-left: 5px solid var(--primary);">
            <h1><?php echo htmlspecialchars($course['title']); ?></h1>
            <p style="margin-top: 10px; color: var(--text-muted);"><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>
            
            <?php if(!empty($course['live_link'])): ?>
                <div style="margin-top: 20px;">
                    <a href="<?php echo htmlspecialchars($course['live_link']); ?>" target="_blank" class="btn btn-primary" style="background-color: #ef4444;">
                        <i class="fas fa-video"></i> Join Live Class
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <!-- Recordings -->
            <div>
                <h2 class="mb-4">Class Recordings</h2>
                <div class="card">
                    <?php 
                    $hasRecordings = false;
                    foreach($resources as $res) {
                        if ($res['type'] == 'recording') {
                            $hasRecordings = true;
                            echo "<div style='padding: 10px 0; border-bottom: 1px solid #eee; display: flex; justify-content: space-between;'>";
                            echo "<span><i class='fas fa-play-circle' style='color: var(--danger); margin-right: 10px;'></i>" . htmlspecialchars($res['title']) . "</span>";
                            echo "<a href='" . htmlspecialchars($res['url']) . "' target='_blank' style='color: var(--primary);'>Watch</a>";
                            echo "</div>";
                        }
                    } 
                    if (!$hasRecordings) echo "<p style='color: var(--text-muted);'>No recordings available yet.</p>";
                    ?>
                </div>
            </div>

            <!-- Documents -->
            <div>
                <h2 class="mb-4">Study Materials</h2>
                <div class="card">
                    <?php 
                    $hasDocs = false;
                    foreach($resources as $res) {
                        if ($res['type'] == 'document') {
                            $hasDocs = true;
                            echo "<div style='padding: 10px 0; border-bottom: 1px solid #eee; display: flex; justify-content: space-between;'>";
                            echo "<span><i class='fas fa-file-alt' style='color: var(--primary); margin-right: 10px;'></i>" . htmlspecialchars($res['title']) . "</span>";
                            echo "<a href='" . htmlspecialchars($res['url']) . "' Download style='color: var(--primary);'>Download</a>";
                            echo "</div>";
                        }
                    } 
                    if (!$hasDocs) echo "<p style='color: var(--text-muted);'>No documents available yet.</p>";
                    ?>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
