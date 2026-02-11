<?php
session_start();
require '../db_conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$month_id = $_GET['id'];
$month_result = $conn->query("SELECT * FROM months WHERE id = $month_id");
$month = $month_result->fetch_assoc();

if (!$month) {
    echo "Month not found!";
    exit;
}

$course_id = $month['course_id'];
$course = $conn->query("SELECT * FROM courses WHERE id = $course_id")->fetch_assoc();

// Handle Add Resource
if (isset($_POST['add_resource'])) {
    $title = $_POST['title'];
    $type = $_POST['type'];
    $url = $_POST['url'];

    $sql = "INSERT INTO resources (month_id, title, type, url) VALUES ('$month_id', '$title', '$type', '$url')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Resource added!'); window.location.href='view_month.php?id=$month_id';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

// Handle Enroll Student (Mark as Paid)
if (isset($_POST['enroll_student'])) {
    $student_id = $_POST['student_id'];

    $sql = "INSERT INTO enrollments (student_id, month_id, is_paid) VALUES ('$student_id', '$month_id', 1)";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Student enrolled successfully!'); window.location.href='view_month.php?id=$month_id';</script>";
    } else {
        // likely duplicate key
        echo "<script>alert('Student already enrolled or Error: " . $conn->error . "');</script>";
    }
}

// Handle Update Live Link
if (isset($_POST['update_link'])) {
    $live_link = $_POST['live_link'];
    $conn->query("UPDATE months SET live_link='$live_link' WHERE id=$month_id");
    echo "<script>alert('Link updated!'); window.location.href='view_month.php?id=$month_id';</script>";
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>Manage <?php echo $month['name']; ?> - ClassTrack</title>
    <link rel="stylesheet" href="../../css/style.css" />
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
          <a href="view_course.php?id=<?php echo $course_id; ?>" class="btn btn-outline">Back to Course</a>
        </nav>
      </div>
    </header>

    <main class="container" style="padding-top: 40px; padding-bottom: 50px">
      <h1><?php echo $course['title']; ?>: <?php echo $month['name'] . " " . $month['year']; ?></h1>

      <div
        style="
          display: grid;
          grid-template-columns: 2fr 1fr;
          gap: 40px;
          margin-top: 30px;
        "
      >
        <!-- Left Column: Resources & Students -->
        <div>
          <!-- Enrolled Students -->
          <div class="card mb-4">
            <h3>Enrolled Students (Paid)</h3>
            <table
              style="width: 100%; margin-top: 15px; border-collapse: collapse"
            >
              <thead style="background: #f1f5f9">
                <tr>
                  <th style="padding: 10px; text-align: left">Name</th>
                  <th style="padding: 10px; text-align: left">Email</th>
                  <th style="padding: 10px; text-align: left">Status</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $enrolled_sql = "SELECT users.full_name, users.email FROM enrollments JOIN users ON enrollments.student_id = users.id WHERE enrollments.month_id = $month_id AND enrollments.is_paid = 1";
                $enrolled_result = $conn->query($enrolled_sql);
                while($student = $enrolled_result->fetch_assoc()) {
                ?>
                <tr style="border-bottom: 1px solid #eee">
                  <td style="padding: 10px"><?php echo $student['full_name']; ?></td>
                  <td style="padding: 10px"><?php echo $student['email']; ?></td>
                  <td
                    style="
                      padding: 10px;
                      color: var(--success);
                      font-weight: bold;
                    "
                  >
                    PAID
                  </td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>

          <!-- Resources List -->
          <div class="card">
            <h3>Resources</h3>
            <ul style="margin-top: 15px">
              <?php
              $res_result = $conn->query("SELECT * FROM resources WHERE month_id = $month_id");
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
                <span>[<?php echo ucfirst($res['type']); ?>] <?php echo $res['title']; ?></span>
                <a href="<?php echo $res['url']; ?>" target="_blank" style="color: var(--primary)"
                  >Link</a
                >
              </li>
              <?php } ?>
            </ul>
          </div>
        </div>

        <!-- Right Column: Actions -->
        <div style="display: flex; flex-direction: column; gap: 20px">
          <!-- Enroll Student Form -->
          <div class="card">
            <h3>Enroll Student (Mark as Paid)</h3>
            <form action="view_month.php?id=<?php echo $month_id; ?>" method="POST" style="margin-top: 15px">
              <input type="hidden" name="enroll_student" value="1">
              <div class="form-group">
                <label class="form-label">Select Student</label>
                <select name="student_id" class="form-control" required>
                  <option value="">Choose...</option>
                  <?php
                  // Get students NOT enrolled in this month
                  $all_students = $conn->query("SELECT * FROM users WHERE role='student' AND id NOT IN (SELECT student_id FROM enrollments WHERE month_id=$month_id)");
                  while($s = $all_students->fetch_assoc()) {
                      echo "<option value='".$s['id']."'>".$s['full_name']." (".$s['email'].")</option>";
                  }
                  ?>
                </select>
              </div>
              <button
                type="submit"
                class="btn btn-primary"
                style="width: 100%; background: var(--success)"
              >
                Mark as Paid & Enroll
              </button>
            </form>
          </div>

          <!-- Add Resource Form -->
          <div class="card">
            <h3>Add Resource</h3>
            <form action="view_month.php?id=<?php echo $month_id; ?>" method="POST" style="margin-top: 15px">
              <input type="hidden" name="add_resource" value="1">
              <div class="form-group">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" required />
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
                <input type="text" name="url" class="form-control" required />
              </div>
              <button type="submit" class="btn btn-primary" style="width: 100%">
                Upload Resource
              </button>
            </form>
          </div>

          <!-- Update Live Link Form -->
          <div class="card">
            <h3>Update Live Link</h3>
            <form action="view_month.php?id=<?php echo $month_id; ?>" method="POST" style="margin-top: 15px">
              <input type="hidden" name="update_link" value="1">
              <div class="form-group">
                <label class="form-label">Link URL</label>
                <input
                  type="text"
                  name="live_link"
                  class="form-control"
                  value="<?php echo $month['live_link']; ?>"
                />
              </div>
              <button type="submit" class="btn btn-outline" style="width: 100%">
                Update Link
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
