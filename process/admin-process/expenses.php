<?php
/**
 * expenses_ajax.php
 * AJAX back-end for the Expenses page.
 * Handles: create | update | delete
 */

header('Content-Type: application/json');

include '../../includes/session.php';
include '../../includes/db.php';

function json_out(bool $ok, string $msg = '', array $extra = []): void {
    echo json_encode(array_merge(['success' => $ok, 'message' => $msg], $extra));
    exit;
}

if (!isset($_SESSION['user_id'])) {
    json_out(false, 'Unauthorised');
}

$action = trim($_POST['action'] ?? '');

// ════════════════════════════════════════════════════════════════════════════
// CREATE
// ════════════════════════════════════════════════════════════════════════════
if ($action === 'create') {

    $property_id      = $_POST['property_id']      ?? null;
    $unit_id          = $_POST['unit_id']          ?? null;
    $expense_category = trim($_POST['expense_category'] ?? '');
    $description      = trim($_POST['description']      ?? '');
    $amount           = filter_var($_POST['amount'] ?? 0, FILTER_VALIDATE_FLOAT);
    $expense_date     = trim($_POST['expense_date']      ?? '');
    $recorded_by      = (int)$_SESSION['user_id'];

    if (!$expense_category || !$description || $amount === false || $amount <= 0 || !$expense_date) {
        json_out(false, 'Missing or invalid required fields.');
    }
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $expense_date)) {
        json_out(false, 'Invalid date format.');
    }

    $property_id = ($property_id !== '' && $property_id !== null) ? (int)$property_id : null;
    $unit_id = ($unit_id !== '' && $unit_id !== null) ? (int)$unit_id : null;

    $st = $conn->prepare(
        "INSERT INTO expenses
            (property_id, unit_id, expense_category, description, amount, expense_date, recorded_by)
         VALUES
            (?, ?, ?, ?, ?, ?, ?)"
    );
    if (!$st) {
        json_out(false, 'DB error: ' . $conn->error);
    }

    $st->bind_param(
        'iissdsi',
        $property_id,
        $unit_id,
        $expense_category,
        $description,
        $amount,
        $expense_date,
        $recorded_by
    );

    if (!$st->execute()) {
        json_out(false, 'DB error: ' . $st->error);
    }

    $insert_id = $conn->insert_id;
    $st->close();
    json_out(true, 'Expense logged.', ['expense_id' => $insert_id]);
}

// ════════════════════════════════════════════════════════════════════════════
// UPDATE
// ════════════════════════════════════════════════════════════════════════════
if ($action === 'update') {

    $expense_id       = (int)($_POST['expense_id'] ?? 0);
    $property_id      = $_POST['property_id']      ?? null;
    $unit_id          = $_POST['unit_id']          ?? null;
    $expense_category = trim($_POST['expense_category'] ?? '');
    $description      = trim($_POST['description']      ?? '');
    $amount           = filter_var($_POST['amount'] ?? 0, FILTER_VALIDATE_FLOAT);
    $expense_date     = trim($_POST['expense_date']      ?? '');
    $recorded_by      = (int)$_SESSION['user_id'];

    if (!$expense_id) json_out(false, 'Invalid expense ID.');
    if (!$expense_category || !$description || $amount === false || $amount <= 0 || !$expense_date) {
        json_out(false, 'Missing or invalid required fields.');
    }
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $expense_date)) {
        json_out(false, 'Invalid date format.');
    }

    $property_id = ($property_id !== '' && $property_id !== null) ? (int)$property_id : null;
    $unit_id = ($unit_id !== '' && $unit_id !== null) ? (int)$unit_id : null;

    $st = $conn->prepare(
        "UPDATE expenses SET
            property_id      = ?,
            unit_id          = ?,
            expense_category = ?,
            description      = ?,
            amount           = ?,
            expense_date     = ?,
            recorded_by      = ?
         WHERE expense_id = ?"
    );
    if (!$st) {
        json_out(false, 'DB error: ' . $conn->error);
    }

    $st->bind_param(
        'iissdsii',
        $property_id,
        $unit_id,
        $expense_category,
        $description,
        $amount,
        $expense_date,
        $recorded_by,
        $expense_id
    );

    if (!$st->execute()) {
        json_out(false, 'DB error: ' . $st->error);
    }

    $st->close();
    json_out(true, 'Expense updated.');
}

// ════════════════════════════════════════════════════════════════════════════
// DELETE
// ════════════════════════════════════════════════════════════════════════════
if ($action === 'delete') {

    $expense_id = (int)($_POST['expense_id'] ?? 0);
    if (!$expense_id) json_out(false, 'Invalid expense ID.');

    $st = $conn->prepare("DELETE FROM expenses WHERE expense_id = ?");
    if (!$st) {
        json_out(false, 'DB error: ' . $conn->error);
    }

    $st->bind_param('i', $expense_id);

    if (!$st->execute()) {
        json_out(false, 'DB error: ' . $st->error);
    }

    $st->close();
    json_out(true, 'Expense deleted.');
}

json_out(false, 'Unknown action.');