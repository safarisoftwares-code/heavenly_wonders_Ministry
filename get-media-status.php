<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

$authFile = dirname(__DIR__) . '/media-auth.json';

if (!file_exists($authFile)) {
    echo json_encode(['set' => false]);
    exit;
}

$raw = file_get_contents($authFile);
$data = json_decode($raw, true);

if (!is_array($data) || empty($data['hash'])) {
    echo json_encode(['set' => false]);
    exit;
}

echo json_encode(['set' => true]);