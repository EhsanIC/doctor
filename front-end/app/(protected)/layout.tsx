"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { useAuthStore } from "@/lib/auth-store";
import { apiFetch } from "@/lib/api";
import type { AppUser } from "@/lib/auth-store";
import { Skeleton } from "@/components/ui/skeleton";

export default function ProtectedLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const router = useRouter();
  const { user, setUser } = useAuthStore();
  const [checking, setChecking] = useState(!user);

  useEffect(() => {
    // Already have a user in the store (e.g. just logged in) — skip the /me call.
    if (user) {
      setChecking(false);
      return;
    }

    let cancelled = false;

    apiFetch<AppUser>("/api/v1/me")
      .then((u) => {
        if (!cancelled) {
          setUser(u);
          setChecking(false);
        }
      })
      .catch(() => {
        if (!cancelled) {
          router.replace("/login");
        }
      });

    return () => {
      cancelled = true;
    };
  }, [user, setUser, router]);

  if (checking) {
    return (
      <div className="flex min-h-screen items-center justify-center p-8">
        <div className="w-full max-w-sm space-y-4">
          <Skeleton className="h-8 w-3/4" />
          <Skeleton className="h-4 w-full" />
          <Skeleton className="h-4 w-5/6" />
          <Skeleton className="h-10 w-full mt-8" />
          <Skeleton className="h-10 w-full" />
        </div>
      </div>
    );
  }

  return <>{children}</>;
}