<?php
session_start();
require '../db_conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: ../login.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$user_result = $conn->query("SELECT * FROM users WHERE id = $student_id");
$user = $user_result->fetch_assoc();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>My Profile - ClassTrack</title>
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
          <a href="dashboard.php" class="btn btn-outline">Back to Dashboard</a>
        </nav>
      </div>
    </header>

    <main class="container" style="padding-top: 40px">
      <div class="card" style="max-width: 600px; margin: 0 auto;">
        <h2 style="text-align: center;">My Profile</h2>
        
        <div style="margin-top: 30px; display: flex; flex-direction: column; gap: 20px;">
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" class="form-control" value="<?php echo $user['full_name']; ?>" readonly disabled>
            </div>
            
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control" value="<?php echo $user['email']; ?>" readonly disabled>
            </div>
            
            <div class="form-group">
                <label class="form-label">Role</label>
                <input type="text" class="form-control" value="<?php echo ucfirst($user['role']); ?>" readonly disabled>
            </div>

            <div class="alert" style="background: #f0fdf4; padding: 15px; border-radius: 8px; color: #15803d; border: 1px solid #bbf7d0;">
                <i class="fas fa-info-circle"></i> To update your profile details or password, please contact the administrator.
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
