<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../pages/auth/Signin.php');
    exit;
}

$conn = require '../config/koneksi.php';

// Auto-migrasi tabel ulasan jika belum ada
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `ulasan` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `dokter_id` INT NOT NULL,
  `pasien_id` INT NOT NULL,
  `appointment_id` INT NULL,
  `rating` TINYINT NOT NULL DEFAULT 5,
  `komentar` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`dokter_id`) REFERENCES `dokter` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`pasien_id`) REFERENCES `pasien` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$user_id    = (int)$_SESSION['user_id'];
$dokter_id  = (int)($_POST['dokter_id'] ?? 0);
$rating_val = (int)($_POST['rating'] ?? 5);
$komentar   = trim($_POST['ulasan'] ?? '');

if ($dokter_id <= 0) {
    header('Location: ../../pages/user/dashboarduser.php?error=' . urlencode('Dokter tidak valid.'));
    exit;
}

// Batasi rating 1-5
$rating_val = min(5, max(1, $rating_val));

// Ambil pasien_id dari user_id
$qPasien = mysqli_query($conn, "SELECT id FROM pasien WHERE user_id = $user_id LIMIT 1");
if (!$qPasien || mysqli_num_rows($qPasien) === 0) {
    header('Location: ../../pages/user/dashboarduser.php?error=' . urlencode('Data pasien tidak ditemukan.'));
    exit;
}
$pasien_id = (int)mysqli_fetch_assoc($qPasien)['id'];

// Simpan ulasan ke tabel ulasan
$stmt = mysqli_prepare($conn, "INSERT INTO ulasan (dokter_id, pasien_id, rating, komentar) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, 'iiis', $dokter_id, $pasien_id, $rating_val, $komentar);
mysqli_stmt_execute($stmt);

// Hitung ulang rata-rata rating dari tabel ulasan
$qAvg = mysqli_query($conn, "SELECT AVG(rating) AS avg_rating FROM ulasan WHERE dokter_id = $dokter_id");
if ($qAvg) {
    $avg = round((float)mysqli_fetch_assoc($qAvg)['avg_rating'], 1);
    mysqli_query($conn, "UPDATE dokter SET rating = $avg WHERE id = $dokter_id");
}

header('Location: ../../pages/user/dashboarduser.php?page=riwayat&success=' . urlencode('Terima kasih! Ulasan Anda berhasil terkirim.'));
exit;