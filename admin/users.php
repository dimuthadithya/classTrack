<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$message = '';
$messageType = 'success';
$editUser = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle Add User
    if (isset($_POST['add_user'])) {
        $name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'student';

        if ($name !== '' && $email !== '' && $password !== '' && in_array($role, ['admin', 'student'], true)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $email, $password, $role]);
                $message = "User added successfully!";
            } catch (PDOException $e) {
                $message = "Error: Email already exists or invalid data.";
                $messageType = 'error';
            }
        } else {
            $message = "Please fill all required fields correctly.";
            $messageType = 'error';
        }
    }

    // Handle Update User
    if (isset($_POST['update_user'])) {
        $editId = (int)($_POST['edit_user_id'] ?? 0);
        $name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? 'student';
        $newPassword = $_POST['password'] ?? '';

        if ($editId <= 0 || $name === '' || $email === '' || !in_array($role, ['admin', 'student'], true)) {
            $message = "Invalid update request.";
            $messageType = 'error';
        } else {
            $stmt = $pdo->prepare("SELECT id, role FROM users WHERE id = ?");
            $stmt->execute([$editId]);
            $targetUser = $stmt->fetch();

            if (!$targetUser) {
                $message = "User not found.";
                $messageType = 'error';
            } else {
                // Prevent demoting the last admin account.
                if ($targetUser['role'] === 'admin' && $role !== 'admin') {
                    $adminCount = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
                    if ($adminCount <= 1) {
                        $message = "Cannot change role. System must keep at least one admin.";
                        $messageType = 'error';
                    }
                }

                if ($messageType !== 'error') {
                    try {
                        if ($newPassword !== '') {
                            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, role = ?, password = ? WHERE id = ?");
                            $stmt->execute([$name, $email, $role, $newPassword, $editId]);
                        } else {
                            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, role = ? WHERE id = ?");
                            $stmt->execute([$name, $email, $role, $editId]);
                        }
                        $message = "User updated successfully!";
                    } catch (PDOException $e) {
                        $message = "Update failed. Email may already exist.";
                        $messageType = 'error';
                    }
                }
            }
        }
    }

    // Handle Delete User
    if (isset($_POST['delete_user'])) {
        $deleteId = (int)($_POST['delete_user_id'] ?? 0);

        if ($deleteId <= 0) {
            $message = "Invalid delete request.";
            $messageType = 'error';
        } elseif ($deleteId === (int)$_SESSION['user_id']) {
            $message = "You cannot delete your own account.";
            $messageType = 'error';
        } else {
            $stmt = $pdo->prepare("SELECT id, role FROM users WHERE id = ?");
            $stmt->execute([$deleteId]);
            $targetUser = $stmt->fetch();

            if (!$targetUser) {
                $message = "User not found.";
                $messageType = 'error';
            } else {
                // Prevent deleting the last admin account.
                if ($targetUser['role'] === 'admin') {
                    $adminCount = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
                    if ($adminCount <= 1) {
                        $message = "Cannot delete the last admin user.";
                        $messageType = 'error';
                    }
                }

                if ($messageType !== 'error') {
                    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                    $stmt->execute([$deleteId]);
                    $message = "User deleted successfully!";
                }
            }
        }
    }
}

// Edit mode
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    if ($editId > 0) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$editId]);
        $editUser = $stmt->fetch();
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        <?php if ($message): ?>
            <div style="padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; background: <?php echo $messageType === 'error' ? '#fee2e2' : '#dcfce7'; ?>; color: <?php echo $messageType === 'error' ? '#b91c1c' : '#166534'; ?>;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

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
                            <?php foreach ($users as $user): ?>
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 15px;"><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td style="padding: 15px;"><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td style="padding: 15px;">
                                        <span style="padding: 4px 8px; border-radius: 4px; background: <?php echo $user['role'] == 'admin' ? '#dbeafe' : '#f0fdf4'; ?>; color: <?php echo $user['role'] == 'admin' ? '#1e40af' : '#15803d'; ?>; font-size: 0.85rem; font-weight: 600;">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 15px; display: flex; gap: 8px; align-items: center;">
                                        <a href="users.php?edit=<?php echo (int)$user['id']; ?>" class="btn btn-outline" style="padding: 6px 10px; font-size: 0.85rem;">
                                            <i class="fas fa-pen-to-square" style="margin-right: 6px;"></i>Edit
                                        </a>

                                        <?php if ((int)$user['id'] !== (int)$_SESSION['user_id']): ?>
                                            <form method="POST" onsubmit="return confirm('Delete this user? This action cannot be undone.');" style="margin: 0;">
                                                <input type="hidden" name="delete_user_id" value="<?php echo (int)$user['id']; ?>">
                                                <button type="submit" name="delete_user" class="btn" style="padding: 6px 10px; font-size: 0.85rem; background: #fee2e2; color: #b91c1c;">
                                                    <i class="fas fa-trash" style="margin-right: 6px;"></i>Delete
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span style="font-size: 0.8rem; color: #64748b;">Current User</span>
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
                    <?php if ($editUser): ?>
                        <h3>Edit User</h3>
                        <form method="POST">
                            <input type="hidden" name="edit_user_id" value="<?php echo (int)$editUser['id']; ?>">

                            <div class="form-group">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($editUser['full_name']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($editUser['email']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">New Password (optional)</label>
                                <input type="password" name="password" class="form-control">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-control">
                                    <option value="student" <?php echo $editUser['role'] === 'student' ? 'selected' : ''; ?>>Student</option>
                                    <option value="admin" <?php echo $editUser['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                </select>
                            </div>

                            <button type="submit" name="update_user" class="btn btn-primary" style="width: 100%;">Update User</button>
                            <a href="users.php" class="btn btn-outline" style="width: 100%; margin-top: 10px; text-align: center;">Cancel</a>
                        </form>
                    <?php else: ?>
                        <h3>Add New User</h3>
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
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</body>

</html>