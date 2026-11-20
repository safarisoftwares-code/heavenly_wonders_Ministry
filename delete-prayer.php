<?php
require_once dirname(__DIR__) . '/private-config.php';

if (!isset($_POST['password']) || $_POST['password'] !== $ADMIN_PASSWORD) {
    http_response_code(401);
    echo "Unauthorized: Incorrect password.";
    exit;
}

$file = dirname(__DIR__) . '/prayers.json';
$prayers = [];
if (file_exists($file)) {
    $raw = file_get_contents($file);
    $prayers = json_decode($raw, true);
    if (!is_array($prayers)) $prayers = [];
}

$index = isset($_POST['index']) ? (int)$_POST['index'] : -1;
if ($index >= 0 && isset($prayers[$index])) {
    array_splice($prayers, $index, 1);
    file_put_contents($file, json_encode($prayers, JSON_PRETTY_PRINT), LOCK_EX);
    echo "Deleted.";
} else {
    echo "Prayer not found.";
}
?>