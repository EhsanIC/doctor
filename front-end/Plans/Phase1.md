# Phase 1 Build Plan — Doctor Appointment System (Functional MVP)

Goal: get one complete end-to-end cycle working — **doctor registers → admin approves → doctor completes profile → patient books → doctor approves appointment.** No polish, no extra pages.

---

## Build Order (do in this sequence)

Each step depends on the one before it, so don't skip ahead — you won't be able to test Step 4 without Step 3 working, etc.

### Step 1 — Login Page
- One form: email + password
- On success, redirect based on role:
  - Admin → Doctor List page
  - Doctor → My Appointments page
  - Patient → Find a Doctor page
- Show a simple error message on failed login (wrong password, etc.)

**Backend route used:** `POST /login`

---

### Step 2 — Patient Signup Page
- Form: name, email, password, confirm password
- On success: auto-login and redirect to Find a Doctor page
- Basic validation: valid email format, passwords match, password minimum length

**Backend route used:** `POST /register`

---

### Step 3 — Doctor Signup Page
- Form: name, email, password, confirm password (specialty optional at this stage)
- On success: show message *"Registered — your account is pending admin approval"* and redirect to Login
- Same basic validation as patient signup

**Backend route used:** `POST /doctor/register`

---

### Step 4 — Admin: Doctor List + Approve/Disable
- Table showing all doctors: name, email, status (Pending/Active/Disabled)
- A button per row to change status (Approve / Disable)
- No filters or search needed yet — a plain table is fine
- This is the page Admin lands on after login

**Backend routes used:** `GET /admin/doctors`, `PUT` or `PATCH /admin/doctors/{doctor}`

---

### Step 5 — Doctor: Complete Profile
- Form: mobile number, image upload, specialty (dropdown — hardcode 2–3 specialty options for now, e.g. Cardiology, Dermatology, Neurology), medical license code, address, description, working hours
- Doctor must complete this before patients can find them
- Simple "Save" button, no drafts/autosave needed

**Backend route used:** `PATCH /doctor/profile/{profile}`

> Note: since Specialty Management UI is skipped in Phase 1, specialties need to exist in the database already — add 2–3 directly via a seeder or manually, so this dropdown has options.

---

### Step 6 — Patient: Find a Doctor (simple list)
- Plain list/grid of approved doctors only: name, specialty, photo
- No search or filter bar yet — just show all approved doctors
- Each doctor is clickable → goes to Step 7

**Backend route used:** `GET /patient/appointment` (per your API doc, this endpoint returns approved/active doctors)

---

### Step 7 — Patient: Doctor Detail + Book Appointment
- Show doctor's full profile: name, specialty, bio, address, working hours
- Booking form below/beside it: date picker, time picker, optional notes field
- Submit button creates the appointment and shows a confirmation message

**Backend route used:** `POST /patient/appointment`

---

### Step 8 — Doctor: My Appointments (Approve/Cancel)
- Table/list of the doctor's incoming appointments: patient name, date, time, notes, status
- Approve / Cancel buttons per row
- This is where the full loop closes — once a doctor approves, the cycle is proven end-to-end

**Backend routes used:** `GET /doctor/appointment`, `PATCH /doctor/appointment/{appointment}`

---

## What Each Role Can Do By End of Phase 1

| Role | Can do |
|---|---|
| **Admin** | Log in, view all doctors, approve or disable a doctor |
| **Doctor** | Sign up, log in, complete profile, view appointments, approve/cancel appointments |
| **Patient** | Sign up, log in, view approved doctors, book an appointment |

---

## Explicitly Not in Phase 1
- Home/landing page
- Public doctor browsing (logged-out visitors)
- Specialty management UI (add/edit/delete) — specialties exist only via seed data
- Dashboard "Overview" home screens for any role
- Pending-approval dedicated screen for doctors — a generic "not approved yet" message is enough
- Search, filters, pagination UI
- Separate Appointment Detail page — show info inline in the list

---

## Suggested Checklist

- [ ] Step 1 — Login page
- [ ] Step 2 — Patient signup
- [ ] Step 3 — Doctor signup
- [ ] Step 4 — Admin doctor list + approve/disable
- [ ] Step 5 — Doctor profile completion form
- [ ] Step 6 — Patient: find a doctor (simple list)
- [ ] Step 7 — Patient: doctor detail + booking form
- [ ] Step 8 — Doctor: my appointments (approve/cancel)
- [ ] End-to-end test: register doctor → approve → complete profile → patient books → doctor approves

Once every box is checked, you have a fully working product — Phase 2 (landing page, specialty management, dashboard overviews, filters/search, polish) can be layered on top without touching this core flow.