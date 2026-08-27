"use client";

import { useEffect } from "react";
import { useRouter } from "next/navigation";
import { useUser } from "@/hooks/useUser";
import { Skeleton } from "@/components/ui/skeleton";

export default function ProtectedLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const router = useRouter();
  const { user, isLoading } = useUser();

  useEffect(() => {
    const logAuthDebug = (event: string, details: Record<string, unknown> = {}) => {
      void fetch("/api/debug-auth", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ event, ...details }),
        keepalive: true,
      }).catch(() => {
        // Debug logging must never affect authentication.
      });
    };

    logAuthDebug("protected_layout_effect", {
      isLoading,
      hasUser: !!user,
      user,
      userRole: user?.role ?? null,
    });

    if (isLoading) {
      logAuthDebug("protected_layout_waiting_for_user");
      return;
    }

    if (!user) {
      logAuthDebug("protected_layout_redirect_login", {
        reason: "no_user_after_loading",
      });
      router.replace("/login");
      return;
    }

    if (user.role !== "admin") {
      const destination =
        user.role === "doctor" ? "/doctor/appointments" : "/patient/doctors";
      logAuthDebug("protected_layout_redirect_by_role", {
        role: user.role,
        destination,
      });
      router.replace(destination);
      return;
    }

    logAuthDebug("protected_layout_admin_allowed", { role: user.role });
  }, [isLoading, user, router]);

  if (isLoading || !user) {
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