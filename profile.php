<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    if (!empty($_POST['password'])) {
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET name=?, phone=?, email=?, password_hash=? WHERE id=?");
        $stmt->execute([$name, $phone, $email, $password_hash, $_SESSION['user_id']]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET name=?, phone=?, email=? WHERE id=?");
        $stmt->execute([$name, $phone, $email, $_SESSION['user_id']]);
    }

    header("Location: profile.php?updated=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="mb-4">My Profile</h1>

        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success">
                Profile updated successfully!
            </div>
        <?php endif; ?>

        <form method="post" class="bg-white p-4 rounded shadow-sm">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    class="form-control"
                    value="<?= htmlspecialchars($user['name']) ?>"
                    required
                >
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input
                    type="text"
                    name="phone"
                    id="phone"
                    class="form-control"
                    value="<?= htmlspecialchars($user['phone']) ?>"
                    required
                >
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    class="form-control"
                    value="<?= htmlspecialchars($user['email']) ?>"
                    required
                >
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-control"
                    placeholder="Leave blank to keep current password"
                >
            </div>

            <button type="submit" class="btn btn-primary">Update Profile</button>
            <a href="dashboard.php" class="btn btn-secondary ms-2">Back to Dashboard</a>
        </form>
    </div>
</body>
</html>
