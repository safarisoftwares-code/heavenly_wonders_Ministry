<?php
require_once dirname(__DIR__) . '/private-config.php';

if (!isset($_GET['password']) || $_GET['password'] !== $ADMIN_PASSWORD) {
    http_response_code(401);
    echo '{"error":"Unauthorized"}';
    exit;
}

header('Content-Type: application/json');
$file = dirname(__DIR__) . '/prayers.json';
if (!file_exists($file)) { echo '[]'; exit; }
$c = file_get_contents($file);
$d = json_decode($c, true);
echo json_encode(is_array($d) ? $d : []);
?>