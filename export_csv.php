<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM messages WHERE user_id = ?");
$stmt->execute([$user_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="messages.csv"');

$output = fopen('php://output', 'w');

// Write header
fputcsv($output, array_keys($messages[0]));

// Write rows
foreach ($messages as $row) {
    fputcsv($output, $row);
}

fclose($output);
exit;
