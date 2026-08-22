# Phase 1 — Technical Plan

Lean technical plan: what to use, per page, and how the pieces connect. Assumes Next.js (App Router) + shadcn/ui + Bun, matching your earlier setup.

---

## Core Libraries

| Purpose | Library | Why |
|---|---|---|
| Forms + validation | `react-hook-form` + `zod` + `@hookform/resolvers` | Pairs directly with shadcn's `form` component |
| API calls | `fetch` (built-in) or `axios` | Either works; axios is a bit easier for attaching the auth token globally |
| Server state / caching | `@tanstack/react-query` | Handles loading/error states, refetching after approve/cancel actions — saves a lot of manual `useState` |
| Auth token storage | `js-cookie` or plain `localStorage` | Store the Sanctum bearer token after login |
| Date/time picking | shadcn `calendar` + `popover` (for booking form) | Already in shadcn |
| Notifications | shadcn `sonner` | Success/error toasts on form submit |

Install:
```bash
bun add react-hook-form zod @hookform/resolvers @tanstack/react-query axios js-cookie
```

---

## Folder Structure (App Router)

```
app/
  login/page.tsx
  signup/
    patient/page.tsx
    doctor/page.tsx
  admin/
    doctors/page.tsx              # Step 4
  doctor/
    profile/page.tsx              # Step 5
    appointments/page.tsx         # Step 8
  patient/
    doctors/page.tsx              # Step 6
    doctors/[id]/page.tsx         # Step 7
lib/
  api.ts                          # axios instance with base URL + auth header
  auth.ts                         # token save/read/clear, role helpers
components/
  ui/                             # shadcn components live here
```

---

## Auth Handling (applies to every page)

- **On login success:** save `{ token, role }` to a cookie (so middleware can read it) — `js-cookie` is fine for this
- **`lib/api.ts`:** create one axios instance with `baseURL: '/api/v1'` and an interceptor that attaches `Authorization: Bearer <token>` to every request automatically — don't repeat this per page
- **Route protection:** use Next.js `middleware.ts` to check the cookie and redirect:
  - No token → send to `/login`
  - Wrong role trying to access `/admin/*`, `/doctor/*`, `/patient/*` → redirect to their own dashboard
- **Logout:** clear the cookie, call `POST /logout`, redirect to `/login`

This one auth setup covers Steps 1–8 — build it once, first, before any page.

---

## Page-by-Page Component Plan

### Step 1 — Login
- shadcn: `form`, `input`, `label`, `button`, `card` (wrap the form)
- `zod` schema: email + password required
- On submit: call `POST /login`, save token, redirect by role (from the response)

### Step 2 — Patient Signup
- Same shadcn set as login + a "confirm password" field
- `zod`: email format, password match, min length
- On submit: `POST /register`, then auto-login (reuse the login logic) → redirect

### Step 3 — Doctor Signup
- Same as patient signup, but `POST /doctor/register`
- On success: don't auto-login — show a `sonner` toast/message ("pending approval") and redirect to `/login`

### Step 4 — Admin: Doctor List
- shadcn: `table`, `badge` (status color), `button`, `dropdown-menu` (Approve/Disable actions per row)
- Data: `react-query` `useQuery` on `GET /admin/doctors`
- Approve/Disable: `useMutation` calling `PATCH /admin/doctors/{id}`, then `invalidateQueries` to refresh the table
- Use `skeleton` while loading

### Step 5 — Doctor: Complete Profile
- shadcn: `form`, `input`, `select` (specialty — hardcoded/seeded options), `textarea` (description), `label`, `button`
- Image upload: plain `<input type="file">` styled with shadcn `input`, since this is `multipart/form-data` — don't use JSON axios call for this one, use `FormData`
- On submit: `PATCH /doctor/profile/{id}`, show `sonner` success toast

### Step 6 — Patient: Find a Doctor
- shadcn: `card` (one per doctor), `avatar`/image, `badge` (specialty)
- Data: `react-query` on `GET /patient/appointment` (per your API doc, returns approved doctors)
- Simple grid layout, `skeleton` cards while loading
- Each card links to `/patient/doctors/[id]`

### Step 7 — Doctor Detail + Booking
- shadcn: `card` (profile display), `calendar` + `popover` (date picker), `select` or native time input (time), `textarea` (notes), `form`, `button`
- `zod`: date and time required, notes optional
- On submit: `POST /patient/appointment`, `sonner` confirmation, redirect to patient's appointment list (or just show success state on the same page)

### Step 8 — Doctor: My Appointments
- shadcn: `table`, `badge` (status), `dropdown-menu` or inline `button`s (Approve/Cancel)
- Data: `react-query` on `GET /doctor/appointment`
- Approve/Cancel: `useMutation` → `PATCH /doctor/appointment/{id}` → `invalidateQueries` to refresh

---

## Pattern Used Repeatedly (build once, reuse)

Almost every page follows the same shape:
1. `react-query` `useQuery` to fetch data → show `skeleton` while loading
2. Data rendered in a `table` or `card` grid
3. Actions (approve/cancel/submit) use `useMutation` → on success, `invalidateQueries` + `sonner` toast
4. Forms use `react-hook-form` + `zod` + shadcn `form` component consistently

Because the pattern repeats, consider building **one reusable data-table wrapper** (for Steps 4, 8) and **one reusable form wrapper** (for Steps 1, 2, 3, 5, 7) early — it'll save rebuilding boilerplate on each page.

---

## Component Checklist (shadcn — should already have most from earlier)

```bash
bunx --bun shadcn@latest add form input label select textarea button card table badge avatar dropdown-menu calendar popover sonner skeleton
```