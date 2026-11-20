<?php
require_once dirname(__DIR__) . '/private-config.php';

if (!isset($_POST['password']) || $_POST['password'] !== $ADMIN_PASSWORD) {
    http_response_code(401);
    echo "Unauthorized";
    exit;
}

$file = dirname(__DIR__) . '/subscribers.json';

// Load existing — handle both old (array of strings) and new (array of objects) formats
$subscribers = [];
if (file_exists($file)) {
    $raw = file_get_contents($file);
    $subscribers = json_decode($raw, true);
    if (!is_array($subscribers)) $subscribers = [];
    
    // Normalize: convert plain strings to objects
    $normalized = [];
    foreach ($subscribers as $s) {
        if (is_string($s)) {
            $normalized[] = ['email' => $s, 'status' => 'active'];
        } elseif (is_array($s) && isset($s['email'])) {
            $normalized[] = $s;
        }
    }
    $subscribers = $normalized;
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$index = isset($_POST['index']) ? (int)$_POST['index'] : -1;

if ($index >= 0 && isset($subscribers[$index])) {
    if ($action === 'delete') {
        array_splice($subscribers, $index, 1);
        file_put_contents($file, json_encode($subscribers, JSON_PRETTY_PRINT), LOCK_EX);
        echo "Deleted.";
    } elseif ($action === 'suspend') {
        $subscribers[$index]['status'] = 'suspended';
        file_put_contents($file, json_encode($subscribers, JSON_PRETTY_PRINT), LOCK_EX);
        echo "Suspended.";
    } elseif ($action === 'activate') {
        $subscribers[$index]['status'] = 'active';
        file_put_contents($file, json_encode($subscribers, JSON_PRETTY_PRINT), LOCK_EX);
        echo "Activated.";
    } else {
        echo "Unknown action.";
    }
} else {
    echo "Subscriber not found.";
}
?>