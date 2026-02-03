<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$course_id = $_GET['id'] ?? 0;
$message = '';

// Fetch Course
$stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch();

if (!$course) die("Course not found");

// Handle Create Month
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_month'])) {
    $name = trim($_POST['name']);
    $year = $_POST['year'];
    $link = trim($_POST['live_link']);
    $fee = $_POST['fee'];

    $stmt = $pdo->prepare("INSERT INTO course_months (course_id, name, year, live_link, fee) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$course_id, $name, $year, $link, $fee])) {
        $message = "Month added successfully!";
    }
}

// Fetch Months
$monthStmt = $pdo->prepare("SELECT * FROM course_months WHERE course_id = ? ORDER BY year DESC, id DESC");
$monthStmt->execute([$course_id]);
$months = $monthStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage <?php echo htmlspecialchars($course['title']); ?> - ClassTrack</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <div class="container nav-flex">
            <a href="dashboard.php" class="logo">ClassTrack Admin</a>
            <nav>
                <a href="courses.php" class="btn btn-outline">Back to Classes</a>
            </nav>
        </div>
    </header>

    <main class="container" style="padding-top: 40px;">
        <h1 class="mb-4"><?php echo htmlspecialchars($course['title']); ?>: Months</h1>
        
        <?php if($message): ?>
            <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 40px;">
            <!-- Month List -->
            <div>
                <div style="display: grid; gap: 20px;">
                    <?php foreach($months as $month): ?>
                        <div class="card">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <h3><?php echo htmlspecialchars($month['name']) . ' ' . $month['year']; ?></h3>
                                    <p style="color: var(--success); font-weight: bold;">Fee: $<?php echo $month['fee']; ?></p>
                                </div>
                                <a href="view_month.php?id=<?php echo $month['id']; ?>" class="btn btn-primary">Manage Content & Students</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Add Month Form -->
            <div>
                <div class="card" style="position: sticky; top: 100px;">
                    <h3>Add New Month</h3>
                    <form method="POST" style="margin-top: 15px;">
                        <input type="hidden" name="create_month" value="1">
                        <div class="form-group">
                            <label class="form-label">Month Name (e.g., January)</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Year</label>
                            <input type="number" name="year" class="form-control" value="<?php echo date('Y'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Monthly Fee</label>
                            <input type="number" step="0.01" name="fee" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Live Link (Optional)</label>
                            <input type="text" name="live_link" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Add Month</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
