<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dokter') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

$conn = require '../config/koneksi.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    echo json_encode([]);
    exit;
}

$q = mysqli_query($conn, "
    SELECT p.*, 
           (SELECT COUNT(*) FROM rekam_medis WHERE pasien_id = p.id) AS total_kunjungan
    FROM pasien p 
    WHERE p.id = $id 
    LIMIT 1
");

if ($row = mysqli_fetch_assoc($q)) {
    // Map missing or custom fields
    $row['alergi'] = $row['catatan_medis'] ?: 'Tidak ada';
    $row['gol_darah'] = '-';
    $row['kondisi_khusus'] = '-';
    echo json_encode($row);
} else {
    echo json_encode([]);
}
exit;
