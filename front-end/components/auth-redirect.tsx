"use client"

import { useEffect } from "react"
import { useRouter } from "next/navigation"
import { useUser } from "@/hooks/useUser"

const roleRedirects = {
  admin: "/admin/doctors",
  doctor: "/doctor",
  patient: "/patient",
} as const

export function AuthRedirect({ children }: { children: React.ReactNode }) {
  const router = useRouter()
  const { user, isLoading } = useUser()

  useEffect(() => {
    if (!isLoading && user) {
      router.replace(roleRedirects[user.role])
    }
  }, [isLoading, router, user])

  if (isLoading || user) return null
  return <>{children}</>
}
