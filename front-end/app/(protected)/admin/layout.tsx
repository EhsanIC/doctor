"use client";

import { useEffect } from "react";
import { useRouter } from "next/navigation";
import { useUser } from "@/hooks/useUser";

export default function AdminLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const { user } = useUser();
  const router = useRouter();

  useEffect(() => {
    if (!user) return;
    if (user.role !== "admin") {
      router.replace(
        user.role === "doctor" ? "/doctor" : "/patient/doctors",
      );
    }
  }, [user, router]);

  if (!user || user.role !== "admin") return null;
  return <>{children}</>;
}