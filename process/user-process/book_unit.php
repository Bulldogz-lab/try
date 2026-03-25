<?php
// ============================================================
//  process/book_unit.php
//  Handles AJAX booking submission from the room modal
//  - Saves booking as 'pending'
//  - Marks unit as 'occupied'
//  - Prevents double-booking
//  - Returns JSON for SweetAlert handling in JS
// ============================================================
header('Content-Type: application/json');
include '../../includes/session.php';
include '../../includes/db.php';

// ── AUTH CHECK ───────────────────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to make a booking.']);
    exit;
}

// ── INPUT ────────────────────────────────────────────────────
$userId   = (int) $_SESSION['user_id'];
$unitId   = (int) ($_POST['unit_id']  ?? 0);
$checkin  = trim($_POST['checkin']  ?? '');
$checkout = trim($_POST['checkout'] ?? '');
$guests   = max(1, (int) ($_POST['guests'] ?? 1));

// ── VALIDATION ───────────────────────────────────────────────
if (!$unitId || !$checkin || !$checkout) {
    echo json_encode(['success' => false, 'message' => 'Missing required booking details.']);
    exit;
}

$dtIn  = DateTime::createFromFormat('Y-m-d', $checkin);
$dtOut = DateTime::createFromFormat('Y-m-d', $checkout);

if (!$dtIn || !$dtOut) {
    echo json_encode(['success' => false, 'message' => 'Invalid date format.']);
    exit;
}
if ($dtOut <= $dtIn) {
    echo json_encode(['success' => false, 'message' => 'Check-out must be after check-in.']);
    exit;
}
if ($dtIn < new DateTime('today')) {
    echo json_encode(['success' => false, 'message' => 'Check-in date cannot be in the past.']);
    exit;
}

$nights = $dtIn->diff($dtOut)->days;
if ($nights < 1) {
    echo json_encode(['success' => false, 'message' => 'Minimum stay is 1 night.']);
    exit;
}

// ── FETCH UNIT ───────────────────────────────────────────────
$unitSql = "
    SELECT u.unit_id, u.rent_amount, u.status, u.unit_name, u.unit_number,
           p.property_name
    FROM   units u
    LEFT JOIN properties p ON p.property_id = u.property_id
    WHERE  u.unit_id = $unitId
    LIMIT  1
";
$unitRes = mysqli_query($conn, $unitSql);
$unit    = mysqli_fetch_assoc($unitRes);

if (!$unit) {
    echo json_encode(['success' => false, 'message' => 'Unit not found.']);
    exit;
}
if ($unit['status'] !== 'vacant') {
    $reason = 'This unit is not available for booking.';
    if ($unit['status'] === 'occupied')    $reason = 'This unit is already occupied.';
    if ($unit['status'] === 'maintenance') $reason = 'This unit is currently under maintenance.';
    echo json_encode(['success' => false, 'message' => $reason]);
    exit;
}

// ── DOUBLE-BOOKING CHECK ─────────────────────────────────────
$checkinEsc  = mysqli_real_escape_string($conn, $checkin);
$checkoutEsc = mysqli_real_escape_string($conn, $checkout);

$conflictSql = "
    SELECT booking_id FROM bookings
    WHERE  unit_id = $unitId
      AND  status NOT IN ('cancelled')
      AND  checkin_date  < '$checkoutEsc'
      AND  checkout_date > '$checkinEsc'
    LIMIT 1
";
$conflictRes = mysqli_query($conn, $conflictSql);
if (mysqli_fetch_assoc($conflictRes)) {
    echo json_encode(['success' => false, 'message' => 'These dates are already booked. Please choose different dates.']);
    exit;
}

// ── GUEST LIMIT ──────────────────────────────────────────────
if ($guests > 10) {
    echo json_encode(['success' => false, 'message' => 'Maximum 10 guests allowed.']);
    exit;
}

// ── CALCULATE TOTAL ──────────────────────────────────────────
$totalAmount = $nights * (float) $unit['rent_amount'];

// ── GET OR CREATE TENANT RECORD ──────────────────────────────
$email    = mysqli_real_escape_string($conn, $_SESSION['email'] ?? '');
$fullName = mysqli_real_escape_string($conn, $_SESSION['name']  ?? '');

$tenantRes = mysqli_query($conn, "SELECT tenant_id FROM tenants WHERE email = '$email' LIMIT 1");
$tenant    = mysqli_fetch_assoc($tenantRes);

if (!$tenant) {
    mysqli_query($conn, "
        INSERT INTO tenants (full_name, email, move_in_date)
        VALUES ('$fullName', '$email', '$checkinEsc')
    ");
    $tenantId = mysqli_insert_id($conn);
} else {
    $tenantId = (int) $tenant['tenant_id'];
}

// ── TRANSACTION: INSERT BOOKING + MARK UNIT OCCUPIED ─────────
mysqli_begin_transaction($conn);

try {
    // 1. Insert booking as 'pending'
    $insertSql = "
        INSERT INTO bookings
            (unit_id, tenant_id, user_id, checkin_date, checkout_date, guests, total_amount, status)
        VALUES
            ($unitId, $tenantId, $userId, '$checkinEsc', '$checkoutEsc', $guests, $totalAmount, 'pending')
    ";
    if (!mysqli_query($conn, $insertSql)) {
        throw new Exception('Failed to create booking: ' . mysqli_error($conn));
    }
    $bookingId = mysqli_insert_id($conn);

    // 2. Mark unit as occupied immediately
    if (!mysqli_query($conn, "UPDATE units SET status = 'occupied' WHERE unit_id = $unitId")) {
        throw new Exception('Failed to update unit status: ' . mysqli_error($conn));
    }

    mysqli_commit($conn);

    // Build display name
    $unitDisplay = !empty($unit['unit_name'])
        ? $unit['unit_name']
        : (($unit['property_name'] ?? '') . ' — Unit ' . ($unit['unit_number'] ?? $unitId));

    echo json_encode([
        'success'      => true,
        'booking_id'   => $bookingId,
        'unit_name'    => $unitDisplay,
        'checkin'      => $dtIn->format('M j, Y'),
        'checkout'     => $dtOut->format('M j, Y'),
        'nights'       => $nights,
        'guests'       => $guests,
        'total_amount' => '₱' . number_format($totalAmount, 2),
        'status'       => 'pending',
        'message'      => 'Booking submitted successfully!',
    ]);

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}