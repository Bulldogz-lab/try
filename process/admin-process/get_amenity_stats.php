<?php
ob_start();
session_start();
$conn = new mysqli('localhost', 'root', '', 'propsight_db');
ob_end_clean();
header('Content-Type: application/json');

$row = $conn->query("
    SELECT COUNT(*) AS total,
           SUM(status='available')   AS available,
           SUM(status='unavailable') AS unavailable,
           SUM(status='maintenance') AS maintenance
    FROM amenities
")->fetch_assoc();

echo json_encode([
    'status' => 'success',
    'stats' => [
        'total' => (int) $row['total'],
        'available' => (int) $row['available'],
        'unavailable' => (int) $row['unavailable'],
        'maintenance' => (int) $row['maintenance'],
    ]
]);
$conn->close();