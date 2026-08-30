"use client"

import Link from "next/link"
import { useParams, useRouter } from "next/navigation"
import { useState } from "react"
import useSWR from "swr"
import { toast } from "sonner"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Skeleton } from "@/components/ui/skeleton"
import { apiFetch } from "@/lib/api"

type Doctor = {
  id: number
  name: string
  specialty: string | { name: string } | null
  image_url: string | null
  bio: string | null
  mobile: string | null
  address: string | null
  working_hours: string | null
}

export default function DoctorDetailPage() {
  const params = useParams<{ id: string }>()
  const router = useRouter()
  const [date, setDate] = useState("")
  const [time, setTime] = useState("")
  const [description, setDescription] = useState("")
  const [isSubmitting, setIsSubmitting] = useState(false)
  const { data: doctor, error, isLoading } = useSWR<Doctor | { data: Doctor }>(
    params.id ? `/api/v1/patient/appointment/${params.id}` : null,
    (url) => apiFetch<Doctor>(url),
    { shouldRetryOnError: false },
  )

  async function bookAppointment(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault()
    if (!date || !time) {
      toast.error("Choose an appointment date and time.")
      return
    }

    setIsSubmitting(true)
    try {
      await apiFetch("/api/v1/patient/appointment", {
        method: "POST",
        body: JSON.stringify({
          doctor_id: Number(params.id),
          appointment_date: date,
          appointment_time: time,
          description: description || null,
        }),
      })
      toast.success("Appointment request sent successfully.")
      router.push("/patient/appointments")
    } catch (bookingError) {
      toast.error(bookingError instanceof Error ? bookingError.message : "Could not book appointment.")
    } finally {
      setIsSubmitting(false)
    }
  }

  if (isLoading) {
    return <main className="flex flex-1 flex-col gap-6 p-4 md:p-8"><Skeleton className="h-8 w-56" /><Skeleton className="h-64 w-full" /></main>
  }

  const doctorData = doctor && "data" in doctor ? doctor.data : doctor

  if (error || !doctorData) {
    return <main className="flex flex-1 flex-col gap-4 p-4 md:p-8"><p className="text-destructive">Could not load this doctor.</p><Link href="/patient/doctors">Back to doctors</Link></main>
  }

  return (
    <main className="flex flex-1 flex-col gap-6 p-4 md:p-8">
      <Link href="/patient/doctors" className="text-sm text-muted-foreground hover:text-foreground">← Back to doctors</Link>
      <div className="grid gap-6 lg:grid-cols-[1fr_420px]">
        <Card>
          <CardHeader className="flex flex-row items-center gap-4">
            <Avatar className="!size-24 shrink-0">
              {doctorData.image_url ? <AvatarImage src={doctorData.image_url} alt={`${doctorData.name ?? "Doctor"} profile photo`} /> : null}
              <AvatarFallback>{(doctorData.name ?? "Doctor").slice(0, 2).toUpperCase()}</AvatarFallback>
            </Avatar>
            <div>
              <CardTitle>{doctorData.name ?? "Doctor"}</CardTitle>
              {doctorData.specialty && <Badge variant="secondary" className="mt-2">{typeof doctorData.specialty === "string" ? doctorData.specialty : doctorData.specialty.name}</Badge>}
            </div>
          </CardHeader>
          <CardContent className="space-y-4">
            <div><h2 className="font-medium">About</h2><p className="mt-1 whitespace-pre-wrap text-sm text-muted-foreground">{doctorData.bio || "No biography provided."}</p></div>
            {doctorData.address && <div><h2 className="font-medium">Address</h2><p className="text-sm text-muted-foreground">{doctorData.address}</p></div>}
            {doctorData.working_hours && <div><h2 className="font-medium">Working hours</h2><p className="text-sm text-muted-foreground">{doctorData.working_hours}</p></div>}
          </CardContent>
        </Card>

        <Card>
          <CardHeader><CardTitle>Book an appointment</CardTitle><CardDescription>Send an appointment request to this doctor.</CardDescription></CardHeader>
          <CardContent>
            <form onSubmit={bookAppointment} className="space-y-4">
              <label className="block text-sm font-medium">Date<input required type="date" min={new Date().toISOString().split("T")[0]} value={date} onChange={(event) => setDate(event.target.value)} className="mt-1 flex h-9 w-full rounded-md border bg-transparent px-3 text-sm" /></label>
              <label className="block text-sm font-medium">Time<input required type="time" value={time} onChange={(event) => setTime(event.target.value)} className="mt-1 flex h-9 w-full rounded-md border bg-transparent px-3 text-sm" /></label>
              <label className="block text-sm font-medium">Notes<textarea value={description} onChange={(event) => setDescription(event.target.value)} placeholder="Optional notes" className="mt-1 min-h-24 w-full rounded-md border bg-transparent px-3 py-2 text-sm" /></label>
              <Button type="submit" disabled={isSubmitting} className="w-full">{isSubmitting ? "Booking..." : "Book appointment"}</Button>
            </form>
          </CardContent>
        </Card>
      </div>
    </main>
  )
}
