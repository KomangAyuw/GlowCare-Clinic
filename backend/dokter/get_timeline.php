<?php
// backend/dokter/get_timeline.php
require '../koneksi.php';
require '../guard_dokter.php';

header('Content-Type: application/json');

$pasien_id = (int)($_GET['pasien_id'] ?? 0);
$dokter_id_session = (int)($_SESSION['user_id'] ?? 0);

// Ambil dokter_id dari tabel dokter
$qD = mysqli_query($conn, "SELECT id FROM dokter WHERE user_id = $dokter_id_session LIMIT 1");
$dokter_id = $qD && ($row = mysqli_fetch_assoc($qD)) ? (int)$row['id'] : 0;

if ($pasien_id === 0 || $dokter_id === 0) {
    echo json_encode([]);
    exit;
}

$q = mysqli_query($conn, "
    SELECT rm.id, rm.treatment, rm.anamnesis, rm.pemeriksaan, rm.tanggal, rm.status
    FROM   rekam_medis rm
    WHERE  rm.pasien_id = $pasien_id
      AND  rm.dokter_id = $dokter_id
    ORDER  BY rm.tanggal DESC
    LIMIT  20
");

$bulan = ['','Januari','Februari','Maret','April','Mei','Juni',
          'Juli','Agustus','September','Oktober','November','Desember'];

$result = [];
while ($row = mysqli_fetch_assoc($q)) {
    [$y, $m, $d] = explode('-', $row['tanggal']);
    $row['tanggal_label'] = (int)$d . ' ' . $bulan[(int)$m] . ' ' . $y;
    // Potong teks panjang untuk tampilan timeline
    $row['anamnesis']   = mb_substr($row['anamnesis'] ?? '', 0, 120) . (mb_strlen($row['anamnesis'] ?? '') > 120 ? '...' : '');
    $row['pemeriksaan'] = mb_substr($row['pemeriksaan'] ?? '', 0, 120) . (mb_strlen($row['pemeriksaan'] ?? '') > 120 ? '...' : '');
    $result[] = $row;
}

echo json_encode($result);
?>