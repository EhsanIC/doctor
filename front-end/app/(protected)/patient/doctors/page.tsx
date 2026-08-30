"use client"

import Link from "next/link"
import useSWR from "swr"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { Badge } from "@/components/ui/badge"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { Skeleton } from "@/components/ui/skeleton"
import { apiFetch } from "@/lib/api"

type Doctor = {
  id: number
  image: string | null
  user?: { name: string; email: string }
  specialty?: { name: string }
}

type DoctorsResponse = Doctor[] | { data: Doctor[] }

function getDoctors(response: DoctorsResponse): Doctor[] {
  return Array.isArray(response) ? response : response.data
}

function getInitials(name: string): string {
  return name
    .trim()
    .split(/\s+/)
    .map((part) => part[0] ?? "")
    .join("")
    .slice(0, 2)
    .toUpperCase()
}

export default function FindDoctorPage() {
  const { data, error, isLoading } = useSWR<DoctorsResponse>(
    "/api/v1/patient/appointment",
    (url) => apiFetch<DoctorsResponse>(url),
    { shouldRetryOnError: false },
  )
  const doctors = data ? getDoctors(data) : []

  return (
    <main className="flex flex-1 flex-col gap-6 p-4 md:p-8">
      <div>
        <h1 className="text-2xl font-semibold">Find a doctor</h1>
        <p className="text-muted-foreground">Browse approved doctors and choose the right specialist for you.</p>
      </div>

      {error && <p className="text-sm text-destructive">Could not load doctors. Please try again.</p>}

      {isLoading ? (
        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {["doctor-1", "doctor-2", "doctor-3"].map((id) => (
            <Card key={id}>
              <CardHeader className="flex flex-row items-center gap-4">
                <Skeleton className="size-16 rounded-full" />
                <div className="space-y-2">
                  <Skeleton className="h-5 w-32" />
                  <Skeleton className="h-4 w-24" />
                </div>
              </CardHeader>
              <CardContent>
                <Skeleton className="h-9 w-full" />
              </CardContent>
            </Card>
          ))}
        </div>
      ) : doctors.length ? (
        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {doctors.map((doctor) => {
            const name = doctor.user?.name ?? "Doctor"
            const imageUrl = doctor.image
            return (
              <Card key={doctor.id} className="transition-shadow hover:shadow-md">
                <CardHeader className="flex flex-row items-center gap-4">
                  <Avatar className="!size-16 shrink-0">
                    {imageUrl ? <AvatarImage src={imageUrl} alt={`${name} profile photo`} /> : null}
                    <AvatarFallback>{getInitials(name) || "DR"}</AvatarFallback>
                  </Avatar>
                  <div className="min-w-0 space-y-1">
                    <CardTitle className="truncate text-lg">{name}</CardTitle>
                    {doctor.specialty?.name && <Badge variant="secondary">{doctor.specialty.name}</Badge>}
                  </div>
                </CardHeader>
                <CardContent>
                  <Link
                    href={`/patient/doctors/${doctor.id}`}
                    className="inline-flex h-9 w-full items-center justify-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground hover:bg-primary/90"
                  >
                    View profile
                  </Link>
                </CardContent>
              </Card>
            )
          })}
        </div>
      ) : (
        <Card>
          <CardContent className="py-12 text-center text-muted-foreground">
            No approved doctors are available yet.
          </CardContent>
        </Card>
      )}
    </main>
  )
}
