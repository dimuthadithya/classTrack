<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch Enrolled Courses
// Only show if payment_status is 'paid' OR show with 'Locked' status if pending?
// User said: "user can view the course based onthey pay or not" -> implies access control. 
// I will show all enrolled courses but lock access if pending.
$query = "
    SELECT c.*, e.payment_status 
    FROM courses c 
    JOIN enrollments e ON c.id = e.course_id 
    WHERE e.student_id = ?
";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$my_courses = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard - ClassTrack</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container nav-flex">
            <a href="dashboard.php" class="logo">ClassTrack Student</a>
            <nav>
                <a href="dashboard.php" class="btn btn-outline" style="margin-right: 10px;">My Courses</a>
                <a href="profile.php" class="btn btn-outline" style="margin-right: 10px;">Profile</a>
                <a href="../logout.php" class="btn btn-primary">Logout</a>
            </nav>
        </div>
    </header>

    <main class="container" style="padding-top: 40px;">
        <h1 class="mb-4">My Courses</h1>

        <?php if(empty($my_courses)): ?>
            <div class="card text-center" style="padding: 50px;">
                <p>You are not enrolled in any courses yet.</p>
                <p style="color: var(--text-muted);">Please contact the administrator.</p>
            </div>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px;">
                <?php foreach($my_courses as $course): ?>
                    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div style="background: #e2e8f0; height: 150px; border-radius: 8px; margin-bottom: 15px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-book" style="font-size: 3rem; color: #94a3b8;"></i>
                            </div>
                            <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                            <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 5px;">
                                <?php echo htmlspecialchars(substr($course['description'], 0, 100)) . '...'; ?>
                            </p>
                        </div>

                        <div style="margin-top: 20px;">
                            <?php if($course['payment_status'] == 'paid'): ?>
                                <a href="course.php?id=<?php echo $course['id']; ?>" class="btn btn-primary" style="width: 100%; text-align: center;">View Course</a>
                            <?php else: ?>
                                <button class="btn" style="width: 100%; background: #e2e8f0; color: #64748b; cursor: not-allowed;" disabled>
                                    <i class="fas fa-lock"></i> Payment Pending
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
