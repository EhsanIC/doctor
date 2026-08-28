"use client"

import { DoctorSidebar } from "@/components/doctor-sidebar"
import { SidebarInset, SidebarProvider, SidebarTrigger } from "@/components/ui/sidebar"

export function DoctorShell({
  title,
  children,
}: {
  title: string
  children: React.ReactNode
}) {
  return (
    <SidebarProvider>
      <DoctorSidebar />
      <SidebarInset>
        <header className="flex h-14 items-center gap-2 border-b border-slate-300/70 px-4">
          <SidebarTrigger />
          <div className="flex items-center gap-2 text-sm font-medium">{title}</div>
        </header>
        {children}
      </SidebarInset>
    </SidebarProvider>
  )
}