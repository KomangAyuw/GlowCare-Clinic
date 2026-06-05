<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/Signin.php'); exit;
}
$conn = require '../koneksi.php';

$sql = "CREATE TABLE IF NOT EXISTS `keuangan` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `tanggal`     DATE NOT NULL,
  `jenis`       ENUM('Pemasukan','Pengeluaran') NOT NULL DEFAULT 'Pemasukan',
  `kategori`    VARCHAR(100) NOT NULL,
  `keterangan`  VARCHAR(255) NOT NULL,
  `jumlah`      DECIMAL(15,2) NOT NULL DEFAULT 0,
  `metode`      VARCHAR(50)  DEFAULT 'Tunai',
  `referensi`   VARCHAR(100) NULL COMMENT 'Nomor bukti/invoice',
  `catatan`     TEXT NULL,
  `dibuat_oleh` INT NULL,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`dibuat_oleh`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (mysqli_query($conn, $sql)) {
    echo '<p style="font-family:sans-serif;color:green;padding:20px">✅ Tabel <strong>keuangan</strong> berhasil dibuat. <a href="../../pages/admin/dashboard.php">Kembali ke Dashboard</a></p>';
} else {
    echo '<p style="font-family:sans-serif;color:red;padding:20px">❌ Gagal: ' . mysqli_error($conn) . '</p>';
}
?>
