"use client"

import Link from "next/link"
import { useRouter } from "next/navigation"
import { mutate } from "swr"
import { useForm } from "react-hook-form"
import { z } from "zod"
import { zodResolver } from "@hookform/resolvers/zod"
import { cn } from "@/lib/utils"
import { Button } from "@/components/ui/button"
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card"
import {
  Field,
  FieldDescription,
  FieldError,
  FieldGroup,
  FieldLabel,
} from "@/components/ui/field"
import { Input } from "@/components/ui/input"
import { apiFetch, csrfCookie } from "@/lib/api"
import { toAppUser } from "@/hooks/useUser"

const signupSchema = z
  .object({
    name: z.string().trim().min(1, "Full name is required").max(255),
    email: z.string().trim().min(1, "Email is required").email("Invalid email address"),
    password: z.string().min(8, "Password must be at least 8 characters"),
    password_confirmation: z.string().min(1, "Please confirm your password"),
  })
  .refine((data) => data.password === data.password_confirmation, {
    message: "Passwords do not match",
    path: ["password_confirmation"],
  })

type SignupFormData = z.infer<typeof signupSchema>
type RawUser = { id: number; name: string; roles?: { name: string }[] }

export function SignupForm({
  className,
  ...props
}: React.ComponentProps<"div">) {
  const router = useRouter()
  const {
    register,
    handleSubmit,
    setError,
    formState: { errors, isSubmitting },
  } = useForm<SignupFormData>({
    resolver: zodResolver(signupSchema),
  })

  async function onSubmit(data: SignupFormData) {
    try {
      await csrfCookie()
      const response = await apiFetch<{ user: RawUser }>("/api/v1/register", {
        method: "POST",
        body: JSON.stringify(data),
      })

      const appUser = toAppUser(response.user)
      mutate("/api/v1/me", appUser, false)
      router.replace(appUser.role === "patient" ? "/patient/doctors" : "/")
    } catch (err) {
      setError("root", {
        message: err instanceof Error ? err.message : "Something went wrong",
      })
    }
  }

  return (
    <div className={cn("flex flex-col gap-6", className)} {...props}>
      <Card>
        <CardHeader className="text-center">
          <CardTitle className="text-xl">Create an account</CardTitle>
          <CardDescription>Create an account to get started.</CardDescription>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleSubmit(onSubmit)}>
            <FieldGroup>
              <Field>
                <FieldLabel htmlFor="name">Full Name</FieldLabel>
                <Input
                  id="name"
                  type="text"
                  placeholder="John Doe"
                  {...register("name")}
                />
                <FieldError errors={[errors.name]} />
              </Field>
              <Field>
                <FieldLabel htmlFor="email">Email</FieldLabel>
                <Input
                  id="email"
                  type="email"
                  placeholder="m@example.com"
                  {...register("email")}
                />
                <FieldError errors={[errors.email]} />
              </Field>
              <Field>
                <Field className="grid grid-cols-2 gap-4">
                  <Field>
                    <FieldLabel htmlFor="password">Password</FieldLabel>
                    <Input
                      id="password"
                      type="password"
                      {...register("password")}
                    />
                    <FieldError errors={[errors.password]} />
                  </Field>
                  <Field>
                    <FieldLabel htmlFor="password_confirmation">
                      Confirm Password
                    </FieldLabel>
                    <Input
                      id="password_confirmation"
                      type="password"
                      {...register("password_confirmation")}
                    />
                    <FieldError errors={[errors.password_confirmation]} />
                  </Field>
                </Field>
              </Field>
              {errors.root && (
                <FieldDescription className="text-destructive">
                  {errors.root.message}
                </FieldDescription>
              )}
              <Field>
                <Button type="submit" disabled={isSubmitting}>
                  {isSubmitting ? "Creating account..." : "Create Account"}
                </Button>
                <FieldDescription className="text-center">
                  Already have an account? <Link href="/login">Sign in</Link>
                </FieldDescription>
              </Field>
            </FieldGroup>
          </form>
        </CardContent>
      </Card>
      <FieldDescription className="px-6 text-center">
        By creating an account, you agree to our <a href="#">Terms of Service</a>{" "}
        and <a href="#">Privacy Policy</a>.
      </FieldDescription>
    </div>
  )
}
