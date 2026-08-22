"use client";

import { useEffect, useRef } from "react";
import { useRouter } from "next/navigation";
import { apiFetchNoContent, csrfCookie } from "@/lib/api";
import { useAuthStore } from "@/lib/auth-store";

export default function LogoutPage() {
  const router = useRouter();
  const clearUser = useAuthStore((s) => s.clearUser);
  const called = useRef(false);

  useEffect(() => {
    if (called.current) return;
    called.current = true;

    async function logout() {
      try {
        // Rotate the CSRF cookie before the POST so the token is valid.
        await csrfCookie();
        await apiFetchNoContent("/api/v1/logout", { method: "POST" });
      } finally {
        // Clear zustand and redirect regardless of server response.
        clearUser();
        router.replace("/login");
      }
    }

    logout();
  }, [clearUser, router]);

  return null;
}