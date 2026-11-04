#!/bin/sh
set -e
until mc alias set root http://minio:9000 "$MINIO_ROOT_USER" "$MINIO_ROOT_PASSWORD" >/dev/null 2>&1; do
  echo "waiting for minio..."; sleep 2
done

mc mb -p root/snapvault || true

# FIX 1: no anonymous access at all. The bucket is private by default; make it explicit.
mc anonymous set none root/snapvault

# FIX 4: create a dedicated service account for the app with a tight, read/write-only
# policy scoped to this one bucket -- the app never holds the root keys.
cat > /tmp/snapvault-policy.json <<JSON
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Action": ["s3:GetObject", "s3:PutObject", "s3:ListBucket"],
      "Resource": ["arn:aws:s3:::snapvault", "arn:aws:s3:::snapvault/*"]
    }
  ]
}
JSON
mc admin policy create root snapvault-rw /tmp/snapvault-policy.json || true
mc admin user svcacct add root "$MINIO_ROOT_USER" \
    --access-key "$APP_ACCESS_KEY" --secret-key "$APP_SECRET_KEY" \
    --policy /tmp/snapvault-policy.json || true

echo "[hardened] bucket private; scoped service account '$APP_ACCESS_KEY' created."
mc anonymous get root/snapvault
