<?php
require_once dirname(__DIR__) . '/private-config.php';

if (!isset($_POST['password']) || $_POST['password'] !== $ADMIN_PASSWORD) {
    http_response_code(401);
    echo "Unauthorized: Incorrect password.";
    exit;
}

$file = __DIR__ . '/notices.json';
$notices = [];
if (file_exists($file)) {
    $raw = file_get_contents($file);
    $notices = json_decode($raw, true);
    if (!is_array($notices)) $notices = [];
}

$action = isset($_GET['action']) ? $_GET['action'] : 'add';

if ($action === 'add') {
    $category = isset($_POST['category']) ? trim($_POST['category']) : '';
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $text = isset($_POST['text']) ? trim($_POST['text']) : '';

    if (empty($title) || empty($text)) {
        echo "Please fill in title and text.";
        exit;
    }

    $notices[] = [
        'category' => $category,
        'title' => $title,
        'text' => $text
    ];
    file_put_contents($file, json_encode($notices, JSON_PRETTY_PRINT), LOCK_EX);
    echo "Success! Notice added.";

} elseif ($action === 'delete') {
    $index = isset($_POST['index']) ? (int)$_POST['index'] : -1;
    if ($index >= 0 && isset($notices[$index])) {
        array_splice($notices, $index, 1);
        file_put_contents($file, json_encode($notices, JSON_PRETTY_PRINT), LOCK_EX);
        echo "Deleted.";
    } else {
        echo "Notice not found.";
    }
} elseif ($action === 'delete_all') {
    file_put_contents($file, '[]', LOCK_EX);
    echo "All notices cleared.";
} else {
    echo "Unknown action.";
}
?>