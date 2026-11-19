# CBT API

Standalone Laravel 13 API foundation for the CBT service. This repository is isolated from the LMS and uses a local SQLite database by default.

## Requirements

- Docker Desktop installed and active.
- Docker image `ucademy-php83-composer` (PHP 8.3.36, Composer 2.11.2, and zip).

Use the already-provisioned `ucademy-php83-composer` image. All PHP, Composer, Artisan, and test commands below run inside that image; no host PHP or Composer installation is needed. Run commands from this repository root.

## Local setup

```sh
cp .env.example .env
touch database/database.sqlite
docker run --rm -v "$PWD:/app" -w /app ucademy-php83-composer composer install
docker run --rm -v "$PWD:/app" -w /app ucademy-php83-composer php artisan key:generate
```

The `.env` file and SQLite database are local runtime files and are ignored by Git.

## Migrations

Apply pending migrations to the local SQLite database:

```sh
docker run --rm -v "$PWD:/app" -w /app ucademy-php83-composer php artisan migrate
```

The default database connection is SQLite and its path is `database/database.sqlite`. To change local database settings, edit `.env`; do not add LMS database credentials to this service.

## Run locally

The application exposes no user-facing HTML pages. Start the Laravel server when testing API routes:

```sh
docker run --rm -p 8000:8000 -v "$PWD:/app" -w /app ucademy-php83-composer php artisan serve --host=0.0.0.0
```

Keep Docker Desktop active while running any command or the local server. The framework health check is available at `/up`.

## Endpoints

### Health check

- **Route:** `GET /api/health`
- **Purpose:** Availability check for the standalone CBT API service.
- **Response:** HTTP 200 OK
  ```json
  {"success":true,"service":"cbt-api"}
  ```
- **Security & Privacy:** Availability check only; does not expose database credentials, environment variables, debug data, or user/course information.

## Tests

Run the full test suite:

```sh
docker run --rm -v "$PWD:/app" -w /app ucademy-php83-composer php artisan test
```

Run a specific test:

```sh
docker run --rm -v "$PWD:/app" -w /app ucademy-php83-composer php artisan test --filter=HealthEndpointTest
```

## Current scope

This is only the standalone API foundation. There is no login/authentication and no CBT business API endpoint yet. It is not production-ready.
