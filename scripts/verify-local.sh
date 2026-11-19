#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
IMAGE_NAME="ucademy-php83-composer"

printf '==> Membangun image Docker: %s\n' "$IMAGE_NAME"
docker build -t "$IMAGE_NAME" "$ROOT_DIR"

run_php() {
    docker run --rm \
        -e DB_CONNECTION=sqlite \
        -e DB_DATABASE=:memory: \
        -e DB_URL= \
        -v "$ROOT_DIR:/app" \
        -w /app \
        "$IMAGE_NAME" \
        php "$@"
}

printf '\n==> Memverifikasi migrasi pada SQLite dalam memori yang terisolasi\n'
run_php artisan migrate:fresh --force

printf '\n==> Menjalankan seluruh rangkaian pengujian\n'
run_php artisan test

printf '\n==> Memverifikasi daftar rute yang terdaftar\n'
ROUTES_JSON="$(docker run --rm \
    -e DB_CONNECTION=sqlite \
    -e DB_DATABASE=:memory: \
    -e DB_URL= \
    -v "$ROOT_DIR:/app" \
    -w /app \
    "$IMAGE_NAME" \
    php artisan route:list --json)"
ROUTES_JSON="$ROUTES_JSON" python3 - <<'PY'
import json
import os
import sys

routes = json.loads(os.environ["ROUTES_JSON"])
actual = sorted((route["method"], route["uri"]) for route in routes)
expected = sorted([
    ("GET|HEAD", "api/health"),
    ("GET|HEAD", "up"),
])

if actual != expected:
    print("Daftar rute tidak sesuai perkiraan:", file=sys.stderr)
    for method, uri in actual:
        print(f"  {method} {uri}", file=sys.stderr)
    print("Yang diharapkan:", file=sys.stderr)
    for method, uri in expected:
        print(f"  {method} {uri}", file=sys.stderr)
    raise SystemExit(1)

for method, uri in actual:
    print(f"  {method} {uri}")
PY

printf '\nVerifikasi berhasil. Basis data SQLite pengembangan tidak diakses.\n'
