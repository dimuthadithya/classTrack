<?php
session_start();
require '../db_conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: ../login.php");
    exit();
}

$course_id = $_GET['id'];
$student_id = $_SESSION['user_id'];

$course = $conn->query("SELECT * FROM courses WHERE id = $course_id")->fetch_assoc();

if (!$course) {
    echo "Course not found!";
    exit;
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title><?php echo $course['title']; ?> - ClassTrack</title>
    <link rel="stylesheet" href="../css/style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
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

    <main class="container" style="padding-top: 40px">
      <h1><?php echo $course['title']; ?></h1>
      <p style="color: var(--text-muted)"><?php echo $course['description']; ?></p>

      <h2 style="margin-top: 30px">Monthly Modules</h2>
      <div style="display: grid; gap: 20px; margin-top: 20px">
        <?php
        $months = $conn->query("SELECT * FROM months WHERE course_id = $course_id ORDER BY id DESC");
        while($month = $months->fetch_assoc()) {
            // Check enrollment
            $enroll_check = $conn->query("SELECT * FROM enrollments WHERE student_id = $student_id AND month_id = " . $month['id'] . " AND is_paid = 1");
            $is_enrolled = $enroll_check->num_rows > 0;
        ?>
        <div class="card">
          <div
            style="
              display: flex;
              justify-content: space-between;
              align-items: center;
            "
          >
            <div>
              <h3><?php echo $month['name'] . " " . $month['year']; ?></h3>
              <p style="color: var(--success); font-weight: bold">
                Fee: $<?php echo $month['fee']; ?>
              </p>
            </div>
            
            <?php if ($is_enrolled) { ?>
                <a href="month.php?id=<?php echo $month['id']; ?>" class="btn btn-success">
                    <i class="fas fa-check-circle"></i> Access Content
                </a>
            <?php } else { ?>
                 <!-- Simple "Contact Admin" or similar since no online payment gateway requested -->
                 <button class="btn" style="background: #ccc; cursor: not-allowed;" disabled>
                    Not Enrolled (Pay to Access)
                </button>
            <?php } ?>
          </div>
        </div>
        <?php } ?>
      </div>
    </main>
    <footer>
      <div class="container text-center">
        <p>&copy; 2026 Tuition Class Management System. All rights reserved.</p>
        <div style="margin-top: 20px; font-size: 0.9rem; color: #cbd5e1">
          <p><strong>Developed by:</strong> Dulmini Samadhi Murage</p>
          <p><strong>Student No:</strong> DIT/2025/04/141</p>
          <p>
            <strong>Course:</strong> Diploma in Information Technology (DIT 264)
          </p>
        </div>
      </div>
    </footer>
  </body>
</html>
