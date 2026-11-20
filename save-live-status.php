<?php
require_once dirname(__DIR__) . '/private-config.php';
header('Content-Type: application/json');

function respond($ok, $msg, $extra = []) {
    echo json_encode(array_merge(['success' => $ok, 'message' => $msg], $extra));
    exit;
}

$authFile = dirname(__DIR__) . '/media-auth.json';
$statusFile = dirname(__DIR__) . '/live-status.json';

if (!isset($_POST['password'])) {
    respond(false, 'No media password provided.');
}
if (!file_exists($authFile)) {
    respond(false, 'Media password not set. Ask Admin to generate one.');
}
$auth = json_decode(file_get_contents($authFile), true);
if (!is_array($auth) || empty($auth['hash']) || !password_verify($_POST['password'], $auth['hash'])) {
    http_response_code(401);
    respond(false, 'Unauthorized: Incorrect media password.');
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

$default = [
    'youtube'  => ['live' => false, 'url' => '', 'startedAt' => null],
    'facebook' => ['live' => false, 'url' => '', 'startedAt' => null],
    'tv'       => ['live' => false, 'url' => '', 'startedAt' => null],
    'windowHours' => 24,
    'archiveUrl' => ''
];

$status = file_exists($statusFile) ? json_decode(file_get_contents($statusFile), true) : null;
if (!is_array($status)) $status = $default;
foreach (['youtube','facebook','tv'] as $k) {
    if (!isset($status[$k]) || !is_array($status[$k])) $status[$k] = $default[$k];
    $status[$k] += $default[$k];
}
$status += $default;

$validSources = ['youtube', 'facebook', 'tv'];

if (preg_match('/^([a-z]+)_go_live$/', $action, $m) && in_array($m[1], $validSources, true)) {
    $src = $m[1];
    $url = isset($_POST['url']) ? trim($_POST['url']) : '';
    if ($url === '') {
        respond(false, 'URL is required to go live on ' . $src . '.');
    }
    $status[$src]['live'] = true;
    $status[$src]['url'] = $url;
    $status[$src]['startedAt'] = date('c');
    file_put_contents($statusFile, json_encode($status, JSON_PRETTY_PRINT), LOCK_EX);
    respond(true, ucfirst($src) . ' is now live.', ['status' => $status]);
}

if (preg_match('/^([a-z]+)_end_live$/', $action, $m) && in_array($m[1], $validSources, true)) {
    $src = $m[1];
    $status[$src]['live'] = false;
    file_put_contents($statusFile, json_encode($status, JSON_PRETTY_PRINT), LOCK_EX);
    respond(true, ucfirst($src) . ' ended.', ['status' => $status]);
}

if ($action === 'update_window') {
    $h = isset($_POST['windowHours']) ? (int)$_POST['windowHours'] : 24;
    if ($h < 1) $h = 1;
    if ($h > 720) $h = 720;
    $status['windowHours'] = $h;
    file_put_contents($statusFile, json_encode($status, JSON_PRETTY_PRINT), LOCK_EX);
    respond(true, 'Window updated.', ['status' => $status]);
}

if ($action === 'update_archive') {
    $status['archiveUrl'] = isset($_POST['archiveUrl']) ? trim($_POST['archiveUrl']) : '';
    file_put_contents($statusFile, json_encode($status, JSON_PRETTY_PRINT), LOCK_EX);
    respond(true, 'Archive updated.', ['status' => $status]);
}

respond(false, 'Unknown action.');