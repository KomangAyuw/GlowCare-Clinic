<?php
require '../auth/guard_dokter.php';
$conn = require '../config/koneksi.php';

$user_id = (int)$_SESSION['user_id'];

$password_lama = $_POST['password_lama'] ?? '';
$password_baru = $_POST['password_baru'] ?? '';
$konfirmasi    = $_POST['konfirmasi'] ?? '';

if ($password_lama === '' || $password_baru === '' || $konfirmasi === '') {
    header('Location: ../../pages/dokter/dashboardDokter.php?page=profil&error=' . urlencode('Semua kolom password wajib diisi.'));
    exit;
}

if ($password_baru !== $konfirmasi) {
    header('Location: ../../pages/dokter/dashboardDokter.php?page=profil&error=' . urlencode('Password baru dan konfirmasi tidak cocok.'));
    exit;
}

if (strlen($password_baru) < 8) {
    header('Location: ../../pages/dokter/dashboardDokter.php?page=profil&error=' . urlencode('Password baru minimal 8 karakter.'));
    exit;
}

// Ambil password saat ini dari users
$qUser = mysqli_query($conn, "SELECT password FROM users WHERE id = $user_id LIMIT 1");
if (!$qUser || mysqli_num_rows($qUser) === 0) {
    header('Location: ../../pages/dokter/dashboardDokter.php?page=profil&error=' . urlencode('User tidak ditemukan.'));
    exit;
}
$user = mysqli_fetch_assoc($qUser);

if (!password_verify($password_lama, $user['password'])) {
    header('Location: ../../pages/dokter/dashboardDokter.php?page=profil&error=' . urlencode('Password lama salah.'));
    exit;
}

// Hash password baru
$hashPassword = password_hash($password_baru, PASSWORD_DEFAULT);

$stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'si', $hashPassword, $user_id);

if (mysqli_stmt_execute($stmt)) {
    header('Location: ../../pages/dokter/dashboardDokter.php?page=profil&success=' . urlencode('Password berhasil diubah.'));
} else {
    header('Location: ../../pages/dokter/dashboardDokter.php?page=profil&error=' . urlencode('Gagal mengubah password: ' . mysqli_error($conn)));
}
exit;
?>