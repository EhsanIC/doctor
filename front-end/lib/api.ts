const BASE_URL = process.env.NEXT_PUBLIC_API_URL ?? "http://localhost:8000";

// ── CSRF cookie ──────────────────────────────────────────────────────────────

let csrfPromise: Promise<void> | null = null;

/**
 * Fetch the CSRF cookie from Laravel Sanctum.
 * Safe to call multiple times — only one request is made per page load.
 */
export async function csrfCookie(): Promise<void> {
  if (csrfPromise) return csrfPromise;

  csrfPromise = fetch(`${BASE_URL}/sanctum/csrf-cookie`, {
    credentials: "include",
  }).then(() => {
    // Success — promise stays cached so we never re-fetch.
  });

  return csrfPromise;
}

// ── XSRF token reader ────────────────────────────────────────────────────────

/**
 * Read the XSRF-TOKEN cookie that Laravel set after /sanctum/csrf-cookie.
 * Returns an empty string if the cookie hasn't been set yet.
 */
export function getXsrfToken(): string {
  if (typeof document === "undefined") return "";

  const match = document.cookie
    .split("; ")
    .find((row) => row.startsWith("XSRF-TOKEN="));

  return match ? decodeURIComponent(match.split("=")[1]) : "";
}

// ── Core fetch helpers ───────────────────────────────────────────────────────

async function buildHeaders(options: RequestInit): Promise<Headers> {
  const headers = new Headers(options.headers);

  // json Content-Type is the default, but skip for FormData
  // (the browser auto-sets multipart/form-data with the correct boundary).
  if (!(options.body instanceof FormData)) {
    headers.set("Content-Type", "application/json");
  }

  headers.set("Accept", "application/json");
  headers.set("X-XSRF-TOKEN", getXsrfToken());

  return headers;
}

async function handleResponse(res: Response): Promise<Response> {
  if (res.status === 401) {
    // Session expired — clear auth store and redirect.
    const { useAuthStore } = await import("@/lib/auth-store");
    useAuthStore.getState().clearUser();
    window.location.href = "/login";
  }

  if (!res.ok) {
    const error = await res.json().catch(() => null);
    throw new Error(error?.message ?? "Something went wrong");
  }

  return res;
}

/**
 * Fetch wrapper that handles Sanctum SPA cookies automatically.
 *
 * - Sends `credentials: 'include'` on every request.
 * - Attaches the `X-XSRF-TOKEN` header.
 * - Auto-redirects to `/login` on 401.
 * - Throws an `Error` with the backend's message on any other error.
 *
 * For multipart uploads (image), pass `FormData` as `options.body`.
 * The wrapper will skip the `Content-Type` header so the browser
 * sets the correct `multipart/form-data` boundary.
 */
export async function apiFetch<T = unknown>(
  endpoint: string,
  options: RequestInit = {},
): Promise<T> {
  const headers = await buildHeaders(options);

  const res = await fetch(`${BASE_URL}${endpoint}`, {
    ...options,
    credentials: "include",
    headers,
  });

  await handleResponse(res);

  return res.json() as Promise<T>;
}

/**
 * Like `apiFetch` but for endpoints that return 204 No Content.
 * Does not attempt to parse JSON — just returns void.
 */
export async function apiFetchNoContent(
  endpoint: string,
  options: RequestInit = {},
): Promise<void> {
  const headers = await buildHeaders(options);

  const res = await fetch(`${BASE_URL}${endpoint}`, {
    ...options,
    credentials: "include",
    headers,
  });

  await handleResponse(res);
}