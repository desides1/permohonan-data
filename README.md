# Sistem Manajemen Permohonan Data BPKH

Aplikasi web untuk mengelola permohonan data secara end-to-end, mulai dari pengajuan oleh pemohon, verifikasi internal, disposisi ke seksi, review pimpinan, hingga penyerahan hasil kepada pemohon.

Project ini dibangun dengan Laravel 12, Inertia.js, Vue 3, dan Tailwind CSS, serta dilengkapi integrasi notifikasi dan backup ke Google Drive.

## Daftar Isi

- [Gambaran Umum](#gambaran-umum)
- [Fitur Utama](#fitur-utama)
- [Alur Proses Permohonan](#alur-proses-permohonan)
- [Role dan Hak Akses](#role-dan-hak-akses)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Struktur Project](#struktur-project)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Instalasi dan Setup](#instalasi-dan-setup)
- [Konfigurasi Environment](#konfigurasi-environment)
- [Menjalankan Project](#menjalankan-project)
- [Akun Seeder Default](#akun-seeder-default)
- [Alur Penggunaan Singkat](#alur-penggunaan-singkat)
- [API Internal](#api-internal)
- [Testing](#testing)
- [Backup dan Scheduler](#backup-dan-scheduler)
- [Notifikasi dan Integrasi n8n](#notifikasi-dan-integrasi-n8n)
- [Troubleshooting](#troubleshooting)
- [Kontribusi](#kontribusi)
- [Lisensi](#lisensi)

## Gambaran Umum

Sistem ini dirancang untuk mendigitalisasi proses permohonan data di lingkungan BPKH agar lebih terstruktur, terdokumentasi, dan mudah dipantau.

Fungsi utama aplikasi:

- Pemohon dapat mengajukan permohonan data melalui formulir online.
- Sistem membuat kode tiket untuk pelacakan permohonan.
- Admin dan pimpinan memproses tiket berdasarkan workflow dan role masing-masing.
- Petugas seksi dapat mengunggah hasil dokumen pendukung.
- Pemohon dapat memantau status dan mengunduh hasil permohonan jika sudah selesai.
- Sistem mencatat aktivitas, notifikasi, feedback, dan riwayat backup.

## Fitur Utama

- Pengajuan permohonan data melalui landing page.
- Pelacakan status permohonan menggunakan kode tiket.
- Auto-register pemohon saat pengajuan pertama.
- Dashboard admin berdasarkan role.
- Workflow tiket bertingkat:
  - verifikasi
  - persetujuan
  - disposisi
  - review
  - revisi
  - finalisasi
- Upload lampiran saat pengajuan.
- Upload dokumen hasil oleh petugas seksi.
- Download lampiran dan hasil permohonan.
- Log aktivitas permohonan dan notifikasi.
- Survei/feedback kepuasan layanan.
- Backup manual dan terjadwal ke Google Drive.
- Integrasi notifikasi ke email, WhatsApp, dan webhook n8n.
- API internal berbasis Sanctum.

## Alur Proses Permohonan

Alur utama permohonan data dalam sistem ini:

1. Pemohon mengisi formulir permohonan data.
2. Sistem membuat tiket baru dengan status `sent`.
3. Admin TU memverifikasi permohonan.
4. Pimpinan BPKH memberikan persetujuan awal.
5. Pimpinan PPKH mendisposisikan tiket ke petugas seksi.
6. Petugas seksi menyiapkan data dan mengunggah dokumen hasil.
7. Pimpinan PPKH melakukan review.
8. Pimpinan BPKH melakukan review akhir.
9. Jika perlu, tiket dikembalikan ke seksi untuk revisi.
10. Jika disetujui final, Admin TU menyelesaikan permohonan.
11. Pemohon mengunduh hasil permohonan dari dashboard.

### Status Tiket

| Status | Keterangan |
|---|---|
| `sent` | Permohonan baru dikirim |
| `verified` | Diverifikasi oleh Admin TU |
| `approved` | Disetujui Pimpinan BPKH |
| `assigned` | Didisposisikan ke Seksi |
| `ready` | Data telah disiapkan |
| `under_review_ppkh` | Sedang direview Pimpinan PPKH |
| `under_review_bpkh` | Sedang direview Pimpinan BPKH |
| `revision` | Perlu revisi |
| `final_approved` | Disetujui final |
| `completed` | Permohonan selesai |
| `rejected` | Permohonan ditolak |

## Role dan Hak Akses

Sistem menggunakan role dan permission berbasis Spatie Permission.

| Role | Fungsi Utama |
|---|---|
| `pemohon` | Mengajukan permohonan, memantau status, mengunduh hasil |
| `admin_tu` | Verifikasi awal, finalisasi, akses backup |
| `pimpinan_bpkh` | Persetujuan awal, review akhir, final approve, reject |
| `pimpinan_ppkh` | Disposisi ke seksi, review PPKH, forward ke BPKH, request revisi |
| `seksi` | Menyiapkan data, upload dokumen hasil, tandai data siap |

## Teknologi yang Digunakan

### Backend

- PHP 8.2+
- Laravel 12
- Laravel Sanctum
- Laravel Jetstream
- Spatie Laravel Permission
- Spatie Laravel Activitylog
- Spatie Laravel Backup
- Google API Client
- Flysystem Google Drive

### Frontend

- Vue 3
- Inertia.js
- Vite
- Tailwind CSS
- ApexCharts / Unovis untuk visualisasi
- Shacdn
- lucide icon

### Database dan Infrastruktur

- MySQL
- Queue database driver
- Session database driver
- Scheduler Laravel
- Storage lokal dan Google Drive

## Struktur Project

```text
app/
  Enums/                  # Enum status, assignment, metode, dsb
  Http/Controllers/       # Controller web dan API
  Http/Requests/          # Validasi request
  Models/                 # Model Eloquent
  Notifications/          # Notifikasi aplikasi
  Policies/               # Authorization policy
  Services/               # Business logic utama
config/                   # Konfigurasi aplikasi
database/
  migrations/             # Struktur tabel
  seeders/                # Seeder role, user, data awal
resources/
  js/                     # Frontend Vue + Inertia
  views/                  # Blade view tambahan
routes/
  web.php                 # Web routes
  api.php                 # API internal
tests/                    # Test dengan Pest
