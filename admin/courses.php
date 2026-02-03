<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$message = '';

// Handle Create Course (Grade)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_course'])) {
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    // Removed live_link and price from here, as they are now per-month
    $stmt = $pdo->prepare("INSERT INTO courses (title, description, created_by) VALUES (?, ?, ?)");
    if ($stmt->execute([$title, $desc, $_SESSION['user_id']])) {
        $message = "Class/Grade created successfully!";
    }
}

$courses = $pdo->query("SELECT * FROM courses ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Classes - ClassTrack</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container nav-flex">
            <a href="dashboard.php" class="logo">ClassTrack Admin</a>
            <nav>
                <a href="dashboard.php" class="btn btn-outline">Back to Dashboard</a>
            </nav>
        </div>
    </header>

    <main class="container" style="padding-top: 40px; padding-bottom: 50px;">
        <?php if($message): ?>
            <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 40px;">
            <!-- Course List -->
            <div>
                <h2>Your Classes / Grades</h2>
                <div style="display: grid; gap: 20px; margin-top: 20px;">
                    <?php foreach($courses as $course): ?>
                        <div class="card">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                                <a href="view_course.php?id=<?php echo $course['id']; ?>" class="btn btn-primary">Manage Months</a>
                            </div>
                            <p style="color: var(--text-muted); margin: 10px 0;"><?php echo htmlspecialchars($course['description']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Create Course Form -->
            <div>
                <div class="card" style="position: sticky; top: 100px;">
                    <h3>Create New Class</h3>
                    <form method="POST" style="margin-top: 15px;">
                        <input type="hidden" name="create_course" value="1">
                        <div class="form-group">
                            <label class="form-label">Class Name (e.g., Grade 10 Science)</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Create Class</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
