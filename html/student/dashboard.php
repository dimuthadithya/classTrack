<?php
session_start();
require '../db_conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: ../login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>Student Dashboard - Tuition Class Management System</title>
    <link rel="stylesheet" href="../../css/style.css" />
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
          <a href="profile.php" class="btn btn-outline"
            ><i class="fas fa-user"></i
          ></a>
          <a href="../logout.php" class="btn btn-outline">Logout</a>
        </nav>
      </div>
    </header>

    <main class="container" style="padding-top: 40px">
      <h1>Welcome, <?php echo $_SESSION['full_name']; ?></h1>

      <h2 style="margin-top: 30px">All Available Classes</h2>
      <div style="display: grid; gap: 20px; margin-top: 20px">
        <?php
        // Show all courses
        $result = $conn->query("SELECT * FROM courses ORDER BY id DESC");
        while($row = $result->fetch_assoc()) {
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
              <h3 style="margin-bottom: 5px"><?php echo $row['title']; ?></h3>
              <p style="color: var(--text-muted)"><?php echo $row['description']; ?></p>
            </div>
            <a href="course.php?id=<?php echo $row['id']; ?>" class="btn btn-primary"
              >View Content</a
            >
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
