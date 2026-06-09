# Agen Eksekusi (AGENTS.md)

## Rangkuman Kebutuhan & Status Saat Ini (User Requirements & Current Status)
Pengguna meminta pembuatan aplikasi To-Do List pribadi. Berikut adalah spesifikasi dan **status pengerjaan saat ini**:

1. **[SELESAI]** Menggunakan Laravel sebagai Frontend dan Backend (Monolith).
2. **[SELESAI]** UI yang intuitif dan enak dipandang (desain premium menggunakan Tailwind CSS & Alpine.js).
3. **[SELESAI]** Terdapat mode gelap (Dark Mode).
4. **[SELESAI]** Memiliki fungsi CRUD lengkap untuk To-Do.
5. **[SELESAI]** Memiliki fitur History (tugas selesai) dan Backlog (tugas yang belum selesai/akan datang).
6. **[SELESAI]** Sistem Autentikasi Pengguna (Login, Register) beserta verifikasi Email menggunakan SMTP Sumopod.
7. **[SELESAI]** Deployment CI/CD via GitHub Actions ke Server.
8. **[SELESAI]** Setup Docker (menggunakan FrankenPHP & Laravel Octane) untuk performa tingkat tinggi.
9. **[SELESAI]** Terdapat Kalender untuk memetakan To-Do list per hari.
10. **[SELESAI]** Terdapat Grafik/Chart untuk memvisualisasikan data To-Do list.
11. **[MENUNGGU]** Fitur Export to Excel berdasarkan filter Tanggal dan Status.

---

## Langkah Eksekusi (Execution Steps)
Dokumen ini berisi perintah-perintah yang dapat dipanggil untuk mengeksekusi pembangunan aplikasi tahap demi tahap. **Pengguna dapat memberikan perintah kepada saya (Agent) untuk menjalankan langkah-langkah berikut:**

### Langkah 1: Setup Proyek Laravel & Lingkungan Tingkat Lanjut (Selesai ✅)
*   Menginstal Laravel.
*   Mengubah database dari SQLite ke PostgreSQL (Supabase).
*   Setup Docker (FrankenPHP + Octane) dan CI/CD pipeline (GitHub Actions).

### Langkah 2: Autentikasi & Database Migration (Selesai ✅)
*   Membuat model `User` dan `Task`.
*   Sistem registrasi dan verifikasi email.
*   Membuat `TaskController` dengan metode CRUD lengkap.

### Langkah 3: Desain UI, Dark Mode, & Dashboard (Selesai ✅)
*   Mengatur layout utama Blade dengan Tailwind Dark Mode.
*   Membuat halaman Dashboard (Backlog dan History).
*   Menambahkan informasi user (Nama, Email, dan Avatar Acak dari DiceBear) di header.

### Langkah 4: Implementasi Kalender & Grafik (Selesai ✅)
*   Mengintegrasikan library Kalender (FullCalendar.js) ke dalam UI.
*   Mengintegrasikan library Chart (Chart.js) untuk menampilkan visualisasi data statistik To-Do.
*   Menghubungkan data kalender dan grafik secara dinamis dari database.

### Langkah 5: Finalisasi & Polishing Desain (Selesai ✅)
*   Meninjau ulang semua halaman.
*   Memperbaiki bug UI, memastikan transisi Dark Mode mulus.
*   Menyempurnakan fungsionalitas keseluruhan aplikasi (Refactoring sistem Auth layout).

### Langkah 6: Fitur Export to Excel & JSON (Selesai ✅)
*   Menginstal *library* Maatwebsite Excel.
*   Membuat *class* Export (TaskExport) khusus.
*   Menambahkan modal filter (Range Tanggal & Status) di UI Dashboard.
*   Membuat fungsi dan *route* untuk memproses *download* file Excel dan JSON.
*   **[Tambahan]** Fitur *Data Validation (Dropdown)* otomatis pada kolom Status Excel.

### Langkah 7: Peningkatan UX pada Halaman Autentikasi (Selesai ✅)
*   Menambahkan kursor *pointer* saat di-*hover* pada tombol Sign In dan Sign Up.
*   Menambahkan efek visual pemuatan (*loading state*) pada tombol saat data di-*submit*.

---
**Catatan untuk Pengguna:** 
Selamat! Hampir semua langkah utama dalam *roadmap* pengembangan aplikasi To-Do List ini telah selesai dieksekusi. Aplikasi Anda kini siap untuk digunakan secara penuh dengan performa dan desain maksimal!
