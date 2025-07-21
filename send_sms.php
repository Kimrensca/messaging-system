<?php
session_start();
require 'config.php';

require_once 'vendor/autoload.php';

use AfricasTalking\SDK\AfricasTalking;

// Check token
if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
    die("Invalid token.");
}

$user_id = $_SESSION['user_id'];
$message = $_POST['message'];
$recipients = explode(',', $_POST['recipients']);

// Validation function for Kenyan numbers
function validateKenyaNumber($number) {
    $number = trim($number);
    $number = str_replace([' ', '-', '(', ')'], '', $number);

    if (preg_match('/^0\d{9}$/', $number)) {
        $number = '+254' . substr($number, 1);
    }

    if (preg_match('/^254\d{9}$/', $number)) {
        $number = '+' . $number;
    }

    if (preg_match('/^\+2547\d{8}$/', $number)) {
        return $number;
    }

    return false;
}

$validNumbers = [];
$invalidNumbers = [];

foreach ($recipients as $recipient) {
    $validated = validateKenyaNumber($recipient);
    if ($validated) {
        $validNumbers[] = $validated;
    } else {
        $invalidNumbers[] = trim($recipient);
    }
}

// Show error page if there are invalid numbers
if (!empty($invalidNumbers)) {
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Invalid Numbers</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container mt-5">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">Invalid Phone Numbers</h4>
                </div>
                <div class="card-body">
                    <p>The following numbers are invalid and the message was <strong>not sent</strong>:</p>
                    <ul class="list-group mb-3">
                        <?php foreach ($invalidNumbers as $num): ?>
                            <li class="list-group-item text-danger fw-bold"><?= htmlspecialchars($num) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
                </div>
            </div>
        </div>
    </body>
    </html>

    <?php
    exit;
}

// Africa's Talking credentials
$username = "sandbox"; // or your live username when you upgrade
$apiKey = "atsk_04351b370b774f401cd3c49d15267ef760f52d261bb3eafac6cd7ddbcd25b310fed6e71a"; // replace this with your actual API key

// Initialize SDK
$AT = new AfricasTalking($username, $apiKey);

// Get SMS service
$sms = $AT->sms();

$recipients_list = [];

foreach ($validNumbers as $recipient) {
    $recipients_list[] = $recipient;

    try {
        $result = $sms->send([
            'to'      => $recipient,
            'message' => $message
        ]);

        $recipientsData = $result->data->SMSMessageData->Recipients ?? [];

        if (!empty($recipientsData)) {
            $status = $recipientsData[0]->status ?? 'Unknown';
            $cost = $recipientsData[0]->cost ?? '0';
            $messageId = $recipientsData[0]->messageId ?? 'None';
        } else {
            $status = 'No response';
            $cost = '0';
            $messageId = 'None';
        }

        $stmt = $pdo->prepare(
            "INSERT INTO messages (user_id, recipient, message_text, status, cost, message_id) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$user_id, $recipient, $message, $status, $cost, $messageId]);

    } catch (Exception $e) {
        $stmt = $pdo->prepare(
            "INSERT INTO messages (user_id, recipient, message_text, status, cost, message_id) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$user_id, $recipient, $message, 'Error: ' . $e->getMessage(), '0', 'None']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SMS Sent</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <span class="navbar-brand">Messaging System</span>
        <div class="d-flex">
            <a href="dashboard.php" class="btn btn-outline-light">Dashboard</a>
            <a href="logout.php" class="btn btn-outline-light ms-2">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">Messages Queued Successfully</h4>
        </div>
        <div class="card-body">
            <p class="mb-3">Your message has been queued for the following recipients:</p>

            <ul class="list-group mb-3">
                <?php foreach ($recipients_list as $recipient): ?>
                    <li class="list-group-item">
                        <?= htmlspecialchars($recipient) ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="mb-3">
                <strong>Message Content:</strong>
                <div class="border p-3 bg-light">
                    <?= nl2br(htmlspecialchars($message)) ?>
                </div>
            </div>

            <a href="dashboard.php" class="btn btn-success">Back to Dashboard</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
