<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit;
}

$message = '';
$user_id = $_SESSION['user_id'];

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $password = $_POST['password'];

    if (!empty($full_name)) {
        if (!empty($password)) {
            // Updating with plain text password
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, password = ? WHERE id = ?");
            $stmt->execute([$full_name, $password, $user_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ? WHERE id = ?");
            $stmt->execute([$full_name, $user_id]);
        }
        $_SESSION['name'] = $full_name;
        $message = "Profile updated successfully!";
    }
}

$user = $pdo->query("SELECT * FROM users WHERE id = $user_id")->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - ClassTrack</title>
    <link rel="stylesheet" href="../css/style.css">
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

    <main class="container" style="padding-top: 40px; max-width: 600px;">
        <h1 class="mb-4">My Profile</h1>
        
        <?php if($message): ?>
            <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="background: #f1f5f9; cursor: not-allowed;">
                    <small style="color: var(--text-muted);">Email cannot be changed. Contact admin for assistance.</small>
                </div>
                
                <hr style="margin: 20px 0; border: 0; border-top: 1px solid #e2e8f0;">
                
                <div class="form-group">
                    <label class="form-label">New Password (Leave blank to keep current)</label>
                    <input type="password" name="password" class="form-control">
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Update Profile</button>
            </form>
        </div>
    </main>
</body>
</html>
