<?php
ob_start();
session_start();
$conn = new mysqli('localhost', 'root', '', 'propsight_db');
ob_end_clean();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status'=>'error','message'=>'Invalid request.']); exit;
}

$amenity_id = (int)($_POST['amenity_id'] ?? 0);
if ($amenity_id <= 0) { echo json_encode(['status'=>'error','message'=>'Invalid amenity.']); exit; }

$stmt = $conn->prepare("DELETE FROM amenities WHERE amenity_id=?");
$stmt->bind_param('i', $amenity_id);

if ($stmt->execute()) {
    $stmt->close();
    echo json_encode(['status'=>'success','message'=>'Amenity removed.']);
} else {
    $err = $stmt->error; $stmt->close();
    echo json_encode(['status'=>'error','message'=>'Delete failed: '.$err]);
}
$conn->close();