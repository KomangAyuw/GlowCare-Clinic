<?php
require '../auth/guard_dokter.php';
$conn = require '../config/koneksi.php';

$user_id = (int)$_SESSION['user_id'];

// Ambil ID dokter berdasarkan user_id
$qDokter = mysqli_query($conn, "SELECT id FROM dokter WHERE user_id = $user_id LIMIT 1");
if (!$qDokter || mysqli_num_rows($qDokter) === 0) {
    header('Location: ../../pages/dokter/dashboardDokter.php?page=rekam-medis&error=' . urlencode('Profil dokter tidak ditemukan.'));
    exit;
}
$dokter = mysqli_fetch_assoc($qDokter);
$dokter_id = (int)$dokter['id'];

// Ambil data POST
$rm_id           = (int)($_POST['rm_id'] ?? 0);
$anamnesis       = trim($_POST['anamnesis'] ?? '');
$pemeriksaan     = trim($_POST['pemeriksaan'] ?? '');
$tindak_lanjut   = trim($_POST['tindak_lanjut'] ?? '');
$status          = $_POST['status'] ?? 'Selesai';
$jadwal_followup = !empty($_POST['jadwal_followup']) ? $_POST['jadwal_followup'] : null;

if ($rm_id <= 0) {
    header('Location: ../../pages/dokter/dashboardDokter.php?page=rekam-medis&error=' . urlencode('Rekam medis tidak valid.'));
    exit;
}

// Update Rekam Medis
$stmt = mysqli_prepare($conn, "UPDATE rekam_medis SET anamnesis=?, pemeriksaan=?, tindak_lanjut=?, status=?, jadwal_followup=? WHERE id=? AND dokter_id=?");
mysqli_stmt_bind_param($stmt, 'sssssii', $anamnesis, $pemeriksaan, $tindak_lanjut, $status, $jadwal_followup, $rm_id, $dokter_id);
$ok = mysqli_stmt_execute($stmt);

if ($ok) {
    // Log Aktivitas
    $judulLog = 'Rekam Medis Diperbarui';
    $deskLog = "dr. " . $_SESSION['username'] . " memperbarui rekam medis ID #$rm_id.";
    
    $log = mysqli_prepare($conn, "INSERT INTO log_aktivitas (user_id, tipe, judul, deskripsi, referensi_tabel) VALUES (?, 'Rekam Medis', ?, ?, 'rekam_medis')");
    mysqli_stmt_bind_param($log, 'iss', $user_id, $judulLog, $deskLog);
    mysqli_stmt_execute($log);

    header('Location: ../../pages/dokter/dashboardDokter.php?page=rekam-medis&success=' . urlencode('Rekam medis berhasil diperbarui.'));
} else {
    header('Location: ../../pages/dokter/dashboardDokter.php?page=rekam-medis&error=' . urlencode('Gagal memperbarui rekam medis: ' . mysqli_error($conn)));
}
exit;
?>
