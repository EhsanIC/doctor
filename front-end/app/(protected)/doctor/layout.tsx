"use client"

import { usePathname } from "next/navigation"
import { DoctorShell } from "@/components/doctor-shell"

const titles: Record<string, string> = {
  "/doctor": "Doctor dashboard",
  "/doctor/profile": "My profile",
  "/doctor/profile/edit": "Edit profile",
  "/doctor/appointments": "Appointments",
}

export default function DoctorLayout({
  children,
}: {
  children: React.ReactNode
}) {
  const pathname = usePathname()
  const title = titles[pathname] ?? "Doctor dashboard"

  return <DoctorShell title={title}>{children}</DoctorShell>
}
