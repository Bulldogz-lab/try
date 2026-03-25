<?php
// ../../process/admin-process/fetch_reservations.php
include '../../includes/session.php';
include '../../includes/db.php';

header('Content-Type: application/json');

if ($_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$statusFilter = $_GET['status'] ?? 'all';
$search       = trim($_GET['search'] ?? '');

$whereClause = "WHERE 1=1";
if ($statusFilter !== 'all') {
    $statusEsc    = mysqli_real_escape_string($conn, $statusFilter);
    $whereClause .= " AND b.status = '$statusEsc'";
}
if ($search !== '') {
    $searchEsc    = mysqli_real_escape_string($conn, $search);
    $whereClause .= " AND (
        u2.first_name   LIKE '%$searchEsc%' OR
        u2.last_name    LIKE '%$searchEsc%' OR
        u2.email        LIKE '%$searchEsc%' OR
        un.unit_name    LIKE '%$searchEsc%' OR
        un.unit_number  LIKE '%$searchEsc%' OR
        p.property_name LIKE '%$searchEsc%' OR
        b.booking_id    LIKE '%$searchEsc%'
    )";
}

$statsRes = mysqli_query($conn, "
    SELECT
        COUNT(*)                                  AS total,
        SUM(status = 'pending')                   AS pending,
        SUM(status IN ('confirmed','active'))     AS confirmed,
        SUM(status = 'completed')                 AS completed,
        SUM(status = 'cancelled')                 AS cancelled
    FROM bookings
");
$stats = mysqli_fetch_assoc($statsRes);

$sql = "
    SELECT
        b.booking_id,
        b.checkin_date,
        b.checkout_date,
        b.guests,
        b.total_amount,
        b.status,
        b.created_at,
        DATEDIFF(b.checkout_date, b.checkin_date) AS nights,
        CONCAT(u2.first_name, ' ', u2.last_name)  AS user_name,
        u2.email                                   AS user_email,
        un.unit_name,
        un.unit_number,
        p.property_name
    FROM   bookings b
    JOIN   users      u2 ON u2.user_id      = b.user_id
    JOIN   units      un ON un.unit_id      = b.unit_id
    LEFT JOIN properties p ON p.property_id = un.property_id
    $whereClause
    ORDER  BY b.created_at DESC
";

$res      = mysqli_query($conn, $sql);
$bookings = [];
while ($row = mysqli_fetch_assoc($res)) {
    $row['checkin_date']  = $row['checkin_date']  ? date('M j, Y', strtotime($row['checkin_date']))  : '—';
    $row['checkout_date'] = $row['checkout_date'] ? date('M j, Y', strtotime($row['checkout_date'])) : '—';
    $bookings[] = $row;
}

echo json_encode([
    'success'  => true,
    'bookings' => $bookings,
    'stats'    => $stats,
    'count'    => count($bookings),
]);