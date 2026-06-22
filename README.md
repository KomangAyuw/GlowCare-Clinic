# GlowCare Clinic

Sistem Informasi Manajemen Klinik Kecantikan Berbasis Web

## Deskripsi
GlowCare Clinic adalah sebuah sistem informasi berbasis website yang dirancang untuk mengelola seluruh aktivitas operasional klinik kecantikan secara digital. Sistem ini memungkinkan pasien untuk melakukan pendaftaran konsultasi secara online, memilih jadwal dokter, serta melihat informasi treatment yang ditawarkan. Dokter dapat mengelola jadwal konsultasi dan mengisi hasil pemeriksaan pasien secara langsung ke dalam sistem. Di sisi lain, pihak admin memiliki kontrol penuh untuk mengelola data pasien, dokter, treatment, jadwal, keuangan kasir, laporan bulanan, serta pengumuman instan.

# Team & Roles
| Nama Anggota | Role | Tanggung Jawab |
|---|---|---|
| I Gde Surya Laksana | Backend Developer | Pembuatan dan pengelolaan database MySQL, query CRUD (pasien, dokter, appointment, treatment), serta logika server-side menggunakan PHP |
| Nurhidayah Maulidia | Frontend Developer | Mendesain tampilan antarmuka pengguna, mengembangkan form interaktif, dan menyusun layout website menggunakan HTML, CSS, dan JavaScript |
| Ni Komang Ayu Sumeitri | Full Integration & Frontend Developer | Menghubungkan frontend dengan backend (PHP–MySQL) serta mengembangkan halaman dashboard utama untuk pasien, dokter, dan admin |

# Menu / Sitemap
```text
GlowCare Clinic
│
├── PENGGUNA PUBLIK (TAMU / PENGUNJUNG)
│   ├── Landing Page (index.php)
│   ├── Tentang Kami (pages/about.php)
│   ├── Dokter Spesialis (pages/spesialis.php)
│   ├── Hubungi Kami (pages/kontak.php)
│   └── Katalog Treatment (pages/treatment/treatment.php)
│       └── Detail & Info Treatment (pages/treatment/detail_treatment.php)
│
├── AUTENTIKASI PENGGUNA
│   ├── Masuk Akun / Sign In (pages/auth/Signin.php)
│   └── Daftar Akun Pasien / Sign Up (pages/auth/SignUp.php)
│
├── PORTAL PASIEN (PASIEN)
│   ├── Dashboard Pasien (pages/user/dashboarduser.php)
│   │   ├── Beranda Status Janji Temu Aktif
│   │   ├── Jadwal Dokter Real-Time
│   │   ├── Form Booking Konsultasi & Pilih Jadwal
│   │   ├── Riwayat Perawatan & Rekam Medis Detail
│   │   └── Kelola Profil Akun
│   └── Konsultasi Chat (pages/user/chat.php)
│       └── Obrolan Interaktif dengan Dokter Terkait
│
├── PORTAL DOKTER (DOKTER)
│   ├── Dashboard Dokter (pages/dokter/dashboardDokter.php)
│   │   ├── Ringkasan Statistik Bulanan
│   │   ├── Kalender Jadwal Praktik Mingguan
│   │   ├── Daftar Antrean Pasien Terjadwal
│   │   ├── Rekam Medis (Input & Edit Catatan Medis)
│   │   ├── Ulasan & Bintang dari Pasien
│   │   └── Kelola Profil Dokter & Ganti Password
│   └── Konsultasi Chat (pages/dokter/chat.php)
│       └── Obrolan Interaktif dengan Pasien
│
└── ADMIN (SUPER ADMIN)
    └── Dashboard Admin (pages/admin/dashboard.php)
        ├── Ringkasan Statistik & Grafik Pendapatan
        ├── Kelola Data Pasien (CRUD)
        ├── Kelola Data Dokter & Kehadiran (CRUD)
        ├── Atur Jadwal Praktik Harian Dokter (CRUD)
        ├── Manajemen Janji Temu & Konfirmasi Booking
        ├── Kelola Katalog Tindakan Treatment (CRUD)
        ├── Daftar Pesan Kontak (Baca, Hapus, & Balas via Email)
        ├── Kirim Broadcast Pengumuman Instan
        ├── Laporan Rekap & Download CSV/PDF
        └── Kelola Catatan Keuangan Kasir & Pengeluaran (CRUD)
```

