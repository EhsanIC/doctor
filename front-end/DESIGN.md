# Design System — [App Name]

Blue & white theme, built on shadcn/ui + Tailwind CSS, using OKLCH color space for perceptually consistent light/dark modes.

## Setup

Theme variables live in `app/globals.css` (already installed via the shadcn CLI). Tailwind reads these through `tailwind.config.ts` using the `hsl(var(--token))` / `oklch(var(--token))` pattern that `shadcn init` sets up automatically.

---

## Color Tokens — Light Mode

| Token | Value | Usage |
|---|---|---|
| `--background` | `oklch(1 0 0)` | Page background (pure white) |
| `--foreground` | `oklch(0.148 0.004 228.8)` | Default text color |
| `--card` | `oklch(1 0 0)` | Card / panel background |
| `--card-foreground` | `oklch(0.148 0.004 228.8)` | Text on cards |
| `--popover` | `oklch(1 0 0)` | Popover / dropdown background |
| `--popover-foreground` | `oklch(0.148 0.004 228.8)` | Text on popovers |
| `--primary` | `oklch(0.5 0.134 242.749)` | Primary brand blue — buttons, links, active states |
| `--primary-foreground` | `oklch(0.977 0.013 236.62)` | Text on primary elements |
| `--secondary` | `oklch(0.967 0.001 286.375)` | Secondary buttons, subtle backgrounds |
| `--secondary-foreground` | `oklch(0.21 0.006 285.885)` | Text on secondary elements |
| `--muted` | `oklch(0.963 0.002 197.1)` | Muted backgrounds (disabled, subtle sections) |
| `--muted-foreground` | `oklch(0.56 0.021 213.5)` | Muted / secondary text |
| `--accent` | `oklch(0.963 0.002 197.1)` | Hover states, highlighted UI |
| `--accent-foreground` | `oklch(0.218 0.008 223.9)` | Text on accent elements |
| `--destructive` | `oklch(0.577 0.245 27.325)` | Errors, delete actions, destructive buttons |
| `--border` | `oklch(0.925 0.005 214.3)` | Dividers, card borders |
| `--input` | `oklch(0.925 0.005 214.3)` | Input field borders |
| `--ring` | `oklch(0.723 0.014 214.4)` | Focus ring outline |

### Chart Colors (light & dark)
| Token | Value |
|---|---|
| `--chart-1` | `oklch(0.828 0.111 230.318)` |
| `--chart-2` | `oklch(0.685 0.169 237.323)` |
| `--chart-3` | `oklch(0.588 0.158 241.966)` |
| `--chart-4` | `oklch(0.5 0.134 242.749)` |
| `--chart-5` | `oklch(0.443 0.11 240.79)` |

### Sidebar (light mode)
| Token | Value |
|---|---|
| `--sidebar` | `oklch(0.987 0.002 197.1)` |
| `--sidebar-foreground` | `oklch(0.148 0.004 228.8)` |
| `--sidebar-primary` | `oklch(0.588 0.158 241.966)` |
| `--sidebar-primary-foreground` | `oklch(0.977 0.013 236.62)` |
| `--sidebar-accent` | `oklch(0.963 0.002 197.1)` |
| `--sidebar-accent-foreground` | `oklch(0.218 0.008 223.9)` |
| `--sidebar-border` | `oklch(0.925 0.005 214.3)` |
| `--sidebar-ring` | `oklch(0.723 0.014 214.4)` |

---

## Color Tokens — Dark Mode (`.dark`)

| Token | Value | Usage |
|---|---|---|
| `--background` | `oklch(0.148 0.004 228.8)` | Page background (near-black blue) |
| `--foreground` | `oklch(0.987 0.002 197.1)` | Default text color |
| `--card` | `oklch(0.218 0.008 223.9)` | Card / panel background |
| `--card-foreground` | `oklch(0.987 0.002 197.1)` | Text on cards |
| `--popover` | `oklch(0.218 0.008 223.9)` | Popover background |
| `--popover-foreground` | `oklch(0.987 0.002 197.1)` | Text on popovers |
| `--primary` | `oklch(0.443 0.11 240.79)` | Primary brand blue (dark mode) |
| `--primary-foreground` | `oklch(0.977 0.013 236.62)` | Text on primary elements |
| `--secondary` | `oklch(0.274 0.006 286.033)` | Secondary buttons |
| `--secondary-foreground` | `oklch(0.985 0 0)` | Text on secondary elements |
| `--muted` | `oklch(0.275 0.011 216.9)` | Muted backgrounds |
| `--muted-foreground` | `oklch(0.723 0.014 214.4)` | Muted text |
| `--accent` | `oklch(0.275 0.011 216.9)` | Hover / highlighted UI |
| `--accent-foreground` | `oklch(0.987 0.002 197.1)` | Text on accent elements |
| `--destructive` | `oklch(0.704 0.191 22.216)` | Errors, destructive actions |
| `--border` | `oklch(1 0 0 / 10%)` | Dividers, borders (10% white) |
| `--input` | `oklch(1 0 0 / 15%)` | Input borders (15% white) |
| `--ring` | `oklch(0.56 0.021 213.5)` | Focus ring outline |

### Sidebar (dark mode)
| Token | Value |
|---|---|
| `--sidebar` | `oklch(0.218 0.008 223.9)` |
| `--sidebar-foreground` | `oklch(0.987 0.002 197.1)` |
| `--sidebar-primary` | `oklch(0.685 0.169 237.323)` |
| `--sidebar-primary-foreground` | `oklch(0.293 0.066 243.157)` |
| `--sidebar-accent` | `oklch(0.275 0.011 216.9)` |
| `--sidebar-accent-foreground` | `oklch(0.987 0.002 197.1)` |
| `--sidebar-border` | `oklch(1 0 0 / 10%)` |
| `--sidebar-ring` | `oklch(0.56 0.021 213.5)` |

---

## Radius

| Token | Value | Usage |
|---|---|---|
| `--radius` | `0.625rem` (10px) | Base border radius |

Common derived values used by shadcn components:
- `--radius-sm`: `calc(var(--radius) - 4px)`
- `--radius-md`: `calc(var(--radius) - 2px)`
- `--radius-lg`: `var(--radius)`
- `--radius-xl`: `calc(var(--radius) + 4px)`

---

## Typography

> Not defined by the shadcn theme installer — decide and add here.

- Font family: _TBD (e.g. `Inter`, `Geist Sans`)_
- Scale: _TBD (e.g. 12 / 14 / 16 / 20 / 24 / 32px)_
- Weights: _TBD (e.g. 400 regular, 500 medium, 600 semibold, 700 bold)_

---

## Usage Conventions

- **Primary** (`--primary`): main CTAs, active nav items, links, selected states
- **Secondary**: less prominent buttons/actions that still need affordance
- **Muted**: placeholder text, disabled states, subtle backgrounds
- **Accent**: hover states, highlighted rows/items
- **Destructive**: delete, remove, cancel-with-consequence actions only
- **Border/Input**: always pair with `--ring` for focus-visible states (accessibility)

## Component Source

Components are added individually via:
```bash
bunx shadcn@latest add [component-name]
```
Each installed component lives in `components/ui/` and consumes these tokens automatically — do not hardcode colors inside component files; always reference the CSS variables (or their Tailwind equivalents like `bg-primary`, `text-muted-foreground`) so light/dark mode and future theme changes propagate everywhere.

## Dark Mode

Toggle by adding/removing the `dark` class on `<html>` (commonly via `next-themes`). All tokens above swap automatically since both `:root` and `.dark` are defined in `globals.css`.