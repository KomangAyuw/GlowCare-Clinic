<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dokter') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

$conn = require '../koneksi.php';

$pasien_id = (int)($_GET['pasien_id'] ?? 0);
if ($pasien_id <= 0) {
    echo json_encode([]);
    exit;
}

$q = mysqli_query($conn, "
    SELECT * 
    FROM rekam_medis 
    WHERE pasien_id = $pasien_id 
    ORDER BY tanggal DESC, id DESC
");

$timeline = [];
$bulan_ind = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

while ($row = mysqli_fetch_assoc($q)) {
    // Format tanggal
    $tgl = $row['tanggal']; // YYYY-MM-DD
    if (!empty($tgl)) {
        $parts = explode('-', $tgl);
        if (count($parts) === 3) {
            $d = (int)$parts[2];
            $m = (int)$parts[1];
            $y = $parts[0];
            $row['tanggal_label'] = $d . ' ' . ($bulan_ind[$m] ?? '') . ' ' . $y;
        } else {
            $row['tanggal_label'] = $tgl;
        }
    } else {
        $row['tanggal_label'] = '-';
    }
    
    $timeline[] = $row;
}

echo json_encode($timeline);
exit;
