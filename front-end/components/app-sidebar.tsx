"use client"

import * as React from "react"

import { useUser } from "@/hooks/useUser"

import { NavMain } from "@/components/nav-main"
import { NavProjects } from "@/components/nav-projects"
import { NavUser } from "@/components/nav-user"
import { TeamSwitcher } from "@/components/team-switcher"
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarRail,
} from "@/components/ui/sidebar"
import { GalleryVerticalEndIcon, LayoutDashboardIcon, StethoscopeIcon, Settings2Icon } from "lucide-react"

const data = {
  user: {
    name: "Admin",
    email: "admin@example.com",
    avatar: "/avatars/shadcn.jpg",
  },
  teams: [{ name: "Doctor Management", logo: <GalleryVerticalEndIcon />, plan: "Admin" }],
  navMain: [
    { title: "Dashboard", url: "/admin/doctors", icon: <LayoutDashboardIcon />, isActive: true },
    { title: "Doctors", url: "/admin/doctors", icon: <StethoscopeIcon /> },
    { title: "Settings", url: "#", icon: <Settings2Icon /> },
  ],
  projects: [],
}

export function AppSidebar({ ...props }: React.ComponentProps<typeof Sidebar>) {
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
        <NavProjects projects={data.projects} />
      </SidebarContent>
      <SidebarFooter>
        <NavUser user={sidebarUser} />
      </SidebarFooter>
      <SidebarRail />
    </Sidebar>
  )
}
