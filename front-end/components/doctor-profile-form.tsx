"use client";

import { useEffect, useState } from "react";
import useSWR from "swr";
import { Controller, useForm, useWatch } from "react-hook-form";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import { toast } from "sonner";
import { CameraIcon } from "lucide-react";
import { apiFetch } from "@/lib/api";
import { useUser } from "@/hooks/useUser";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Field, FieldDescription, FieldError, FieldGroup, FieldLabel } from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Skeleton } from "@/components/ui/skeleton";
import { Textarea } from "@/components/ui/textarea";

// ── Types ───────────────────────────────────────────────────────────────────

type DoctorProfile = {
  id: number;
  user_id: number;
  specialty_id: number | null;
  status: string;
  image: string | null;
  bio: string | null;
  mobile: string | null;
  medical_code: string | null;
  address: string | null;
  working_hours: string | null;
  user?: { id: number; name: string; email: string };
};

type Specialty = { id: number; name: string };
type SpecialtiesResponse =
  | Specialty[]
  | { data: Specialty[] }
  | { data: { data: Specialty[] } };

function getSpecialties(response: SpecialtiesResponse): Specialty[] {
  if (Array.isArray(response)) return response;
  if (Array.isArray(response.data)) return response.data;
  return response.data.data;
}

const allowedImageTypes = ["image/jpeg", "image/png", "image/webp"];
const maxImageSize = 2 * 1024 * 1024;

// ── Validation ──────────────────────────────────────────────────────────────

const profileSchema = z.object({
  specialty_id: z.string().min(1, "Please select a specialty"),
  image: z
    .instanceof(FileList)
    .optional()
    .refine((files) => !files || files.length === 0 || allowedImageTypes.includes(files[0].type), {
      message: "Image must be a JPEG, PNG or WebP file",
    })
    .refine((files) => !files || files.length === 0 || files[0].size <= maxImageSize, {
      message: "Image must be 2MB or smaller",
    }),
  bio: z.string().trim().min(1, "Description is required"),
  mobile: z
    .string()
    .trim()
    .min(1, "Mobile number is required")
    .max(20, "Mobile number must be 20 characters or fewer"),
  medical_code: z
    .string()
    .trim()
    .min(1, "Medical code is required")
    .max(50, "Medical code must be 50 characters or fewer"),
  address: z.string().trim().min(1, "Address is required"),
  working_hours: z.string().trim().min(1, "Working hours are required"),
});

type ProfileFormData = z.infer<typeof profileSchema>;

// ── Component ───────────────────────────────────────────────────────────────

