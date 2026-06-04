-- GlowCare Clinic Database Initialization Script
-- Database Name: glowcareclinic

CREATE DATABASE IF NOT EXISTS `glowcareclinic` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `glowcareclinic`;

-- Drop tables in order of dependencies
DROP TABLE IF EXISTS `pembayaran`;
DROP TABLE IF EXISTS `rekam_medis`;
DROP TABLE IF EXISTS `log_aktivitas`;
DROP TABLE IF EXISTS `pesan_kontak`;
DROP TABLE IF EXISTS `jadwal`;
DROP TABLE IF EXISTS `appointment`;
DROP TABLE IF EXISTS `jadwal_dokter`;
DROP TABLE IF EXISTS `treatment`;
DROP TABLE IF EXISTS `pasien`;
DROP TABLE IF EXISTS `dokter`;
DROP TABLE IF EXISTS `users`;

-- 1. users table
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(50) NOT NULL DEFAULT 'user',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. dokter table
CREATE TABLE `dokter` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `nama` VARCHAR(100) NOT NULL,
  `nama_lengkap` VARCHAR(100) NULL,
  `gelar` VARCHAR(50) NULL,
  `no_str` VARCHAR(100) NULL,
  `no_sip` VARCHAR(100) NULL,
  `spesialisasi` VARCHAR(100) NULL,
  `telepon` VARCHAR(50) NULL,
  `no_telp` VARCHAR(50) NULL,
  `email` VARCHAR(255) NULL,
  `pengalaman` INT DEFAULT 0,
  `tahun_pengalaman` INT DEFAULT 0,
  `rating` DECIMAL(3,1) DEFAULT 5.0,
  `status` VARCHAR(50) DEFAULT 'Aktif',
  `bio` TEXT NULL,
  `alamat` TEXT NULL,
  `foto` VARCHAR(255) NULL,
  `total_pasien` INT DEFAULT 0,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. pasien table
CREATE TABLE `pasien` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `nama` VARCHAR(100) NOT NULL,
  `no_pasien` VARCHAR(50) NOT NULL,
  `no_rekam` VARCHAR(50) NULL,
  `tanggal_lahir` DATE NULL,
  `usia` INT NULL,
  `jenis_kelamin` VARCHAR(20) NULL,
  `telepon` VARCHAR(50) NULL,
  `no_telp` VARCHAR(50) NULL,
  `email` VARCHAR(255) NULL,
  `alamat` TEXT NULL,
  `catatan_medis` TEXT NULL,
  `status` VARCHAR(50) DEFAULT 'Aktif',
  `total_kunjungan` INT DEFAULT 0,
  `keluhan` TEXT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. treatment table
CREATE TABLE `treatment` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL,
  `kategori` VARCHAR(50) NULL,
  `deskripsi` TEXT NULL,
  `deskripsi_panjang` TEXT NULL,
  `gambar_url` VARCHAR(255) NULL,
  `gambar_file` VARCHAR(255) NULL,
  `link_halaman` VARCHAR(255) NULL,
  `urutan` INT DEFAULT 1,
  `status` VARCHAR(50) DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. jadwal_dokter table
CREATE TABLE `jadwal_dokter` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `dokter_id` INT NOT NULL,
  `hari` VARCHAR(20) NOT NULL,
  `jam_mulai` TIME NOT NULL,
  `jam_selesai` TIME NOT NULL,
  `max_pasien` INT DEFAULT 8,
  `treatment_id` INT NULL,
  `status` VARCHAR(50) DEFAULT 'Aktif',
  FOREIGN KEY (`dokter_id`) REFERENCES `dokter` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`treatment_id`) REFERENCES `treatment` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. appointment table
CREATE TABLE `appointment` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `pasien_id` INT NOT NULL,
  `dokter_id` INT NOT NULL,
  `treatment_id` INT NULL,
  `tanggal` DATE NOT NULL,
  `jam` TIME NOT NULL,
  `status` VARCHAR(50) DEFAULT 'Terjadwal',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`pasien_id`) REFERENCES `pasien` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`dokter_id`) REFERENCES `dokter` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`treatment_id`) REFERENCES `treatment` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. jadwal table
