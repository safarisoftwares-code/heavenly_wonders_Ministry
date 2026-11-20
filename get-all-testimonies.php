<?php
require_once dirname(__DIR__) . '/private-config.php';

if (!isset($_GET['password']) || $_GET['password'] !== $ADMIN_PASSWORD) {
    http_response_code(401);
    echo '{"error":"Unauthorized"}';
    exit;
}

header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

$file = dirname(__DIR__) . '/testimonies.json';

if (!file_exists($file)) {
    echo '[]';
    exit;
}

$content = file_get_contents($file);
$testimonies = json_decode($content, true);

if (!is_array($testimonies)) {
    echo '[]';
    exit;
}

echo json_encode($testimonies);
?>