<?php
session_start();
require '../db_conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: ../login.php");
    exit();
}

$month_id = $_GET['id'];
$student_id = $_SESSION['user_id'];

// Check enrollment again
$enroll_check = $conn->query("SELECT * FROM enrollments WHERE student_id = $student_id AND month_id = $month_id AND is_paid = 1");
if ($enroll_check->num_rows == 0) {
    echo "<script>alert('Access Denied: You are not enrolled in this month.'); window.location.href='dashboard.php';</script>";
    exit;
}

$month = $conn->query("SELECT * FROM months WHERE id = $month_id")->fetch_assoc();
$course = $conn->query("SELECT * FROM courses WHERE id = " . $month['course_id'])->fetch_assoc();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title><?php echo $month['name']; ?> Content - ClassTrack</title>
    <link rel="stylesheet" href="../../css/style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
  </head>
  <body>
    <header>
      <div class="container nav-flex">
        <a href="dashboard.html" class="logo">ClassTrack Student</a>
        <nav>
          <a href="course.php?id=<?php echo $course['id']; ?>" class="btn btn-outline">Back to Course</a>
        </nav>
      </div>
    </header>

    <main class="container" style="padding-top: 40px">
      <h1><?php echo $course['title']; ?>: <?php echo $month['name'] . " " . $month['year']; ?></h1>

      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 40px; margin-top: 30px;">
        
        <!-- Resources List -->
        <div>
           <div class="card">
            <h3>Class Resources</h3>
            <ul style="margin-top: 15px">
              <?php
              $res_result = $conn->query("SELECT * FROM resources WHERE month_id = $month_id");
              if ($res_result->num_rows > 0) {
                  while($res = $res_result->fetch_assoc()) {
              ?>
              <li
                style="
                  display: flex;
                  justify-content: space-between;
                  padding: 10px 0;
                  border-bottom: 1px solid #eee;
                "
              >
                <span>
                    <i class="fas fa-<?php echo ($res['type'] == 'recording' ? 'video' : 'file-alt'); ?>" style="margin-right: 10px; color: var(--primary);"></i>
                    <?php echo $res['title']; ?>
                </span>
                <a href="<?php echo $res['url']; ?>" target="_blank" class="btn btn-primary" style="padding: 0.2rem 0.5rem; font-size: 0.8rem;">
                    View
                </a>
              </li>
              <?php 
                  }
              } else {
                  echo "<p style='color: var(--text-muted); padding: 10px 0;'>No resources uploaded yet.</p>";
              }
              ?>
            </ul>
          </div>
        </div>

        <!-- Live Class Info -->
        <div>
            <div class="card" style="position: sticky; top: 100px; text-align: center;">
                <h3>Live Just for You!</h3>
                <i class="fas fa-broadcast-tower" style="font-size: 3rem; color: var(--danger); margin: 20px 0;"></i>
                
                <?php if (!empty($month['live_link'])) { ?>
                    <p>Join the live session using the link below:</p>
                    <a href="<?php echo $month['live_link']; ?>" target="_blank" class="btn btn-primary" style="width: 100%; margin-top: 10px;">
                        Join Live Class
                    </a>
                <?php } else { ?>
                    <p style="color: var(--text-muted);">No live link available currently.</p>
                <?php } ?>
            </div>
        </div>

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
