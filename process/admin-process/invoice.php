<?php

include '../../includes/session.php';
include '../../includes/db.php';

header('Content-Type: application/json');

// ── Helpers ───────────────────────────────────────────────────────────────────
function respond(bool $success, string $message = ''): void
{
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

// ── Input ─────────────────────────────────────────────────────────────────────
$action = trim($_POST['action'] ?? '');
$id = (int) ($_POST['id'] ?? 0);

if (!$id) {
    respond(false, 'Invalid ID.');
}

// ── Route ─────────────────────────────────────────────────────────────────────
switch ($action) {

    // ── 1. Update status ──────────────────────────────────────────────────────
    case 'update_status':
        $status = trim($_POST['status'] ?? '');
        $allowed = ['Paid', 'Pending', 'Overdue'];

        if (!in_array($status, $allowed)) {
            respond(false, 'Invalid status value.');
        }

        $stmt = mysqli_prepare($conn, "UPDATE invoices SET status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'si', $status, $id);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        respond($ok, $ok ? '' : 'DB update failed.');
        break;


    // ── 2. Delete invoice ─────────────────────────────────────────────────────
    case 'delete':
        $stmt = mysqli_prepare($conn, "DELETE FROM invoices WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        respond($ok, $ok ? '' : 'DB delete failed.');
        break;


    // ── 3. Send invoice via email ─────────────────────────────────────────────
    case 'send':
        $stmt = mysqli_prepare($conn, "
            SELECT i.invoice_no, i.items, i.total, i.due_date,
                   t.name  AS tenant_name,
                   t.email AS tenant_email
            FROM invoices i
            LEFT JOIN tenants t ON t.id = i.tenant_id
            WHERE i.id = ?
            LIMIT 1
        ");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $invoice = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!$invoice) {
            respond(false, 'Invoice not found.');
        }

        if (empty($invoice['tenant_email'])) {
            respond(false, 'Tenant has no email address on file.');
        }

        $to = $invoice['tenant_email'];
        $subject = 'Invoice ' . $invoice['invoice_no'] . ' from Your Property Manager';
        $body = "Dear {$invoice['tenant_name']},\n\n"
            . "Please find your invoice details below:\n\n"
            . "Invoice No : {$invoice['invoice_no']}\n"
            . "Items      : {$invoice['items']}\n"
            . "Total      : ₱" . number_format((float) $invoice['total'], 2) . "\n"
            . "Due Date   : {$invoice['due_date']}\n\n"
            . "Please settle your balance before the due date.\n\n"
            . "Thank you.";
        $headers = 'From: noreply@yourpropertydomain.com';

        // Swap mail() with PHPMailer/SMTP for production
        $sent = mail($to, $subject, $body, $headers);

        if ($sent) {
            // Record the sent timestamp
            $upd = mysqli_prepare($conn, "UPDATE invoices SET sent_at = NOW() WHERE id = ?");
            mysqli_stmt_bind_param($upd, 'i', $id);
            mysqli_stmt_execute($upd);
            mysqli_stmt_close($upd);
        }

        respond($sent, $sent ? '' : 'mail() failed — check server mail config.');
        break;


    // ── Unknown action ────────────────────────────────────────────────────────
    default:
        respond(false, 'Unknown action: ' . htmlspecialchars($action));
        break;
}