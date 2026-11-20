<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

$file = dirname(__DIR__) . '/testimonies.json';

if (!file_exists($file)) {
    echo '[]';
    exit;
}

$content = file_get_contents($file);
$testimonies = json_decode($content, true);

if (!is_array($testimonies)) {
    echo '[]';
    exit;
}

$approved = array_filter($testimonies, function($t) {
    return isset($t['status']) && $t['status'] === 'approved';
});

echo json_encode(array_values($approved));
?>