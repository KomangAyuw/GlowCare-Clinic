<?php
require '../auth/guard_dokter.php';
$conn = require '../config/koneksi.php';

$user_id = (int)$_SESSION['user_id'];

// Ambil ID dokter berdasarkan user_id
$qDokter = $conn->prepare("SELECT id FROM dokter WHERE user_id = :user_id LIMIT 1");
$qDokter->execute(['user_id' => $user_id]);
$dokter = $qDokter->fetch();
if (!$dokter) {
    header('Location: ../../pages/dokter/dashboardDokter.php?page=rekam-medis&error=' . urlencode('Profil dokter tidak ditemukan.'));
    exit;
}
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
$stmt = $conn->prepare("UPDATE rekam_medis SET anamnesis = :anamnesis, pemeriksaan = :pemeriksaan, tindak_lanjut = :tindak_lanjut, status = :status, jadwal_followup = :jadwal_followup WHERE id = :rm_id AND dokter_id = :dokter_id");
$ok = $stmt->execute([
    'anamnesis'       => $anamnesis,
    'pemeriksaan'     => $pemeriksaan,
    'tindak_lanjut'   => $tindak_lanjut,
    'status'          => $status,
    'jadwal_followup' => $jadwal_followup,
    'rm_id'           => $rm_id,
    'dokter_id'       => $dokter_id
]);

if ($ok) {
    // Log Aktivitas
    $judulLog = 'Rekam Medis Diperbarui';
    $deskLog = "dr. " . $_SESSION['username'] . " memperbarui rekam medis ID #$rm_id.";
    
    $log = $conn->prepare("INSERT INTO log_aktivitas (user_id, tipe, judul, deskripsi, referensi_tabel) VALUES (:user_id, 'Rekam Medis', :judul, :deskripsi, 'rekam_medis')");
    $log->execute([
        'user_id'   => $user_id,
        'judul'     => $judulLog,
        'deskripsi' => $deskLog
    ]);

    header('Location: ../../pages/dokter/dashboardDokter.php?page=rekam-medis&success=' . urlencode('Rekam medis berhasil diperbarui.'));
} else {
    header('Location: ../../pages/dokter/dashboardDokter.php?page=rekam-medis&error=' . urlencode('Gagal memperbarui rekam medis.'));
}
exit;
?>