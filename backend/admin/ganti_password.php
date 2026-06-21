<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/Signin.php'); exit;
}
$conn = require '../koneksi.php';

$lama      = $_POST['password_lama'] ?? '';
$baru      = $_POST['password_baru'] ?? '';
$konfirmasi= $_POST['konfirmasi'] ?? '';

if ($baru !== $konfirmasi) {
    header('Location: ../../pages/admin/dashboard.php?panel=profil&error='.urlencode('Password baru dan konfirmasi tidak cocok.')); exit;
}
if (strlen($baru) < 8) {
    header('Location: ../../pages/admin/dashboard.php?panel=profil&error='.urlencode('Password minimal 8 karakter.')); exit;
}

$uid  = (int)$_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT password FROM users WHERE id=$uid"));

if (!$user || !password_verify($lama, $user['password'])) {
    header('Location: ../../pages/admin/dashboard.php?panel=profil&error='.urlencode('Password lama salah.')); exit;
}

$hash = password_hash($baru, PASSWORD_DEFAULT);
$stmt = mysqli_prepare($conn, "UPDATE users SET password=? WHERE id=?");
mysqli_stmt_bind_param($stmt, 'si', $hash, $uid);
$ok = mysqli_stmt_execute($stmt);

if ($ok) {
    $tipe = 'Sistem'; $judul = 'Password Diubah'; $desk = "Admin mengubah password akun.";
    $log  = mysqli_prepare($conn, "INSERT INTO log_aktivitas (user_id,tipe,judul,deskripsi) VALUES (?,?,?,?)");
    mysqli_stmt_bind_param($log, 'isss', $uid, $tipe, $judul, $desk);
    mysqli_stmt_execute($log);
    $param = 'success='.urlencode('Password berhasil diubah.');
} else {
    $param = 'error='.urlencode('Gagal mengubah password.');
}

header("Location: ../../pages/admin/dashboard.php?panel=profil&$param");
exit;