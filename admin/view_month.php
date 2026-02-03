<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$month_id = $_GET['id'] ?? 0;
$message = '';

// Fetch Month Details
$stmt = $pdo->prepare("SELECT m.*, c.title as course_title, c.id as course_id FROM course_months m JOIN courses c ON m.course_id = c.id WHERE m.id = ?");
$stmt->execute([$month_id]);
$month = $stmt->fetch();

if (!$month) die("Month not found");

// Handle Add Resource
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_resource'])) {
    $title = trim($_POST['title']);
    $type = $_POST['type'];
    $url = trim($_POST['url']);
    
    $stmt = $pdo->prepare("INSERT INTO resources (month_id, title, type, url) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$month_id, $title, $type, $url])) {
        $message = "Resource added!";
    }
}

// Handle Update Live Link
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_link'])) {
    $link = trim($_POST['live_link']);
    $stmt = $pdo->prepare("UPDATE course_months SET live_link = ? WHERE id = ?");
    $stmt->execute([$link, $month_id]);
    $month['live_link'] = $link; // Update local var
    $message = "Live link updated!";
}

// Handle Enrollment (Mark Paid)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['enroll_student'])) {
    $student_id = $_POST['student_id'];
    
    // Check if duplicate
    $check = $pdo->prepare("SELECT id FROM enrollments WHERE student_id = ? AND month_id = ?");
    $check->execute([$student_id, $month_id]);
    
    if ($check->rowCount() > 0) {
        $message = "Student already enrolled in this month.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO enrollments (student_id, month_id, payment_status) VALUES (?, ?, 'paid')");
        if ($stmt->execute([$student_id, $month_id])) {
            $message = "Student enrolled (Marked Paid)!";
        }
    }
}

// Fetch Resources
$resources = $pdo->query("SELECT * FROM resources WHERE month_id = $month_id ORDER BY uploaded_at DESC")->fetchAll();

// Fetch Enrolled Students for this Month
$enrolled = $pdo->query("
    SELECT u.full_name, u.email, e.payment_status, e.enrolled_at 
    FROM enrollments e 
    JOIN users u ON e.student_id = u.id 
    WHERE e.month_id = $month_id
")->fetchAll();

// Fetch All Students (for dropdown)
$all_students = $pdo->query("SELECT * FROM users WHERE role = 'student'")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage <?php echo htmlspecialchars($month['name']); ?> - ClassTrack</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <div class="container nav-flex">
            <a href="dashboard.php" class="logo">ClassTrack Admin</a>
            <nav>
                <a href="view_course.php?id=<?php echo $month['course_id']; ?>" class="btn btn-outline">Back to Course</a>
            </nav>
        </div>
    </header>

    <main class="container" style="padding-top: 40px; padding-bottom: 50px;">
        <h1><?php echo htmlspecialchars($month['course_title']); ?>: <?php echo htmlspecialchars($month['name'] . ' ' . $month['year']); ?></h1>
        
        <?php if($message): ?>
            <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 40px; margin-top: 30px;">
            
            <!-- Left Column: Resources & Students -->
            <div>
                <!-- Enrolled Students -->
                <div class="card mb-4">
                    <h3>Enrolled Students (Paid)</h3>
                    <?php if(empty($enrolled)): ?>
                        <p style="color: var(--text-muted); margin-top: 10px;">No students paid for this month yet.</p>
                    <?php else: ?>
                        <table style="width: 100%; margin-top: 15px; border-collapse: collapse;">
                            <thead style="background: #f1f5f9;">
                                <tr>
                                    <th style="padding: 10px; text-align: left;">Name</th>
                                    <th style="padding: 10px; text-align: left;">Email</th>
                                    <th style="padding: 10px; text-align: left;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($enrolled as $st): ?>
                                    <tr style="border-bottom: 1px solid #eee;">
                                        <td style="padding: 10px;"><?php echo htmlspecialchars($st['full_name']); ?></td>
                                        <td style="padding: 10px;"><?php echo htmlspecialchars($st['email']); ?></td>
                                        <td style="padding: 10px; color: var(--success); font-weight: bold;">PAID</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <!-- Resources List -->
                <div class="card">
                    <h3>Resources</h3>
                    <?php if(empty($resources)): ?>
                        <p style="color: var(--text-muted); margin-top: 10px;">No resources uploaded.</p>
                    <?php else: ?>
                        <ul style="margin-top: 15px;">
                            <?php foreach($resources as $res): ?>
                                <li style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee;">
                                    <span>[<?php echo ucfirst($res['type']); ?>] <?php echo htmlspecialchars($res['title']); ?></span>
                                    <a href="<?php echo htmlspecialchars($res['url']); ?>" target="_blank" style="color: var(--primary);">Link</a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column: Actions -->
            <div style="display: flex; flex-direction: column; gap: 20px;">
                
                <!-- Enroll Student Form -->
                <div class="card">
                    <h3>Enroll Student (Mark as Paid)</h3>
                    <form method="POST" style="margin-top: 15px;">
                        <input type="hidden" name="enroll_student" value="1">
                        <div class="form-group">
                            <label class="form-label">Select Student</label>
                            <select name="student_id" class="form-control" required>
                                <option value="">Choose...</option>
                                <?php foreach($all_students as $std): ?>
                                    <option value="<?php echo $std['id']; ?>"><?php echo htmlspecialchars($std['full_name']); ?> (<?php echo htmlspecialchars($std['email']); ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%; background: var(--success);">Mark as Paid & Enroll</button>
                    </form>
                </div>

                <!-- Add Resource Form -->
                <div class="card">
                    <h3>Add Resource</h3>
                    <form method="POST" style="margin-top: 15px;">
                        <input type="hidden" name="add_resource" value="1">
                        <div class="form-group">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-control">
                                <option value="recording">Recording</option>
                                <option value="document">Document</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">URL</label>
                            <input type="text" name="url" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Upload Resource</button>
                    </form>
                </div>

                <!-- Update Live Link Form -->
                <div class="card">
                    <h3>Update Live Link</h3>
                    <form method="POST" style="margin-top: 15px;">
                        <input type="hidden" name="update_link" value="1">
                        <div class="form-group">
                            <label class="form-label">Link URL</label>
                            <input type="text" name="live_link" class="form-control" value="<?php echo htmlspecialchars($month['live_link'] ?? ''); ?>">
                        </div>
                        <button type="submit" class="btn btn-outline" style="width: 100%;">Update Link</button>
                    </form>
                </div>

            </div>
        </div>
    </main>
</body>
</html>
