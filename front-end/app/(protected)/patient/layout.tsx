"use client";

import { useEffect } from "react";
import { useRouter } from "next/navigation";
import { useUser } from "@/hooks/useUser";

export default function PatientLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const { user } = useUser();
  const router = useRouter();

  useEffect(() => {
    if (!user) return;
    if (user.role !== "patient") {
      router.replace(
        user.role === "admin" ? "/admin/doctors" : "/doctor",
      );
    }
  }, [user, router]);

  if (!user || user.role !== "patient") return null;
  return <>{children}</>;
}