
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require 'config.php';
require 'load_env.php';

if (!isset($_POST['email'])) {
    die('Email is required.');
}

$email = trim($_POST['email']);

// Check if user exists
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    echo "No account found with that email.";
    exit;
}

// Generate token and expiry
$token = bin2hex(random_bytes(32));
$expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

// Save to users table
$stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
$stmt->execute([$token, $expires, $email]);

// Send email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = $_ENV['MAIL_HOST'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['MAIL_USERNAME'];
    $mail->Password   = $_ENV['MAIL_PASSWORD'];
    $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];
    $mail->Port       = $_ENV['MAIL_PORT'];

    $mail->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_FROM_NAME']);
    $mail->addAddress($email);

    $reset_link = "http://localhost/test/messaging_system/reset_password.php?token=$token";

    $mail->isHTML(true);
    $mail->Subject = 'Password Reset Request';
    $mail->Body    = "Hi,<br><br>Click the link to reset your password:<br>
                      <a href='$reset_link'>$reset_link</a><br><br>
                      This link expires in 1 hour.";

    $mail->send();
    echo "✅ Password reset email sent!";
} catch (Exception $e) {
    echo "❌ Failed to send email. Mailer Error: {$mail->ErrorInfo}";
}
?>
