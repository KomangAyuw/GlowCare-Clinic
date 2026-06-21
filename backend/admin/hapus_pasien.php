<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/Signin.php'); exit;
}
$conn = require '../koneksi.php';

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: ../../pages/admin/dashboard.php?panel=pasien&error='.urlencode('ID tidak valid.')); exit;
}

// Ambil nama dulu untuk log
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama FROM pasien WHERE id=$id"));
$nama = $row['nama'] ?? 'Tidak diketahui';

$stmt = mysqli_prepare($conn, "DELETE FROM pasien WHERE id=?");
mysqli_stmt_bind_param($stmt,'i',$id);
$ok = mysqli_stmt_execute($stmt);

if ($ok) {
    $uid = (int)$_SESSION['user_id'];
    $judul = 'Pasien Dihapus';
    $desk  = "$nama dihapus oleh admin.";
    $tipe  = 'Pasien';
    $log = mysqli_prepare($conn,"INSERT INTO log_aktivitas (user_id,tipe,judul,deskripsi,referensi_tabel) VALUES (?,?,?,?,'pasien')");
    mysqli_stmt_bind_param($log,'isss',$uid,$tipe,$judul,$desk);
    mysqli_stmt_execute($log);
    $param = 'success='.urlencode("Pasien $nama berhasil dihapus.");
} else {
    $param = 'error='.urlencode('Gagal menghapus pasien. Mungkin ada data terkait (appointment).');
}

header("Location: ../../pages/admin/dashboard.php?panel=pasien&$param");
exit;
?>