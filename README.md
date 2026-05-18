# 💆‍♀️ GlowCare Clinic

> **Sistem Informasi Manajemen Klinik Kecantikan Berbasis Website**

## Detailed Description

GlowCare Clinic adalah sebuah sistem informasi berbasis website yang dirancang untuk mengelola seluruh aktivitas operasional klinik kecantikan secara digital. Sistem ini dibangun untuk menggantikan proses manual yang sebelumnya digunakan dalam pencatatan data pasien, pengelolaan jadwal dokter, serta administrasi treatment yang masih dilakukan secara konvensional menggunakan dokumen fisik.

Dalam praktik klinik kecantikan tradisional, berbagai permasalahan sering muncul, seperti kesulitan dalam pencarian data pasien karena masih berbentuk arsip manual, risiko terjadinya double booking jadwal konsultasi, lambatnya proses administrasi dan pelayanan pasien, tidak tersedianya akses real-time terhadap informasi treatment dan riwayat perawatan, serta tingginya potensi kesalahan pencatatan rekam medis. Berdasarkan permasalahan tersebut, GlowCare Clinic dikembangkan sebagai solusi digital yang mampu meningkatkan efisiensi, akurasi, dan transparansi layanan klinik.

Sistem ini memungkinkan pasien untuk melakukan pendaftaran konsultasi secara online, memilih jadwal dokter yang tersedia, serta melihat informasi treatment yang ditawarkan tanpa harus datang langsung ke klinik. Selain itu, dokter dapat mengakses jadwal konsultasi dan mengisi hasil pemeriksaan pasien secara langsung ke dalam sistem, sehingga rekam medis tersimpan secara terstruktur dan mudah diakses.

Di sisi lain, pihak admin klinik memiliki kontrol penuh terhadap manajemen data, seperti pengelolaan pasien, dokter, treatment, jadwal, serta laporan aktivitas klinik. Dengan adanya integrasi ini, seluruh proses bisnis klinik menjadi lebih terorganisir dalam satu sistem terpusat.

Secara keseluruhan, GlowCare Clinic bertujuan untuk meningkatkan efisiensi operasional klinik kecantikan, meminimalkan kesalahan akibat proses manual, mengoptimalkan pengelolaan jadwal dan layanan pasien, serta memberikan pengalaman layanan yang lebih modern, cepat, dan mudah diakses. Sistem ini juga selaras dengan perkembangan teknologi digital saat ini, di mana industri kesehatan dan kecantikan mulai bertransformasi menuju sistem berbasis teknologi informasi yang lebih efektif dan fleksibel.

### ✨ Apa yang bisa dilakukan GlowCare Clinic?

| Untuk Pasien | Untuk Dokter | Untuk Admin |
|---|---|---|
| Daftar & booking konsultasi online | Kelola jadwal praktik | Pantau seluruh operasional klinik |
| Lihat jadwal & profil dokter | Akses data pasien sebelum konsultasi | Manajemen dokter, layanan & booking |
| Akses riwayat perawatan & rekam medis | Input catatan medis setelah konsultasi | Lihat laporan kunjungan & pendapatan |

---

## 👥 Tim Pengembang

| Nama | NIM | Tanggung Jawab |
|---|---|---|
| I Gde Surya Laksana | F1D02410051 | Backend Development: pembuatan dan pengelolaan database MySQL, query CRUD (pasien, dokter, appointment, treatment), serta logic server-side menggunakan PHP |
| Nurhidayah Maulidia | F1D02410022 | Frontend Development: pembuatan tampilan website menggunakan HTML, CSS, dan JavaScript, termasuk halaman login, form booking, dan dashboard user/admin |
| Ni Komang Ayu Sumeitri | F1D02410084 | Full Integration & Testing: menghubungkan frontend dengan backend (PHP–MySQL), melakukan testing fitur, debugging error, serta membantu penyempurnaan UI dan sistem |

---

## Aktor / Pengguna Sistem

Sistem GlowCare Clinic melayani tiga aktor utama:

| Aktor | Deskripsi |
|---|---|
| **Pasien** | Pengguna umum yang mendaftar, memesan konsultasi, dan memantau riwayat perawatan |
| **Dokter** | Tenaga medis yang mengelola jadwal, melihat data pasien, dan mengisi rekam medis |
| **Admin** | Pengelola klinik yang mengelola seluruh data sistem, termasuk layanan, dokter, dan laporan |

---

## Fitur & Sitemap

### Pasien
- **Beranda** : Informasi umum klinik, layanan unggulan, dan testimoni
- **Registrasi & Login** : Pendaftaran akun baru dan masuk ke sistem
- **Profil** : Kelola data diri dan informasi akun
- **Layanan** : Daftar dan detail semua treatment kecantikan beserta harga
- **Jadwal Dokter** : Melihat ketersediaan dan jadwal dokter aktif
- **Booking Konsultasi** : Pemilihan dokter, tanggal, dan jam konsultasi
- **Riwayat Booking** : Melihat daftar dan status janji temu sebelumnya
- **Rekam Medis** : Mengakses riwayat diagnosis dan catatan perawatan pribadi

### Dokter
- **Dashboard** : Ringkasan jadwal dan pasien hari ini
- **Jadwal Saya** : Manajemen jadwal praktik harian dan mingguan
- **Daftar Pasien** : Melihat pasien yang terjadwal untuk konsultasi
- **Rekam Medis** : Input dan pembaruan catatan medis setelah konsultasi
- **Riwayat Konsultasi** : Arsip semua sesi konsultasi yang telah dilakukan

### Admin
- **Dashboard** : Statistik klinik: total pasien, booking, pendapatan
- **Manajemen Pasien** : CRUD data seluruh pasien terdaftar
- **Manajemen Dokter** : CRUD data dokter, spesialisasi, dan jadwal
- **Manajemen Layanan** : Kelola daftar treatment beserta deskripsi dan harga
- **Manajemen Booking** : Pantau dan kelola semua janji temu (konfirmasi, tolak, reschedule)
- **Rekam Medis** : Akses dan kelola seluruh rekam medis pasien
- **Laporan** : Ekspor data laporan kunjungan dan pendapatan klinik

---

## Tech Stack

| Kategori | Teknologi |
|---|---|
| **Frontend** | HTML5, CSS, JavaScript |
| **Backend** | PHP |
| **Database** | MySQL |
| **Web Server** | Apache (XAMPP / Laragon) |
| **Version Control** | Git & GitHub |
| **Tools** | Visual Studio Code, phpMyAdmin |

---

## 🗄️ DBMS

### Konfigurasi Database

| Parameter | Nilai |
|---|---|
| **DBMS** | MySQL |
| **Nama Database** | `glowcare_clinic` |
| **Host** | `localhost` |
| **Port** | `3306` |
| **Charset** | `utf8mb4` |
| **Collation** | `utf8mb4_unicode_ci` |

## 📁 Struktur Direktori

```
GlowCare-Clinic/
├── asset/
│   ├── css/
│   ├── js/
│   └── images/
├── backend/
│   ├── admin/
│   ├── kirim_pesan
│   ├── koneksi
│   ├── log
│   ├── logout
│   ├── nav
│   └── Regist
├── pages/
│   ├── admin/
│   ├── auth/
│   ├── dokter/
│   ├── treatment/
│   └── user/
├── index.php
├── jadwal.php
└── README.md
```

---

## 📄 Lisensi

Proyek ini dibuat untuk keperluan akademik. Seluruh hak cipta milik tim pengembang GlowCare Clinic.
