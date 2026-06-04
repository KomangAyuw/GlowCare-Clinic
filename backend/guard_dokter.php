<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dokter') {
    header('Location: ../../pages/auth/Signin.php?error=' . urlencode('Akses ditolak. Halaman ini hanya untuk Dokter.'));
    exit;
}
?>
