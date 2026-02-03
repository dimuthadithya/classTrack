<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$month_id = $_GET['id'] ?? 0;

// Verify Access & Fetch Month Details
$query = "
    SELECT m.*, c.title as course_title 
    FROM course_months m 
    JOIN courses c ON m.course_id = c.id
    JOIN enrollments e ON m.id = e.month_id 
    WHERE m.id = ? AND e.student_id = ? AND e.payment_status = 'paid'
";
$stmt = $pdo->prepare($query);
$stmt->execute([$month_id, $user_id]);
$month = $stmt->fetch();

if (!$month) {
    die("Access Denied: You have not paid for this month.");
}

// Fetch Resources
$resStmt = $pdo->prepare("SELECT * FROM resources WHERE month_id = ? ORDER BY uploaded_at DESC");
$resStmt->execute([$month_id]);
$resources = $resStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($month['name']); ?> - ClassTrack</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container nav-flex">
            <a href="dashboard.php" class="logo">ClassTrack Student</a>
            <nav>
                <a href="course.php?id=<?php echo $month['course_id']; ?>" class="btn btn-outline">Back to Modules</a>
            </nav>
        </div>
    </header>

    <main class="container" style="padding-top: 40px;">
        <div class="card mb-4" style="border-left: 5px solid var(--primary);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h2 style="color: var(--text-muted); font-size: 1.2rem;"><?php echo htmlspecialchars($month['course_title']); ?></h2>
                    <h1 style="margin-top: 5px;"><?php echo htmlspecialchars($month['name'] . ' ' . $month['year']); ?></h1>
                </div>
                <?php if(!empty($month['live_link'])): ?>
                    <a href="<?php echo htmlspecialchars($month['live_link']); ?>" target="_blank" class="btn btn-primary" style="background-color: #ef4444;">
                        <i class="fas fa-video"></i> Join Live Class
                    </a>
                <?php else: ?>
                    <button class="btn" disabled style="background: #e2e8f0; color: #64748b;">No Live Link</button>
                <?php endif; ?>
            </div>
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
