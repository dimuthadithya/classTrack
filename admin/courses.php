<?php
session_start();
require '../db_conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle Add Course
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];

    $sql = "INSERT INTO courses (title, description) VALUES ('$title', '$description')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Class added successfully!'); window.location.href='courses.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>Manage Classes - ClassTrack</title>
    <link rel="stylesheet" href="../css/style.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
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

    <main class="container" style="padding-top: 40px; padding-bottom: 50px">
      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 40px">
        <!-- Course List -->
        <div>
          <h2>Your Classes / Grades</h2>
          <div style="display: grid; gap: 20px; margin-top: 20px">
            <?php
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
                <h3><?php echo $row['title']; ?></h3>
                <a href="view_course.php?id=<?php echo $row['id']; ?>" class="btn btn-primary"
                  >Manage Months</a
                >
              </div>
              <p style="color: var(--text-muted); margin: 10px 0">
                <?php echo $row['description']; ?>
              </p>
            </div>
            <?php } ?>
          </div>
        </div>

        <!-- Create Course Form -->
        <div>
          <div class="card" style="position: sticky; top: 100px">
            <h3>Create New Class</h3>
            <form action="courses.php" method="POST" style="margin-top: 15px">
              <div class="form-group">
                <label class="form-label">Class Name</label>
                <input
                  type="text"
                  name="title"
                  class="form-control"
                  placeholder="e.g. Grade 10 English"
                  required
                />
              </div>
              <div class="form-group">
                <label class="form-label">Description</label>
                <textarea
                  name="description"
                  class="form-control"
                  rows="3"
                ></textarea>
              </div>
              <button type="submit" class="btn btn-primary" style="width: 100%">
                Create Class
              </button>
            </form>
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
