"use client"

import * as React from "react"
import { CalendarDaysIcon, LayoutDashboardIcon, SearchIcon, UserRoundIcon, StethoscopeIcon } from "lucide-react"
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

const data = {
  user: {
    name: "Patient",
    email: "patient@example.com",
    avatar: "",
  },
  teams: [{ name: "Patient Panel", logo: <StethoscopeIcon />, plan: "Patient" }],
  navMain: [
    {
      title: "Dashboard",
      url: "/patient/doctors",
      icon: <LayoutDashboardIcon />,
      isActive: true,
      items: [{ title: "Find a doctor", url: "/patient/doctors" }],
    },
    {
      title: "Doctors",
      url: "/patient/doctors",
      icon: <SearchIcon />,
      items: [{ title: "Find a doctor", url: "/patient/doctors" }],
    },
    {
      title: "Appointments",
      url: "/patient/appointments",
      icon: <CalendarDaysIcon />,
      items: [{ title: "My appointments", url: "/patient/appointments" }],
    },
    {
      title: "Profile",
      url: "/patient/profile",
      icon: <UserRoundIcon />,
      items: [{ title: "View profile", url: "/patient/profile" }],
    },
  ],
}

export function PatientSidebar({ ...props }: React.ComponentProps<typeof Sidebar>) {
  const { user } = useUser()
  const sidebarUser = {
    ...data.user,
    name: user?.name ?? data.user.name,
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
        <NavUser user={sidebarUser} profileHref="/patient/profile" />
      </SidebarFooter>
      <SidebarRail />
    </Sidebar>
  )
}
