<?php
// backend/dokter/ganti_password.php
require '../koneksi.php';
require '../guard_dokter.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/dokter/dashboard.php');
    exit;
}

$user_id      = (int)$_SESSION['user_id'];
$pass_lama    = $_POST['password_lama']  ?? '';
$pass_baru    = $_POST['password_baru']  ?? '';
$konfirmasi   = $_POST['konfirmasi']     ?? '';

if ($pass_lama === '' || $pass_baru === '' || $konfirmasi === '') {
    header('Location: ../../pages/dokter/dashboard.php?error=' . urlencode('Semua field password harus diisi.'));
    exit;
}

if (strlen($pass_baru) < 8) {
    header('Location: ../../pages/dokter/dashboard.php?error=' . urlencode('Password baru minimal 8 karakter.'));
    exit;
}

if ($pass_baru !== $konfirmasi) {
    header('Location: ../../pages/dokter/dashboard.php?error=' . urlencode('Konfirmasi password tidak cocok.'));
    exit;
}

// Ambil password lama dari DB
$q = mysqli_query($conn, "SELECT password FROM users WHERE id = $user_id LIMIT 1");
$user = $q ? mysqli_fetch_assoc($q) : null;

if (!$user || !password_verify($pass_lama, $user['password'])) {
    header('Location: ../../pages/dokter/dashboard.php?error=' . urlencode('Password lama tidak sesuai.'));
    exit;
}

$hash = mysqli_real_escape_string($conn, password_hash($pass_baru, PASSWORD_DEFAULT));
mysqli_query($conn, "UPDATE users SET password = '$hash' WHERE id = $user_id");

header('Location: ../../pages/dokter/dashboard.php?success=' . urlencode('Password berhasil diubah.'));
exit;
?>