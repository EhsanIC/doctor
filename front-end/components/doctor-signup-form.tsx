"use client"

import Link from "next/link"
import { useRouter } from "next/navigation"
import { useForm } from "react-hook-form"
import { z } from "zod"
import { zodResolver } from "@hookform/resolvers/zod"
import { toast } from "sonner"
import { mutate } from "swr"
import { toAppUser } from "@/hooks/useUser"
import { cn } from "@/lib/utils"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Field, FieldDescription, FieldError, FieldGroup, FieldLabel } from "@/components/ui/field"
import { Input } from "@/components/ui/input"
import { PasswordInput } from "@/components/password-input"
import { apiFetch } from "@/lib/api"

const doctorSignupSchema = z.object({
  name: z.string().trim().min(1, "Full name is required").max(255),
  email: z.string().trim().min(1, "Email is required").email("Invalid email address"),
  password: z.string().min(8, "Password must be at least 8 characters"),
  password_confirmation: z.string().min(1, "Please confirm your password"),
}).refine((data) => data.password === data.password_confirmation, {
  message: "Passwords do not match",
  path: ["password_confirmation"],
})

type DoctorSignupFormData = z.infer<typeof doctorSignupSchema>
type RawUser = { id: number; name: string; roles?: { name: string }[] }

type Props = React.ComponentProps<"div">

export function DoctorSignupForm({ className, ...props }: Props) {
  const router = useRouter()
  const { register, handleSubmit, setError, formState: { errors, isSubmitting } } = useForm<DoctorSignupFormData>({
    resolver: zodResolver(doctorSignupSchema),
  })

  async function onSubmit(data: DoctorSignupFormData) {
    try {
      const response = await apiFetch<{ user: RawUser }>("/api/v1/doctor/register", {
        method: "POST",
        body: JSON.stringify(data),
      })
      const appUser = toAppUser(response.user)
      mutate("/api/v1/me", appUser, false)
      toast.success("Registration submitted", {
        description: "Your account is pending admin approval.",
      })
      router.replace("/doctor")
    } catch (err) {
      setError("root", { message: err instanceof Error ? err.message : "Something went wrong" })
    }
  }

  return (
    <div className={cn("flex flex-col gap-6", className)} {...props}>
      <Card>
        <CardHeader className="text-center">
          <CardTitle className="text-xl">Doctor registration</CardTitle>
          <CardDescription>Create an account to join our medical team.</CardDescription>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleSubmit(onSubmit)}>
            <FieldGroup>
              <Field>
                <FieldLabel htmlFor="name">Full Name</FieldLabel>
                <Input id="name" placeholder="Dr. Jane Doe" {...register("name")} />
                <FieldError errors={[errors.name]} />
              </Field>
              <Field>
                <FieldLabel htmlFor="email">Email</FieldLabel>
                <Input id="email" type="email" placeholder="doctor@example.com" {...register("email")} />
                <FieldError errors={[errors.email]} />
              </Field>
              <Field>
                <FieldLabel htmlFor="password">Password</FieldLabel>
                <PasswordInput id="password" {...register("password")} />
                <FieldError errors={[errors.password]} />
              </Field>
              <Field>
                <FieldLabel htmlFor="password_confirmation">Confirm Password</FieldLabel>
                <PasswordInput id="password_confirmation" {...register("password_confirmation")} />
                <FieldError errors={[errors.password_confirmation]} />
              </Field>
              {errors.root && <FieldDescription className="text-destructive">{errors.root.message}</FieldDescription>}
              <Field>
                <Button type="submit" disabled={isSubmitting}>
                  {isSubmitting ? "Submitting..." : "Create Doctor Account"}
                </Button>
                <FieldDescription className="text-center">
                  Already have an account? <Link href="/login">Sign in</Link>
                </FieldDescription>
              </Field>
            </FieldGroup>
          </form>
        </CardContent>
      </Card>
    </div>
  )
}
