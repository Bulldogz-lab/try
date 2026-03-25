<?php
ob_start();
session_start();
$conn = new mysqli('localhost', 'root', '', 'propsight_db');
ob_end_clean();

header('Content-Type: application/json');

$result = $conn->query("SELECT
        COUNT(*)                      AS total,
        SUM(status = 'occupied')      AS occupied,
        SUM(status = 'vacant')        AS vacant,
        SUM(status = 'maintenance')   AS maintenance
    FROM units
");

$stats = $result->fetch_assoc();

echo json_encode([
    'status' => 'success',
    'stats'  => [
        'total'       => (int) $stats['total'],
        'occupied'    => (int) $stats['occupied'],
        'vacant'      => (int) $stats['vacant'],
        'maintenance' => (int) $stats['maintenance'],
    ]
]);

$conn->close();
exit;