<?php
// process/admin-process/process_checkin.php
include '../../includes/session.php';
include '../../includes/db.php';

header('Content-Type: application/json');

if ($_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$booking_id = (int)($_POST['booking_id'] ?? 0);
$action     = $_POST['action'] ?? '';

if (!$booking_id || !in_array($action, ['checkin', 'checkout'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$res = mysqli_query($conn, "SELECT * FROM bookings WHERE booking_id = $booking_id");
$booking = mysqli_fetch_assoc($res);

if (!$booking) {
    echo json_encode(['success' => false, 'message' => 'Booking not found.']);
    exit;
}

if ($action === 'checkin') {
    $sql = "UPDATE bookings SET status = 'active' WHERE booking_id = $booking_id";
    $extra = mysqli_query($conn, "UPDATE bookings SET checkin_status = 'done', checkin_actual = NOW() WHERE booking_id = $booking_id");
    $msg = "Guest checked in successfully.";
} else {
    $sql = "UPDATE bookings SET status = 'completed' WHERE booking_id = $booking_id";
    $extra = mysqli_query($conn, "UPDATE bookings SET checkout_status = 'done', checkout_actual = NOW() WHERE booking_id = $booking_id");
    $msg = "Guest checked out successfully.";
}

if (mysqli_query($conn, $sql)) {
    echo json_encode(['success' => true, 'message' => $msg]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
}