CREATE TABLE `jadwal` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `pasien_id` INT NOT NULL,
  `dokter_id` INT NOT NULL,
  `tanggal` DATE NOT NULL,
  `jam_mulai` TIME NOT NULL,
  `jam_selesai` TIME NULL,
  `treatment` VARCHAR(100) NULL,
  `ruangan` VARCHAR(50) DEFAULT 'Ruang A-1',
  `durasi` VARCHAR(50) DEFAULT '60 menit',
  `status` VARCHAR(50) DEFAULT 'Terjadwal',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`pasien_id`) REFERENCES `pasien` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`dokter_id`) REFERENCES `dokter` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. pembayaran table
CREATE TABLE `pembayaran` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `appointment_id` INT NOT NULL,
  `jumlah` DECIMAL(12,2) NOT NULL,
  `status` VARCHAR(50) DEFAULT 'Lunas',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`appointment_id`) REFERENCES `appointment` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. rekam_medis table
CREATE TABLE `rekam_medis` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `pasien_id` INT NOT NULL,
  `dokter_id` INT NOT NULL,
  `tanggal` DATE NOT NULL,
  `treatment` VARCHAR(100) NULL,
  `anamnesis` TEXT NULL,
  `pemeriksaan` TEXT NULL,
  `tindak_lanjut` TEXT NULL,
  `status` VARCHAR(50) DEFAULT 'Selesai',
  `jadwal_followup` DATE NULL,
  `ruangan` VARCHAR(50) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`pasien_id`) REFERENCES `pasien` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`dokter_id`) REFERENCES `dokter` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. log_aktivitas table
CREATE TABLE `log_aktivitas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `tipe` VARCHAR(50) NULL,
  `judul` VARCHAR(255) NOT NULL,
  `deskripsi` TEXT NULL,
  `referensi_tabel` VARCHAR(100) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. pesan_kontak table
CREATE TABLE `pesan_kontak` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL,
  `telepon` VARCHAR(50) NULL,
  `email` VARCHAR(255) NULL,
  `pesan` TEXT NOT NULL,
  `sudah_baca` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ──────────────────────────────────────────────────────────
-- SEED DATA (DATA AWAL UNTUK PENGUJIAN)
-- ──────────────────────────────────────────────────────────

-- Users Seed
-- admin123  -> $2y$10$xSwt.GZ3OaRbphZB0pzZg.fPKlPoGdGbOYHYIm7woV9ZaXGvpbq/m
-- doctor123 -> $2y$10$SbRNo0ZZlM7hvGjgwqi.qefw5XlHRTA4A06G5h7V.x6zPefsvSaZG
-- patient123-> $2y$10$sOe7plAOPdKv25XkK18GV.K0Eg8QliJ4p3JX9LMIORuJ3GeikuUA.
INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`) VALUES
(1, 'SuperAdmin', 'admin@glowcareclinic.com', '$2y$10$xSwt.GZ3OaRbphZB0pzZg.fPKlPoGdGbOYHYIm7woV9ZaXGvpbq/m', 'admin'),
(2, 'dr. Anisa Putri', 'anisa@glowcare.com', '$2y$10$SbRNo0ZZlM7hvGjgwqi.qefw5XlHRTA4A06G5h7V.x6zPefsvSaZG', 'dokter'),
(3, 'dr. Marina Crystine', 'marina@glowcare.com', '$2y$10$SbRNo0ZZlM7hvGjgwqi.qefw5XlHRTA4A06G5h7V.x6zPefsvSaZG', 'dokter'),
(4, 'dr. Michael Chen', 'michael@glowcare.com', '$2y$10$SbRNo0ZZlM7hvGjgwqi.qefw5XlHRTA4A06G5h7V.x6zPefsvSaZG', 'dokter'),
(5, 'Siti Rahayu', 'siti@gmail.com', '$2y$10$sOe7plAOPdKv25XkK18GV.K0Eg8QliJ4p3JX9LMIORuJ3GeikuUA.', 'user');

