<?php
function req_env($k) {
    $v = getenv($k);
    if ($v === false || $v === '') { http_response_code(500); die("config error"); }
    return $v;
}
$MINIO_ENDPOINT = req_env('MINIO_ENDPOINT');
$MINIO_ACCESS   = req_env('MINIO_ACCESS_KEY');
$MINIO_SECRET   = req_env('MINIO_SECRET_KEY');
$MINIO_BUCKET   = req_env('MINIO_BUCKET');

$ADMIN_USER      = req_env('APP_ADMIN_USER');
$ADMIN_PASS_HASH = req_env('APP_ADMIN_PASS_HASH');

function mc_setup() {
    global $MINIO_ENDPOINT, $MINIO_ACCESS, $MINIO_SECRET;
    putenv('MC_CONFIG_DIR=/home/app/.mc');
    shell_exec("mc alias set store " . escapeshellarg($MINIO_ENDPOINT) . " "
        . escapeshellarg($MINIO_ACCESS) . " " . escapeshellarg($MINIO_SECRET) . " 2>&1");
}
