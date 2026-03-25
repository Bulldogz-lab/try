<?php
include '../../includes/session.php';
include '../../includes/db.php';
header('Content-Type: application/json');

if ($_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'invite') {
    $first = mysqli_real_escape_string($conn, trim($_POST['first_name'] ?? ''));
    $last = mysqli_real_escape_string($conn, trim($_POST['last_name'] ?? ''));
    $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $role = mysqli_real_escape_string($conn, trim($_POST['role'] ?? 'frontdesk'));
    $password = $_POST['password'] ?? '';

    if (!$first || !$last || !$email || !$password) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    if (strlen($password) < 8) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters.']);
        exit;
    }

    $check = mysqli_query($conn, "SELECT user_id FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already in use.']);
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (first_name, last_name, email, password, role, is_active, created_at)VALUES ('$first','$last','$email','$hash','$role',1,NOW())";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'message' => "Account created for $first $last."]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
    }

} elseif ($action === 'activate' || $action === 'deactivate') {
    $user_id = (int) ($_POST['user_id'] ?? 0);
    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid user.']);
        exit;
    }

    $val = ($action === 'activate') ? 1 : 0;

    mysqli_query($conn, "UPDATE users SET is_active=$val WHERE user_id=$user_id");
    echo json_encode(['success' => true, 'message' => "Staff member " . ($val ? 'activated' : 'deactivated') . "."]);

} elseif ($action === 'remove') {
    $user_id = (int) ($_POST['user_id'] ?? 0);

    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid user.']);
        exit;
    }

    if ($user_id === (int) $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'You cannot remove yourself.']);
        exit;
    }

    mysqli_query($conn, "DELETE FROM users WHERE user_id = $user_id AND role != 'user'");
    echo json_encode(['success' => true, 'message' => 'Staff member removed.']);

} else {
    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
}