-- Dokter Seed
INSERT INTO `dokter` (`id`, `user_id`, `nama`, `nama_lengkap`, `gelar`, `no_str`, `no_sip`, `spesialisasi`, `telepon`, `no_telp`, `email`, `pengalaman`, `tahun_pengalaman`, `rating`, `status`, `bio`, `alamat`, `foto`, `total_pasien`) VALUES
(1, 2, 'dr. Anisa Putri', 'Anisa Putri', 'dr. Sp.BP-RE', 'STR-BP-898213', 'SIP-BP-00122', 'Plastic Surgeon', '+62 812 3456 7890', '+62 812 3456 7890', 'anisa@glowcare.com', 10, 10, 5.0, 'Aktif', 'Spesialis bedah plastik rekonstruksi wajah terkemuka dengan keahlian khusus pada SMAS Facelift dan Blepharoplasty.', 'Jl. Kecantikan No. 12, Mataram', 'https://images.unsplash.com/photo-1651008376811-b90baee60c1f?auto=format&fit=crop&w=400&q=80', 12),
(2, 3, 'dr. Marina Crystine', 'Marina Crystine', 'dr. M.Biomed (AAM)', 'STR-AM-767812', 'SIP-AM-00125', 'Aesthetic Physician', '+62 812 3456 7891', '+62 812 3456 7891', 'marina@glowcare.com', 8, 8, 5.0, 'Aktif', 'Dokter estetika bersertifikat internasional dengan fokus perawatan dermatologi non-invasif, thread lifts, dan coolsculpting.', 'Jl. Kecantikan No. 12, Mataram', 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=400&q=80', 5),
(3, 4, 'dr. Michael Chen', 'Michael Chen', 'dr. Sp.KK', 'STR-KK-121289', 'SIP-KK-00130', 'Dermatologist', '+62 812 3456 7892', '+62 812 3456 7892', 'michael@glowcare.com', 12, 12, 5.0, 'Aktif', 'Spesialis Kulit & Kelamin senior berpengalaman luas dalam terapi dermatologi berbasis laser dan penanganan hiperpigmentasi.', 'Jl. Kecantikan No. 12, Mataram', 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=400&q=80', 8);

-- Pasien Seed
INSERT INTO `pasien` (`id`, `user_id`, `nama`, `no_pasien`, `no_rekam`, `tanggal_lahir`, `usia`, `jenis_kelamin`, `telepon`, `no_telp`, `email`, `alamat`, `catatan_medis`, `status`, `total_kunjungan`) VALUES
(1, 5, 'Siti Rahayu', 'P-0041', 'RM-0041', '1998-05-12', 28, 'Perempuan', '+62 812 1234 5678', '+62 812 1234 5678', 'siti@gmail.com', 'Jl. Mawar No. 4, Ampenan, Mataram', 'Alergi penisilin ringan.', 'Aktif', 12),
(2, NULL, 'Budi Santoso', 'P-0042', 'RM-0042', '1985-09-24', 41, 'Laki-laki', '+62 813 9876 5432', '+62 813 9876 5432', 'budi@yahoo.com', 'Jl. Selaparang No. 18, Mataram', 'Hipertensi terkontrol.', 'Aktif', 3);

-- Treatment Seed
INSERT INTO `treatment` (`id`, `nama`, `kategori`, `deskripsi`, `deskripsi_panjang`, `gambar_url`, `gambar_file`, `link_halaman`, `urutan`, `status`) VALUES
(1, 'Facelift Procedures', 'Surgical', 'Teknik bedah canggih untuk memulihkan kontur wajah yang tampak muda.', 'Facelift merupakan teknik bedah plastik wajah eksklusif yang menarik kembali lapisan SMAS wajah secara presisi untuk memulihkan penampilan muda Anda.', 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?w=700&auto=format&fit=crop&q=80', NULL, 'pages/treatment/facelift.php', 1, 'Aktif'),
(2, 'Botox & Fillers', 'Injectable', 'Perawatan suntik yang disetujui FDA untuk menghaluskan kerutan wajah secara alami.', 'Suntikan Botox wajah aman yang secara instan merelaksasi otot wajah, menghaluskan kerutan dahi, kaki gagak, serta mengembalikan volume pipi.', 'https://images.unsplash.com/photo-1512290923902-8a9f81dc236c?w=700&auto=format&fit=crop&q=80', NULL, 'pages/treatment/botox.php', 2, 'Aktif'),
(3, 'Laser Treatments', 'Technology', 'Teknologi laser untuk mengatasi pigmentasi, bekas jerawat, dan tanda penuaan.', 'Menggunakan teknologi Laser Nd:YAG terbaik untuk peremajaan kolagen, meratakan warna kulit wajah, dan menyembuhkan jaringan parut bekas jerawat.', 'https://images.unsplash.com/photo-1515377905703-c4788e51af15?w=700&auto=format&fit=crop&q=80', NULL, 'pages/treatment/laser.php', 3, 'Aktif'),
(4, 'Body Contouring', 'Contouring', 'Perawatan khusus untuk membentuk dan merampingkan tubuh dengan presisi.', 'CoolSculpting non-bedah untuk membekukan sel lemak membandel di area perut, paha, dan lengan tanpa rasa sakit.', 'https://images.unsplash.com/photo-1607619056574-7b8d3ee536b2?w=700&auto=format&fit=crop&q=80', NULL, 'pages/treatment/contouring.php', 4, 'Aktif');

-- Jadwal Dokter (Template Mingguan) Seed
INSERT INTO `jadwal_dokter` (`id`, `dokter_id`, `hari`, `jam_mulai`, `jam_selesai`, `max_pasien`, `treatment_id`, `status`) VALUES
(1, 1, 'Senin', '09:00:00', '14:00:00', 8, 1, 'Aktif'),
(2, 1, 'Selasa', '09:00:00', '14:00:00', 8, 1, 'Aktif'),
(3, 1, 'Rabu', '09:00:00', '14:00:00', 8, 1, 'Aktif'),
(4, 1, 'Kamis', '13:00:00', '18:00:00', 8, 1, 'Aktif'),
(5, 1, 'Jumat', '13:00:00', '18:00:00', 8, 1, 'Aktif'),
(6, 2, 'Senin', '10:00:00', '16:00:00', 8, 2, 'Aktif'),
(7, 2, 'Selasa', '10:00:00', '16:00:00', 8, 2, 'Aktif'),
(8, 2, 'Rabu', '10:00:00', '16:00:00', 8, 2, 'Aktif'),
(9, 2, 'Kamis', '10:00:00', '16:00:00', 8, 2, 'Aktif'),
(10, 2, 'Jumat', '10:00:00', '16:00:00', 8, 2, 'Aktif'),
(11, 2, 'Sabtu', '09:00:00', '13:00:00', 5, 2, 'Aktif'),
(12, 3, 'Senin', '13:00:00', '19:00:00', 8, 3, 'Aktif'),
(13, 3, 'Selasa', '13:00:00', '19:00:00', 8, 3, 'Aktif'),
(14, 3, 'Rabu', '13:00:00', '19:00:00', 8, 3, 'Aktif'),
(15, 3, 'Kamis', '13:00:00', '19:00:00', 8, 3, 'Aktif'),
(16, 3, 'Jumat', '13:00:00', '19:00:00', 8, 3, 'Aktif'),
(17, 3, 'Sabtu', '10:00:00', '15:00:00', 6, 3, 'Aktif');

-- Seeding Appointment, Jadwal Hari Ini, Rekam Medis & Pembayaran (Untuk visual dashboard yang cantik)
INSERT INTO `appointment` (`id`, `pasien_id`, `dokter_id`, `treatment_id`, `tanggal`, `jam`, `status`) VALUES
(1, 1, 1, 1, CURDATE(), '09:00:00', 'Terjadwal'),
(2, 2, 1, 1, CURDATE(), '13:00:00', 'Selesai'),
(3, 1, 1, 1, DATE_SUB(CURDATE(), INTERVAL 22 DAY), '09:00:00', 'Selesai'),
(4, 1, 1, 2, DATE_SUB(CURDATE(), INTERVAL 52 DAY), '10:00:00', 'Selesai'),
(5, 1, 3, 3, DATE_SUB(CURDATE(), INTERVAL 73 DAY), '13:00:00', 'Selesai');

-- Jadwal Dokter Seed (Tabel jadwal dokter praktis)
INSERT INTO `jadwal` (`id`, `pasien_id`, `dokter_id`, `tanggal`, `jam_mulai`, `jam_selesai`, `treatment`, `ruangan`, `durasi`, `status`) VALUES
(1, 1, 1, CURDATE(), '09:00:00', '10:00:00', 'Facelift Consultation', 'Ruang A-1', '60 menit', 'Terjadwal'),
(2, 2, 1, CURDATE(), '13:00:00', '14:00:00', 'Facelift Procedures', 'Ruang B-2', '60 menit', 'Selesai'),
(3, 1, 1, DATE_SUB(CURDATE(), INTERVAL 22 DAY), '09:00:00', '10:00:00', 'Facelift Consultation', 'Ruang A-1', '60 menit', 'Selesai'),
(4, 1, 1, DATE_SUB(CURDATE(), INTERVAL 52 DAY), '10:00:00', '10:30:00', 'Botox Forehead', 'Ruang A-1', '30 menit', 'Selesai'),
(5, 1, 3, DATE_SUB(CURDATE(), INTERVAL 73 DAY), '13:00:00', '13:45:00', 'Laser Treatment', 'Ruang C-1', '45px', 'Selesai');

-- Pembayaran Seed (Laporan Bulanan Admin)
INSERT INTO `pembayaran` (`id`, `appointment_id`, `jumlah`, `status`, `created_at`) VALUES
(1, 1, 500000.00, 'Belum Lunas', CURRENT_TIMESTAMP),
(2, 2, 25000000.00, 'Lunas', CURRENT_TIMESTAMP),
(3, 3, 500000.00, 'Lunas', DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 22 DAY)),
(4, 4, 3500000.00, 'Lunas', DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 52 DAY)),
(5, 5, 2000000.00, 'Lunas', DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 73 DAY));

