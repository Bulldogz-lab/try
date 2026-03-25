<?php
include '../../includes/session.php';
require_once '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/admin/add_property.php');
    exit;
}

$name = trim($_POST['name'] ?? '');
$type = trim($_POST['type'] ?? '');
$address = trim($_POST['address'] ?? '');
$city = trim($_POST['city'] ?? '');
$state = trim($_POST['state'] ?? '');
$zip = trim($_POST['zip'] ?? '');

$errors = [];

if ($name === '')
    $errors['name'] = 'Property name is required.';
if ($address === '')
    $errors['address'] = 'Street address is required.';
if ($city === '')
    $errors['city'] = 'City is required.';

if ($type === '')
    $type = 'Residential';

if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_old'] = $_POST;
    header('Location: ../../pages/admin/add_property.php');
    exit;
}

$full_address = $address;
if ($city !== '')
    $full_address .= ', ' . $city;
if ($state !== '')
    $full_address .= ', ' . $state;
if ($zip !== '')
    $full_address .= ' ' . $zip;

$status = 'Active';

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO properties
        (property_name, property_type, address, city, state, zip, status, created_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, NOW())"
);

if (!$stmt) {
    $_SESSION['form_errors'] = ['db' => 'Database error: ' . mysqli_error($conn)];
    $_SESSION['form_old'] = $_POST;
    header('Location: ../../pages/admin/add_property.php');
    exit;
}

mysqli_stmt_bind_param($stmt, "sssssss", $name, $type, $full_address, $city, $state, $zip, $status);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    $_SESSION['form_success'] = true;
    header('Location: ../../pages/admin/add_property.php');
    exit;
} else {
    $err_msg = mysqli_stmt_error($stmt);
    mysqli_stmt_close($stmt);
    $_SESSION['form_errors'] = ['db' => 'Failed to save property: ' . $err_msg];
    $_SESSION['form_old'] = $_POST;
    header('Location: ../../pages/admin/add_property.php');
    exit;
}