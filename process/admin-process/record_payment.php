<?php
// process/admin-process/record_payment.php
include '../../includes/session.php';
include '../../includes/db.php';

header('Content-Type: application/json');

if ($_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? 'record';
$booking_id = (int) ($_POST['booking_id'] ?? 0);

if (!$booking_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid booking.']);
    exit;
}

$bk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM bookings WHERE booking_id=$booking_id"));
if (!$bk) {
    echo json_encode(['success' => false, 'message' => 'Booking not found.']);
    exit;
}

if ($action === 'mark_paid') {
    $sql = "UPDATE bookings
            SET status='completed', payment_method=COALESCE(payment_method,'Cash'), paid_at=NOW()
            WHERE booking_id=$booking_id";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'message' => 'Booking marked as paid.']);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }
    exit;
}

$amount = (float) ($_POST['amount'] ?? 0);
$method = mysqli_real_escape_string($conn, trim($_POST['method'] ?? 'Cash'));
$date = mysqli_real_escape_string($conn, trim($_POST['date'] ?? date('Y-m-d')));
$ref = mysqli_real_escape_string($conn, trim($_POST['ref'] ?? ''));
$notes = mysqli_real_escape_string($conn, trim($_POST['notes'] ?? ''));

if ($amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Amount must be greater than 0.']);
    exit;
}

$sql = "UPDATE bookings
        SET status='completed',
            payment_method='$method',
            paid_at='$date',
            payment_ref='$ref',
            payment_notes='$notes'
        WHERE booking_id=$booking_id";

if (mysqli_query($conn, $sql)) {
    echo json_encode(['success' => true, 'message' => 'Payment recorded successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
}