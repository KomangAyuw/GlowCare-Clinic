<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../pages/auth/Signin.php');
    exit;
}

$conn = require '../koneksi.php';

$dokter_id  = (int)($_POST['dokter_id'] ?? 0);
$rating_val = (float)($_POST['rating'] ?? 5.0);
$ulasan     = trim($_POST['ulasan'] ?? '');

if ($dokter_id <= 0) {
    header('Location: ../../pages/user/dashboarduser.php?error=' . urlencode('Dokter tidak valid.'));
    exit;
}

// Batasi rating 1-5
$rating_val = min(5.0, max(1.0, $rating_val));

// Update rating dokter secara dinamis
$qDokter = mysqli_query($conn, "SELECT rating, total_pasien FROM dokter WHERE id = $dokter_id LIMIT 1");
if ($qDokter && mysqli_num_rows($qDokter) > 0) {
    $d = mysqli_fetch_assoc($qDokter);
    $rating_lama = (float)$d['rating'];
    $total_pasien = (int)$d['total_pasien'];
    
    // Hitung rating baru
    $total_baru = $total_pasien + 1;
    $rating_baru = (($rating_lama * $total_pasien) + $rating_val) / $total_baru;
    $rating_baru = round($rating_baru, 1);
    
    mysqli_query($conn, "UPDATE dokter SET rating = $rating_baru, total_pasien = $total_baru WHERE id = $dokter_id");
}

header('Location: ../../pages/user/dashboarduser.php?success=' . urlencode('Terima kasih! Ulasan Anda berhasil terkirim.'));
exit;
?>
