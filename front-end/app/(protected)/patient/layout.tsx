"use client"

import Link from "next/link"
import { useEffect } from "react"
import { usePathname, useRouter } from "next/navigation"
import { useUser } from "@/hooks/useUser"
import { Avatar, AvatarFallback } from "@/components/ui/avatar"
import { Button } from "@/components/ui/button"

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
    <div className="flex min-h-screen bg-background">
      <aside className="hidden w-64 shrink-0 border-r bg-card md:flex md:flex-col">
        <div className="border-b p-6">
          <div className="flex items-center gap-3">
            <Avatar>
              <AvatarFallback>{user.name.slice(0, 2).toUpperCase()}</AvatarFallback>
            </Avatar>
            <div className="min-w-0">
              <p className="truncate font-medium">{user.name}</p>
              <p className="text-xs text-muted-foreground">Patient</p>
            </div>
          </div>
        </div>
        <nav className="flex-1 space-y-1 p-4">
          <Button
            variant={pathname.startsWith("/patient/doctors") ? "secondary" : "ghost"}
            className="w-full justify-start"
            render={<Link href="/patient/doctors" />}
          >
            Find a doctor
          </Button>
        </nav>
      </aside>
      <div className="flex min-w-0 flex-1 flex-col">
        <header className="flex h-14 items-center border-b px-4 md:px-8">
          <span className="font-medium">{pathname.startsWith("/patient/doctors") ? "Find a doctor" : "Patient portal"}</span>
        </header>
        {children}
      </div>
    </div>
  )
}
