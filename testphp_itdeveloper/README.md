# IT Fullstack Laravel Test - CRUD + Redis + S3 + PostgreSQL

**Author:** Lagsya Akrama

## Deskripsi
Aplikasi Laravel ini melakukan CRUD pada tabel `my_client` menggunakan PostgreSQL sebagai database utama, Redis untuk cache data (berdasarkan slug), dan AWS S3 untuk menyimpan file logo.

## Fitur
- Create, Read, Update, Delete (soft delete)
- Caching data client ke Redis (berdasarkan slug)
- Upload logo ke AWS S3
- Soft delete via `deleted_at`
- Auto regenerate cache saat update

## Teknologi
- Laravel 11 (project template)
- PostgreSQL
- Redis
- AWS S3
- PHP 8.1+

## Cara pakai (lokal)
1. `composer install`
2. Copy `.env.example` ke `.env` dan isi credential (DB, Redis, AWS)
3. `php artisan key:generate`
4. `php artisan migrate`
5. `php artisan serve`
6. Test API via Postman / Thunder Client

## Endpoints (API)
- `GET /api/clients`
- `POST /api/clients` (multipart/form-data untuk upload client_logo)
- `GET /api/clients/{id}`
- `PUT /api/clients/{id}`
- `DELETE /api/clients/{id}`

## Note
- Jangan upload file `.env` atau folder `vendor` ke GitHub.
- Invite user `asiasiapac` ke repository setelah push.
