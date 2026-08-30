"use client"

import { useEffect } from "react"
import { useRouter } from "next/navigation"
import { useUser } from "@/hooks/useUser"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"

export function DoctorAccessGuard({ children }: { children: React.ReactNode }) {
  const router = useRouter()
  const { user, isLoading } = useUser()

  useEffect(() => {
    if (!isLoading && user && user.role !== "doctor") {
      router.replace(user.role === "admin" ? "/admin/doctors" : "/patient")
    }
  }, [isLoading, router, user])

  if (isLoading || !user) return null

  if (user.role !== "doctor") return null

  return <>{children}</>
}

export function DoctorPendingMessage() {
  return (
    <main className="flex flex-1 items-center justify-center p-4 md:p-8">
      <Card className="w-full max-w-lg">
        <CardHeader><CardTitle>Access unavailable</CardTitle></CardHeader>
        <CardContent className="text-muted-foreground">
          Your doctor account is waiting for admin approval. You cannot access the doctor dashboard until your account is approved.
        </CardContent>
      </Card>
    </main>
  )
}
