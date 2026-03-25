<?php
// ============================================================
//  process/user-process/cancel_booking.php
//  Allows a logged-in user to cancel their own booking
// ============================================================
ob_start();
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) session_start();

$dbPaths = [
    '../../../includes/db.php',
    '../../includes/db.php',
    __DIR__ . '/../../../includes/db.php',
];
foreach ($dbPaths as $path) {
    if (file_exists($path)) { include $path; break; }
}
ob_clean();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

$userId    = (int) $_SESSION['user_id'];
$bookingId = (int) ($_POST['booking_id'] ?? 0);

if (!$bookingId) {
    echo json_encode(['success' => false, 'message' => 'Invalid booking.']);
    exit;
}

// Fetch booking — must belong to this user
$res     = mysqli_query($conn, "SELECT booking_id, unit_id, status, user_id FROM bookings WHERE booking_id = $bookingId LIMIT 1");
$booking = mysqli_fetch_assoc($res);

if (!$booking || (int)$booking['user_id'] !== $userId) {
    echo json_encode(['success' => false, 'message' => 'Booking not found.']);
    exit;
}
if (in_array($booking['status'], ['cancelled', 'completed'])) {
    echo json_encode(['success' => false, 'message' => 'This booking is already ' . $booking['status'] . '.']);
    exit;
}

// Transaction: cancel booking + set unit back to vacant
mysqli_begin_transaction($conn);
try {
    if (!mysqli_query($conn, "UPDATE bookings SET status = 'cancelled' WHERE booking_id = $bookingId")) {
        throw new Exception(mysqli_error($conn));
    }
    $unitId = (int) $booking['unit_id'];
    if (!mysqli_query($conn, "UPDATE units SET status = 'vacant' WHERE unit_id = $unitId")) {
        throw new Exception(mysqli_error($conn));
    }
    mysqli_commit($conn);
    echo json_encode(['success' => true, 'message' => 'Your booking has been cancelled. The unit is now available.']);
} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}