"use client";

import { useEffect, useRef } from "react";
import { useRouter } from "next/navigation";
import { mutate } from "swr";
import { apiFetchNoContent, csrfCookie } from "@/lib/api";

export default function LogoutPage() {
  const router = useRouter();
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
        // Clear SWR cache and redirect regardless of server response.
        mutate("/api/v1/me", undefined, { revalidate: false });
        router.replace("/login");
      }
    }

    logout();
  }, [router]);

  return null;
}