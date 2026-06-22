<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/Signin.php'); exit;
}
$conn = require '../config/koneksi.php';

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: ../../pages/admin/dashboard.php?panel=dokter&error='.urlencode('ID tidak valid.')); exit;
}

$row  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama FROM dokter WHERE id=$id"));
$nama = $row['nama'] ?? 'Tidak diketahui';

$stmt = mysqli_prepare($conn, "DELETE FROM dokter WHERE id=?");
mysqli_stmt_bind_param($stmt, 'i', $id);
$ok = mysqli_stmt_execute($stmt);

if ($ok) {
    $uid  = (int)$_SESSION['user_id'];
    $tipe = 'Dokter';
    $judul = 'Dokter Dihapus';
    $desk  = "$nama dihapus oleh admin.";
    $log  = mysqli_prepare($conn, "INSERT INTO log_aktivitas (user_id,tipe,judul,deskripsi,referensi_tabel) VALUES (?,?,?,?,'dokter')");
    mysqli_stmt_bind_param($log, 'isss', $uid, $tipe, $judul, $desk);
    mysqli_stmt_execute($log);
    $param = 'success='.urlencode("Dokter $nama berhasil dihapus.");
} else {
    $param = 'error='.urlencode('Gagal menghapus dokter. Mungkin ada jadwal atau appointment terkait.');
}

header("Location: ../../pages/admin/dashboard.php?panel=dokter&$param");
exit;
