<?php
require_once dirname(__DIR__) . '/private-config.php';

if (!isset($_POST['password']) || $_POST['password'] !== $ADMIN_PASSWORD) {
    http_response_code(401);
    echo "Unauthorized: Incorrect password.";
    exit;
}

$file = dirname(__DIR__) . '/sermons.json';
$sermons = [];
if (file_exists($file)) {
    $raw = file_get_contents($file);
    $sermons = json_decode($raw, true);
    if (!is_array($sermons)) $sermons = [];
}

$action = isset($_GET['action']) ? $_GET['action'] : 'add';

if ($action === 'add') {
    $topic = isset($_POST['topic']) ? trim($_POST['topic']) : '';
    $verse = isset($_POST['verse']) ? trim($_POST['verse']) : '';
    $explanation = isset($_POST['explanation']) ? trim($_POST['explanation']) : '';
    $date = isset($_POST['date']) ? trim($_POST['date']) : '';

    if (empty($topic) || empty($verse) || empty($explanation)) {
        echo "Please fill in topic, verse, and explanation.";
        exit;
    }

    $youtube = isset($_POST['youtube']) ? trim($_POST['youtube']) : '';
    
    $sermons[] = [
        'topic' => $topic,
        'verse' => $verse,
        'explanation' => $explanation,
        'youtube' => $youtube,
        'date' => $date
    ];
    file_put_contents($file, json_encode($sermons, JSON_PRETTY_PRINT), LOCK_EX);
    echo "Success! Sermon added.";

} elseif ($action === 'delete') {
    $index = isset($_POST['index']) ? (int)$_POST['index'] : -1;
    if ($index >= 0 && isset($sermons[$index])) {
        array_splice($sermons, $index, 1);
        file_put_contents($file, json_encode($sermons, JSON_PRETTY_PRINT), LOCK_EX);
        echo "Deleted.";
    } else {
        echo "Sermon not found.";
    }
}
?>

