# Filament v5 Upgrade Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Upgrade Filament from v4 to v5, including the required Livewire v3→v4 and Tailwind CSS v3→v4 upgrades.

**Architecture:** Run Filament's automated upgrade script first (it audits the app and drives the sequence), then upgrade Tailwind CSS using its official tool, then clean up Livewire config changes. All commands run directly using the system PHP/Composer/Node since Sail mounts a different directory.

**Tech Stack:** PHP 8.5 (system), Composer 2.9, Node 24, npm 11, Filament v5, Livewire v4, Tailwind CSS v4, Vite

---

## Context

- **Worktree:** `/home/greg/workspace/electricity-calculator.cy/.worktrees/filament-v5-upgrade`
- **Branch:** `filament-v5-upgrade`
- **All commands:** Run from within the worktree directory unless stated otherwise
- **Sail note:** Sail runs in the main project directory. All PHP/Composer/npm commands in this plan use system binaries (`php`, `composer`, `node`, `npm`) instead of `vendor/bin/sail` since they are available on the host.
- **Filament footprint:** 4 basic CRUD resources (Tariffs, Adjustments, Costs, Parserlogs), 1 admin panel provider, no custom widgets/pages/third-party plugins

---

## Setup: Prepare the Worktree

The worktree does not have its own `vendor/` or `node_modules/` since these are gitignored.

**Step 1: Install PHP dependencies**

```bash
cd /home/greg/workspace/electricity-calculator.cy/.worktrees/filament-v5-upgrade
composer install
```

Expected: vendor directory created.

**Step 2: Install Node dependencies**

```bash
npm install
```

Expected: node_modules directory created.

**Step 3: Copy .env**

```bash
cp /home/greg/workspace/electricity-calculator.cy/.env .env
```

**Step 4: Commit (nothing to commit — just confirming setup is clean)**

```bash
git status
```

Expected: clean working tree.

---

## Task 1: Run Filament's Automated Upgrade Script

The script audits the codebase and outputs app-specific migration instructions. Run it BEFORE bumping Filament's version.

**Step 1: Install the Filament upgrade tool**

```bash
composer require filament/upgrade:"^5.0" -W --dev
```

Expected: `filament/upgrade` added to `require-dev` in `composer.json`.

**Step 2: Run the upgrade script**

```bash
vendor/bin/filament-v5
```

Expected: The script outputs a list of app-specific changes needed. Read the output carefully.

**Step 3: Apply all instructions from the script output**

The script typically modifies files automatically and/or outputs manual steps. Apply every manual step listed before moving on.

After applying: run `git diff` to review what changed.

**Step 4: Bump Filament to v5**

```bash
composer require filament/filament:"^5.0" -W --no-update
composer update
```

Expected: `composer.json` updated to `^5.0`, Livewire v4 pulled in as a transitive dependency.

**Step 5: Remove the upgrade tool**

```bash
composer remove filament/upgrade --dev
```

**Step 6: Verify composer resolves without errors**

```bash
composer validate
php artisan about 2>/dev/null | head -20
```

Expected: no errors.

**Step 7: Commit**

```bash
git add -A
git commit -m "feat: upgrade Filament to v5 via automated script"
```

---

## Task 2: Upgrade Tailwind CSS v3 → v4

Tailwind v4 has significant breaking changes. The official upgrade tool handles most of them automatically.

**Step 1: Run the Tailwind automated upgrade tool**

```bash
npx @tailwindcss/upgrade
```

Expected: The tool modifies `postcss.config.js`, `tailwind.config.js`, `resources/css/app.css`, and potentially `vite.config.js` and `package.json`.

**Step 2: Review what changed**

```bash
git diff
```

Key things to verify the tool handled:
- `resources/css/app.css`: `@tailwind base/components/utilities` replaced with `@import "tailwindcss"`
- `postcss.config.js`: `tailwindcss: {}` replaced with `"@tailwindcss/postcss": {}`
- `package.json`: `tailwindcss` updated to v4, `@tailwindcss/forms` updated to v4-compatible version

**Step 3: Update vite.config.js to use the Tailwind Vite plugin**

Tailwind v4 recommends the dedicated Vite plugin over the PostCSS approach. Update `vite.config.js`:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: 'localhost',
    },
});
```

If using the Vite plugin, remove `tailwindcss` from `postcss.config.js` plugins (keep only `autoprefixer` if needed, or remove the file entirely if Tailwind was the only plugin).

**Step 4: Install updated npm packages**

```bash
npm install
```

**Step 5: Run a build to catch any remaining CSS issues**

```bash
npm run build
```

Expected: build succeeds. Fix any errors before continuing.

**Step 6: Commit**

```bash
git add -A
git commit -m "feat: upgrade Tailwind CSS to v4"
```

---

## Task 3: Verify and Fix Livewire v4 Changes

Livewire v4 was pulled in by Filament in Task 1. This task ensures any Livewire-specific changes are clean.

**Step 1: Check for a published Livewire config**

```bash
ls config/livewire.php 2>/dev/null || echo "no livewire config"
```

If the file exists, update renamed keys:
- `layout` → `component_layout`
- `lazy_placeholder` → `component_placeholder`

**Step 2: Check for any `wire:transition` usage with removed modifiers**

```bash
grep -r "wire:transition\." resources/views --include="*.blade.php"
```

Modifiers `.opacity`, `.scale`, `.duration`, `.origin` no longer work in v4. Remove them (the directive still works without modifiers).

**Step 3: Check for unclosed Livewire component tags**

```bash
grep -r "<livewire:" resources/views --include="*.blade.php" | grep -v "/>"
```

Any `<livewire:foo>` tags (without self-closing `/>`) need to become `<livewire:foo />`.

**Step 4: Commit if any changes were needed**

```bash
git add -A
git commit -m "feat: fix Livewire v4 breaking changes"
```

If nothing changed, skip the commit.

---

## Task 4: Verify the Admin Panel Works

**Step 1: Clear caches**

```bash
php artisan optimize:clear
```

**Step 2: Run another build**

```bash
npm run build
```

Expected: clean build with no errors.

**Step 3: Publish Filament assets (if needed)**

```bash
php artisan filament:upgrade
```

**Step 4: Check for any remaining deprecation errors**

```bash
php artisan about
```

**Step 5: Run Pint to fix any code style issues**

```bash
vendor/bin/pint --dirty
```

**Step 6: Commit**

```bash
git add -A
git commit -m "chore: post-upgrade cleanup and asset publishing"
```

---

## Task 5: Final Review and Merge Prep

**Step 1: Manually open the admin panel**

Ask the user to:
1. Start Sail in the worktree (or temporarily point Sail at the worktree): `vendor/bin/sail up -d` from the worktree
2. Visit `/admin` and verify:
   - Login works
   - All 4 resources (Tariffs, Adjustments, Costs, Parserlogs) load
   - Create, Edit, and Delete actions work on each resource
   - No console errors

**Step 2: Review the full diff**

```bash
git log master..filament-v5-upgrade --oneline
git diff master..filament-v5-upgrade
```

**Step 3: Use the finishing-a-development-branch skill**

Once all checks pass, use `superpowers:finishing-a-development-branch` to decide how to merge.
