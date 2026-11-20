<?php
require_once dirname(__DIR__) . '/private-config.php';

if (!isset($_GET['password']) || $_GET['password'] !== $ADMIN_PASSWORD) {
    http_response_code(401);
    echo '{"error":"Unauthorized"}';
    exit;
}

header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

$file = dirname(__DIR__) . '/subscribers.json';

if (!file_exists($file)) { echo '[]'; exit; }

$c = file_get_contents($file);
$d = json_decode($c, true);

if (!is_array($d)) { echo '[]'; exit; }

// Normalize: convert plain strings to objects with status
$normalized = [];
foreach ($d as $s) {
    if (is_string($s)) {
        $normalized[] = ['email' => $s, 'status' => 'active'];
    } elseif (is_array($s) && isset($s['email'])) {
        if (!isset($s['status'])) $s['status'] = 'active';
        $normalized[] = $s;
    }
}

echo json_encode($normalized);
?>