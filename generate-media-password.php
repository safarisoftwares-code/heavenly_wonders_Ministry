<?php
require_once dirname(__DIR__) . '/private-config.php';

header('Content-Type: application/json');

function respond($ok, $msg, $extra = []) {
    echo json_encode(array_merge(['success' => $ok, 'message' => $msg], $extra));
    exit;
}

function pw_rules_ok($pw) {
    if (strlen($pw) < 8) return false;
    if (!preg_match('/[A-Z]/', $pw)) return false;
    if (!preg_match('/[a-z]/', $pw)) return false;
    if (!preg_match('/[0-9]/', $pw)) return false;
    if (!preg_match('/[^A-Za-z0-9]/', $pw)) return false;
    if (preg_match('/\s/', $pw)) return false;
    return true;
}

function pw_generate() {
    $upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    $lower = 'abcdefghijkmnopqrstuvwxyz';
    $digit = '23456789';
    $sym   = '!@#$%^&*';
    $all   = $upper . $lower . $digit . $sym;
    $pw  = $upper[random_int(0, strlen($upper)-1)];
    $pw .= $lower[random_int(0, strlen($lower)-1)];
    $pw .= $digit[random_int(0, strlen($digit)-1)];
    $pw .= $sym[random_int(0, strlen($sym)-1)];
    for ($i = 0; $i < 8; $i++) {
        $pw .= $all[random_int(0, strlen($all)-1)];
    }
    return str_shuffle($pw);
}

if (!isset($_POST['password']) || $_POST['password'] !== $ADMIN_PASSWORD) {
    http_response_code(401);
    respond(false, 'Unauthorized: Incorrect admin password.');
}

$mode = isset($_POST['mode']) ? $_POST['mode'] : '';
$authFile = dirname(__DIR__) . '/media-auth.json';

if ($mode === 'generate') {
    $pw = pw_generate();
    $hash = password_hash($pw, PASSWORD_DEFAULT);
    $json = json_encode(['hash' => $hash], JSON_PRETTY_PRINT);
    file_put_contents($authFile, $json, LOCK_EX);
    respond(true, 'New media password generated.', ['password' => $pw]);
}

if ($mode === 'custom') {
    $pw = isset($_POST['newpass']) ? $_POST['newpass'] : '';
    if (!pw_rules_ok($pw)) {
        respond(false, 'Password must be at least 8 characters and include uppercase, lowercase, number, and symbol (no spaces).');
    }
    $hash = password_hash($pw, PASSWORD_DEFAULT);
    $json = json_encode(['hash' => $hash], JSON_PRETTY_PRINT);
    file_put_contents($authFile, $json, LOCK_EX);
    respond(true, 'Media password saved.');
}

respond(false, 'Unknown mode.');