<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/Signin.php'); exit;
}
$conn = require '../config/koneksi.php';

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: ../../pages/admin/dashboard.php?panel=keuangan&error=' . urlencode('ID tidak valid.')); exit;
}

// Ambil info dulu sebelum hapus
$info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT jenis,keterangan,jumlah FROM keuangan WHERE id=$id"));

$stmt = mysqli_prepare($conn, "DELETE FROM keuangan WHERE id=?");
mysqli_stmt_bind_param($stmt, 'i', $id);
$ok = mysqli_stmt_execute($stmt);

if ($ok && $info) {
    $uid  = (int)$_SESSION['user_id'];
    $desk = "Transaksi '{$info['jenis']} - {$info['keterangan']}' (Rp " . number_format($info['jumlah'], 0, ',', '.') . ") dihapus.";
    $log  = mysqli_prepare($conn, "INSERT INTO log_aktivitas (user_id,tipe,judul,deskripsi,referensi_tabel) VALUES (?,'Keuangan','Transaksi Dihapus',?,'keuangan')");
    mysqli_stmt_bind_param($log, 'is', $uid, $desk);
    mysqli_stmt_execute($log);
    header('Location: ../../pages/admin/dashboard.php?panel=keuangan&success=' . urlencode('Transaksi berhasil dihapus.'));
} else {
    header('Location: ../../pages/admin/dashboard.php?panel=keuangan&error=' . urlencode('Gagal menghapus transaksi.'));
}
exit;
?>
