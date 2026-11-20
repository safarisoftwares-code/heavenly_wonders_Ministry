<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

$statusFile = dirname(__DIR__) . '/live-status.json';

$default = [
    'youtube'  => ['live' => false, 'url' => '', 'startedAt' => null],
    'facebook' => ['live' => false, 'url' => '', 'startedAt' => null],
    'tv'       => ['live' => false, 'url' => '', 'startedAt' => null],
    'windowHours' => 24,
    'archiveUrl' => ''
];

if (!file_exists($statusFile)) {
    echo json_encode($default);
    exit;
}

$raw = file_get_contents($statusFile);
$data = json_decode($raw, true);
if (!is_array($data)) $data = $default;

foreach (['youtube','facebook','tv'] as $k) {
    if (!isset($data[$k]) || !is_array($data[$k])) $data[$k] = $default[$k];
    $data[$k] += $default[$k];
}
$data += $default;

echo json_encode($data);