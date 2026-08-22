import { create } from "zustand";

export type AppUser = {
  id: number;
  name: string;
  role: "admin" | "doctor" | "patient";
};

type AuthStore = {
  user: AppUser | null;
  setUser: (user: AppUser) => void;
  clearUser: () => void;
};

export const useAuthStore = create<AuthStore>((set) => ({
  user: null,
  setUser: (user) => set({ user }),
  clearUser: () => set({ user: null }),
}));