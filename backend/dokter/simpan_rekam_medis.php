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
$stmt = mysqli_prepare($conn, "INSERT INTO rekam_medis (pasien_id, dokter_id, tanggal, treatment, anamnesis, pemeriksaan, tindak_lanjut, status, jadwal_followup, ruangan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, 'iissssssss', $pasien_id, $dokter_id, $tanggal, $treatment, $anamnesis, $pemeriksaan, $tindak_lanjut, $status, $jadwal_followup, $ruangan);
$ok = mysqli_stmt_execute($stmt);

if ($ok) {
    // 1. Update status jadwal/appointment hari ini menjadi Selesai
    mysqli_query($conn, "UPDATE jadwal SET status='Selesai' WHERE dokter_id=$dokter_id AND pasien_id=$pasien_id AND tanggal='$tanggal'");
    mysqli_query($conn, "UPDATE appointment SET status='Selesai' WHERE dokter_id=$dokter_id AND pasien_id=$pasien_id AND tanggal='$tanggal'");

    // 2. Increment total kunjungan pasien
    mysqli_query($conn, "UPDATE pasien SET total_kunjungan = total_kunjungan + 1 WHERE id = $pasien_id");

    // 3. Increment total pasien unik di dokter
    $qUnik = mysqli_query($conn, "SELECT COUNT(DISTINCT pasien_id) AS n FROM rekam_medis WHERE dokter_id = $dokter_id");
    $nUnik = $qUnik ? (int)mysqli_fetch_assoc($qUnik)['n'] : 0;
    mysqli_query($conn, "UPDATE dokter SET total_pasien = $nUnik WHERE id = $dokter_id");

    // 4. Log Aktivitas
    $qPasien = mysqli_query($conn, "SELECT nama FROM pasien WHERE id = $pasien_id LIMIT 1");
    $namaPasien = $qPasien ? mysqli_fetch_assoc($qPasien)['nama'] : 'Pasien';
    $judulLog = 'Rekam Medis Ditambahkan';
    $deskLog = "dr. " . $_SESSION['username'] . " menambahkan rekam medis untuk $namaPasien.";
    
    $log = mysqli_prepare($conn, "INSERT INTO log_aktivitas (user_id, tipe, judul, deskripsi, referensi_tabel) VALUES (?, 'Rekam Medis', ?, ?, 'rekam_medis')");
    mysqli_stmt_bind_param($log, 'iss', $user_id, $judulLog, $deskLog);
    mysqli_stmt_execute($log);

    header('Location: ../../pages/dokter/dashboardDokter.php?page=rekam-medis&success=' . urlencode('Rekam medis berhasil ditambahkan.'));
} else {
    header('Location: ../../pages/dokter/dashboardDokter.php?page=rekam-medis&error=' . urlencode('Gagal menyimpan rekam medis: ' . mysqli_error($conn)));
}
exit;
?>
