<?php
header('Content-Type: application/json');
include '../../includes/session.php';
include '../../includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$bookingId = (int) ($_POST['booking_id'] ?? 0);
$newStatus = trim($_POST['status'] ?? '');
$allowed = ['confirmed', 'cancelled', 'completed', 'active'];

if (!$bookingId || !in_array($newStatus, $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$bRes = mysqli_query($conn, "SELECT booking_id, unit_id, status FROM bookings WHERE booking_id = $bookingId LIMIT 1");
$booking = mysqli_fetch_assoc($bRes);

if (!$booking) {
    echo json_encode(['success' => false, 'message' => 'Booking not found.']);
    exit;
}

mysqli_begin_transaction($conn);

try {
    $statusEsc = mysqli_real_escape_string($conn, $newStatus);

    if (!mysqli_query($conn, "UPDATE bookings SET status = '$statusEsc' WHERE booking_id = $bookingId")) {
        throw new Exception('Failed to update booking: ' . mysqli_error($conn));
    }
    
    $unitId = (int) $booking['unit_id'];
    $unitStatus = match ($newStatus) {
        'confirmed', 'active' => 'occupied',
        'cancelled' => 'vacant',
        'completed' => 'vacant',
        default => null
    };

    if ($unitStatus) {
        $unitStatusEsc = mysqli_real_escape_string($conn, $unitStatus);
        if (!mysqli_query($conn, "UPDATE units SET status = '$unitStatusEsc' WHERE unit_id = $unitId")) {
            throw new Exception('Failed to update unit status: ' . mysqli_error($conn));
        }
    }

    mysqli_commit($conn);

    $labels = [
        'confirmed' => 'Booking confirmed successfully.',
        'cancelled' => 'Booking has been cancelled. Unit is now vacant.',
        'completed' => 'Booking marked as completed. Unit is now vacant.',
        'active' => 'Booking is now active.',
    ];

    echo json_encode([
        'success' => true,
        'message' => $labels[$newStatus] ?? 'Status updated.',
        'new_status' => $newStatus,
    ]);

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}