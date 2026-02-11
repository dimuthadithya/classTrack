<?php
session_start();
require 'db_conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Simple SQL query (vulnerable to injection but requested "simple")
    $sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['full_name'] = $row['full_name'];

        if ($row['role'] == 'admin') {
            echo "<script>alert('Login successful! Welcome Admin.'); window.location.href='admin/dashboard.php';</script>";
        } else {
            echo "<script>alert('Login successful! Welcome Student.'); window.location.href='student/dashboard.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid email or password!'); window.location.href='login.php';</script>";
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Tuition Class Management System</title>
    <link rel="stylesheet" href="css/style.css" />
  </head>
  <body
    style="
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
    "
  >
    <div class="card" style="width: 100%; max-width: 400px">
      <h2 class="text-center mb-4">Welcome Back</h2>

      <form action="login.php" method="POST">
        <div class="form-group">
          <label class="form-label" for="email">Email Address</label>
          <input
            type="email"
            id="email"
            name="email"
            class="form-control"
            required
            placeholder="admin@classtrack.com"
          />
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <input
            type="password"
            id="password"
            name="password"
            class="form-control"
            required
          />
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%">
          Login
        </button>
      </form>

      <div class="text-center mt-4">
        <p style="color: var(--text-muted); font-size: 0.9rem">
          Contact your administrator to register.
        </p>
        <p style="margin-top: 10px; font-size: 0.8rem">
          <a href="index.html">Back to Home</a>
        </p>
      </div>

      <div class="mt-4 text-center">
        <small style="display: block; color: var(--text-muted)"
          >Demo Login:</small
        >
        <p style="font-size: 0.8rem; color: var(--text-muted)">
          Admin: admin@classtrack.com / 12345<br>
          Student: student@classtrack.com / 12345
        </p>
      </div>
    </div>
  </body>
</html>
