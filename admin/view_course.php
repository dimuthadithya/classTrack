<?php
session_start();
require '../db_conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$course_id = $_GET['id'];
$course_result = $conn->query("SELECT * FROM courses WHERE id = $course_id");
$course = $course_result->fetch_assoc();

if (!$course) {
    echo "Course not found!";
    exit;
}

// Handle Add Month
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $year = $_POST['year'];
    $fee = $_POST['fee'];
    $live_link = $_POST['live_link'];

    $sql = "INSERT INTO months (course_id, name, year, fee, live_link) VALUES ('$course_id', '$name', '$year', '$fee', '$live_link')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Month added successfully!'); window.location.href='view_course.php?id=$course_id';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>Manage <?php echo $course['title']; ?> - ClassTrack</title>
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
          <a href="courses.php" class="btn btn-outline">Back to Classes</a>
        </nav>
      </div>
    </header>

    <main class="container" style="padding-top: 40px">
      <h1 class="mb-4"><?php echo $course['title']; ?>: Months</h1>

      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 40px">
        <!-- Month List -->
        <div>
          <div style="display: grid; gap: 20px">
            <?php
            $months_result = $conn->query("SELECT * FROM months WHERE course_id = $course_id ORDER BY id DESC");
            while($month = $months_result->fetch_assoc()) {
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
                <a href="view_month.php?id=<?php echo $month['id']; ?>" class="btn btn-primary"
                  >Manage Content & Students</a
                >
              </div>
            </div>
            <?php } ?>
          </div>
        </div>

        <!-- Add Month Form -->
        <div>
          <div class="card" style="position: sticky; top: 100px">
            <h3>Add New Month</h3>
            <form action="view_course.php?id=<?php echo $course_id; ?>" method="POST" style="margin-top: 15px">
              <div class="form-group">
                <label class="form-label">Month Name (e.g., March)</label>
                <input type="text" name="name" class="form-control" required />
              </div>
              <div class="form-group">
                <label class="form-label">Year</label>
                <input
                  type="number"
                  name="year"
                  class="form-control"
                  value="2026"
                  required
                />
              </div>
              <div class="form-group">
                <label class="form-label">Monthly Fee</label>
                <input
                  type="number"
                  step="0.01"
                  name="fee"
                  class="form-control"
                  required
                />
              </div>
              <div class="form-group">
                <label class="form-label">Live Link (Optional)</label>
                <input type="text" name="live_link" class="form-control" />
              </div>
              <button type="submit" class="btn btn-primary" style="width: 100%">
                Add Month
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
