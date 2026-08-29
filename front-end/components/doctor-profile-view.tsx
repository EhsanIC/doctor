"use client";

import useSWR from "swr";
import { apiFetch } from "@/lib/api";
import { useUser } from "@/hooks/useUser";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Badge } from "@/components/ui/badge";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Skeleton } from "@/components/ui/skeleton";

// ── Types ───────────────────────────────────────────────────────────────────

type DoctorProfile = {
  id: number;
  user_id: number;
  specialty_id: number | null;
  status: string;
  image: string | null;
  image_url?: string | null;
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

function getInitials(name: string): string {
  const parts = name.trim().split(/\s+/);
  const first = parts[0]?.[0] ?? "";
  const last = parts.length > 1 ? parts[parts.length - 1]?.[0] ?? "" : "";
  return (first + last).toUpperCase();
}

// ── Component ───────────────────────────────────────────────────────────────

export function DoctorProfileView() {
  const { user, doctorProfileId, isLoading: isUserLoading, isValidating: isUserValidating } = useUser();
  const profileId = doctorProfileId ?? null;

  const { data: profile, error: profileError, isLoading: isProfileLoading } = useSWR<DoctorProfile>(
    profileId ? `/api/v1/doctor/profile/${profileId}` : null,
    (url) => apiFetch<DoctorProfile>(url),
    { shouldRetryOnError: false },
  );

  const { data: specialtiesData } = useSWR<SpecialtiesResponse>(
    "/api/v1/specialties",
    (url) => apiFetch<SpecialtiesResponse>(url),
    { shouldRetryOnError: false },
  );
  const specialties = specialtiesData ? getSpecialties(specialtiesData) : [];
  const specialtyName = specialties.find((s) => s.id === profile?.specialty_id)?.name;

  // ── Content per state ─────────────────────────────────────────────────────

  let content: React.ReactNode;

  const stillLookingUpProfile = isUserValidating && !profileId;

  if (isUserLoading || stillLookingUpProfile || isProfileLoading) {
    content = (
      <div className="mx-auto flex w-full max-w-3xl flex-col gap-6">
        <div className="space-y-2">
          <Skeleton className="h-8 w-48" />
          <Skeleton className="h-4 w-72 max-w-full" />
        </div>
        <div className="rounded-xl bg-card p-6 shadow-xs">
          <div className="mb-6 flex items-center gap-4">
            <Skeleton className="size-16 rounded-full" />
            <div className="space-y-2">
              <Skeleton className="h-5 w-44" />
              <Skeleton className="h-4 w-56" />
            </div>
          </div>
          <div className="grid gap-4 sm:grid-cols-2">
            <Skeleton className="h-16 rounded-lg" />
            <Skeleton className="h-16 rounded-lg" />
            <Skeleton className="h-16 rounded-lg" />
            <Skeleton className="h-16 rounded-lg" />
          </div>
          <Skeleton className="mt-4 h-24 w-full rounded-lg" />
        </div>
      </div>
    );
  } else if (!profileId) {
    content = (
      <Card className="mx-auto w-full max-w-3xl">
        <CardHeader>
          <CardTitle className="text-xl">Profile not found</CardTitle>
          <CardDescription>
            No doctor profile is linked to your account. Please contact an administrator.
          </CardDescription>
        </CardHeader>
      </Card>
    );
  } else if (profileError || !profile) {
    content = (
      <Card className="mx-auto w-full max-w-3xl">
        <CardHeader>
          <CardTitle className="text-xl">Could not load your profile</CardTitle>
          <CardDescription>
            {profileError instanceof Error
              ? profileError.message
              : "Something went wrong while loading your profile."}
          </CardDescription>
        </CardHeader>
      </Card>
    );
  } else {
    const name = profile.user?.name ?? user?.name ?? "Doctor";
    const email = profile.user?.email ?? "—";

    const infoRows = [
      { label: "Mobile", value: profile.mobile ?? "—" },
      { label: "Medical code", value: profile.medical_code ?? "—" },
      { label: "Address", value: profile.address ?? "—" },
      { label: "Working hours", value: profile.working_hours ?? "—" },
    ];

    content = (
      <div className="mx-auto flex w-full max-w-3xl flex-col gap-6">
        <div>
          <h1 className="text-2xl font-semibold">My profile</h1>
          <p className="text-muted-foreground">How patients see you on the platform.</p>
        </div>
        <Card>
          <CardHeader>
            <div className="flex flex-col gap-4 sm:flex-row sm:items-center">
              <Avatar size="lg" className="!size-[100px] shrink-0 text-base">
                {profile.image_url ? <AvatarImage src={profile.image_url} alt={`${name} profile photo`} /> : null}
                <AvatarFallback>{getInitials(name)}</AvatarFallback>
              </Avatar>
              <div className="space-y-1">
                <CardTitle className="text-xl">{name}</CardTitle>
                <CardDescription>{email}</CardDescription>
                <div className="flex flex-wrap gap-2 pt-1">
                  {specialtyName && <Badge variant="secondary">{specialtyName}</Badge>}
                  <Badge
                    variant={
                      profile.status === "active"
                        ? "default"
                        : profile.status === "disabled"
                          ? "destructive"
                          : "secondary"
                    }
                  >
                    {profile.status}
                  </Badge>
                </div>
              </div>
            </div>
          </CardHeader>
          <CardContent>
            <div className="grid gap-4 sm:grid-cols-2">
              {infoRows.map((row) => (
                <div key={row.label} className="rounded-lg border border-border p-4">
                  <div className="text-xs font-medium text-muted-foreground">{row.label}</div>
                  <div className="mt-1 text-sm font-medium">{row.value}</div>
                </div>
              ))}
            </div>
            <div className="mt-4 rounded-lg border border-border p-4">
              <div className="text-xs font-medium text-muted-foreground">Description</div>
              <p className="mt-1 text-sm whitespace-pre-wrap">{profile.bio || "—"}</p>
            </div>
          </CardContent>
        </Card>
      </div>
    );
  }

  return <main className="flex flex-1 flex-col p-4 md:p-8">{content}</main>;
}