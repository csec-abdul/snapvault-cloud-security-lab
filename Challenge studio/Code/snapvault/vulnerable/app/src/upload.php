<?php
require __DIR__ . '/config.php';
mc_setup();

$ALLOWED = ['jpg','jpeg','png','gif','webp','pdf','txt'];

if (!empty($_FILES['file']['tmp_name'])) {
    $orig = $_FILES['file']['name'];
    $ext  = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
    if (!in_array($ext, $ALLOWED, true)) {
        http_response_code(400);
        die("File type not allowed.");
    }
    $safe = bin2hex(random_bytes(8)) . '.' . $ext;
    $tmp  = $_FILES['file']['tmp_name'];
    shell_exec("mc cp " . escapeshellarg($tmp) . " store/$MINIO_BUCKET/" . escapeshellarg($safe) . " 2>&1");
}
header('Location: index.php');
