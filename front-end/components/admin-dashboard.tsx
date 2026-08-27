"use client"

import { useState } from "react"
import useSWR from "swr"
import { toast } from "sonner"
import { Activity, CalendarDays, Stethoscope, Users } from "lucide-react"
import { apiFetch } from "@/lib/api"
import { useUser } from "@/hooks/useUser"
import { AppSidebar } from "@/components/app-sidebar"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { SidebarInset, SidebarProvider, SidebarTrigger } from "@/components/ui/sidebar"
import { Skeleton } from "@/components/ui/skeleton"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"

type Doctor = {
  id: number
  name?: string
  email?: string
  status?: string
  user?: { name?: string; email?: string }
}

type DoctorsResponse = Doctor[] | { data: Doctor[] }

function getDoctors(response: DoctorsResponse) {
  return Array.isArray(response) ? response : response.data
}

export function AdminDashboard() {
  const { user } = useUser()
  const [updatingId, setUpdatingId] = useState<number | null>(null)
  const { data, error, isLoading, mutate } = useSWR<DoctorsResponse>(
    "/api/v1/admin/doctors",
    (url) => apiFetch<DoctorsResponse>(url),
    { shouldRetryOnError: false },
  )
  const doctors = data ? getDoctors(data) : []

  async function updateStatus(id: number, status: "active" | "disabled") {
    setUpdatingId(id)
    try {
      await apiFetch(`/api/v1/admin/doctors/${id}`, {
        method: "PATCH",
        body: JSON.stringify({ status }),
      })
      await mutate()
      toast.success(`Doctor ${status === "active" ? "approved" : "disabled"}.`)
    } catch (err) {
      toast.error(err instanceof Error ? err.message : "Could not update doctor")
    } finally {
      setUpdatingId(null)
    }
  }

  return (
    <SidebarProvider>
      <AppSidebar />
      <SidebarInset>
        <header className="flex h-14 items-center gap-2 border-b border-slate-300/70 px-4">
          <SidebarTrigger />
          <div className="flex items-center gap-2 text-sm font-medium">
            Admin dashboard
          </div>
        </header>
        <main className="flex flex-1 flex-col gap-6 p-4 md:p-8">
          <div>
            <h1 className="text-2xl font-semibold">Welcome, {user?.name}</h1>
            <p className="text-muted-foreground">Manage doctors and review registration requests.</p>
          </div>
          <div className="grid gap-4 md:grid-cols-3">
            <Card><CardHeader className="flex flex-row items-center justify-between pb-2"><CardTitle className="text-sm font-medium">Doctors</CardTitle><Stethoscope className="size-4 text-muted-foreground" /></CardHeader><CardContent><div className="text-2xl font-bold">{isLoading ? "—" : doctors.length}</div></CardContent></Card>
            <Card><CardHeader className="flex flex-row items-center justify-between pb-2"><CardTitle className="text-sm font-medium">Patients</CardTitle><Users className="size-4 text-muted-foreground" /></CardHeader><CardContent><div className="text-2xl font-bold">—</div></CardContent></Card>
            <Card><CardHeader className="flex flex-row items-center justify-between pb-2"><CardTitle className="text-sm font-medium">Appointments</CardTitle><CalendarDays className="size-4 text-muted-foreground" /></CardHeader><CardContent><div className="text-2xl font-bold">—</div></CardContent></Card>
          </div>            <Card>
            <CardHeader><CardTitle>Doctor registrations</CardTitle></CardHeader>
            <CardContent>
              {error && <p className="text-sm text-destructive">Could not load doctors.</p>}
              {isLoading ? (
                <div className="space-y-4">
                  <div className="grid grid-cols-4 gap-4 pb-3">
                    <Skeleton className="h-4 w-20" />
                    <Skeleton className="h-4 w-32" />
                    <Skeleton className="h-4 w-20" />
                    <Skeleton className="ml-auto h-4 w-16" />
                  </div>
                  {["row-1", "row-2", "row-3"].map((row) => (
                    <div key={row} className="grid grid-cols-4 items-center gap-4">
                      <Skeleton className="h-5 w-32" />
                      <Skeleton className="h-5 w-48 max-w-full" />
                      <Skeleton className="h-5 w-20 rounded-full" />
                      <Skeleton className="ml-auto h-9 w-24" />
                    </div>
                  ))}
                </div>
              ) : (
                <Table>
                  <TableHeader><TableRow className="border-slate-300/70"><TableHead>Name</TableHead><TableHead>Email</TableHead><TableHead>Status</TableHead><TableHead className="text-right">Action</TableHead></TableRow></TableHeader>
                  <TableBody>
                    {doctors.map((doctor) => {
                      const name = doctor.name ?? doctor.user?.name ?? "Unknown doctor"
                      const email = doctor.email ?? doctor.user?.email ?? "—"
                      const status = doctor.status ?? "pending"
                      return <TableRow key={doctor.id}><TableCell className="font-medium">{name}</TableCell><TableCell>{email}</TableCell><TableCell><Badge variant={status === "active" ? "default" : status === "disabled" ? "destructive" : "secondary"}>{status}</Badge></TableCell><TableCell className="text-right"><div className="flex justify-end gap-2">{status !== "active" && <Button size="sm" disabled={updatingId === doctor.id} onClick={() => updateStatus(doctor.id, "active")}>Approve</Button>}{status === "active" && <Button size="sm" variant="destructive" disabled={updatingId === doctor.id} onClick={() => updateStatus(doctor.id, "disabled")}>Disable</Button>}</div></TableCell></TableRow>
                    })}
                    {!doctors.length && !error && <TableRow><TableCell colSpan={4} className="h-24 text-center text-muted-foreground">No doctors found.</TableCell></TableRow>}
                  </TableBody>
                </Table>
              )}
            </CardContent>
          </Card>
          <div className="flex items-center gap-2 text-sm text-muted-foreground"><Activity className="size-4" /> Doctor approval actions update the backend immediately.</div>
        </main>
      </SidebarInset>
    </SidebarProvider>
  )
}
