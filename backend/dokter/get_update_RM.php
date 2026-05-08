<?php
// backend/dokter/update_rekam_medis.php
require '../koneksi.php';
require '../guard_dokter.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/dokter/dashboard.php');
    exit;
}

$rm_id         = (int)($_POST['rm_id'] ?? 0);
$anamnesis     = mysqli_real_escape_string($conn, trim($_POST['anamnesis']     ?? ''));
$pemeriksaan   = mysqli_real_escape_string($conn, trim($_POST['pemeriksaan']   ?? ''));
$tindak_lanjut = mysqli_real_escape_string($conn, trim($_POST['tindak_lanjut'] ?? ''));
$status        = mysqli_real_escape_string($conn, $_POST['status']             ?? 'Selesai');
$followup      = !empty($_POST['jadwal_followup'])
                 ? "'" . mysqli_real_escape_string($conn, $_POST['jadwal_followup']) . "'"
                 : 'NULL';

if ($rm_id === 0) {
    header('Location: ../../pages/dokter/dashboard.php?error=' . urlencode('ID rekam medis tidak valid.'));
    exit;
}

// Pastikan rekam medis ini milik dokter yang login
$user_id = (int)$_SESSION['user_id'];
$qD = mysqli_query($conn, "SELECT id FROM dokter WHERE user_id = $user_id LIMIT 1");
$dokter_id = $qD && ($r = mysqli_fetch_assoc($qD)) ? (int)$r['id'] : 0;

$sql = "UPDATE rekam_medis SET
    anamnesis     = '$anamnesis',
    pemeriksaan   = '$pemeriksaan',
    tindak_lanjut = '$tindak_lanjut',
    status        = '$status',
    jadwal_followup = $followup
WHERE id = $rm_id AND dokter_id = $dokter_id";

if (mysqli_query($conn, $sql) && mysqli_affected_rows($conn) > 0) {
    header('Location: ../../pages/dokter/dashboard.php?success=' . urlencode('Rekam medis berhasil diperbarui.'));
} else {
    header('Location: ../../pages/dokter/dashboard.php?error=' . urlencode('Gagal memperbarui rekam medis.'));
}
exit;
?>