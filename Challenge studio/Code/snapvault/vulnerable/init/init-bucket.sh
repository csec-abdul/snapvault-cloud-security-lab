#!/bin/sh
# Sets up the vulnerable bucket: private, versioned, with a leaked read-only key and an
# old config.bak version that still contains privileged (root) credentials.
set -e
until mc alias set root http://minio:9000 svadmin Sv-r00t-pass >/dev/null 2>&1; do
  echo "waiting for minio..."; sleep 2
done

mc mb -p root/snapvault || true

# Bucket is PRIVATE -- the old anonymous shortcut is gone.
mc anonymous set none root/snapvault

# Object versioning ON. This is what preserves the old, secret-bearing config.bak.
mc version enable root/snapvault

# --- Scoped read/write account the APP uses internally (least privilege for the app) ---
cat > /tmp/rw.json <<'JSON'
{ "Version":"2012-10-17","Statement":[{"Effect":"Allow",
  "Action":["s3:GetObject","s3:PutObject","s3:ListBucket"],
  "Resource":["arn:aws:s3:::snapvault","arn:aws:s3:::snapvault/*"]}]}
JSON
mc admin user svcacct add root svadmin \
  --access-key snapvault-app --secret-key app-rw-secret-key-01 --policy /tmp/rw.json || true

# --- Leaked READ-ONLY "viewer" key (this one ends up embedded in the frontend JS) ---
# MISCONFIG: the read-only policy also allows listing object VERSIONS, which is what lets
# an attacker discover the old config.bak.
cat > /tmp/ro.json <<'JSON'
{ "Version":"2012-10-17","Statement":[{"Effect":"Allow",
  "Action":["s3:GetObject","s3:GetObjectVersion","s3:ListBucket","s3:ListBucketVersions"],
  "Resource":["arn:aws:s3:::snapvault","arn:aws:s3:::snapvault/*"]}]}
JSON
mc admin user svcacct add root svadmin \
  --access-key SNAPVAULTVIEWERRO --secret-key viewer-readonly-secret-key-01 --policy /tmp/ro.json || true

# --- Plant config.bak: first the OLD version with privileged creds, then "sanitise" it ---
cat > /tmp/config.bak <<'JSON'
# SnapVault storage config (LEGACY - pre-cleanup)
MINIO_ENDPOINT=http://minio:9000
MINIO_ACCESS_KEY=svadmin
MINIO_SECRET_KEY=Sv-r00t-pass
MINIO_BUCKET=snapvault
JSON
mc cp /tmp/config.bak root/snapvault/config.bak

cat > /tmp/config.bak <<'JSON'
# SnapVault storage config
MINIO_ENDPOINT=http://minio:9000
MINIO_ACCESS_KEY=__REDACTED__
MINIO_SECRET_KEY=__REDACTED__
MINIO_BUCKET=snapvault
JSON
mc cp /tmp/config.bak root/snapvault/config.bak   # current version = redacted

# A harmless sample object so the gallery isn't empty.
echo "welcome to snapvault" > /tmp/readme.txt
mc cp /tmp/readme.txt root/snapvault/readme.txt

echo "[vuln] private+versioned bucket ready; viewer RO key leaked; old config.bak retains root creds."
mc ls --versions root/snapvault
