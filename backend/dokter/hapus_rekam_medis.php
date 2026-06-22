<?php
require '../auth/guard_dokter.php';
$conn = require '../config/koneksi.php';

$user_id = (int)$_SESSION['user_id'];

// Ambil ID dokter
$qDokter = $conn->prepare("SELECT id FROM dokter WHERE user_id = :user_id LIMIT 1");
$qDokter->execute(['user_id' => $user_id]);
$dokter = $qDokter->fetch();
if (!$dokter) {
    header('Location: ../../pages/dokter/dashboardDokter.php?page=rekam-medis&error=' . urlencode('Profil dokter tidak ditemukan.'));
    exit;
}
$dokter_id = (int)$dokter['id'];

$rm_id = (int)($_POST['rm_id'] ?? 0);
if ($rm_id <= 0) {
    header('Location: ../../pages/dokter/dashboardDokter.php?page=rekam-medis&error=' . urlencode('Rekam medis tidak valid.'));
    exit;
}

// Pastikan rekam medis milik dokter ini
$qCek = $conn->prepare("SELECT id FROM rekam_medis WHERE id = :rm_id AND dokter_id = :dokter_id LIMIT 1");
$qCek->execute(['rm_id' => $rm_id, 'dokter_id' => $dokter_id]);
if (!$qCek->fetch()) {
    header('Location: ../../pages/dokter/dashboardDokter.php?page=rekam-medis&error=' . urlencode('Rekam medis tidak ditemukan atau bukan milik Anda.'));
    exit;
}

$stmt = $conn->prepare("DELETE FROM rekam_medis WHERE id = :rm_id AND dokter_id = :dokter_id");
$ok = $stmt->execute(['rm_id' => $rm_id, 'dokter_id' => $dokter_id]);

if ($ok) {
    // Log aktivitas
    $judulLog = 'Rekam Medis Dihapus';
    $deskLog  = 'dr. ' . $_SESSION['username'] . " menghapus rekam medis ID #$rm_id.";
    
    $log = $conn->prepare("INSERT INTO log_aktivitas (user_id, tipe, judul, deskripsi, referensi_tabel) VALUES (:user_id, 'Rekam Medis', :judul, :deskripsi, 'rekam_medis')");
    $log->execute([
        'user_id'   => $user_id,
        'judul'     => $judulLog,
        'deskripsi' => $deskLog
    ]);

    header('Location: ../../pages/dokter/dashboardDokter.php?page=rekam-medis&success=' . urlencode('Rekam medis berhasil dihapus.'));
} else {
    header('Location: ../../pages/dokter/dashboardDokter.php?page=rekam-medis&error=' . urlencode('Gagal menghapus rekam medis.'));
}
exit;