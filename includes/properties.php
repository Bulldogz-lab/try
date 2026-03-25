<?php
include 'db.php';
$stats_result = $conn->query("
        SELECT
        COUNT(DISTINCT p.property_id)                AS total,
        SUM(u.status = 'occupied')                   AS occupied,
        SUM(u.status = 'vacant')                     AS vacant,
        SUM(u.status = 'maintenance')                AS maintenance,
        SUM(CASE WHEN
            MONTH(p.created_at) = MONTH(NOW()) AND
            YEAR(p.created_at)  = YEAR(NOW())
            THEN 1 ELSE 0 END)                       AS new_this_month
    FROM properties p
    LEFT JOIN units u ON u.property_id = p.property_id
");

$stats = mysqli_fetch_assoc($stats_result);
$total = (int) $stats['total'];
$occupied = (int) $stats['occupied'];
$vacant = (int) $stats['vacant'];
$maintenance = (int) $stats['maintenance'];
$new_month = (int) $stats['new_this_month'];
$occ_pct = $total > 0 ? round(($occupied / $total) * 100) : 0;

$allowed_types = ['Apartment', 'House', 'Commercial', 'Condo', 'Villa'];
$filter_type = isset($_GET['type']) && in_array($_GET['type'], $allowed_types)
    ? $_GET['type'] : '';

if ($filter_type !== '') {
    $stmt = mysqli_prepare($conn, "
        SELECT * FROM properties
        WHERE type = ?
        ORDER BY created_at DESC
    ");
    mysqli_stmt_bind_param($stmt, 's', $filter_type);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, "
        SELECT * FROM properties
        ORDER BY created_at DESC
    ");
}

function property_emoji($type)
{
    switch (strtolower($type)) {
        case 'apartment':
            return '🏢';
        case 'house':
            return '🏠';
        case 'commercial':
            return '🏬';
        case 'condo':
            return '🏙️';
        case 'villa':
            return '🏡';
        default:
            return '🏗️';
    }
}

function status_badge($status)
{
    switch (strtolower($status)) {
        case 'active':
            return '<span class="badge badge-success">Active</span>';
        case 'maintenance':
            return '<span class="badge badge-danger">Maintenance</span>';
        case 'vacant':
            return '<span class="badge badge-pending">Vacant</span>';
        default:
            return '<span class="badge">' . htmlspecialchars(ucfirst($status)) . '</span>';
    }
}

function bar_color($pct)
{
    if ($pct >= 80)
        return 'var(--success)';
    if ($pct >= 50)
        return 'var(--gold)';
    return 'var(--danger)';
}
?>