# Struktur Direktori & File
```text
GlowCare-Clinic/
├── index.php                 # Landing page publik (beranda utama)
├── glowcareclinic.sql        # File backup/dump database MySQL
├── README.md                 # Dokumentasi proyek
│
├── pages/                    # Folder halaman antarmuka pengguna (View)
│   ├── about.php             # Halaman Tentang Kami
│   ├── spesialis.php         # Halaman profil dokter spesialis kecantikan
│   ├── kontak.php            # Halaman Hubungi Kami (form kontak masuk)
│   │
│   ├── auth/                 # Halaman modul autentikasi
│   │   ├── Signin.php        # Form login (pasien, dokter, admin)
│   │   └── SignUp.php        # Form pendaftaran pasien baru
│   │
│   ├── treatment/            # Halaman katalog tindakan kecantikan
│   │   ├── treatment.php     # Katalog seluruh layanan
│   │   └── detail_treatment.php # Informasi detail spesifik treatment
│   │
│   ├── user/                 # Portal interaktif khusus pasien
│   │   ├── dashboarduser.php # Dashboard utama pasien (booking, riwayat, rekam medis)
│   │   └── chat.php          # Halaman konsultasi chat dengan dokter
│   │
│   ├── dokter/               # Portal interaktif khusus dokter
│   │   ├── dashboardDokter.php # Dashboard utama dokter (jadwal praktik, rekam medis)
│   │   └── chat.php          # Halaman konsultasi chat dengan pasien
│   │
│   └── admin/                # Portal interaktif khusus admin
│       └── dashboard.php     # Dashboard Super Admin (manajemen data master, laporan, dll.)
│
├── backend/                  # Berkas logika pemrosesan data (Controller & Model)
│   ├── config/               # Konfigurasi basis data
│   │   └── koneksi.php       # File koneksi database MySQLi
│   │
│   ├── auth/                 # Logika program pendaftaran & masuk sistem
│   │   ├── Regist.php        # Pemrosesan registrasi pasien baru
│   │   ├── log.php           # Pemrosesan login & set session role
│   │   ├── logout.php        # Pengakhiran sesi (logout)
│   │   └── guard_dokter.php  # Pembatasan hak akses dokter
│   │
│   ├── chat/                 # Logika program ruang konsultasi chat
│   │   ├── chat_get.php      # Mengambil pesan obrolan & penandaan sudah dibaca
│   │   └── chat_send.php     # Pengiriman pesan teks & upload gambar
│   │
│   ├── config/               # Berkas konfigurasi basis data
│   │   └── koneksi.php       # File koneksi database MySQL
│   │
│   ├── dokter/               # Logika program portal dokter
│   │   ├── simpan_rekam_medis.php # Menyimpan diagnosis & tindak lanjut rekam medis
│   │   ├── update_profil.php # Perbarui identitas & profil oleh dokter
│   │   └── get_pasien_detail.php # Mengambil info ringkasan medis pasien
│   │
│   ├── kontak/               # Logika penanganan pesan masuk
│   │   └── pesan_kontak.php  # Pengiriman formulir kontak pasien ke database
│   │
│   ├── notifikasi/           # Logika counter pemberitahuan sistem
│   │   └── notif_count.php   # Menghitung notifikasi baru secara dinamis
│   │
│   ├── user/                 # Logika program portal pasien
│   │   ├── simpan_booking.php # Pemrosesan transaksi janji temu baru
│   │   ├── batal_booking.php  # Pembatalan janji temu oleh pasien
│   │   └── simpan_ulasan.php  # Pengiriman feedback & ulasan bintang
│   │
│   ├── admin/                # Logika program portal admin
│   │   ├── simpan_*.php      # Aksi insert/update data (dokter, pasien, treatment, dll.)
│   │   ├── hapus_*.php       # Aksi delete data (dokter, pasien, treatment, dll.)
│   │   ├── kelola_pesan.php  # Proses verifikasi baca & hapus pesan kontak
│   │   └── laporan_download.php # Proses rekap ekspor data format CSV & PDF
│   │
│   └── uploads/              # Direktori penyimpanan media unggahan dinamis
│       ├── dokter/           # File foto profil dokter
│       └── treatment/        # File foto layanan treatment
│
└── asset/                    # Berkas aset statis (frontend global)
    ├── css/                  # File styling (style.css, admin.css, user.css, dokter.css)
    ├── js/                   # File script interaktif (script.js, admin.js, user.js, dokter.js)
    └── img/                  # Folder gambar statis & ikon aset
```

