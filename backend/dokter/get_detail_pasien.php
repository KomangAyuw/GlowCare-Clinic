<?php
// backend/dokter/get_pasien_detail.php
require '../koneksi.php';
require '../guard_dokter.php';

header('Content-Type: application/json');

$pasien_id = (int)($_GET['id'] ?? 0);
$dokter_id = (int)($_SESSION['dokter_id'] ?? 0); // set saat login atau ambil dari DB

if ($pasien_id === 0) {
    echo json_encode([]);
    exit;
}

// Pastikan pasien ini memang pernah ditangani dokter ini
$q = mysqli_query($conn, "
    SELECT p.*,
           COUNT(rm.id) AS total_kunjungan
    FROM   pasien p
    LEFT JOIN rekam_medis rm ON rm.pasien_id = p.id AND rm.dokter_id = $dokter_id
    WHERE  p.id = $pasien_id
    GROUP  BY p.id
    LIMIT  1
");

$data = $q ? mysqli_fetch_assoc($q) : [];
echo json_encode($data ?: new stdClass());
?>