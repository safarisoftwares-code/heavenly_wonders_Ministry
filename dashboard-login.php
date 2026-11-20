<?php
require_once dirname(__DIR__) . '/private-config.php';

header('Content-Type: application/json');

if (!isset($_POST['password']) || $_POST['password'] !== $ADMIN_PASSWORD) {
    http_response_code(401);
    echo '{"success":false,"message":"Incorrect password."}';
    exit;
}

echo '{"success":true}';
?>