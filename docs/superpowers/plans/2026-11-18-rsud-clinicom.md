# RSUD Clinicom Implementation Plan

> **For agentic workers:** Implement task-by-task on branch `opd/rsud` only.

**Goal:** Rebuild RSUD public site with Clinicom UI, doctors/schedules, dynamic nav, WA aduan.

**Architecture:** Laravel Blade layouts using Clinicom assets in `public/clinicom`; keep existing content modules; add dokter + jadwal + nav_menus.

**Tech Stack:** Laravel, Clinicom (Bootstrap 4 era), Cropper.js, SortableJS, Asia/Jakarta

## Global Constraints
- Branch `opd/rsud` only
- Keep website carousel for hero
- WA from `contact_whatsapp`
- Responsive desktop + mobile

## Tasks
1. Copy assets + clinicom layout/partials
2. Restyle public pages to clinicom layout
3. Dokter + jadwal migrations/models/admin/public
4. Dynamic nav menus + drag sort
5. Home search + WA FAB + tests
