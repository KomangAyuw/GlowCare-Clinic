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
$pasien_id       = (int)($_POST['pasien_id'] ?? 0);
$tanggal         = $_POST['tanggal'] ?? date('Y-m-d');
$treatment       = trim($_POST['treatment'] ?? '');
$ruangan         = trim($_POST['ruangan'] ?? 'Ruang A-1');
$anamnesis       = trim($_POST['anamnesis'] ?? '');
$pemeriksaan     = trim($_POST['pemeriksaan'] ?? '');
$tindak_lanjut   = trim($_POST['tindak_lanjut'] ?? '');
$status          = $_POST['status'] ?? 'Selesai';
$jadwal_followup = !empty($_POST['jadwal_followup']) ? $_POST['jadwal_followup'] : null;

if ($pasien_id <= 0) {
    header('Location: ../../pages/dokter/dashboardDokter.php?page=rekam-medis&error=' . urlencode('Pasien wajib dipilih.'));
    exit;
}

// Simpan Rekam Medis
$stmt = $conn->prepare("INSERT INTO rekam_medis (pasien_id, dokter_id, tanggal, treatment, anamnesis, pemeriksaan, tindak_lanjut, status, jadwal_followup, ruangan) VALUES (:pasien_id, :dokter_id, :tanggal, :treatment, :anamnesis, :pemeriksaan, :tindak_lanjut, :status, :jadwal_followup, :ruangan)");
$ok = $stmt->execute([
    'pasien_id'       => $pasien_id,
    'dokter_id'       => $dokter_id,
    'tanggal'         => $tanggal,
    'treatment'       => $treatment,
    'anamnesis'       => $anamnesis,
    'pemeriksaan'     => $pemeriksaan,
    'tindak_lanjut'   => $tindak_lanjut,
    'status'          => $status,
    'jadwal_followup' => $jadwal_followup,
    'ruangan'         => $ruangan
]);

if ($ok) {
    // 1. Update status jadwal/appointment hari ini menjadi Selesai
    $stmtUpdateJadwal = $conn->prepare("UPDATE jadwal SET status='Selesai' WHERE dokter_id = :dokter_id AND pasien_id = :pasien_id AND tanggal = :tanggal");
    $stmtUpdateJadwal->execute(['dokter_id' => $dokter_id, 'pasien_id' => $pasien_id, 'tanggal' => $tanggal]);

    $stmtUpdateAppt = $conn->prepare("UPDATE appointment SET status='Selesai' WHERE dokter_id = :dokter_id AND pasien_id = :pasien_id AND tanggal = :tanggal");
    $stmtUpdateAppt->execute(['dokter_id' => $dokter_id, 'pasien_id' => $pasien_id, 'tanggal' => $tanggal]);

    // 2. Increment total kunjungan pasien
    $stmtUpdatePasien = $conn->prepare("UPDATE pasien SET total_kunjungan = total_kunjungan + 1 WHERE id = :pasien_id");
    $stmtUpdatePasien->execute(['pasien_id' => $pasien_id]);

    // 3. Increment total pasien unik di dokter
    $stmtUnik = $conn->prepare("SELECT COUNT(DISTINCT pasien_id) AS n FROM rekam_medis WHERE dokter_id = :dokter_id");
    $stmtUnik->execute(['dokter_id' => $dokter_id]);
    $nUnik = ($uRow = $stmtUnik->fetch()) ? (int)$uRow['n'] : 0;
    
    $stmtUpdateDokter = $conn->prepare("UPDATE dokter SET total_pasien = :total_pasien WHERE id = :dokter_id");
    $stmtUpdateDokter->execute(['total_pasien' => $nUnik, 'dokter_id' => $dokter_id]);

    // 4. Log Aktivitas
    $qPasien = $conn->prepare("SELECT nama FROM pasien WHERE id = :pasien_id LIMIT 1");
    $qPasien->execute(['pasien_id' => $pasien_id]);
    $namaPasien = ($pRow = $qPasien->fetch()) ? $pRow['nama'] : 'Pasien';
    
    $judulLog = 'Rekam Medis Ditambahkan';
    $deskLog = "dr. " . $_SESSION['username'] . " menambahkan rekam medis untuk $namaPasien.";
    
    $log = $conn->prepare("INSERT INTO log_aktivitas (user_id, tipe, judul, deskripsi, referensi_tabel) VALUES (:user_id, 'Rekam Medis', :judul, :deskripsi, 'rekam_medis')");
    $log->execute([
        'user_id'   => $user_id,
        'judul'     => $judulLog,
        'deskripsi' => $deskLog
    ]);

    header('Location: ../../pages/dokter/dashboardDokter.php?page=rekam-medis&success=' . urlencode('Rekam medis berhasil ditambahkan.'));
} else {
    header('Location: ../../pages/dokter/dashboardDokter.php?page=rekam-medis&error=' . urlencode('Gagal menyimpan rekam medis.'));
}
exit;
?>