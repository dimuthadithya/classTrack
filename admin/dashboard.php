<?php
session_start();
require '../db_conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get counts
$student_count = $conn->query("SELECT COUNT(*) as count FROM users WHERE role='student'")->fetch_assoc()['count'];
$course_count = $conn->query("SELECT COUNT(*) as count FROM courses")->fetch_assoc()['count'];
$revenue = $conn->query("SELECT SUM(months.fee) as total FROM enrollments JOIN months ON enrollments.month_id = months.id WHERE enrollments.is_paid=1")->fetch_assoc()['total'];
$revenue = $revenue ? number_format($revenue, 2) : "0.00";
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>Admin Dashboard - Tuition Class Management System</title>
    <link rel="stylesheet" href="../css/style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
  </head>
  <body>
    <header>
      <div class="container nav-flex">
        <a href="dashboard.php" class="logo"
          >Tuition Class Management System Admin</a
        >
        <nav>
          <a href="../logout.php" class="btn btn-outline">Logout</a>
        </nav>
      </div>
    </header>

    <main class="container" style="padding-top: 40px">
      <h1>Welcome, <?php echo $_SESSION['full_name']; ?></h1>

      <div class="grid-3" style="margin-top: 30px">
        <!-- Stats Cards -->
        <div class="card text-center">
          <i
            class="fas fa-users"
            style="font-size: 2rem; color: var(--primary)"
          ></i>
          <h3 style="margin: 10px 0"><?php echo $student_count; ?></h3>
          <p>Total Students</p>
        </div>
        <div class="card text-center">
          <i
            class="fas fa-book"
            style="font-size: 2rem; color: var(--secondary)"
          ></i>
          <h3 style="margin: 10px 0"><?php echo $course_count; ?></h3>
          <p>Active Classes</p>
        </div>
        <div class="card text-center">
          <i
            class="fas fa-dollar-sign"
            style="font-size: 2rem; color: var(--success)"
          ></i>
          <h3 style="margin: 10px 0">$<?php echo $revenue; ?></h3>
          <p>Total Revenue</p>
        </div>
      </div>

      <h2 style="margin-top: 40px">Quick Actions</h2>
      <div class="grid-3" style="margin-top: 20px">
        <a
          href="courses.php"
          class="card"
          style="
            text-decoration: none;
            color: inherit;
            transition: transform 0.2s;
          "
        >
          <div style="display: flex; align-items: center; gap: 15px">
            <div
              style="
                background: #e0f2fe;
                padding: 10px;
                border-radius: 50%;
                color: var(--primary);
              "
            >
              <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div>
              <h3 style="font-size: 1.1rem">Manage Classes</h3>
              <p style="font-size: 0.9rem; color: var(--text-muted)">
                Create flow, add months, uploads
              </p>
            </div>
          </div>
        </a>

        <a
          href="users.php"
          class="card"
          style="
            text-decoration: none;
            color: inherit;
            transition: transform 0.2s;
          "
        >
          <div style="display: flex; align-items: center; gap: 15px">
            <div
              style="
                background: #f0fdf4;
                padding: 10px;
                border-radius: 50%;
                color: var(--success);
              "
            >
              <i class="fas fa-user-graduate"></i>
            </div>
            <div>
              <h3 style="font-size: 1.1rem">Manage Students</h3>
              <p style="font-size: 0.9rem; color: var(--text-muted)">
                View list, enrollments
              </p>
            </div>
          </div>
        </a>

        <!-- Removed settings/reports as per scope, or kept as placeholder -->
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