export function DoctorProfileForm() {
  const { doctorProfileId, isLoading: isUserLoading, isValidating: isUserValidating } = useUser();
  const profileId = doctorProfileId ?? null;

  const {
    register,
    handleSubmit,
    control,
    reset,
    setError,
    formState: { errors, isSubmitting },
  } = useForm<ProfileFormData>({
    resolver: zodResolver(profileSchema),
    defaultValues: {
      specialty_id: "",
      bio: "",
      mobile: "",
      medical_code: "",
      address: "",
      working_hours: "",
    },
  });

  const { data: profileData, error: profileError, isLoading: isProfileLoading, mutate: mutateProfile } = useSWR<DoctorProfile>(
    profileId ? `/api/v1/doctor/profile/${profileId}` : null,
    (url) => apiFetch<DoctorProfile>(url),
    { shouldRetryOnError: false },
  );

  const { data: specialtiesData, error: specialtiesError } = useSWR<SpecialtiesResponse>(
    "/api/v1/specialties",
    (url) => apiFetch<SpecialtiesResponse>(url),
    { shouldRetryOnError: false },
  );
  const specialties = specialtiesData ? getSpecialties(specialtiesData) : [];
  const selectedSpecialtyId = profileData?.specialty_id;
  const selectedSpecialty = specialties.find(
    (specialty) => String(specialty.id) === String(selectedSpecialtyId),
  );
  const selectedSpecialtyExists = specialties.some(
    (specialty) => String(specialty.id) === String(profileData?.specialty_id),
  );

  // Prefill the form once the profile loads.
  useEffect(() => {
    if (!profileData) return;
    reset({
      specialty_id: profileData.specialty_id !== null && profileData.specialty_id !== undefined
        ? String(profileData.specialty_id)
        : "",
      image: undefined,
      bio: profileData.bio ?? "",
      mobile: profileData.mobile ?? "",
      medical_code: profileData.medical_code ?? "",
      address: profileData.address ?? "",
      working_hours: profileData.working_hours ?? "",
    });
  }, [profileData, reset]);

  // Live preview of the selected image (derived during render).
  const imageList = useWatch({ control, name: "image" });
  const file = imageList?.[0] ?? null;
  const [preview, setPreview] = useState<string | null>(null);
  const [prevFile, setPrevFile] = useState<File | null>(null);
  if (file !== prevFile) {
    setPrevFile(file);
    setPreview(file ? URL.createObjectURL(file) : null);
  }

  async function onSubmit(values: ProfileFormData) {
    if (!profileId) return;

    const formData = new FormData();
    formData.append("specialty_id", values.specialty_id);
    formData.append("mobile", values.mobile);
    formData.append("medical_code", values.medical_code);
    formData.append("address", values.address);
    formData.append("working_hours", values.working_hours);
    formData.append("bio", values.bio);
    // Only send the image when a new file was picked.
    const file = values.image?.[0];
    if (file) {
      formData.append("image", file);
    }

    try {
      await apiFetch(`/api/v1/doctor/profile/${profileId}`, {
        method: "PATCH",
        // FormData body — apiFetch skips the Content-Type header so the
        // browser sets the correct multipart boundary. Credentials and the
        // X-XSRF-TOKEN header are still attached.
        body: formData,
      });
      toast.success("Profile saved", {
        description: "Your profile has been updated.",
        className: "border-green-200 bg-green-50 text-green-950",
        descriptionClassName: "text-green-800",
      });
      await mutateProfile();
    } catch (err) {
      setError("root", { message: err instanceof Error ? err.message : "Could not save your profile" });
    }
  }

  // ── Loading / missing profile states ──────────────────────────────────────

  const stillLookingUpProfile = isUserValidating && !profileId;

  if (isUserLoading || stillLookingUpProfile || isProfileLoading) {
    return (
      <div className="mx-auto flex w-full max-w-3xl flex-col gap-6 p-4 md:p-8">
        <div className="space-y-2">
          <Skeleton className="h-8 w-64" />
          <Skeleton className="h-4 w-80 max-w-full" />
        </div>
        <div className="rounded-xl bg-card p-6 shadow-xs">
          <div className="mb-6 flex items-center gap-4">
            <Skeleton className="size-16 rounded-full" />
            <div className="space-y-2">
              <Skeleton className="h-5 w-40" />
              <Skeleton className="h-4 w-56" />
            </div>
          </div>
          <div className="space-y-4">
            <Skeleton className="h-9 w-full" />
            <Skeleton className="h-9 w-full" />
            <Skeleton className="h-9 w-full" />
            <Skeleton className="h-9 w-full" />
            <Skeleton className="h-24 w-full" />
            <Skeleton className="h-9 w-32" />
          </div>
        </div>
      </div>
    );
  }

  if (!profileId) {
    return (
      <div className="mx-auto flex w-full max-w-3xl flex-col gap-6 p-4 md:p-8">
        <Card>
          <CardHeader>
            <CardTitle className="text-xl">Profile not found</CardTitle>
            <CardDescription>
              No doctor profile is linked to your account. Please contact an administrator.
            </CardDescription>
          </CardHeader>
        </Card>
      </div>
    );
  }

  if (profileError) {
    return (
      <div className="mx-auto flex w-full max-w-3xl flex-col gap-6 p-4 md:p-8">
        <Card>
          <CardHeader>
            <CardTitle className="text-xl">Could not load your profile</CardTitle>
            <CardDescription>
              {profileError instanceof Error ? profileError.message : "Something went wrong while loading your profile."}
            </CardDescription>
          </CardHeader>
        </Card>
      </div>
    );
  }

  // ── Form ──────────────────────────────────────────────────────────────────

  return (
    <div className="mx-auto flex w-full max-w-3xl flex-col gap-6 p-4 md:p-8">
      <div>
        <h1 className="text-2xl font-semibold">Edit your profile</h1>
        <p className="text-muted-foreground">
          Keep your information up to date so patients can find and book you.
        </p>
      </div>
      <Card>
        <CardHeader>
          <CardTitle className="text-xl">Profile details</CardTitle>
          <CardDescription>Update your information at any time.</CardDescription>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleSubmit(onSubmit)}>
            <FieldGroup>
              <Field>
                <FieldLabel htmlFor="image">Profile photo</FieldLabel>
                <div className="flex items-center gap-4">
                  <Avatar size="lg" className="size-16">
                    {preview ? <AvatarImage src={preview} alt="Profile preview" /> : null}
                    <AvatarFallback>
                      {preview ? null : <CameraIcon className="size-6" />}
                    </AvatarFallback>
                  </Avatar>
                  <Input
                    id="image"
                    type="file"
                    accept="image/jpeg,image/png,image/webp"
                    aria-invalid={!!errors.image}
                    className="max-w-xs"
                    {...register("image")}
                  />
                </div>
                <FieldDescription>JPEG, PNG or WebP — max 2MB.</FieldDescription>
                <FieldError errors={[errors.image]} />
              </Field>

              <Field>
                <FieldLabel htmlFor="specialty_id">Specialty</FieldLabel>
                <Controller
                  control={control}
                  name="specialty_id"
                  render={({ field }) => (
                    <Select value={field.value} onValueChange={field.onChange}>
                      <SelectTrigger
                        id="specialty_id"
                        className="w-full"
                        aria-invalid={!!errors.specialty_id}
                      >
                        <SelectValue placeholder="Select a specialty">
                          {selectedSpecialty?.name}
                        </SelectValue>
                      </SelectTrigger>
                      <SelectContent>
                        {specialties.map((specialty) => (
                          <SelectItem key={specialty.id} value={String(specialty.id)}>
                            {specialty.name}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                  )}
                />
                {specialtiesError && (
                  <FieldDescription className="text-destructive">Could not load specialties.</FieldDescription>
                )}
                {!specialtiesError && profileData?.specialty_id && !selectedSpecialtyExists && (
                  <FieldDescription className="text-destructive">
                    Your current specialty is unavailable. Please select another specialty.
                  </FieldDescription>
                )}
                <FieldError errors={[errors.specialty_id]} />
              </Field>

              <Field>
                <FieldLabel htmlFor="mobile">Mobile</FieldLabel>
                <Input id="mobile" placeholder="+989123456789" {...register("mobile")} />
                <FieldError errors={[errors.mobile]} />
              </Field>

              <Field>
                <FieldLabel htmlFor="medical_code">Medical code</FieldLabel>
                <Input id="medical_code" placeholder="MC-12345" {...register("medical_code")} />
                <FieldError errors={[errors.medical_code]} />
              </Field>

              <Field>
                <FieldLabel htmlFor="address">Address</FieldLabel>
                <Input id="address" placeholder="Tehran, Valiasr St." {...register("address")} />
                <FieldError errors={[errors.address]} />
              </Field>

              <Field>
                <FieldLabel htmlFor="working_hours">Working hours</FieldLabel>
                <Input id="working_hours" placeholder="Sat-Wed 9:00-17:00" {...register("working_hours")} />
                <FieldError errors={[errors.working_hours]} />
              </Field>

              <Field>
                <FieldLabel htmlFor="bio">Description</FieldLabel>
                <Textarea
                  id="bio"
                  placeholder="Experienced cardiologist with 10+ years…"
                  className="min-h-28"
                  {...register("bio")}
                />
                <FieldError errors={[errors.bio]} />
              </Field>

              {errors.root && <FieldDescription className="text-destructive">{errors.root.message}</FieldDescription>}

              <Field>
                <Button type="submit" disabled={isSubmitting} className="w-fit">
                  {isSubmitting ? "Saving…" : "Save profile"}
                </Button>
              </Field>
            </FieldGroup>
          </form>
        </CardContent>
      </Card>
    </div>
  );
}