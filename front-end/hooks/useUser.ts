"use client";

import useSWR from "swr";
import { apiFetch } from "@/lib/api";

function logAuthDebug(event: string, details: Record<string, unknown> = {}) {
  void fetch("/api/debug-auth", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ event, ...details }),
    keepalive: true,
  }).catch(() => {
    // Debug logging must never affect authentication.
  });
}

export type AppUser = {
  id: number;
  name: string;
  role: "admin" | "doctor" | "patient";
  /** Doctor profile id — present when the user has a doctor profile. */
  doctorProfileId?: number | null;
  doctorProfileImageUrl?: string | null;
};

// Raw shape from Laravel — Spatie HasRoles appends `roles` array,
// and `/me` includes the eager-loaded `doctor_profile` relation.
type RawUser = {
  id: number;
  name: string;
  roles?: { name: string }[];
  doctor_profile?: { id: number; image_url?: string | null; image?: string | null } | null;
};

function toAppUser(raw: RawUser): AppUser {
  const role = raw.roles?.[0]?.name;
  return {
    id: raw.id,
    name: raw.name,
    role: role === "admin" || role === "doctor" || role === "patient" ? role : "patient",
    doctorProfileId: raw.doctor_profile?.id ?? null,
    doctorProfileImageUrl: raw.doctor_profile?.image_url
      ?? (raw.doctor_profile?.image
        ? `${process.env.NEXT_PUBLIC_API_URL ?? "http://localhost:8000"}/api/v1/doctor/profile/${raw.doctor_profile.id}/image`
        : null),
  };
}

export function useUser() {
  const { data, error, isLoading, isValidating, mutate } = useSWR(
    "/api/v1/me",
    async (url) => {
      logAuthDebug("me_fetch_started", { url });
      try {
        const raw = await apiFetch<RawUser>(url);
        const mappedUser = toAppUser(raw);
        logAuthDebug("me_fetch_succeeded", {
          rawUser: raw,
          mappedUser,
          resolvedRole: mappedUser.role,
        });
        return mappedUser;
      } catch (error) {
        logAuthDebug("me_fetch_failed", {
          error: error instanceof Error ? error.message : String(error),
        });
        throw error;
      }
    },
    { shouldRetryOnError: false, revalidateOnMount: true },
  );

  return {
    user: data ?? null,
    doctorProfileId: data?.doctorProfileId ?? null,
    isAuthenticated: !!data,
    isLoading,
    isValidating,
    isError: !!error,
    mutate,
  };
}

/** Transform Laravel user object (e.g. from login response) into AppUser. */
export { toAppUser };