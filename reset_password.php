<?php
require 'config.php';

$message = '';
$validToken = false;

if (!isset($_GET['token'])) {
    $message = "⚠️ Invalid request. No token provided.";
} else {
    $token = trim($_GET['token']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
        $message = "❌ Invalid token.";
    } elseif (strtotime($user['reset_expires']) < time()) {
        $message = "⏰ Token has expired.";
    } else {
        $validToken = true;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];

            if ($new_password !== $confirm_password) {
                $message = "❌ Passwords do not match.";
            } else {
                $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

                // Update password and clear reset token
                $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, reset_token = NULL, reset_expires = NULL WHERE email = ?");
                $stmt->execute([$hashedPassword, $user['email']]);

                $message = "✅ Password has been reset successfully!";
                $validToken = false;
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">Reset Your Password</h3>

                        <?php if (!empty($message)): ?>
                            <div class="alert alert-info text-center"><?= $message ?></div>
                        <?php endif; ?>

                        <?php if ($validToken): ?>
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">New Password</label>
                                    <input type="password" name="new_password" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Confirm Password</label>
                                    <input type="password" name="confirm_password" class="form-control" required>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Reset Password</button>
                                </div>
                            </form>
                        <?php endif; ?>

                        <div class="mt-3 text-center">
                            <a href="login.php">Back to Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
