<?php
header('Content-Type: application/json');

$authFile = dirname(__DIR__) . '/media-auth.json';

if (!isset($_POST['password'])) {
    echo json_encode(['success' => false, 'message' => 'No password provided.']);
    exit;
}

$pw = $_POST['password'];

if (!file_exists($authFile)) {
    echo json_encode(['success' => false, 'message' => 'Media password not set. Ask Admin to generate one.']);
    exit;
}

$raw = file_get_contents($authFile);
$data = json_decode($raw, true);

if (!is_array($data) || empty($data['hash'])) {
    echo json_encode(['success' => false, 'message' => 'Media password not set. Ask Admin to generate one.']);
    exit;
}

if (password_verify($pw, $data['hash'])) {
    echo json_encode(['success' => true, 'message' => 'OK']);
} else {
    echo json_encode(['success' => false, 'message' => 'Incorrect password.']);
}