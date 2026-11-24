<?php
header('Content-Type: application/json');
header('Cache-Control: no-store');
$dir = __DIR__ . '/gallery/';
$out = [];
if (is_dir($dir)) {
    $files = scandir($dir);
    foreach ($files as $f) {
        if ($f === '.' || $f === '..' || $f === 'gallery.json') continue;
        $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
            $out[] = $f;
        }
    }
    sort($out);
}
echo json_encode($out);