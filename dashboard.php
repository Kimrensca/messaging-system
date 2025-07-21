<?php
session_start();
require 'config.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Token already set during login
$user_id = $_SESSION['user_id'];
$token = $_SESSION['token'];

$query = "SELECT * FROM messages WHERE user_id = ?";
$params = [$user_id];

if (!empty($_GET['recipient'])) {
    $query .= " AND recipient LIKE ?";
    $params[] = "%" . $_GET['recipient'] . "%";
}

if (!empty($_GET['status'])) {
    $query .= " AND status = ?";
    $params[] = $_GET['status'];
}

if (!empty($_GET['date'])) {
    $query .= " AND DATE(created_at) = ?";
    $params[] = $_GET['date'];
}

$query .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$messages = $stmt->fetchAll();
// If the user has just sent a message, redirect to avoid resubmission
if (isset($_GET['sent'])) {
    header("Location: dashboard.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <span class="navbar-brand">Messaging System</span>
        <div class="d-flex">
            <a href="logout.php" class="btn btn-outline-light">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-5">

    <h2 class="mb-4">Welcome to Your Dashboard</h2>

    <?php if (isset($_GET['sent'])): ?>
      <div class="alert alert-success">
        Message(s) sent successfully!
      </div>
    <?php endif; ?>

    <div class="card shadow mb-5">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Send Bulk SMS</h5>
        </div>
        <div class="card-body">
            <form action="send_sms.php" method="post">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                <div class="mb-3">
                    <label for="message" class="form-label">Message</label>
                    <textarea class="form-control" id="message" name="message" rows="3" placeholder="Enter your message"></textarea>
                </div>

                <div class="mb-3">
                    <label for="recipients" class="form-label">Recipients</label>
                    <input type="text" class="form-control" id="recipients" name="recipients" placeholder="e.g. 2547XXXXXXX,2547YYYYYYY">
                    <div class="form-text">Separate multiple numbers with commas.</div>
                </div>

                <button type="submit" class="btn btn-success w-100">Send SMS</button>

            </form>

            <a href="export_csv.php" class="btn btn-success mb-3 mt-3">Export CSV</a>
            <a href="profile.php" class="btn btn-secondary mb-3 mt-3 ms-3">Edit Profile</a>

        </div>
    </div>

    <form method="get" class="row g-3 mb-4">
    <div class="col-md-3">
        <input type="text" name="recipient" class="form-control" placeholder="Search by recipient" value="<?= htmlspecialchars($_GET['recipient'] ?? '') ?>">
    </div>
    <div class="col-md-3">
        <select name="status" class="form-select">
            <option value="">All Statuses</option>
            <option value="Queued" <?= ($_GET['status'] ?? '') == 'Queued' ? 'selected' : '' ?>>Queued</option>
            <option value="Sent" <?= ($_GET['status'] ?? '') == 'Sent' ? 'selected' : '' ?>>Sent</option>
            <option value="Failed" <?= ($_GET['status'] ?? '') == 'Failed' ? 'selected' : '' ?>>Failed</option>
            <option value="Pending" <?= ($_GET['status'] ?? '') == 'Pending' ? 'selected' : '' ?>>Pending</option>
            <option value="Delivered" <?= ($_GET['status'] ?? '') == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
            <option value="Rejected" <?= ($_GET['status'] ?? '') == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
            <option value="Unknown" <?= ($_GET['status'] ?? '') == 'Unknown' ? 'selected' : '' ?>>Unknown</option>
            <option value="No response" <?= ($_GET['status'] ?? '') == 'No response' ? 'selected' : '' ?>>No response</option>
            <option value="Invalid" <?= ($_GET['status'] ?? '') == 'Invalid' ? 'selected' : '' ?>>Invalid</option>
            <option value="Success" <?= ($_GET['status'] ?? '') == 'Success' ? 'selected' : '' ?>>Success</option>
            <option value="InvalidPhoneNumber" <?= ($_GET['status'] ?? '') == 'InvalidPhoneNumber' ? 'selected' : '' ?>>InvalidPhoneNumber</option>
            <option value="Error" <?= ($_GET['status'] ?? '') == 'Error' ? 'selected' : '' ?>>Error</option>
        </select>
    </div>
    <div class="col-md-3">
        <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>">
    </div>
    <div class="col-md-3">
        <button type="submit" class="btn btn-primary">Search</button>
    </div>
</form>



    <h3>Messages Sent</h3>
    <?php if (!empty($messages)): ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>Recipient</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Cost</th>
                        <th>Message ID</th>
                        <th>Date Sent</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($messages as $message): ?>
                    <tr>
                        <td><?= htmlspecialchars($message['recipient']) ?></td>
                        <td><?= htmlspecialchars($message['message_text']) ?></td>
                        <td><?= htmlspecialchars($message['status'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($message['cost'] ?? '0') ?></td>
                        <td><?= htmlspecialchars($message['message_id'] ?? 'None') ?></td>
                        <td><?= htmlspecialchars($message['created_at'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-muted mt-3">No messages sent yet.</p>
    <?php endif; ?>

</div>

<!-- Bootstrap JS (optional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
