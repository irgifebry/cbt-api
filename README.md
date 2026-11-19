# CBT API

Fondasi API Laravel 13 mandiri untuk layanan CBT. Repositori ini terpisah dari LMS dan secara default menggunakan basis data SQLite lokal.

## Requirements

- Docker Desktop telah terpasang dan aktif.
- Image Docker `ucademy-php83-composer` (PHP 8.3 CLI, Composer 2, dan ekstensi zip).

Untuk membangun image secara lokal dari `Dockerfile` yang disediakan:

```sh
docker build -t ucademy-php83-composer .
```

Semua perintah PHP, Composer, Artisan, dan pengujian di bawah dijalankan di dalam image tersebut; PHP atau Composer tidak perlu dipasang di host. Jalankan perintah dari direktori utama repositori ini.

## Local setup

```sh
cp .env.example .env
touch database/database.sqlite
docker run --rm -v "$PWD:/app" -w /app ucademy-php83-composer composer install
docker run --rm -v "$PWD:/app" -w /app ucademy-php83-composer php artisan key:generate
```

File `.env` dan basis data SQLite adalah berkas lokal untuk runtime dan diabaikan oleh Git.

## Migrations

Terapkan migrasi yang tertunda ke basis data SQLite lokal:

```sh
docker run --rm -v "$PWD:/app" -w /app ucademy-php83-composer php artisan migrate
```

Koneksi basis data bawaan adalah SQLite dengan lokasi `database/database.sqlite`. Untuk mengubah pengaturan basis data lokal, edit `.env`; jangan tambahkan kredensial basis data LMS ke layanan ini.

## Run locally

Aplikasi ini tidak menyediakan halaman HTML yang ditujukan kepada pengguna. Jalankan server Laravel saat menguji rute API:

```sh
docker run --rm -p 8000:8000 -v "$PWD:/app" -w /app ucademy-php83-composer php artisan serve --host=0.0.0.0
```

Pastikan Docker Desktop tetap aktif saat menjalankan perintah apa pun atau server lokal. Pemeriksaan kesehatan framework tersedia di `/up`.

## API documentation

- [Spesifikasi OpenAPI](docs/openapi.yaml) — kontrak sumber untuk endpoint yang saat ini telah diimplementasikan.
- [Koleksi Postman](docs/postman/CBT-API.postman_collection.json) — impor berkas ini ke Postman untuk mengirim permintaan yang didokumentasikan.

Koleksi ini memiliki variabel `baseUrl` yang diatur ke `http://localhost:8000`. Ubah nilainya di Postman jika server lokal menggunakan alamat lain.

### Health check

- **Route:** `GET /api/health`
- **Tujuan:** Pemeriksaan ketersediaan layanan API CBT mandiri.
- **Response:** HTTP 200 OK
  ```json
  {"success":true,"service":"cbt-api"}
  ```
- **Keamanan & Privasi:** Hanya memeriksa ketersediaan; tidak mengekspos kredensial basis data, variabel lingkungan, data debug, atau informasi pengguna/kursus.

## Tests

Jalankan skrip verifikasi lokal dari direktori kerja mana pun:

```sh
./scripts/verify-local.sh
```

Skrip ini membangun image Docker, menjalankan migrasi pada basis data SQLite dalam memori yang terisolasi, menjalankan seluruh rangkaian pengujian, dan memastikan bahwa satu-satunya rute terdaftar adalah `/api/health` dan `/up`. Skrip ini tidak mengubah basis data pengembangan lokal di `database/database.sqlite`.

Jalankan pengujian langsung dari direktori utama repositori:

```sh
docker run --rm -v "$PWD:/app" -w /app ucademy-php83-composer php artisan test
```

Jalankan pengujian tertentu:

```sh
docker run --rm -v "$PWD:/app" -w /app ucademy-php83-composer php artisan test --filter=HealthEndpointTest
```

## Current scope

Ini hanya fondasi API mandiri. Belum tersedia login/autentikasi maupun endpoint API untuk kebutuhan bisnis CBT. Layanan ini belum siap digunakan di lingkungan produksi.
