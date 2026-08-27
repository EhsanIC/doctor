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
      <div className="flex min-h-screen bg-background">
        <aside className="hidden w-64 shrink-0 bg-sidebar md:block">
          <div className="flex h-14 items-center px-4">
            <Skeleton className="h-6 w-40" />
          </div>
          <div className="space-y-3 p-4">
            <Skeleton className="h-9 w-full" />
            <Skeleton className="h-9 w-full" />
            <Skeleton className="h-9 w-full" />
          </div>
          <div className="absolute bottom-0 w-64 p-4">
            <Skeleton className="h-9 w-full" />
          </div>
        </aside>
        <div className="flex min-w-0 flex-1 flex-col">
          <header className="flex h-14 items-center gap-3 px-4 md:px-8">
            <Skeleton className="size-8 rounded-md" />
            <Skeleton className="h-4 w-32" />
          </header>
          <main className="flex flex-1 flex-col gap-6 p-4 md:p-8">
            <div className="space-y-2">
              <Skeleton className="h-8 w-64" />
              <Skeleton className="h-4 w-80 max-w-full" />
            </div>
            <div className="grid gap-4 md:grid-cols-3">
              <Skeleton className="h-28 rounded-xl" />
              <Skeleton className="h-28 rounded-xl" />
              <Skeleton className="h-28 rounded-xl" />
            </div>
            <div className="rounded-xl bg-card p-6">
              <Skeleton className="mb-6 h-6 w-48" />
              <div className="space-y-4">
                <Skeleton className="h-8 w-full" />
                <Skeleton className="h-10 w-full" />
                <Skeleton className="h-10 w-full" />
                <Skeleton className="h-10 w-full" />
              </div>
            </div>
          </main>
        </div>
      </div>
    );
  }

  return <>{children}</>;
}