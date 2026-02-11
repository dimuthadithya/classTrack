<?php
session_start();
require '../db_conn.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $conn->query("DELETE FROM users WHERE id=$id");
    echo "<script>alert('User deleted successfully!'); window.location.href='users.php';</script>";
}

// Handle Add User
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Storing cleartext as requested
    $role = $_POST['role'];

    $check = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($check->num_rows > 0) {
        echo "<script>alert('Email already exists!');</script>";
    } else {
        $sql = "INSERT INTO users (full_name, email, password, role) VALUES ('$full_name', '$email', '$password', '$role')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('User added successfully!'); window.location.href='users.php';</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>Manage Users - Tuition Class Management System</title>
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
          <a href="dashboard.php" class="btn btn-outline">Back to Dashboard</a>
        </nav>
      </div>
    </header>

    <main class="container" style="padding-top: 40px">
      <div style="display: flex; gap: 40px">
        <!-- User List -->
        <div style="flex: 2">
          <h2>All Users</h2>
          <div
            class="card"
            style="margin-top: 20px; padding: 0; overflow: hidden"
          >
            <table style="width: 100%; border-collapse: collapse">
              <thead style="background: #f1f5f9">
                <tr>
                  <th style="padding: 15px; text-align: left">Name</th>
                  <th style="padding: 15px; text-align: left">Email</th>
                  <th style="padding: 15px; text-align: left">Role</th>
                  <th style="padding: 15px; text-align: left">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $result = $conn->query("SELECT * FROM users ORDER BY id DESC");
                while($row = $result->fetch_assoc()) {
                ?>
                <tr style="border-bottom: 1px solid #e2e8f0">
                  <td style="padding: 15px"><?php echo $row['full_name']; ?></td>
                  <td style="padding: 15px"><?php echo $row['email']; ?></td>
                  <td style="padding: 15px">
                    <?php if($row['role'] == 'admin') { ?>
                    <span
                      style="
                        padding: 4px 8px;
                        border-radius: 4px;
                        background: #dbeafe;
                        color: #1e40af;
                        font-size: 0.85rem;
                        font-weight: 600;
                      "
                      >Admin</span
                    >
                    <?php } else { ?>
                    <span
                      style="
                        padding: 4px 8px;
                        border-radius: 4px;
                        background: #f0fdf4;
                        color: #15803d;
                        font-size: 0.85rem;
                        font-weight: 600;
                      "
                      >Student</span
                    >
                    <?php } ?>
                  </td>
                  <td style="padding: 15px">
                    <div style="display: flex; gap: 5px">
                      <!-- Edit can be implemented similarly, skipping for simplicity as instructed "add/delete" usually enough for simple -->
                      <a
                        href="users.php?delete_id=<?php echo $row['id']; ?>"
                        onclick="return confirm('Are you sure you want to delete this user?');"
                        class="btn"
                        style="
                          background-color: var(--danger);
                          color: white;
                          padding: 0.5rem 0.8rem;
                          text-decoration: none;
                        "
                        title="Delete"
                      >
                        <i class="fas fa-trash"></i>
                      </a>
                    </div>
                  </td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Add User Form -->
        <div style="flex: 1">
          <div class="card" style="position: sticky; top: 100px">
            <h3>Add New User</h3>
            <form action="users.php" method="POST">
              <div class="form-group">
                <label class="form-label">Full Name</label>
                <input
                  type="text"
                  name="full_name"
                  class="form-control"
                  required
                />
              </div>
              <div class="form-group">
                <label class="form-label">Email</label>
                <input
                  type="email"
                  name="email"
                  class="form-control"
                  required
                />
              </div>
              <div class="form-group">
                <label class="form-label">Password</label>
                <input
                  type="password"
                  name="password"
                  class="form-control"
                  required
                />
              </div>
              <div class="form-group">
                <label class="form-label">Role</label>
                <select name="role" class="form-control">
                  <option value="student">Student</option>
                  <option value="admin">Admin</option>
                </select>
              </div>
              <button type="submit" class="btn btn-primary" style="width: 100%">
                <i class="fas fa-plus-circle"></i> Create User
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
