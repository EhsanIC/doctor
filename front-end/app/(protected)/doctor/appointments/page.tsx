"use client"

import { useState } from "react"
import useSWR from "swr"
import { toast } from "sonner"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
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
  user?: { name: string | null }
}

type AppointmentsResponse = Appointment[] | { data: Appointment[] }

function getAppointments(response: AppointmentsResponse): Appointment[] {
  return Array.isArray(response) ? response : response.data
}

export default function DoctorAppointmentsPage() {
  const [updatingId, setUpdatingId] = useState<number | null>(null)
  const { data, error, isLoading, mutate } = useSWR<AppointmentsResponse>(
    "/api/v1/doctor/appointment",
    (url) => apiFetch<AppointmentsResponse>(url),
    { shouldRetryOnError: false },
  )
  const appointments = data ? getAppointments(data) : []

  async function updateStatus(id: number, status: "approved" | "canceled") {
    setUpdatingId(id)
    try {
      await apiFetch(`/api/v1/doctor/appointment/${id}`, {
        method: "PATCH",
        body: JSON.stringify({ status }),
      })
      await mutate()
      toast.success(`Appointment ${status}.`)
    } catch (updateError) {
      toast.error(updateError instanceof Error ? updateError.message : "Could not update appointment.")
    } finally {
      setUpdatingId(null)
    }
  }

  return (
    <main className="flex flex-1 flex-col gap-6 p-4 md:p-8">
      <div><h1 className="text-2xl font-semibold">My appointments</h1><p className="text-muted-foreground">Review patient requests and manage their status.</p></div>
      <Card>
        <CardHeader><CardTitle>Appointment requests</CardTitle><CardDescription>{isLoading ? "Loading appointments..." : `${appointments.length} appointment${appointments.length === 1 ? "" : "s"}`}</CardDescription></CardHeader>
        <CardContent>
          {error ? <p className="text-sm text-destructive">Could not load appointments.</p> : null}
          {isLoading ? <div className="space-y-4">{["one", "two", "three"].map((row) => <Skeleton key={row} className="h-12 w-full" />)}</div> : !error && !appointments.length ? <p className="py-10 text-center text-muted-foreground">No appointment requests yet.</p> : !error ? (
            <div className="overflow-x-auto"><Table><TableHeader><TableRow><TableHead>Patient</TableHead><TableHead>Date</TableHead><TableHead>Time</TableHead><TableHead>Notes</TableHead><TableHead>Status</TableHead><TableHead className="text-right">Actions</TableHead></TableRow></TableHeader><TableBody>
              {appointments.map((appointment) => <TableRow key={appointment.id}><TableCell className="font-medium">{appointment.user?.name ?? "Unknown patient"}</TableCell><TableCell>{appointment.appointment_date}</TableCell><TableCell>{appointment.appointment_time.slice(0, 5)}</TableCell><TableCell className="max-w-56 truncate text-muted-foreground">{appointment.description || "—"}</TableCell><TableCell><Badge variant={appointment.status === "approved" ? "default" : appointment.status === "canceled" ? "destructive" : "secondary"}>{appointment.status}</Badge></TableCell><TableCell className="text-right"><div className="flex justify-end gap-2">{appointment.status === "pending" ? <><Button size="sm" onClick={() => updateStatus(appointment.id, "approved")} disabled={updatingId === appointment.id}>Approve</Button><Button size="sm" variant="destructive" onClick={() => updateStatus(appointment.id, "canceled")} disabled={updatingId === appointment.id}>Cancel</Button></> : <span className="text-sm text-muted-foreground">No actions</span>}</div></TableCell></TableRow>)}
            </TableBody></Table></div>
          ) : null}
        </CardContent>
      </Card>
    </main>
  )
}
