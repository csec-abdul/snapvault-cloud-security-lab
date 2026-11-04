# Challenge Studio — SnapVault Cloud Security Lab

A university Challenge Studio project built around **SnapVault**, a small file-sharing web
app shipped in two forms — a deliberately **vulnerable** build and a **hardened** build —
to demonstrate a realistic object-storage ("cloud") misconfiguration exploit chain and the
fixes that shut it down.

> ⚠️ **Educational use only.** The vulnerable stack is intentionally insecure and is locked
> to `127.0.0.1` (loopback) by design. Run it only in an isolated local environment. Do not
> expose it to a network or point any tooling here at systems you do not own.

## What's inside

```
Challenge studio/
├── Code/
│   ├── snapvault/
│   │   ├── vulnerable/     # intentionally-insecure stack (app :8080, MinIO :9000/:9001)
│   │   └── hardened/       # same app, every finding closed (app :8081, MinIO internal-only)
│   ├── SnapVault_Attack_Walkthrough.pdf
│   ├── SnapVault_Environment_Comparison.pdf
│   ├── SnapVault_Challenge_Studio.pptx
│   └── snapvault-local-pwner(the AI agent).md   # scoped, loopback-only demo agent spec
├── Result/                 # HTB-style machine walkthroughs + secure-environment guideline
├── Subject Report/         # written subject report
├── Work Summary/           # summary presentation
└── Contribution Table/     # group contribution record
```

## The SnapVault lab

SnapVault is a PHP app that stores uploads in a MinIO (S3-compatible) bucket and mirrors the
bucket into its web root. The **vulnerable** build chains a set of cloud-storage
misconfigurations into remote code execution:

0. **Recon** — confirm the bucket is private (anonymous PUT → 403).
1. **Leaked key** — a read-only access key is hardcoded in the frontend `app.js`.
2. **Enumeration** — list the bucket with the leaked key (read works, write denied).
3. **Versioning leak** — an over-broad read policy exposes object *versions*; an old
   `config.bak` version still holds privileged root credentials.
4. **Webshell upload** — write a PHP webshell into the bucket with the recovered creds.
5. **RCE** — the app serves the mirrored webshell; on the vulnerable stack the container
   runs as root → `uid=0(root)`.

The **hardened** build closes each finding: no leaked key, least-privilege scoped service
account, MinIO kept on the internal network only, a non-root read-only container with
dropped capabilities, and secrets supplied from an untracked `.env`.

## Running the lab locally

Requires Docker + Docker Compose.

```bash
cd "Challenge studio/Code/snapvault/vulnerable"
cp .env.example .env            # weak demo creds, fine as-is for the vulnerable stack
docker compose up --build       # app → http://127.0.0.1:8080
```

For the hardened stack:

```bash
cd "Challenge studio/Code/snapvault/hardened"
cp .env.example .env            # then replace every CHANGE_ME with your own strong values
docker compose up --build       # app → http://127.0.0.1:8081
```

Both stacks bind only to `127.0.0.1`.

## Secrets

No real credentials are committed. Each stack ships a `.env.example`; copy it to `.env`
(ignored by git) before running. For the hardened stack, generate your own values:

```bash
openssl rand -base64 24         # for MINIO_ROOT_PASSWORD / APP_SECRET_KEY
```

## Authors

Group university project. See `Challenge studio/Contribution Table/` for the contribution
breakdown.
