<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/Signin.php'); exit;
}
$conn = require '../config/koneksi.php';

$id            = (int)($_POST['id'] ?? 0);
$nama          = trim($_POST['nama'] ?? '');
$no_pasien     = trim($_POST['no_pasien'] ?? '');
$tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
$jenis_kelamin = $_POST['jenis_kelamin'] ?? 'Perempuan';
$telepon       = trim($_POST['telepon'] ?? '');
$email         = trim($_POST['email'] ?? '');
$alamat        = trim($_POST['alamat'] ?? '');
$catatan       = trim($_POST['catatan_medis'] ?? '');
$status        = $_POST['status'] ?? 'Aktif';

if ($nama === '' || $no_pasien === '') {
    header('Location: ../../pages/admin/dashboard.php?panel=pasien&error='.urlencode('Nama dan No. Pasien wajib diisi.')); exit;
}

$tgl = $tanggal_lahir ?: null;

if ($id > 0) {
    // UPDATE
    $stmt = mysqli_prepare($conn,
        "UPDATE pasien SET nama=?,no_pasien=?,tanggal_lahir=?,jenis_kelamin=?,telepon=?,email=?,alamat=?,catatan_medis=?,status=? WHERE id=?");
    mysqli_stmt_bind_param($stmt,'sssssssssi',
        $nama,$no_pasien,$tgl,$jenis_kelamin,$telepon,$email,$alamat,$catatan,$status,$id);
    $ok = mysqli_stmt_execute($stmt);
    $msg = $ok ? 'Data pasien berhasil diperbarui.' : 'Gagal memperbarui data pasien.';
    $tipe = 'Pasien'; $judul = 'Pasien Diperbarui'; $desk = "$nama diperbarui oleh admin.";
} else {
    // INSERT
    $stmt = mysqli_prepare($conn,
        "INSERT INTO pasien (nama,no_pasien,tanggal_lahir,jenis_kelamin,telepon,email,alamat,catatan_medis,status) VALUES (?,?,?,?,?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt,'sssssssss',
        $nama,$no_pasien,$tgl,$jenis_kelamin,$telepon,$email,$alamat,$catatan,$status);
    $ok = mysqli_stmt_execute($stmt);
    $msg = $ok ? 'Pasien baru berhasil ditambahkan.' : 'Gagal menambahkan pasien.';
    $tipe = 'Pasien'; $judul = 'Pasien Baru'; $desk = "$nama ditambahkan oleh admin.";
}

if ($ok) {
    $uid = (int)$_SESSION['user_id'];
    $log = mysqli_prepare($conn,"INSERT INTO log_aktivitas (user_id,tipe,judul,deskripsi,referensi_tabel) VALUES (?,?,?,?,'pasien')");
    mysqli_stmt_bind_param($log,'isss',$uid,$tipe,$judul,$desk);
    mysqli_stmt_execute($log);
}

$param = $ok ? 'success='.urlencode($msg) : 'error='.urlencode($msg);
header("Location: ../../pages/admin/dashboard.php?panel=pasien&$param");
exit;
?>
