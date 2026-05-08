<?php
// backend/dokter/simpan_rekam_medis.php
require '../koneksi.php';
require '../guard_dokter.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil dokter_id
$res = mysqli_query($conn, "SELECT id FROM dokter WHERE user_id = $user_id LIMIT 1");
if (!$res || mysqli_num_rows($res) === 0) {
    header('Location: ../../pages/dokter/dashboard.php?error=' . urlencode('Profil dokter tidak ditemukan.'));
    exit;
}
$dokter = mysqli_fetch_assoc($res);
$dokter_id = $dokter['id'];

$pasien_id     = (int)($_POST['pasien_id'] ?? 0);
$tanggal       = mysqli_real_escape_string($conn, $_POST['tanggal'] ?? date('Y-m-d'));
$treatment     = mysqli_real_escape_string($conn, trim($_POST['treatment'] ?? ''));
$ruangan       = mysqli_real_escape_string($conn, trim($_POST['ruangan'] ?? ''));
$anamnesis     = mysqli_real_escape_string($conn, trim($_POST['anamnesis'] ?? ''));
$pemeriksaan   = mysqli_real_escape_string($conn, trim($_POST['pemeriksaan'] ?? ''));
$tindak_lanjut = mysqli_real_escape_string($conn, trim($_POST['tindak_lanjut'] ?? ''));
$followup      = $_POST['jadwal_followup'] ? mysqli_real_escape_string($conn, $_POST['jadwal_followup']) : 'NULL';
$status        = mysqli_real_escape_string($conn, $_POST['status'] ?? 'Selesai');

if ($pasien_id === 0) {
    header('Location: ../../pages/dokter/dashboard.php?error=' . urlencode('Pasien tidak valid.'));
    exit;
}

$followupVal = $followup === 'NULL' ? 'NULL' : "'$followup'";

$sql = "INSERT INTO rekam_medis
    (dokter_id, pasien_id, tanggal, treatment, ruangan, anamnesis, pemeriksaan, tindak_lanjut, jadwal_followup, status)
    VALUES
    ($dokter_id, $pasien_id, '$tanggal', '$treatment', '$ruangan',
     '$anamnesis', '$pemeriksaan', '$tindak_lanjut', $followupVal, '$status')";

if (mysqli_query($conn, $sql)) {
    header('Location: ../../pages/dokter/dashboard.php?success=' . urlencode('Rekam medis berhasil disimpan.'));
} else {
    header('Location: ../../pages/dokter/dashboard.php?error=' . urlencode('Gagal menyimpan rekam medis.'));
}
exit;
?>