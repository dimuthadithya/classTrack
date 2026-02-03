<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit;
}

// Fetch All Courses (Grades)
// In a pure tuition model, students might see all available grades to join.
$courses = $pdo->query("SELECT * FROM courses ORDER BY created_at DESC")->fetchAll();
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
                <a href="dashboard.php" class="btn btn-outline" style="margin-right: 10px;">My Classes</a>
                <a href="profile.php" class="btn btn-outline" style="margin-right: 10px;">Profile</a>
                <a href="../logout.php" class="btn btn-primary">Logout</a>
            </nav>
        </div>
    </header>

    <main class="container" style="padding-top: 40px;">
        <h1 class="mb-4">Available Classes / Grades</h1>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px;">
            <?php foreach($courses as $course): ?>
                <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div style="background: #e2e8f0; height: 150px; border-radius: 8px; margin-bottom: 15px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-graduation-cap" style="font-size: 3rem; color: #94a3b8;"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                        <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: 5px;">
                            <?php echo htmlspecialchars($course['description']); ?>
                        </p>
                    </div>

                    <div style="margin-top: 20px;">
                        <a href="course.php?id=<?php echo $course['id']; ?>" class="btn btn-primary" style="width: 100%; text-align: center;">View Months</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>
