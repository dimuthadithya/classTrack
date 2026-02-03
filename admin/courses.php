<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$message = '';

// Handle Create Course
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_course'])) {
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $link = trim($_POST['live_link']);
    $price = $_POST['price'] ?? 0;

    $stmt = $pdo->prepare("INSERT INTO courses (title, description, live_link, price, created_by) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$title, $desc, $link, $price, $_SESSION['user_id']])) {
        $message = "Course created successfully!";
    }
}

// Handle Enrollment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['enroll_student'])) {
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];
    $payment_status = $_POST['payment_status'];

    // Check if already enrolled
    $check = $pdo->prepare("SELECT id FROM enrollments WHERE student_id = ? AND course_id = ?");
    $check->execute([$student_id, $course_id]);
    
    if ($check->rowCount() > 0) {
        $message = "Student already enrolled in this course.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO enrollments (student_id, course_id, payment_status) VALUES (?, ?, ?)");
        if ($stmt->execute([$student_id, $course_id, $payment_status])) {
            $message = "Student enrolled successfully!";
        }
    }
}

// Fetch Data
$courses = $pdo->query("SELECT * FROM courses ORDER BY created_at DESC")->fetchAll();
$students = $pdo->query("SELECT id, full_name, email FROM users WHERE role = 'student'")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Courses - ClassTrack</title>
    <link rel="stylesheet" href="../css/style.css">
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
            <!-- Course List & Enrollment -->
            <div>
                <h2>Your Courses</h2>
                <div style="display: grid; gap: 20px; margin-top: 20px;">
                    <?php foreach($courses as $course): ?>
                        <div class="card">
                            <div style="display: flex; justify-content: space-between;">
                                <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                                <span style="font-weight: bold; color: var(--success);">$<?php echo htmlspecialchars($course['price']); ?></span>
                            </div>
                            <p style="color: var(--text-muted); margin: 10px 0;"><?php echo htmlspecialchars($course['description']); ?></p>
                            
                            <!-- Enrollment Form for this Course -->
                            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e2e8f0;">
                                <h4>Enroll Student</h4>
                                <form method="POST" style="display: flex; gap: 10px; margin-top: 10px;">
                                    <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                    <select name="student_id" class="form-control" required style="flex: 2;">
                                        <option value="">Select Student</option>
                                        <?php foreach($students as $student): ?>
                                            <option value="<?php echo $student['id']; ?>">
                                                <?php echo htmlspecialchars($student['full_name']); ?> (<?php echo htmlspecialchars($student['email']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <select name="payment_status" class="form-control" style="flex: 1;">
                                        <option value="pending">Pending</option>
                                        <option value="paid">Paid</option>
                                    </select>
                                    <button type="submit" name="enroll_student" class="btn btn-primary" style="padding: 0.5rem 1rem;">Enroll</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Create Course Form -->
            <div>
                <div class="card" style="position: sticky; top: 100px;">
                    <h3>Create New Course</h3>
                    <form method="POST" style="margin-top: 15px;">
                        <input type="hidden" name="create_course" value="1">
                        <div class="form-group">
                            <label class="form-label">Course Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Live Class Link (e.g., Zoom)</label>
                            <input type="text" name="live_link" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Price (Optional)</label>
                            <input type="number" step="0.01" name="price" class="form-control" value="0.00">
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Create Course</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
