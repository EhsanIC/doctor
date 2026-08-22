# Backend API Routes

Reference for all backend routes. Generated from the OpenAPI/Swagger doc (`storage/api-docs/api-docs.json`) and the route definitions in `routes/api_v1.php`.

- **Base URL:** `/api/v1`
- **Content type:** `application/json` (profile image update uses `multipart/form-data`)
- **Auth scheme:** Sanctum SPA cookie session. Call `GET /sanctum/csrf-cookie` before POST/PATCH/DELETE requests, and always send `credentials: 'include'`. The browser manages the session cookie automatically — no bearer token needed.

| Label | Meaning |
| --- | --- |
| Public | No authentication required |
| Auth | Requires a valid Sanctum session cookie (`auth:sanctum`) |
| Admin | Auth + `role:admin` |
| Auth + permission | Auth + the listed Spatie permission (and, for profile routes, the `DoctorProfile` policy) |

All non-Public routes also return `401` (missing/invalid token) and, where a role/permission is enforced, `403` when the check fails.

---

## Auth

| Method | Path | Summary | Access | Success |
| --- | --- | --- | --- | --- |
| POST | `/login` | Authenticate a user and start a Sanctum SPA cookie session | Public | 200 |
| POST | `/logout` | Destroy the session and log out | Auth | 204 |
| POST | `/register` | Register a new patient (regular user), auto-login | Public | 201 |
| POST | `/doctor/register` | Register a new doctor (pending admin approval) | Public | 201 |

| GET | `/me` | Get the currently authenticated user (for SPA rehydration on refresh) | Auth | 200 |

---

## Admin

| Method | Path | Summary | Access | Success |
| --- | --- | --- | --- | --- |
| GET | `/admin/doctors` | List all doctors | Admin | 200 |
| PUT | `/admin/doctors/{doctor}` | Approve / reject / deactivate a doctor (PUT) | Admin | 200 |
| PATCH | `/admin/doctors/{doctor}` | Approve / reject / deactivate a doctor | Admin | 200 |
| GET | `/admin/doctors/profile` | List all doctor profiles (paginated) | Admin | 200 |
| PUT | `/admin/doctors/profile/{profile}` | Edit a doctor profile (PUT) | Admin | 200 |
| PATCH | `/admin/doctors/profile/{profile}` | Edit a doctor profile | Admin | 200 |
| GET | `/admin/specialties` | List all specialties (paginated) | Admin | 200 |
| POST | `/admin/specialties` | Add a specialty | Admin | 201 |
| GET | `/admin/specialties/{specialty}` | Show a specialty | Admin | 200 |
| PUT | `/admin/specialties/{specialty}` | Edit a specialty (PUT) | Admin | 200 |
| PATCH | `/admin/specialties/{specialty}` | Edit a specialty | Admin | 200 |
| DELETE | `/admin/specialties/{specialty}` | Delete a specialty (soft delete) | Admin | 204 |

---

## Doctor

| Method | Path | Summary | Access | Success |
| --- | --- | --- | --- | --- |
| GET | `/doctor/appointment` | View own appointments (paginated) | Auth + `appointment.view` | 200 |
| PATCH | `/doctor/appointment/{appointment}` | Approve / cancel an appointment | Auth + `appointment.pending\|appointment.cancel` | 200 |
| GET | `/doctor/profile/{profile}` | View own doctor profile | Auth + `profile.view` + policy | 200 |
| PATCH | `/doctor/profile/{profile}` | Complete / update own profile | Auth + `profile.update` + policy | 200 |

Doctor routes also require the doctor profile to be `active` (`doctor.active` middleware).

---

## Patient

| Method | Path | Summary | Access | Success |
| --- | --- | --- | --- | --- |
| GET | `/patient/appointment` | View approved (active) doctors (paginated) | Auth + `doctor.view` | 200 |
| POST | `/patient/appointment` | Book an appointment | Auth + `appointment.create` | 201 |

---

## General

| Method | Path | Summary | Access | Success |
| --- | --- | --- | --- | --- |
| GET | `/hello` | API root / health check | Public | 200 |
| GET | `/test` | Authenticated test endpoint | Auth | 200 |

---

## Request bodies

| Endpoint | Schema |
| --- | --- |
| POST `/login` | `LoginRequest` — `email`, `password` |
| POST `/register` | `PatientRegisterRequest` — `name`, `email`, `password`, `password_confirmation` |
| POST `/doctor/register` | `DoctorRegisterRequest` — `name`, `email`, `password`, `password_confirmation`, `specialty_id` (optional) |
| PUT/PATCH `/admin/doctors/{doctor}` | `UpdateDoctorStatusRequest` — `status` (`pending`/`active`/`disabled`) |
| PUT/PATCH `/admin/doctors/profile/{profile}` | `UpdateDoctorProfileAdminRequest` — `specialty_id`, `status`, `image`, `bio` |
| POST `/admin/specialties` | `StoreSpecialtyRequest` — `name` |
| PUT/PATCH `/admin/specialties/{specialty}` | `UpdateSpecialtyRequest` — `name` |
| PATCH `/doctor/profile/{profile}` | `UpdateDoctorProfileRequest` (`multipart/form-data`) — `specialty_id`, `image`, `bio`, `mobile`, `medical_code`, `address`, `working_hours` |
| PATCH `/doctor/appointment/{appointment}` | `UpdateAppointmentStatusRequest` — `status` (`approved`/`canceled`) |
| POST `/patient/appointment` | `PatientAppointmentRequest` — `doctor_id`, `appointment_date`, `appointment_time`, `description` (optional) |

## Response schemas

- `User`, `Specialty`, `DoctorProfile`, `DoctorProfileFull`, `DoctorProfileDetail`
- `DoctorManagementItem`
- `Appointment`
- `PaginatedDoctorProfile`, `PaginatedDoctorProfileFull`, `PaginatedSpecialty`, `PaginatedAppointment`
- `LoginResponse`, `PatientRegisterResponse`, `RegisterResponse`, `BookAppointmentResponse`

## Standard error responses

| Code | Meaning |
| --- | --- |
| 401 | `Unauthorized` — missing or expired session |
| 403 | `Forbidden` — insufficient role/permission |
| 404 | `NotFound` — resource does not exist |
| 422 | `ValidationError` — request validation failed |
