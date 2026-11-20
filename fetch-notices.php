<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
$file = __DIR__ . '/notices.json';
if (file_exists($file)) {
    echo file_get_contents($file);
} else {
    echo '[]';
}
?>