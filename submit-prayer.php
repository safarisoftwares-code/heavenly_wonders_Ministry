<?php
// Prayer Request Handler — Version 2
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$request = isset($_POST['request']) ? trim($_POST['request']) : '';

// Honeypot
if (!empty($_POST['website'])) { exit; }

// Validate
if (empty($request)) {
    http_response_code(400);
    echo "Prayer request cannot be empty.";
    exit;
}
if (strlen($request) > 5000) {
    http_response_code(400);
    echo "Prayer request is too long.";
    exit;
}
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo "Invalid email.";
    exit;
}

// Save to prayers.json (above public_html)
$file = dirname(__DIR__) . '/prayers.json';
$prayers = [];
if (file_exists($file)) {
    $raw = file_get_contents($file);
    $prayers = json_decode($raw, true);
    if (!is_array($prayers)) $prayers = [];
}
$church = isset($_POST['church']) ? trim($_POST['church']) : '';
$role = isset($_POST['role']) ? trim($_POST['role']) : '';

$prayers[] = [
    'date' => date('Y-m-d H:i:s'),
    'name' => $name ?: 'Anonymous',
    'email' => $email,
    'phone' => isset($_POST['phone']) ? trim($_POST['phone']) : '',
    'church' => $church,
    'role' => $role,
    'request' => $request
];
$saved = file_put_contents($file, json_encode($prayers, JSON_PRETTY_PRINT), LOCK_EX);

if ($saved === false) {
    http_response_code(500);
    echo "Could not save your request. Please try again.";
    exit;
}

// Try to email (but do not fail the whole request if email fails)
$to = 'biblestudyprograme@gmail.com';
$subject = 'New Prayer Request — Heavenly Wonders Ministry';
$body = "New prayer request:\n\n";
$body .= "Date: " . date('Y-m-d H:i:s') . "\n";
$body .= "Name: " . ($name ?: 'Anonymous') . "\n";
$body .= "Email: " . ($email ?: 'Not provided') . "\n\n";
$body .= $request . "\n";
$headers = "From: Heavenly Wonders Ministry <noreply@heavenlywonders.co.ke>\r\n";
$headers .= "Reply-To: " . ($email ?: 'biblestudyprograme@gmail.com') . "\r\n";

$emailSent = @mail($to, $subject, $body, $headers);
if (!$emailSent) {
    // Log the failure silently (do not show to the visitor)
    @file_put_contents(dirname(__DIR__) . '/email-errors.log', 
        date('Y-m-d H:i:s') . " — Prayer request email failed\n", FILE_APPEND);
}

// Always return success if the JSON save worked
echo "Success! Your prayer request has been received.";
?>