-- Rekam Medis Seed
INSERT INTO `rekam_medis` (`id`, `pasien_id`, `dokter_id`, `tanggal`, `treatment`, `anamnesis`, `pemeriksaan`, `tindak_lanjut`, `status`, `jadwal_followup`, `ruangan`) VALUES
(1, 2, 1, CURDATE(), 'Facelift Procedures', 'Pasien mengeluhkan kulit leher dan rahang kendur (jowls) tingkat sedang.', 'Dilakukan tindakan traditional facelift. Kulit ditarik dan SMAS dikencangkan dengan hasil simetris sempurna.', 'Follow-up dalam 7 hari untuk pelepasan jahitan pasca operasi.', 'Selesai', DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'Ruang B-2'),
(2, 1, 1, DATE_SUB(CURDATE(), INTERVAL 22 DAY), 'Facelift Consultation', 'Pasien menanyakan opsi prosedur mini facelift untuk menyamarkan kerutan dalam di garis marionette.', 'Kondisi kulit sangat baik, elastisitas cukup tinggi. Direkomendasikan mini facelift atau thread lift.', 'Pasien menjadwalkan konsultasi pre-operasi lebih lanjut.', 'Selesai', CURDATE(), 'Ruang A-1'),
(3, 1, 1, DATE_SUB(CURDATE(), INTERVAL 52 DAY), 'Botox Forehead', 'Pasien ingin mengurangi garis kerutan dinamis di area dahi.', 'Injeksi Allergan Botox sebanyak 20 unit di area frontalis dahi.', 'Hindari berbaring selama 4 jam pasca injeksi.', 'Selesai', NULL, 'Ruang A-1'),
(4, 1, 3, DATE_SUB(CURDATE(), INTERVAL 73 DAY), 'Laser Treatment', 'Pasien memiliki hiperpigmentasi (melasma) ringan di pipi kiri.', 'Terapi Nd:YAG Laser Rejuvenation dengan fluks sedang.', 'Gunakan sunscreen SPF 50+ secara berkala.', 'Selesai', NULL, 'Ruang C-1');

