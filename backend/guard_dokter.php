<?php

// Include ini di awal SETIAP halaman dokter
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'dokter') {
    header('Location: ../auth/Signin.php?error=' . urlencode('Akses ditolak. Silakan login sebagai dokter.'));
    exit;
}
?>