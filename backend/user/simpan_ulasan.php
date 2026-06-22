<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../pages/auth/Signin.php');
    exit;
}

$conn = require '../config/koneksi.php';

// Auto-migrasi tabel ulasan jika belum ada
$conn->exec("CREATE TABLE IF NOT EXISTS `ulasan` (
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
$qPasien = $conn->prepare("SELECT id FROM pasien WHERE user_id = :user_id LIMIT 1");
$qPasien->execute(['user_id' => $user_id]);
$pasienRow = $qPasien->fetch();
if (!$pasienRow) {
    header('Location: ../../pages/user/dashboarduser.php?error=' . urlencode('Data pasien tidak ditemukan.'));
    exit;
}
$pasien_id = (int)$pasienRow['id'];

// Simpan ulasan ke tabel ulasan
$stmt = $conn->prepare("INSERT INTO ulasan (dokter_id, pasien_id, rating, komentar) VALUES (:dokter_id, :pasien_id, :rating, :komentar)");
$stmt->execute([
    'dokter_id' => $dokter_id,
    'pasien_id' => $pasien_id,
    'rating'    => $rating_val,
    'komentar'  => $komentar
]);

// Hitung ulang rata-rata rating dari tabel ulasan
$qAvg = $conn->prepare("SELECT AVG(rating) AS avg_rating FROM ulasan WHERE dokter_id = :dokter_id");
$qAvg->execute(['dokter_id' => $dokter_id]);
if ($avgRow = $qAvg->fetch()) {
    $avg = round((float)$avgRow['avg_rating'], 1);
    $stmtUpdateDokter = $conn->prepare("UPDATE dokter SET rating = :rating WHERE id = :dokter_id");
    $stmtUpdateDokter->execute(['rating' => $avg, 'dokter_id' => $dokter_id]);
}

header('Location: ../../pages/user/dashboarduser.php?page=riwayat&success=' . urlencode('Terima kasih! Ulasan Anda berhasil terkirim.'));
exit;