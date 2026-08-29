"use client"

import * as React from "react"

import { useUser } from "@/hooks/useUser"

import { NavMain } from "@/components/nav-main"
import { NavUser } from "@/components/nav-user"
import { TeamSwitcher } from "@/components/team-switcher"
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarRail,
} from "@/components/ui/sidebar"
import { CalendarDaysIcon, LayoutDashboardIcon, StethoscopeIcon, UserRoundIcon } from "lucide-react"

const data = {
  user: {
    name: "Doctor",
    email: "doctor@example.com",
    avatar: "",
  },
  teams: [{ name: "Doctor Panel", logo: <StethoscopeIcon />, plan: "Doctor" }],
  navMain: [
    { title: "Dashboard", url: "/doctor", icon: <LayoutDashboardIcon />, isActive: true },
    { title: "Appointments", url: "/doctor/appointments", icon: <CalendarDaysIcon /> },
    {
      title: "Profile",
      url: "/doctor/profile",
      icon: <UserRoundIcon />,
      items: [
        { title: "View profile", url: "/doctor/profile" },
        { title: "Edit profile", url: "/doctor/profile/edit" },
      ],
    },
  ],
}

export function DoctorSidebar({ ...props }: React.ComponentProps<typeof Sidebar>) {
  const { user } = useUser()
  const sidebarUser = {
    ...data.user,
    name: user?.name ?? data.user.name,
    avatar: user?.doctorProfileImageUrl ?? data.user.avatar,
  }

  return (
    <Sidebar collapsible="icon" {...props}>
      <SidebarHeader>
        <TeamSwitcher teams={data.teams} />
      </SidebarHeader>
      <SidebarContent>
        <NavMain items={data.navMain} />
      </SidebarContent>
      <SidebarFooter>
        <NavUser user={sidebarUser} profileHref="/doctor/profile" />
      </SidebarFooter>
      <SidebarRail />
    </Sidebar>
  )
}