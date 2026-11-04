<?php
require __DIR__ . '/config.php';
mc_setup();


$f = $_GET['f'] ?? '';

if ($f === '' || !preg_match('/^[A-Za-z0-9._-]+$/', $f)) {
    http_response_code(400); die("bad name");
}

$tmp = tempnam('/tmp', 'sv');
shell_exec("mc cp " . escapeshellarg("store/$MINIO_BUCKET/$f") . " " . escapeshellarg($tmp) . " 2>&1");
if (!is_file($tmp) || filesize($tmp) === 0) { http_response_code(404); die("not found"); }

header('Content-Type: application/octet-stream');
header('X-Content-Type-Options: nosniff');
header('Content-Disposition: attachment; filename="' . $f . '"');
readfile($tmp);
unlink($tmp);
