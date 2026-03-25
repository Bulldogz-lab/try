<?php
// process/admin-process/guest_action.php
include '../../includes/session.php';
include '../../includes/db.php';
header('Content-Type: application/json');

if ($_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = (int) ($_POST['user_id'] ?? 0);
$action = $_POST['action'] ?? '';

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid user.']);
    exit;
}

if ($action === 'blacklist') {
    mysqli_query($conn, "UPDATE users SET is_blacklisted=1 WHERE user_id=$user_id");
    echo json_encode(['success' => true, 'message' => 'Guest has been blocked.']);
} elseif ($action === 'unblacklist') {
    mysqli_query($conn, "UPDATE users SET is_blacklisted=0 WHERE user_id=$user_id");
    echo json_encode(['success' => true, 'message' => 'Guest has been unblocked.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
}