# Bug Logs (Riwayat Perbaikan Bug)

### Bug Log 1 (Nama Dokter Menampilkan Kode Script)

1. **Gejala**:
   Muncul tulisan `<script>console.log('XSS_WORKED')</script>` pada nama dokter di dashboard admin.

2. **Langkah reproduksi**:
   Masukkan kode script pada kolom nama dokter, simpan data, lalu buka dashboard admin.

3. **Hipotesis penyebab**:
   Sistem menyimpan semua data yang dimasukkan pengguna tanpa memeriksa apakah terdapat kode yang tidak seharusnya disimpan sebagai nama dokter (Stored XSS).

4. **Fix (apa yang diubah)**:
   * **File**: [backend/admin/simpan_dokter.php](file:///c:/xampp/htdocs/GlowCare-Clinic/backend/admin/simpan_dokter.php#L9) (Baris 9)
   * Menambahkan pembersihan tag HTML dan Script lewat fungsi `strip_tags()` sebelum nama dokter disimpan:
     ```php
     $nama = trim(strip_tags($_POST['nama'] ?? ''));
     ```

5. **Bukti (Untuk Screenshot)**:
   * Sebelum: Kode script tampil pada nama dokter.
   * Sesudah: Hanya nama dokter bersih (`dr. Anisa Putri, Sp.BP-RE`) yang tersimpan dan ditampilkan.

---

### Bug Log 2 (Gagal Menghapus Pesan Kontak)

1. **Gejala**:
   Muncul pesan error "Aksi tidak dikenali" saat admin menghapus pesan kontak.

2. **Langkah reproduksi**:
   Admin membuka menu pesan kontak, menekan tombol Hapus, lalu mengonfirmasi penghapusan.

3. **Hipotesis penyebab**:
   Sistem tidak menerima informasi bahwa pengguna sedang melakukan proses penghapusan data, sehingga perintah hapus tidak dapat dijalankan (form konfirmasi modal tidak menyertakan parameter `aksi`).

4. **Fix (apa yang diubah)**:
   * **File**: [pages/admin/dashboard.php](file:///c:/xampp/htdocs/GlowCare-Clinic/pages/admin/dashboard.php#L1925) (Baris 1925)
   * Menambahkan input tersembunyi `aksi="hapus"` pada form modal konfirmasi agar dikenali oleh logika backend:
     ```html
     <input type="hidden" name="aksi" value="hapus">
     ```

5. **Bukti (Untuk Screenshot)**:
   * Sebelum: Muncul pesan "Aksi tidak dikenali".
   * Sesudah: Pesan kontak berhasil terhapus secara permanen dari database.

---

### Bug Log 3 (Layanan & Dashboard Tidak Dapat Diakses)

1. **Gejala**:
   Pasien atau dokter gagal mengakses dashboard dan menu layanan, serta sering kembali ke halaman login.

2. **Langkah reproduksi**:
   Login sebagai pasien atau dokter, lalu coba akses dashboard atau halaman layanan.

3. **Hipotesis penyebab**:
   Sistem salah mengenali jenis pengguna yang sedang login karena data role yang dibaca tidak sesuai dengan data yang tersimpan di database (masalah pencocokan string role secara case-sensitive).

4. **Fix (apa yang diubah)**:
   * **File**: [backend/notifikasi/notif_count.php](file:///c:/xampp/htdocs/GlowCare-Clinic/backend/notifikasi/notif_count.php#L13-L17) (Baris 13, 17, dan 49)
   * Memperbaiki pembacaan role dengan mengonversinya menjadi huruf kecil menggunakan `strtolower()` dan memperbarui validasi agar mengenali role `'user'` dan `'pasien'` dengan benar:
     ```php
     $role = strtolower($_SESSION['role'] ?? 'user');
     if ($role === 'user' || $role === 'pasien') { ... }
     ```

5. **Bukti (Untuk Screenshot)**:
   * Sebelum: Pengguna tidak dapat mengakses dashboard atau layanan.
   * Sesudah: Dashboard dan layanan dapat diakses dengan normal.

---

### Bug Log 4 (Chat Tidak Terkirim ke Dokter & Sebaliknya)

1. **Gejala**:
   Pesan chat yang dikirim oleh pasien maupun dokter tidak muncul pada ruang percakapan dan tidak diterima oleh lawan bicara.

2. **Langkah reproduksi**:
   Buka halaman chat konsultasi, ketik pesan atau kirim gambar, lalu tekan tombol Kirim.

3. **Hipotesis penyebab**:
   Sistem masih mengarah ke lokasi file chat yang lama (`../../backend/chat_send.php`) karena letak file backend chat dipindahkan ke dalam folder `backend/chat/` pasca restrukturisasi.

4. **Fix (apa yang diubah)**:
   * **File**: [pages/user/chat.php](file:///c:/xampp/htdocs/GlowCare-Clinic/pages/user/chat.php#L548) (Baris 548) & [pages/dokter/chat.php](file:///c:/xampp/htdocs/GlowCare-Clinic/pages/dokter/chat.php#L392) (Baris 392)
   * Memperbarui alamat file target fetch pada fitur chat agar mengarah ke lokasi folder backend yang baru:
     ```javascript
     fetch('../../backend/chat/chat_send.php', { ... })
     fetch('../../backend/chat/chat_get.php?consultation_id=...', { ... })
     ```

5. **Bukti (Untuk Screenshot)**:
   * Sebelum: Pesan tidak terkirim dan tidak muncul pada ruang chat.
   * Sesudah: Pesan berhasil terkirim dan tampil pada kedua sisi percakapan.

---

### Bug Log 5 (Tombol Sidebar Navigasi Tidak Merespons)

1. **Gejala**:
   Beberapa menu sidebar seperti "Pengumuman" tidak menampilkan halaman yang sesuai saat diklik.

2. **Langkah reproduksi**:
   Login sebagai admin lalu klik menu Pengumuman pada sidebar.

3. **Hipotesis penyebab**:
   Sistem belum memiliki pengaturan pemetaan navigasi di JavaScript yang menghubungkan menu Pengumuman dengan panel halaman yang harus ditampilkan.

4. **Fix (apa yang diubah)**:
   * **File**: [asset/js/admin.js](file:///c:/xampp/htdocs/GlowCare-Clinic/asset/js/admin.js#L1) (Baris 1) & [pages/admin/dashboard.php](file:///c:/xampp/htdocs/GlowCare-Clinic/pages/admin/dashboard.php#L369) (Baris 369)
   * Menambahkan registrasi menu `'pengumuman'` pada variabel `titles` sistem navigasi dashboard admin:
     ```javascript
     const titles = { ..., pengumuman:'Pengumuman', ... };
     ```

5. **Bukti (Untuk Screenshot)**:
   * Sebelum: Halaman tidak berubah saat menu diklik.
   * Sesudah: Halaman Pengumuman berhasil ditampilkan.

---

# AI Tools Used
* **Claude**: Digunakan untuk mendeteksi, mendiagnosis, dan menulis kode perbaikan bug/kesalahan sistem pada logika PHP & JavaScript di backend dan database.
* **Stitch**: Digunakan untuk brainstorming ide visual, skema warna premium (harmonious colors), serta pemetaan layout antarmuka (UI/UX) website agar tampak elegan.

---

## Lisensi
Proyek ini dibuat untuk keperluan akademik. Seluruh hak cipta milik tim pengembang GlowCare Clinic.
