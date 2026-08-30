"use client"

import Link from "next/link"
import useSWR from "swr"
import { Badge } from "@/components/ui/badge"
import { buttonVariants } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Skeleton } from "@/components/ui/skeleton"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"
import { apiFetch } from "@/lib/api"

type Appointment = {
  id: number
  appointment_date: string
  appointment_time: string
  description: string | null
  status: "pending" | "approved" | "canceled"
  doctor_profile?: {
    name?: string | null
    user?: { name: string | null }
    specialty?: { name: string } | null
  }
}

type AppointmentsResponse = Appointment[] | { data: Appointment[] }

function getAppointments(response: AppointmentsResponse): Appointment[] {
  return Array.isArray(response) ? response : response.data
}

function statusVariant(status: Appointment["status"]) {
  if (status === "approved") return "default" as const
  if (status === "canceled") return "destructive" as const
  return "secondary" as const
}

export default function PatientAppointmentsPage() {
  const { data, error, isLoading } = useSWR<AppointmentsResponse>(
    "/api/v1/patient/appointment/mine",
    (url) => apiFetch<AppointmentsResponse>(url),
    { shouldRetryOnError: false },
  )
  const appointments = data ? getAppointments(data) : []

  return (
    <main className="flex flex-1 flex-col gap-6 p-4 md:p-8">
      <div className="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
          <h1 className="text-2xl font-semibold">My appointments</h1>
          <p className="text-muted-foreground">Track your appointment requests and their status.</p>
        </div>
        <Link href="/patient/doctors" className={buttonVariants()}>Find a doctor</Link>
      </div>

      <Card>
        <CardHeader>
          <CardTitle>Appointments</CardTitle>
          <CardDescription>{isLoading ? "Loading your appointments..." : `${appointments.length} appointment${appointments.length === 1 ? "" : "s"}`}</CardDescription>
        </CardHeader>
        <CardContent>
          {error ? <p className="text-sm text-destructive">Could not load your appointments.</p> : null}
          {isLoading ? (
            <div className="space-y-4">
              {["one", "two", "three"].map((row) => <Skeleton key={row} className="h-12 w-full" />)}
            </div>
          ) : !error && appointments.length === 0 ? (
            <div className="flex flex-col items-center gap-3 py-10 text-center">
              <p className="text-muted-foreground">You have no appointments yet.</p>
              <Link href="/patient/doctors" className={buttonVariants({ variant: "outline" })}>Find a doctor</Link>
            </div>
          ) : !error ? (
            <div className="overflow-x-auto">
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Doctor</TableHead>
                    <TableHead>Specialty</TableHead>
                    <TableHead>Date</TableHead>
                    <TableHead>Time</TableHead>
                    <TableHead>Notes</TableHead>
                    <TableHead className="text-right">Status</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {appointments.map((appointment) => (
                    <TableRow key={appointment.id}>
                      <TableCell className="font-medium">{appointment.doctor_profile?.name ?? appointment.doctor_profile?.user?.name ?? "Unknown doctor"}</TableCell>
                      <TableCell>{appointment.doctor_profile?.specialty?.name ?? "—"}</TableCell>
                      <TableCell>{appointment.appointment_date}</TableCell>
                      <TableCell>{appointment.appointment_time.slice(0, 5)}</TableCell>
                      <TableCell className="max-w-56 truncate text-muted-foreground">{appointment.description || "—"}</TableCell>
                      <TableCell className="text-right"><Badge variant={statusVariant(appointment.status)}>{appointment.status}</Badge></TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            </div>
          ) : null}
        </CardContent>
      </Card>
    </main>
  )
}
