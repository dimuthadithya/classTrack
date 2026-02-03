<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$message = '';

// Handle Add User
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Simple validation
    if ($name && $email && $password) {
        // Storing plain text password as requested
        try {
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $password, $role]);
            $message = "User added successfully!";
        } catch (PDOException $e) {
            $message = "Error: Email likely already exists.";
        }
    }
}

// Fetch Users
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - ClassTrack</title>
    <link rel="stylesheet" href="../css/style.css">
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

    <main class="container" style="padding-top: 40px;">
        <div style="display: flex; gap: 40px;">
            <!-- User List -->
            <div style="flex: 2;">
                <h2>All Users</h2>
                <div class="card" style="margin-top: 20px; padding: 0; overflow: hidden;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead style="background: #f1f5f9;">
                            <tr>
                                <th style="padding: 15px; text-align: left;">Name</th>
                                <th style="padding: 15px; text-align: left;">Email</th>
                                <th style="padding: 15px; text-align: left;">Role</th>
                                <th style="padding: 15px; text-align: left;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($users as $user): ?>
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 15px;"><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td style="padding: 15px;"><?php echo htmlspecialchars($user['email']); ?></td>
                                <td style="padding: 15px;">
                                    <span style="padding: 4px 8px; border-radius: 4px; background: <?php echo $user['role'] == 'admin' ? '#dbeafe' : '#f0fdf4'; ?>; color: <?php echo $user['role'] == 'admin' ? '#1e40af' : '#15803d'; ?>; font-size: 0.85rem; font-weight: 600;">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td style="padding: 15px;">
                                    <?php if($user['id'] != $_SESSION['user_id']): ?>
                                        <!-- Placeholder for delete/edit -->
                                        <button disabled style="opacity: 0.5;">Edit</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add User Form -->
            <div style="flex: 1;">
                <div class="card" style="position: sticky; top: 100px;">
                    <h3>Add New User</h3>
                    <?php if($message): ?>
                        <div style="color: green; margin-bottom: 10px;"><?php echo $message; ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-control">
                                <option value="student">Student</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <button type="submit" name="add_user" class="btn btn-primary" style="width: 100%;">Create User</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
