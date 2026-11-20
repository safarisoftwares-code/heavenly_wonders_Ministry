<?php
// Receives form data, saves to JSON, Web3Forms handles email via JS on client side.
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$location = isset($_POST['location']) ? trim($_POST['location']) : '';
$testimony = isset($_POST['testimony']) ? trim($_POST['testimony']) : '';

if (empty($name) || empty($testimony) || (empty($email) && empty($phone))) {
    http_response_code(400);
    echo "Missing required fields.";
    exit;
}

$file = dirname(__DIR__) . '/testimonies.json';
$testimonies = [];
if (file_exists($file)) {
    $raw = file_get_contents($file);
    $testimonies = json_decode($raw, true);
    if (!is_array($testimonies)) $testimonies = [];
}

$testimonies[] = [
    'name' => $name,
    'phone' => $phone,
    'email' => $email,
    'location' => $location,
    'testimony' => $testimony,
    'date' => date('Y-m-d'),
    'status' => 'pending'
];

file_put_contents($file, json_encode($testimonies, JSON_PRETTY_PRINT), LOCK_EX);
echo "Success";
?>
