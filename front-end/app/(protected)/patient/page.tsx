"use client"

import Link from "next/link"
import useSWR from "swr"
import { CalendarDaysIcon, SearchIcon, UserRoundIcon } from "lucide-react"
import { useUser } from "@/hooks/useUser"
import { apiFetch } from "@/lib/api"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { buttonVariants } from "@/components/ui/button"
import { Skeleton } from "@/components/ui/skeleton"

type Appointment = {
  id: number
  appointment_date: string
  appointment_time: string
  status: string
  doctor_profile?: { name?: string | null }
}

type AppointmentsResponse = Appointment[] | { data: Appointment[] }

function getAppointments(response: AppointmentsResponse): Appointment[] {
  return Array.isArray(response) ? response : response.data
}

export default function PatientDashboardPage() {
  const { user } = useUser()
  const { data, isLoading } = useSWR<AppointmentsResponse>(
    "/api/v1/patient/appointment/mine",
    (url) => apiFetch<AppointmentsResponse>(url),
    { shouldRetryOnError: false },
  )
  const appointments = data ? getAppointments(data) : []
  const upcoming = appointments[0]

  return (
    <main className="flex flex-1 flex-col gap-6 p-4 md:p-8">
      <div>
        <h1 className="text-2xl font-semibold">Welcome, {user?.name ?? "Patient"}</h1>
        <p className="text-muted-foreground">Manage your healthcare appointments and find a doctor.</p>
      </div>

      <div className="grid gap-4 md:grid-cols-3">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between pb-2"><CardTitle className="text-sm font-medium">My appointments</CardTitle><CalendarDaysIcon className="size-4 text-muted-foreground" /></CardHeader>
          <CardContent><div className="text-2xl font-bold">{isLoading ? "—" : appointments.length}</div><p className="text-xs text-muted-foreground">Total appointment requests</p></CardContent>
        </Card>
        <Card>
          <CardHeader className="flex flex-row items-center justify-between pb-2"><CardTitle className="text-sm font-medium">Pending requests</CardTitle><CalendarDaysIcon className="size-4 text-muted-foreground" /></CardHeader>
          <CardContent><div className="text-2xl font-bold">{isLoading ? "—" : appointments.filter((appointment) => appointment.status === "pending").length}</div><p className="text-xs text-muted-foreground">Waiting for doctor approval</p></CardContent>
        </Card>
        <Card>
          <CardHeader className="flex flex-row items-center justify-between pb-2"><CardTitle className="text-sm font-medium">Quick access</CardTitle><SearchIcon className="size-4 text-muted-foreground" /></CardHeader>
          <CardContent><Link href="/patient/doctors" className={buttonVariants({ variant: "outline", size: "sm" })}>Find a doctor</Link></CardContent>
        </Card>
      </div>

      <div className="grid gap-6 md:grid-cols-2">
        <Card>
          <CardHeader><CardTitle>Next appointment</CardTitle><CardDescription>Your most recently scheduled appointment.</CardDescription></CardHeader>
          <CardContent>{isLoading ? <Skeleton className="h-16 w-full" /> : upcoming ? <div className="space-y-1"><p className="font-medium">{upcoming.doctor_profile?.name ?? "Doctor"}</p><p className="text-sm text-muted-foreground">{upcoming.appointment_date} at {upcoming.appointment_time.slice(0, 5)}</p><p className="text-sm capitalize text-muted-foreground">Status: {upcoming.status}</p></div> : <p className="text-sm text-muted-foreground">No appointments yet.</p>}</CardContent>
        </Card>
        <Card>
          <CardHeader><CardTitle>Get started</CardTitle><CardDescription>What would you like to do?</CardDescription></CardHeader>
          <CardContent className="flex flex-wrap gap-3"><Link href="/patient/doctors" className={buttonVariants()}><SearchIcon /> Find a doctor</Link><Link href="/patient/appointments" className={buttonVariants({ variant: "outline" })}><CalendarDaysIcon /> View appointments</Link><Link href="/patient/profile" className={buttonVariants({ variant: "outline" })}><UserRoundIcon /> View profile</Link></CardContent>
        </Card>
      </div>
    </main>
  )
}
