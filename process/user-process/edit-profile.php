<?php
session_start();
require_once '../../includes/db.php';

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Invalid CSRF token!");
}

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to edit your profile.");
}

if (isset($_POST['edit_profile'])) {

    $user_id     = $_SESSION['user_id'];
    $first_name  = trim($_POST['first_name']);
    $last_name   = trim($_POST['last_name']);
    $email       = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone       = trim($_POST['phone']);
    $nationality = trim($_POST['nationality']);
    $birthday    = trim($_POST['birthday']);
    $gender      = trim($_POST['gender']);

    if (empty($first_name) || empty($last_name) || empty($email) || empty($phone)) {
        echo "First name, last name, email, and phone are required!";
        exit;
    }

    if ($phone && !preg_match("/^\+?[0-9]{7,15}$/", $phone)) {
        echo "Invalid phone number!";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email!";
        exit;
    }

    $check = $conn->prepare("SELECT user_id FROM users WHERE (email = ? OR phone = ?) AND user_id != ?");
    $check->bind_param("ssi", $email, $phone, $user_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "Email or phone number already in use by another account!";
        exit;
    }

    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, nationality = ?, birthday = ?, gender = ? WHERE user_id = ?");
    $stmt->bind_param("sssssssi", $first_name, $last_name, $email, $phone, $nationality, $birthday, $gender, $user_id);

    if ($stmt->execute()) {
        echo "Profile updated successfully!";
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name']  = $last_name;
        $_SESSION['email']      = $email;
        $_SESSION['phone']      = $phone;
        $_SESSION['nationality'] = $nationality;
        $_SESSION['birthday']    = $birthday;
        $_SESSION['gender']      = $gender;

        header("Location: ../../pages/user/profile.php");
        exit;
    } else {
        error_log($stmt->error);
        echo "Something went wrong. Try again.";
    }

    $stmt->close();
    $check->close();
    $conn->close();

} else {
    echo "Invalid request!";
}
?>