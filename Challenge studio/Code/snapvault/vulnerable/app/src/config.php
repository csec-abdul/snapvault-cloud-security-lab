<?php

$MINIO_ENDPOINT = getenv('MINIO_ENDPOINT') ?: 'http://minio:9000';
$MINIO_ACCESS   = getenv('MINIO_ACCESS_KEY') ?: 'snapvault-app';
$MINIO_SECRET   = getenv('MINIO_SECRET_KEY') ?: 'app-rw-secret-key-01';
$MINIO_BUCKET   = getenv('MINIO_BUCKET') ?: 'snapvault';

function mc_setup() {
    global $MINIO_ENDPOINT, $MINIO_ACCESS, $MINIO_SECRET;

    putenv('MC_CONFIG_DIR=/tmp/.mc');
    shell_exec("mc alias set store $MINIO_ENDPOINT $MINIO_ACCESS $MINIO_SECRET 2>&1");
}