-- Log Aktivitas Seed
INSERT INTO `log_aktivitas` (`id`, `user_id`, `tipe`, `judul`, `deskripsi`, `referensi_tabel`) VALUES
(1, 1, 'Pasien', 'Pasien Baru', 'Siti Rahayu didaftarkan oleh sistem.', 'pasien'),
(2, 1, 'Dokter', 'Dokter Baru', 'dr. Anisa Putri ditambahkan ke sistem.', 'dokter'),
(3, 2, 'Rekam Medis', 'Rekam Medis Selesai', 'Rekam medis Budi Santoso ditambahkan oleh dr. Anisa.', 'rekam_medis');

-- Pesan Kontak Seed
INSERT INTO `pesan_kontak` (`id`, `nama`, `telepon`, `email`, `pesan`, `sudah_baca`) VALUES
(1, 'Lalu Ahmad', '+62 878 6543 2100', 'ahmad.lalu@gmail.com', 'Halo GlowCare, apakah ada promo paket facelift untuk menyambut hari raya bulan depan? Terima kasih.', 0),
(2, 'Dewi Lestari', '+62 819 1122 3344', 'dewi.lestari@gmail.com', 'Ingin menanyakan ketersediaan dokter Marina untuk hari Sabtu sore. Terima kasih.', 1);
