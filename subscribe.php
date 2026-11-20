<?php
$email = isset($_POST['email']) ? trim($_POST['email']) : '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo "Invalid email address.";
    exit;
}

$file = dirname(__DIR__) . '/subscribers.json';

// Load existing — handle both old and new formats
$subscribers = [];
if (file_exists($file)) {
    $raw = file_get_contents($file);
    $subscribers = json_decode($raw, true);
    if (!is_array($subscribers)) $subscribers = [];
    
    // Normalize
    $normalized = [];
    foreach ($subscribers as $s) {
        if (is_string($s)) {
            $normalized[] = ['email' => $s, 'status' => 'active'];
        } elseif (is_array($s) && isset($s['email'])) {
            $normalized[] = $s;
        }
    }
    $subscribers = $normalized;
}

// Check for duplicate (case-insensitive)
foreach ($subscribers as $sub) {
    if (strtolower($sub['email']) === strtolower($email)) {
        echo "You are already subscribed.";
        exit;
    }
}

// Add new subscriber
$subscribers[] = ['email' => $email, 'status' => 'active'];

file_put_contents($file, json_encode($subscribers, JSON_PRETTY_PRINT), LOCK_EX);
echo "Success! You are subscribed.";
?>