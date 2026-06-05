# Agen Eksekusi (AGENTS.md)

## Rangkuman Kebutuhan (User Requirements Summary)
Pengguna meminta pembuatan aplikasi To-Do List pribadi dengan spesifikasi berikut:
1. Menggunakan Laravel sebagai Frontend dan Backend (Monolith).
2. UI yang intuitif dan enak dipandang (desain premium).
3. Terdapat mode gelap (Dark Mode).
4. Memiliki fungsi CRUD lengkap untuk To-Do.
5. Memiliki fitur History (tugas selesai) dan Backlog (tugas yang belum selesai/akan datang).
6. Terdapat Kalender untuk memetakan To-Do list per hari.
7. Terdapat Grafik/Chart untuk memvisualisasikan data To-Do list.
8. Desain keseluruhan yang menarik dan rapi.

## Langkah Eksekusi (Execution Steps)
Dokumen ini berisi perintah-perintah yang dapat dipanggil untuk mengeksekusi pembangunan aplikasi tahap demi tahap. **Pengguna dapat memberikan perintah kepada saya (Agent) untuk menjalankan langkah-langkah berikut:**

### Langkah 1: Setup Proyek Laravel & Environment
**Perintah ke Agent:** `Agent, jalankan Langkah 1`
*   **Aksi yang akan dilakukan Agent:**
    *   Menginstal Laravel baru menggunakan Composer di direktori ini.
    *   Mengatur database (menggunakan SQLite).
    *   Menginstal framework frontend (Tailwind CSS, Alpine.js) untuk UI dan interaktivitas.

### Langkah 2: Pembuatan Model, Migration, & Controller
**Perintah ke Agent:** `Agent, jalankan Langkah 2`
*   **Aksi yang akan dilakukan Agent:**
    *   Membuat model `Task`.
    *   Membuat migration file dengan kolom yang dibutuhkan (`title`, `description`, `status`, `due_date`, dll).
    *   Membuat `TaskController` dengan metode CRUD lengkap.

### Langkah 3: Desain UI, Dark Mode, & CRUD Dasar (Frontend)
**Perintah ke Agent:** `Agent, jalankan Langkah 3`
*   **Aksi yang akan dilakukan Agent:**
    *   Mengatur layout utama Blade dengan konfigurasi Tailwind Dark Mode.
    *   Membuat halaman Dashboard/Home yang menampilkan daftar Backlog dan History.
    *   Membangun komponen form interaktif untuk Create/Edit/Delete tugas.

### Langkah 4: Implementasi Kalender & Grafik
**Perintah ke Agent:** `Agent, jalankan Langkah 4`
*   **Aksi yang akan dilakukan Agent:**
    *   Mengintegrasikan library Kalender (misal: FullCalendar.js) ke dalam UI.
    *   Mengintegrasikan library Chart (misal: Chart.js) untuk menampilkan visualisasi data.
    *   Menghubungkan data kalender dan grafik secara dinamis dari database.

### Langkah 5: Finalisasi & Polishing Desain
**Perintah ke Agent:** `Agent, jalankan Langkah 5`
*   **Aksi yang akan dilakukan Agent:**
    *   Meninjau ulang semua halaman.
    *   Memperbaiki bug UI, memastikan transisi Dark Mode mulus.
    *   Menjalankan server development untuk memverifikasi fungsionalitas keseluruhan.

---
**Catatan untuk Pengguna:** 
Silakan perintahkan saya dengan format `"Tolong jalankan Langkah 1"` untuk memulai proses inisiasi pengembangan!
