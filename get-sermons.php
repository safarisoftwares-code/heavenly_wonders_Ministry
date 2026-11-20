<?php
// Serves sermons.json from above public_html as JSON
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

$file = dirname(__DIR__) . '/sermons.json';

if (!file_exists($file)) {
    echo '[]';
    exit;
}

$content = file_get_contents($file);
$sermons = json_decode($content, true);

if (!is_array($sermons)) {
    echo '[]';
    exit;
}

echo json_encode($sermons);
?>
