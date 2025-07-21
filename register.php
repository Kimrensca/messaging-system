<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $existingUser = $stmt->fetch();

    if ($existingUser) {
        $error = "Email already registered. Please use a different email.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (name, phone, email, password_hash) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $phone, $email, $password]);

        header('Location: login.php?registered=1');
        exit;

    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white text-center">
                    <h4 class="mb-0">Register</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($error)) : ?>
                       <div class="alert alert-danger">
                          <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>



                    <form action="register.php" method="post">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="mb-3 position-relative">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <span class="position-absolute top-50 end-0 translate-middle-y me-3" style="cursor:pointer;" onclick="togglePassword('password', this)">👁️</span>
                        </div>

                        <div class="mb-3 position-relative">
                           <label for="confirm_password" class="form-label">Confirm Password</label>
                           <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                           <span class="position-absolute top-50 end-0 translate-middle-y me-3" style="cursor:pointer;" onclick="togglePassword('confirm_password', this)">👁️</span>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Register</button>
                    </form>

                    <div class="mt-3 text-center">
                        <a href="login.php">Already have an account? Login here</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS (optional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
function togglePassword(id, el) {
    const input = document.getElementById(id);
    const isHidden = input.getAttribute("type") === "password";
    input.setAttribute("type", isHidden ? "text" : "password");
    el.textContent = isHidden ? "🙈" : "👁️";
}
</script>

</body>
</html>
