"use client"

import Link from "next/link"
import useSWR from "swr"
import { CalendarDaysIcon, CheckCircle2Icon, ClockIcon } from "lucide-react"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { apiFetch } from "@/lib/api"
import { useUser } from "@/hooks/useUser"
import { Badge } from "@/components/ui/badge"
import { buttonVariants } from "@/components/ui/button"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Skeleton } from "@/components/ui/skeleton"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"

type Appointment = {
  id: number
  appointment_date: string
  appointment_time: string
  description: string | null
  status: "pending" | "approved" | "canceled"
  user?: { id: number; name: string; email: string }
}

type AppointmentsResponse = Appointment[] | { data: Appointment[] }

function getAppointments(response: AppointmentsResponse): Appointment[] {
  return Array.isArray(response) ? response : response.data
}

export function DoctorDashboard() {
  const { user } = useUser()
  const profileImageUrl = user?.doctorProfileImageUrl ?? null
  const { data, error, isLoading } = useSWR<AppointmentsResponse>(
    "/api/v1/doctor/appointment",
    (url) => apiFetch<AppointmentsResponse>(url),
    { shouldRetryOnError: false },
  )
  const appointments = data ? getAppointments(data) : []

  // Most recent first (backend returns them in insertion order).
  const sorted = [...appointments].sort((a, b) => {
    const dateDiff = (b.appointment_date ?? "").localeCompare(a.appointment_date ?? "")
    if (dateDiff !== 0) return dateDiff
    return (b.appointment_time ?? "").localeCompare(a.appointment_time ?? "")
  })

  const pendingCount = appointments.filter((a) => a.status === "pending").length
  const approvedCount = appointments.filter((a) => a.status === "approved").length

  return (
      <main className="flex flex-1 flex-col gap-6 p-4 md:p-8">
          <div>
            <h1 className="text-2xl font-semibold">Welcome, {user?.name}</h1>
            <div className="flex items-center gap-4">
              <Avatar className="!size-[100px] shrink-0">
                {profileImageUrl ? <AvatarImage src={profileImageUrl} alt="Profile photo" /> : null}
                <AvatarFallback>{user?.name?.slice(0, 2).toUpperCase() ?? "DR"}</AvatarFallback>
              </Avatar>
              <p className="text-muted-foreground">Manage your appointments and profile.</p>
            </div>
          </div>
          <div className="grid gap-4 md:grid-cols-3">
            <Card>
              <CardHeader className="flex flex-row items-center justify-between pb-2">
                <CardTitle className="text-sm font-medium">Appointments</CardTitle>
                <CalendarDaysIcon className="size-4 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">{isLoading ? "—" : appointments.length}</div>
              </CardContent>
            </Card>
            <Card>
              <CardHeader className="flex flex-row items-center justify-between pb-2">
                <CardTitle className="text-sm font-medium">Pending</CardTitle>
                <ClockIcon className="size-4 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">{isLoading ? "—" : pendingCount}</div>
              </CardContent>
            </Card>
            <Card>
              <CardHeader className="flex flex-row items-center justify-between pb-2">
                <CardTitle className="text-sm font-medium">Approved</CardTitle>
                <CheckCircle2Icon className="size-4 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">{isLoading ? "—" : approvedCount}</div>
              </CardContent>
            </Card>
          </div>
          <Card>
            <CardHeader className="flex flex-row items-center justify-between">
              <CardTitle>Recent appointments</CardTitle>
              <Link
                href="/doctor/appointments"
                className={buttonVariants({ variant: "outline", size: "sm" })}
              >
                View all
              </Link>
            </CardHeader>
            <CardContent>
              {error && <p className="text-sm text-destructive">Could not load appointments.</p>}
              {isLoading ? (
                <div className="space-y-4">
                  <div className="grid grid-cols-4 gap-4 pb-3">
                    <Skeleton className="h-4 w-24" />
                    <Skeleton className="h-4 w-24" />
                    <Skeleton className="h-4 w-20" />
                    <Skeleton className="ml-auto h-4 w-16" />
                  </div>
                  {["row-1", "row-2", "row-3"].map((row) => (
                    <div key={row} className="grid grid-cols-4 items-center gap-4">
                      <Skeleton className="h-5 w-32" />
                      <Skeleton className="h-5 w-24" />
                      <Skeleton className="h-5 w-20" />
                      <Skeleton className="ml-auto h-5 w-20 rounded-full" />
                    </div>
                  ))}
                </div>
              ) : (
                <Table>
                  <TableHeader>
                    <TableRow className="border-slate-300/70">
                      <TableHead>Patient</TableHead>
                      <TableHead>Date</TableHead>
                      <TableHead>Time</TableHead>
                      <TableHead>Notes</TableHead>
                      <TableHead className="text-right">Status</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    {sorted.map((appointment) => (
                      <TableRow key={appointment.id}>
                        <TableCell className="font-medium">
                          {appointment.user?.name ?? "Unknown patient"}
                        </TableCell>
                        <TableCell>{appointment.appointment_date}</TableCell>
                        <TableCell>{appointment.appointment_time}</TableCell>
                        <TableCell className="max-w-56 truncate text-muted-foreground">
                          {appointment.description || "—"}
                        </TableCell>
                        <TableCell className="text-right">
                          <Badge
                            variant={
                              appointment.status === "approved"
                                ? "default"
                                : appointment.status === "canceled"
                                  ? "destructive"
                                  : "secondary"
                            }
                          >
                            {appointment.status}
                          </Badge>
                        </TableCell>
                      </TableRow>
                    ))}
                    {!sorted.length && !error && (
                      <TableRow>
                        <TableCell colSpan={5} className="h-24 text-center text-muted-foreground">
                          No appointments yet.
                        </TableCell>
                      </TableRow>
                    )}
                  </TableBody>
                </Table>
              )}
            </CardContent>
          </Card>
      </main>
  )
}