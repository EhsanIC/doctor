"use client";

import { useEffect } from "react";
import { useRouter } from "next/navigation";
import { useAuthStore } from "@/lib/auth-store";

export default function AdminLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const user = useAuthStore((s) => s.user);
  const router = useRouter();

  useEffect(() => {
    if (!user) return;
    if (user.role !== "admin") {
      router.replace(
        user.role === "doctor" ? "/doctor/appointments" : "/patient/doctors",
      );
    }
  }, [user, router]);

  if (!user || user.role !== "admin") return null;
  return <>{children}</>;
}