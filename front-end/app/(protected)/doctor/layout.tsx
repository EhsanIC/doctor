"use client";

import { useEffect } from "react";
import { useRouter } from "next/navigation";
import { useUser } from "@/hooks/useUser";

export default function DoctorLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const { user } = useUser();
  const router = useRouter();

  useEffect(() => {
    if (!user) return;
    if (user.role !== "doctor") {
      router.replace(
        user.role === "admin" ? "/admin/doctors" : "/patient/doctors",
      );
    }
  }, [user, router]);

  if (!user || user.role !== "doctor") return null;
  return <>{children}</>;
}