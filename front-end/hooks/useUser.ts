"use client";

import useSWR from "swr";
import { apiFetch } from "@/lib/api";

export type AppUser = {
  id: number;
  name: string;
  role: "admin" | "doctor" | "patient";
};

// Raw shape from Laravel — Spatie HasRoles appends `roles` array.
type RawUser = {
  id: number;
  name: string;
  roles?: { name: string }[];
};

function toAppUser(raw: RawUser): AppUser {
  return {
    id: raw.id,
    name: raw.name,
    role: (raw.roles?.[0]?.name as AppUser["role"]) ?? "patient",
  };
}

export function useUser() {
  const { data, error, isLoading, mutate } = useSWR(
    "/api/v1/me",
    async (url) => {
      const raw = await apiFetch<RawUser>(url);
      return toAppUser(raw);
    },
    { shouldRetryOnError: false },
  );

  return {
    user: data ?? null,
    isAuthenticated: !!data,
    isLoading,
    isError: !!error,
    mutate,
  };
}

/** Transform Laravel user object (e.g. from login response) into AppUser. */
export { toAppUser };