# RSUD Clinicom Redesign (`opd/rsud` only)

## Goals
- Public UI uses Clinicom template (desktop + mobile)
- Keep admin carousel for hero images
- WhatsApp Aduan from website settings
- Dynamic doctors + per-doctor schedules (day + start/end, Asia/Jakarta)
- Doctor photo crop in admin; public cards clickable + searchable
- Core navbar fixed; extra menu groups/pages dynamic with drag reorder

## Data
- `dokters`: name, specialty, photo_path, bio, is_active, sort_order
- `dokter_jadwals`: dokter_id, day_of_week (1-7 Mon-Sun ID), start_time, end_time, note, is_active
- `nav_menus`: title, sort_order, is_active (dropdown groups)
- `nav_menu_items` / reuse `pages` with menu_id + sort_order for dynamic pages under groups
- `website_settings.contact_whatsapp` for Aduan FAB

## Public
- Layout `public.layouts.clinicom`
- Home: carousel + doctor search + doctor cards
- Routes: /dokter, /dokter/{slug}, /jadwal-dokter
- FAB WhatsApp Aduan

## Admin
- CRUD dokter + jadwal rows + Cropper.js
- Menu builder with SortableJS
- Permission `manage dokter`, extend pages/menus

## Scope
Branch `opd/rsud` only. Other OPD branches unchanged.
