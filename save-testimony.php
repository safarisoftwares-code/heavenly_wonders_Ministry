<?php
require_once dirname(__DIR__) . '/private-config.php';

if (!isset($_POST['password']) || $_POST['password'] !== $ADMIN_PASSWORD) {
    http_response_code(401);
    echo "Unauthorized: Incorrect password.";
    exit;
}

$file = dirname(__DIR__) . '/testimonies.json';
$testimonies = [];
if (file_exists($file)) {
    $raw = file_get_contents($file);
    $testimonies = json_decode($raw, true);
    if (!is_array($testimonies)) $testimonies = [];
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'approve') {
    $index = isset($_POST['index']) ? (int)$_POST['index'] : -1;
    if ($index >= 0 && isset($testimonies[$index])) {
        $testimonies[$index]['status'] = 'approved';
        file_put_contents($file, json_encode($testimonies, JSON_PRETTY_PRINT), LOCK_EX);
        echo "Success! Testimony approved.";
    } else {
        echo "Testimony not found.";
    }
} elseif ($action === 'delete') {
    $index = isset($_POST['index']) ? (int)$_POST['index'] : -1;
    if ($index >= 0 && isset($testimonies[$index])) {
        array_splice($testimonies, $index, 1);
        file_put_contents($file, json_encode($testimonies, JSON_PRETTY_PRINT), LOCK_EX);
        echo "Deleted.";
    } else {
        echo "Testimony not found.";
    }
}
?>
