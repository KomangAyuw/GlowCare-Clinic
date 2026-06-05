<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/Signin.php'); exit;
}
$conn = require '../koneksi.php';

$id               = (int)($_POST['id'] ?? 0);
$nama             = trim($_POST['nama'] ?? '');
$kategori         = $_POST['kategori'] ?? 'Other';
$deskripsi        = trim($_POST['deskripsi'] ?? '');
$deskripsi_panjang= trim($_POST['deskripsi_panjang'] ?? '');
$gambar_url       = trim($_POST['gambar_url'] ?? '');
$link_halaman     = trim($_POST['link_halaman'] ?? '');
$durasi           = trim($_POST['durasi'] ?? '60 Menit');
$urutan           = (int)($_POST['urutan'] ?? 99);
$status           = $_POST['status'] ?? 'Aktif';

if ($nama === '') {
    header('Location: ../../pages/admin/dashboard.php?error='.urlencode('Nama treatment wajib diisi.')); exit;
}

if ($id > 0) {
    $stmt = mysqli_prepare($conn,
        "UPDATE treatment SET nama=?,kategori=?,deskripsi=?,deskripsi_panjang=?,gambar_url=?,link_halaman=?,urutan=?,status=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'sssssssii',
        $nama,$kategori,$deskripsi,$deskripsi_panjang,$gambar_url,$link_halaman,$urutan,$status,$id);
    // fix: i for urutan, s for status, i for id
    mysqli_stmt_close($stmt);
    $stmt = mysqli_prepare($conn,
        "UPDATE treatment SET nama=?,kategori=?,durasi=?,deskripsi=?,deskripsi_panjang=?,gambar_url=?,link_halaman=?,urutan=?,status=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'sssssssisi',
        $nama,$kategori,$durasi,$deskripsi,$deskripsi_panjang,$gambar_url,$link_halaman,$urutan,$status,$id);
    $ok  = mysqli_stmt_execute($stmt);
    $msg = $ok ? 'Treatment berhasil diperbarui.' : mysqli_error($conn);
    $judul = 'Treatment Diperbarui'; $desk = "$nama diperbarui.";
} else {
    $stmt = mysqli_prepare($conn,
        "INSERT INTO treatment (nama,kategori,durasi,deskripsi,deskripsi_panjang,gambar_url,link_halaman,urutan,status) VALUES (?,?,?,?,?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, 'sssssssis',
        $nama,$kategori,$durasi,$deskripsi,$deskripsi_panjang,$gambar_url,$link_halaman,$urutan,$status);
    $ok  = mysqli_stmt_execute($stmt);
    $msg = $ok ? 'Treatment baru berhasil ditambahkan.' : mysqli_error($conn);
    $judul = 'Treatment Baru'; $desk = "$nama ditambahkan.";
}

if ($ok) {
    $uid  = (int)$_SESSION['user_id'];
    $tipe = 'Treatment';
    $log  = mysqli_prepare($conn, "INSERT INTO log_aktivitas (user_id,tipe,judul,deskripsi,referensi_tabel) VALUES (?,?,?,?,'treatment')");
    mysqli_stmt_bind_param($log, 'isss', $uid, $tipe, $judul, $desk);
    mysqli_stmt_execute($log);
}

$param = $ok ? 'success='.urlencode($msg) : 'error='.urlencode($msg);
header("Location: ../../pages/admin/dashboard.php?$param");
exit;