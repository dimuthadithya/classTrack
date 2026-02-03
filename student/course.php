<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$course_id = $_GET['id'] ?? 0;

// Fetch Course
$stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch();

if (!$course) die("Course not found");

// Fetch Months & Payment Status
// LEFT JOIN enrollments to see if THIS student has paid for specific months
$query = "
    SELECT m.*, e.payment_status 
    FROM course_months m 
    LEFT JOIN enrollments e ON m.id = e.month_id AND e.student_id = ? 
    WHERE m.course_id = ? 
    ORDER BY m.year DESC, m.id DESC
";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id, $course_id]);
$months = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($course['title']); ?> - ClassTrack</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container nav-flex">
            <a href="dashboard.php" class="logo">ClassTrack Student</a>
            <nav>
                <a href="dashboard.php" class="btn btn-outline">Back to Classes</a>
            </nav>
        </div>
    </header>

    <main class="container" style="padding-top: 40px;">
        <h1 class="mb-4"><?php echo htmlspecialchars($course['title']); ?>: Study Modules</h1>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
            <?php foreach($months as $month): ?>
                <div class="card" style="border: 1px solid <?php echo ($month['payment_status'] == 'paid') ? 'var(--success)' : '#e2e8f0'; ?>;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <h3><?php echo htmlspecialchars($month['name']); ?></h3>
                            <span style="display: inline-block; background: #e2e8f0; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem; margin-top: 5px;">
                                <?php echo $month['year']; ?>
                            </span>
                        </div>
                        <?php if($month['payment_status'] == 'paid'): ?>
                            <i class="fas fa-check-circle" style="color: var(--success); font-size: 1.5rem;"></i>
                        <?php else: ?>
                            <i class="fas fa-lock" style="color: var(--text-muted); font-size: 1.5rem;"></i>
                        <?php endif; ?>
                    </div>
                    
                    <p style="margin: 15px 0; font-weight: bold;">Fee: $<?php echo $month['fee']; ?></p>

                    <div style="margin-top: 10px;">
                        <?php if($month['payment_status'] == 'paid'): ?>
                            <a href="month.php?id=<?php echo $month['id']; ?>" class="btn btn-primary" style="width: 100%; text-align: center; background: var(--success);">
                                Access Content
                            </a>
                        <?php else: ?>
                            <button class="btn" style="width: 100%; background: #fee2e2; color: #b91c1c; cursor: pointer;" onclick="alert('Please contact admin to make payment for this month.')">
                                Pay to Unlock
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>
