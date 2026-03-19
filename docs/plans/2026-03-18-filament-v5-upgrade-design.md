# Filament v5 Upgrade Design

## Overview

Upgrade Filament from v4 to v5, which requires cascading upgrades to Livewire (v3 → v4) and Tailwind CSS (v3 → v4).

## Current State

- **Filament**: v4
- **Livewire**: v3 (via Filament)
- **Tailwind CSS**: v3
- **PHP**: 8.2 (meets v5 requirements)
- **Laravel**: v12 (meets v5 requirements)

### Filament Footprint

- 1 admin panel (`/admin`, amber color, built-in login)
- 4 resources: Tariffs, Adjustments, Costs, Parserlogs
- All resources are basic CRUD (List, Create, Edit pages)
- No custom widgets, pages, or third-party Filament plugins
- Standard form components only: TextInput, DatePicker, Select
- Standard table columns: TextColumn with sorting/searching/filtering

## New Requirements

| Dependency | Current | Required | Status |
|---|---|---|---|
| PHP | 8.2 | 8.2+ | ✓ |
| Laravel | 12 | 11.28+ | ✓ |
| Livewire | v3 | v4 | needs upgrade |
| Tailwind CSS | v3 | v4 | needs upgrade |

## Upgrade Sequence

### Step 1: Run Filament's Automated Upgrade Script

Filament provides a script that audits the app and outputs specific migration instructions.

```bash
vendor/bin/sail composer require filament/upgrade:"^5.0" -W --dev
vendor/bin/sail vendor/bin/filament-v5
```

Follow the output instructions, then:

```bash
vendor/bin/sail composer require filament/filament:"^5.0" -W --no-update
vendor/bin/sail composer update
vendor/bin/sail composer remove filament/upgrade --dev
```

### Step 2: Upgrade Tailwind CSS v3 → v4

Use the official automated upgrade tool:

```bash
npx @tailwindcss/upgrade
```

Key changes this handles:
- CSS syntax: `@tailwind base/components/utilities` → `@import "tailwindcss"`
- PostCSS config: `tailwindcss: {}` → `"@tailwindcss/postcss": {}`
- Vite plugin: add `@tailwindcss/vite` plugin
- Utility class renames: `shadow-xs` → `shadow-2xs`, `rounded-xs` → `rounded-xs`, etc.
- Removed utilities: `bg-opacity-*` → `bg-black/50` style modifiers

Manual checks after the tool:
- `@tailwindcss/forms` → update to v4-compatible version
- Any `theme()` function calls → replace with CSS variables
- Border colors now default to `currentColor` (was `gray-200`)
- Ring defaults changed (width: 3px → 1px, color: blue-500 → currentColor)

### Step 3: Upgrade Livewire v3 → v4

Since Livewire is used almost exclusively through Filament in this app (no custom Livewire components), changes are minimal.

Key changes to verify:
- Config file: `layout` → `component_layout`, `lazy_placeholder` → `component_placeholder`
- `wire:model` is now deferred by default (was already the case in Livewire 3; verify `.live` usage)
- `wire:transition` no longer supports custom modifiers (`.opacity`, `.scale`, etc.)
- Component tags must be self-closing: `<livewire:component-name />`

### Step 4: Test and Fix

```bash
vendor/bin/sail artisan test
```

Address any remaining issues found by tests or manual review of the admin panel.

## Risk Assessment

**Low risk** given:
- Small, well-contained Filament footprint
- No third-party Filament plugins that might lack v5 support
- No custom Livewire components outside of Filament
- Simple Tailwind usage (mostly basic utilities, no complex config)
- Automated tooling available for all three upgrades

## Files Likely to Change

- `composer.json` — Filament version constraint
- `package.json` — Tailwind packages
- `postcss.config.js` / `vite.config.js` — Tailwind v4 plugin setup
- `resources/css/app.css` — `@tailwind` → `@import "tailwindcss"`
- `tailwind.config.js` — may be replaced by CSS-based config
- `app/Providers/Filament/AdminPanelProvider.php` — any v5 API changes
- `app/Filament/Resources/*.php` — any breaking API changes caught by upgrade script
