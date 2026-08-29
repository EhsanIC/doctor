import { DoctorProfileForm } from "@/components/doctor-profile-form"
import { DoctorShell } from "@/components/doctor-shell"

export default function DoctorProfileEditPage() {
  return (
    <DoctorShell title="Edit profile">
      <main className="flex flex-1 flex-col p-4 md:p-8">
        <DoctorProfileForm />
      </main>
    </DoctorShell>
  )
}
