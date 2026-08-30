"use client"

import { usePathname } from "next/navigation"
import { DoctorShell } from "@/components/doctor-shell"
import { DoctorAccessGuard, DoctorPendingMessage } from "@/components/doctor-access-guard"
import { useUser } from "@/hooks/useUser"

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
  const { user, isLoading } = useUser()
  const title = titles[pathname] ?? "Doctor dashboard"
  const isPending = user?.role === "doctor" && user.doctorStatus !== "active"

  if (isLoading) return null

  return (
    <DoctorAccessGuard>
      <DoctorShell title={title}>{isPending ? <DoctorPendingMessage /> : children}</DoctorShell>
    </DoctorAccessGuard>
  )
}
