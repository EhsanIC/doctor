"use client";

import { useEffect } from "react";
import { useRouter } from "next/navigation";
import { useAuthStore } from "@/lib/auth-store";

export default function PatientLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const user = useAuthStore((s) => s.user);
  const router = useRouter();

  useEffect(() => {
    if (!user) return;
    if (user.role !== "patient") {
      router.replace(
        user.role === "admin" ? "/admin/doctors" : "/doctor/appointments",
      );
    }
  }, [user, router]);

  if (!user || user.role !== "patient") return null;
  return <>{children}</>;
}