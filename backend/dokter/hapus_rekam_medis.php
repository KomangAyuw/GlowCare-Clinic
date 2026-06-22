<?php
require '../auth/guard_dokter.php';
$conn = require '../config/koneksi.php';

$user_id = (int)$_SESSION['user_id'];

// Ambil ID dokter
$qDokter = mysqli_query($conn, "SELECT id FROM dokter WHERE user_id = $user_id LIMIT 1");
if (!$qDokter || mysqli_num_rows($qDokter) === 0) {
    header('Location: ../../pages/dokter/dashboardDokter.php?page=rekam-medis&error=' . urlencode('Profil dokter tidak ditemukan.'));
    exit;
}
$dokter = mysqli_fetch_assoc($qDokter);
$dokter_id = (int)$dokter['id'];

$rm_id = (int)($_POST['rm_id'] ?? 0);
if ($rm_id <= 0) {
    header('Location: ../../pages/dokter/dashboardDokter.php?page=rekam-medis&error=' . urlencode('Rekam medis tidak valid.'));
    exit;
}

// Pastikan rekam medis milik dokter ini
$qCek = mysqli_query($conn, "SELECT id FROM rekam_medis WHERE id = $rm_id AND dokter_id = $dokter_id LIMIT 1");
if (!$qCek || mysqli_num_rows($qCek) === 0) {
    header('Location: ../../pages/dokter/dashboardDokter.php?page=rekam-medis&error=' . urlencode('Rekam medis tidak ditemukan atau bukan milik Anda.'));
    exit;
}

$stmt = mysqli_prepare($conn, "DELETE FROM rekam_medis WHERE id = ? AND dokter_id = ?");
mysqli_stmt_bind_param($stmt, 'ii', $rm_id, $dokter_id);
$ok = mysqli_stmt_execute($stmt);

if ($ok) {
    // Log aktivitas
    $judulLog = 'Rekam Medis Dihapus';
    $deskLog  = 'dr. ' . $_SESSION['username'] . " menghapus rekam medis ID #$rm_id.";
    $log = mysqli_prepare($conn, "INSERT INTO log_aktivitas (user_id, tipe, judul, deskripsi, referensi_tabel) VALUES (?, 'Rekam Medis', ?, ?, 'rekam_medis')");
    mysqli_stmt_bind_param($log, 'iss', $user_id, $judulLog, $deskLog);
    mysqli_stmt_execute($log);

    header('Location: ../../pages/dokter/dashboardDokter.php?page=rekam-medis&success=' . urlencode('Rekam medis berhasil dihapus.'));
} else {
    header('Location: ../../pages/dokter/dashboardDokter.php?page=rekam-medis&error=' . urlencode('Gagal menghapus rekam medis: ' . mysqli_error($conn)));
}
exit;
