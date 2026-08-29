# Doctor Appointment Platform

A full-stack doctor appointment booking platform: patients book appointments with doctors, doctors manage their profile and appointments, and admins approve new doctors and manage specialties.

## Stack

| Layer | Tech |
|---|---|
| Back-end API | Laravel 12 (PHP 8.2+), Sanctum SPA cookie auth, spatie/laravel-permission (roles/permissions), L5-Swagger (OpenAPI docs), Pest for tests |
| Front-end | Next.js 16 + React 19 SPA, Tailwind CSS 4, SWR, Zustand, react-hook-form + Zod, shadcn-style UI, Bun/npm |
| Database | MySQL |

## Project layout

```
back-end/    Laravel 12 JSON API (served on http://localhost:8000)
front-end/   Next.js 16 SPA (served on http://localhost:3000)
```

## Features

- **Auth** — single login endpoint for all users (`POST /api/v1/login`) using Laravel Sanctum SPA cookie sessions (CSRF cookie + `X-XSRF-TOKEN` header). Patient self-registration is instant; doctor registration creates an account with `pending` status.
- **Roles** — `admin`, `doctor`, `patient` via spatie/laravel-permission; fine-grained permissions (e.g. `appointment.view`, `appointment.create`, `profile.update`) enforced as route middleware.
- **Admin panel** — list doctors, approve/reject/disable doctor accounts, manage specialties (CRUD).
- **Doctor panel** — profile view/update (specialty, bio, image, working hours), view and update appointments (confirm/cancel).
- **Patient portal** — browse doctors by specialty, book appointments.
- **API docs** — OpenAPI annotations served by L5-Swagger at `/api/documentation`.

## Installation

### 1. Back-end (Laravel)

```bash
cd back-end
composer install
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set your MySQL credentials (defaults: `DB_DATABASE=interview_project`, user `root`). The SPA defaults are already correct:

```
SANCTUM_STATEFUL_DOMAINS=localhost:3000
FRONTEND_URL=http://localhost:3000
```

Create the database, then migrate + seed (creates default users and sample data):

```bash
php artisan migrate --seed
```

### 2. Front-end (Next.js)

```bash
cd front-end
bun install        # or: npm install
```

If your API is not on `http://localhost:8000`, set it in `front-end/.env.local`:

```
NEXT_PUBLIC_API_URL=http://localhost:8000
```

## Running

Back-end (from `back-end/`):

```bash
php artisan serve          # API on http://localhost:8000
```

Front-end (from `front-end/`):

```bash
bun run dev                # or: npm run dev — app on http://localhost:3000
```

Open **http://localhost:3000**.

### Seeded test accounts (password: `password`)

| Role | Email |
|---|---|
| Admin | admin@test.com |
| Doctor | doctor@test.com |
| Patient | patient@test.com |

### Tests

```bash
cd back-end && php artisan test
```

## API overview (all under `/api/v1`)

| Endpoint | Auth | Purpose |
|---|---|---|
| `POST /login`, `POST /logout`, `GET /me` | — / Sanctum | Session auth, current user |
| `POST /register` | public | Patient self-registration |
| `POST /doctor/register` | public | Doctor registration (status `pending`) |
| `GET /specialties` | public | Specialty list (form dropdown) |
| `GET /doctor/profile/{profile}`, `PATCH /doctor/profile/{profile}` | doctor | View/update own profile |
| `GET /doctor/appointment`, `PATCH /doctor/appointment/{id}` | doctor | List/update own appointments |
| `GET /patient/appointment`, `POST /patient/appointment` | patient | List/book appointments |
| `GET /admin/doctors`, `PUT|PATCH /admin/doctors/{doctor}` | admin | Approve/reject/disable doctors |
| `apiResource /admin/specialties` | admin | Specialty CRUD |
