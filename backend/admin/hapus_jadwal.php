<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/Signin.php'); exit;
}
$conn = require '../koneksi.php';

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: ../../pages/admin/dashboard.php?panel=jadwal&error='.urlencode('ID tidak valid.')); exit;
}

$stmt = mysqli_prepare($conn, "DELETE FROM jadwal_dokter WHERE id=?");
mysqli_stmt_bind_param($stmt, 'i', $id);
$ok = mysqli_stmt_execute($stmt);

if ($ok) {
    $uid  = (int)$_SESSION['user_id'];
    $tipe = 'Jadwal'; $judul = 'Jadwal Dihapus'; $desk = "Jadwal ID #$id dihapus oleh admin.";
    $log  = mysqli_prepare($conn, "INSERT INTO log_aktivitas (user_id,tipe,judul,deskripsi,referensi_tabel) VALUES (?,?,?,?,'jadwal_dokter')");
    mysqli_stmt_bind_param($log, 'isss', $uid, $tipe, $judul, $desk);
    mysqli_stmt_execute($log);
    $param = 'success='.urlencode('Jadwal berhasil dihapus.');
} else {
    $param = 'error='.urlencode('Gagal menghapus jadwal.');
}

header("Location: ../../pages/admin/dashboard.php?panel=jadwal&$param");
exit;