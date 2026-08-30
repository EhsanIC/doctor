"use client"

import { useEffect } from "react"
import { usePathname, useRouter } from "next/navigation"
import { useUser } from "@/hooks/useUser"
import { PatientSidebar } from "@/components/patient-sidebar"
import { SidebarInset, SidebarProvider, SidebarTrigger } from "@/components/ui/sidebar"

const titles: Record<string, string> = {
  "/patient": "Dashboard",
  "/patient/doctors": "Find a doctor",
  "/patient/appointments": "My appointments",
  "/patient/profile": "My profile",
}

export default function PatientLayout({
  children,
}: {
  children: React.ReactNode
}) {
  const { user } = useUser()
  const router = useRouter()
  const pathname = usePathname()

  useEffect(() => {
    if (!user) return
    if (user.role !== "patient") {
      router.replace(user.role === "admin" ? "/admin/doctors" : "/doctor")
    }
  }, [user, router])

  if (!user || user.role !== "patient") return null

  return (
    <SidebarProvider>
      <PatientSidebar />
      <SidebarInset>
        <header className="flex h-14 items-center gap-2 border-b border-slate-300/70 px-4">
          <SidebarTrigger />
          <div className="flex items-center gap-2 text-sm font-medium">
            {titles[pathname] ?? (pathname.startsWith("/patient/doctors/") ? "Doctor profile" : "Patient portal")}
          </div>
        </header>
        {children}
      </SidebarInset>
    </SidebarProvider>
  )
}
