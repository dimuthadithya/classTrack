<?php
session_start();
require_once 'includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT id, role, password, full_name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && $password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['full_name'];

            if ($user['role'] == 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: student/dashboard.php");
            }
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ClassTrack</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body style="display: flex; align-items: center; justify-content: center; min-height: 100vh;">
    
    <div class="card" style="width: 100%; max-width: 400px;">
        <h2 class="text-center mb-4">Welcome Back</h2>
        
        <?php if($error): ?>
            <div style="background: #fee2e2; color: #b91c1c; padding: 10px; border-radius: 8px; margin-bottom: 20px;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
        </form>
        
        <div class="text-center mt-4">
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                Contact your administrator to register.
            </p>
            <p style="margin-top: 10px; font-size: 0.8rem;">
                <a href="index.php">Back to Home</a>
            </p>
        </div>
    </div>

</body>
</html>
