<?php
// pages/admin/payments_ajax.php
include '../../includes/session.php';
include '../../includes/db.php';

if ($_SESSION['role'] !== 'admin') {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Unauthorized.'];
    header('Location: payments.php'); exit;
}

$action   = $_POST['action']   ?? '';
$redirect = $_POST['redirect'] ?? 'payments.php';

function flash(string $type, string $msg, string $redirect): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
    header('Location: ' . $redirect); exit;
}

// ── ADD ───────────────────────────────────────────────────
if ($action === 'add') {
    $booking_id     = (int)($_POST['booking_id']     ?? 0);
    $payment_date   = trim($_POST['payment_date']    ?? '');
    $amount_paid    = (float)($_POST['amount_paid']  ?? 0);
    $payment_method = trim($_POST['payment_method']  ?? '');
    $payment_status = trim($_POST['payment_status']  ?? 'paid');
    $notes          = trim($_POST['notes']           ?? '');

    if (!$booking_id || !$payment_date || $amount_paid <= 0) {
        flash('error', 'Booking, date and amount are required.', $redirect);
    }

    $dateEsc   = mysqli_real_escape_string($conn, $payment_date);
    $methodEsc = mysqli_real_escape_string($conn, $payment_method);
    $statusEsc = mysqli_real_escape_string($conn, $payment_status);
    $notesEsc  = mysqli_real_escape_string($conn, $notes);

    $sql = "INSERT INTO payments (booking_id, payment_date, amount_paid, payment_method, payment_status, notes, created_at)
            VALUES ($booking_id, '$dateEsc', $amount_paid, '$methodEsc', '$statusEsc', '$notesEsc', NOW())";

    if (mysqli_query($conn, $sql)) {
        flash('success', 'Payment recorded successfully.', $redirect);
    } else {
        flash('error', 'Database error: ' . mysqli_error($conn), $redirect);
    }
}

// ── EDIT ──────────────────────────────────────────────────
if ($action === 'edit') {
    $payment_id     = (int)($_POST['payment_id']     ?? 0);
    $booking_id     = (int)($_POST['booking_id']     ?? 0);
    $payment_date   = trim($_POST['payment_date']    ?? '');
    $amount_paid    = (float)($_POST['amount_paid']  ?? 0);
    $payment_method = trim($_POST['payment_method']  ?? '');
    $payment_status = trim($_POST['payment_status']  ?? 'paid');
    $notes          = trim($_POST['notes']           ?? '');

    if (!$payment_id || !$booking_id || !$payment_date || $amount_paid <= 0) {
        flash('error', 'All required fields must be filled.', $redirect);
    }

    $dateEsc   = mysqli_real_escape_string($conn, $payment_date);
    $methodEsc = mysqli_real_escape_string($conn, $payment_method);
    $statusEsc = mysqli_real_escape_string($conn, $payment_status);
    $notesEsc  = mysqli_real_escape_string($conn, $notes);

    $sql = "UPDATE payments
            SET booking_id     = $booking_id,
                payment_date   = '$dateEsc',
                amount_paid    = $amount_paid,
                payment_method = '$methodEsc',
                payment_status = '$statusEsc',
                notes          = '$notesEsc'
            WHERE payment_id = $payment_id";

    if (mysqli_query($conn, $sql)) {
        flash('success', 'Payment updated successfully.', $redirect);
    } else {
        flash('error', 'Database error: ' . mysqli_error($conn), $redirect);
    }
}

// ── DELETE ────────────────────────────────────────────────
if ($action === 'delete') {
    $payment_id = (int)($_POST['payment_id'] ?? 0);
    if (!$payment_id) {
        flash('error', 'Invalid payment ID.', $redirect);
    }

    if (mysqli_query($conn, "DELETE FROM payments WHERE payment_id = $payment_id")) {
        flash('success', 'Payment record deleted.', $redirect);
    } else {
        flash('error', 'Database error: ' . mysqli_error($conn), $redirect);
    }
}

flash('error', 'Unknown action.', $redirect);