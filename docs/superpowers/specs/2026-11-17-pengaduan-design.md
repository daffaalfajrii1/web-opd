# Modul Pengaduan (OPD Dukcapil)

## Ringkasan
Modul pengaduan publik dengan kategori dari admin, nomor unik, dan thread feedback bolak-balik tanpa login (cukup nomor).

## Data
- `pengaduan_kategoris`: name, slug, description, is_active, sort_order
- `pengaduans`: kategori_id, nomor (PGD-YYYY-NNNNNN), nama, no_hp, isi, status (baru|diproses|selesai)
- `pengaduan_feedbacks`: pengaduan_id, sender (user|admin), message, user_id (admin nullable)

## Publik
- Navbar: Pengaduan (dropdown kategori + Cek Status), sebelah Kontak
- Ikon mengambang pojok kanan bawah
- Form: nama, no HP, isi (+ kategori dari URL)
- Setelah kirim: tampil nomor + “Harap diingat nomor pengaduannya”
- Cek status + balas feedback dengan nomor saja

## Admin
- Permission `manage pengaduan`
- CRUD kategori
- Inbox pengaduan, ubah status, kirim feedback
