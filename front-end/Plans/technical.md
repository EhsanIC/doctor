# Phase 1 — Technical Plan

Lean technical plan: what to use, per page, and how the pieces connect. Assumes Next.js (App Router) + shadcn/ui + Bun, matching your earlier setup.

---

## Core Libraries

| Purpose | Library | Why |
|---|---|---|
| Forms + validation | `react-hook-form` + `zod` + `@hookform/resolvers` | Lightweight, fewer re-renders, pairs directly with shadcn's `form` component |
| API calls | `fetch` (built-in) | Auth is now handled by the browser via cookies — no token-attaching logic needed, so no axios required |
| Server state / caching | `swr` | Lightweight fetching/caching for GET requests |
| Global client state | `zustand` | Holds the current logged-in user object (id, name, role) in memory |
| Auth | **Laravel Sanctum SPA cookie mode** | Backend sets an httpOnly session cookie — frontend never touches a token directly |
| Date/time picking | shadcn `calendar` + `popover` (for booking form) | Already in shadcn |
| Notifications | shadcn `sonner` | Success/error toasts on form submit |

Install:
```bash
bun add react-hook-form zod @hookform/resolvers swr zustand
```

> No `axios` and no `js-cookie` needed — SPA cookie mode means the browser handles the cookie automatically (httpOnly, so JS can't read/write it anyway), and every request from `fetch` needs only `credentials: 'include'`.

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
  api.ts                          # fetch helper — always sends credentials: 'include'
  auth-store.ts                   # zustand store — current user (id, name, role)
components/
  ui/                             # shadcn components live here
```

---

## Auth Handling (SPA Cookie Mode — applies to every page)

Sanctum's SPA mode works differently from bearer tokens: the backend issues an **httpOnly session cookie**, so the frontend never sees or stores a token at all. This changes the flow:

1. **Before login, fetch the CSRF cookie once:**
   ```js
   await fetch(`${BASE_URL}/sanctum/csrf-cookie`, { credentials: 'include' });
   ```
   This sets an `XSRF-TOKEN` cookie the browser will need for subsequent unsafe requests (POST/PATCH/DELETE). Do this once, e.g. right before the login/register call, or once on app load.

2. **`lib/api.ts` — fetch helper:**
   ```ts
   const BASE_URL = process.env.NEXT_PUBLIC_API_URL; // e.g. http://localhost:8000

   function getXsrfToken() {
     return decodeURIComponent(
       document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='))?.split('=')[1] || ''
     );
   }

   export async function apiFetch(endpoint: string, options: RequestInit = {}) {
     const res = await fetch(`${BASE_URL}${endpoint}`, {
       ...options,
       credentials: 'include', // sends the session cookie automatically
       headers: {
         'Content-Type': 'application/json',
         Accept: 'application/json',
         'X-XSRF-TOKEN': getXsrfToken(), // required for POST/PATCH/DELETE
         ...options.headers,
       },
     });

     if (!res.ok) {
       const error = await res.json().catch(() => null);
       throw new Error(error?.message || 'Something went wrong');
     }
     return res.json();
   }
   ```
   No `Authorization` header, no manually stored token — the cookie does the work.

3. **On login success:** the backend sets the session cookie itself (via `Set-Cookie`). The frontend just takes the returned user object from the response body and puts it into the **Zustand store** — nothing goes into a cookie manually.

4. **`lib/auth-store.ts` (zustand):**
   ```ts
   import { create } from 'zustand';

   type User = { id: number; name: string; role: 'admin' | 'doctor' | 'patient' } | null;

   export const useAuthStore = create<{
     user: User;
     setUser: (u: User) => void;
     clearUser: () => void;
   }>((set) => ({
     user: null,
     setUser: (user) => set({ user }),
     clearUser: () => set({ user: null }),
   }));
   ```

5. **Route protection:** since there's no JS-readable token/cookie to check in `middleware.ts` (it's httpOnly), route protection happens **client-side**:
   - On app load / layout mount, call a "current user" endpoint (or rely on the login response already in Zustand for the current session) to confirm the session is still valid
   - If no user in the store after that check → redirect to `/login`
   - If role doesn't match the section (`/admin/*`, `/doctor/*`, `/patient/*`) → redirect to their correct dashboard
   - *(Open question: confirm with backend whether a `/me`-style endpoint exists to re-fetch the user on page refresh — needed since Zustand resets on reload)*

6. **Logout:** call `POST /logout` (backend clears the session cookie), then `clearUser()` on the Zustand store, then redirect to `/login`

This one auth setup covers Steps 1–8 — build it once, first, before any page.

---

## Page-by-Page Component Plan

### Step 1 — Login
- shadcn: `form`, `input`, `label`, `button`, `card` (wrap the form)
- `zod` schema: email + password required
- On submit: call `GET /sanctum/csrf-cookie` first (if not already called this session), then `POST /login`
- Put the returned user object into the Zustand store, redirect by role

### Step 2 — Patient Signup
- Same shadcn set as login + a "confirm password" field
- `zod`: email format, password match, min length
- On submit: `POST /register`, then set user in Zustand store (registration likely auto-logs in and returns the user + sets the session cookie) → redirect

### Step 3 — Doctor Signup
- Same as patient signup, but `POST /doctor/register`
- On success: don't auto-login — show a `sonner` toast/message ("pending approval") and redirect to `/login`

### Step 4 — Admin: Doctor List
- shadcn: `table`, `badge` (status color), `button`, `dropdown-menu` (Approve/Disable actions per row)
- Data: `swr` on `GET /admin/doctors` via `apiFetch`
- Approve/Disable: call `apiFetch` with `PATCH /admin/doctors/{id}`, then `mutate()` to refresh the table
- Use `skeleton` while loading

### Step 5 — Doctor: Complete Profile
- shadcn: `form`, `input`, `select` (specialty — hardcoded/seeded options), `textarea` (description), `label`, `button`
- Image upload: plain `<input type="file">` styled with shadcn `input`, since this is `multipart/form-data` — use `FormData` directly with `fetch` (still needs `credentials: 'include'` and the `X-XSRF-TOKEN` header, but skip the `Content-Type: application/json` header so the browser sets the correct multipart boundary itself)
- On submit: `PATCH /doctor/profile/{id}`, show `sonner` success toast

### Step 6 — Patient: Find a Doctor
- shadcn: `card` (one per doctor), `avatar`/image, `badge` (specialty)
- Data: `swr` on `GET /patient/appointment` (per your API doc, returns approved doctors)
- Simple grid layout, `skeleton` cards while loading
- Each card links to `/patient/doctors/[id]`

### Step 7 — Doctor Detail + Booking
- shadcn: `card` (profile display), `calendar` + `popover` (date picker), `select` or native time input (time), `textarea` (notes), `form`, `button`
- `zod`: date and time required, notes optional
- On submit: `POST /patient/appointment` via `apiFetch`, `sonner` confirmation, redirect to patient's appointment list (or just show success state on the same page)

### Step 8 — Doctor: My Appointments
- shadcn: `table`, `badge` (status), `dropdown-menu` or inline `button`s (Approve/Cancel)
- Data: `swr` on `GET /doctor/appointment`
- Approve/Cancel: call `apiFetch` with `PATCH /doctor/appointment/{id}` → `mutate()` to refresh

---

## Pattern Used Repeatedly (build once, reuse)

Almost every page follows the same shape:
1. `swr` (`useSWR` + `apiFetch`) to fetch data → show `skeleton` while loading
2. Data rendered in a `table` or `card` grid
3. Actions (approve/cancel/submit) call `apiFetch` directly → on success, `mutate()` to refresh + `sonner` toast
4. Forms use `react-hook-form` + `zod` + shadcn `form` component consistently

Because the pattern repeats, consider building **one reusable data-table wrapper** (for Steps 4, 8) and **one reusable form wrapper** (for Steps 1, 2, 3, 5, 7) early — it'll save rebuilding boilerplate on each page.

---

## Component Checklist (shadcn — should already have most from earlier)

```bash
bunx --bun shadcn@latest add form input label select textarea button card table badge avatar dropdown-menu calendar popover sonner skeleton
```

---

## Backend Requirements for SPA Cookie Mode (confirm these exist)

Sanctum's SPA mode only works if the backend is configured for it — this isn't purely a frontend decision. Worth confirming/setting up on the Laravel side:

- `SANCTUM_STATEFUL_DOMAINS` in `.env` includes your frontend's domain (e.g. `localhost:3000`)
- `SESSION_DOMAIN` set correctly so the cookie is shared between frontend and backend during local dev
- CORS config (`config/cors.php`) has `supports_credentials: true` and `allowed_origins` includes your frontend URL
- The `EnsureFrontendRequestsAreStateful` middleware is applied to the API routes
- A route exists (or the login response includes) a way to get **"who is the currently logged-in user"** — needed to repopulate Zustand after a page refresh, since the cookie persists but your in-memory store doesn't