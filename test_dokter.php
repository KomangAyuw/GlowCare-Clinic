<?php
// Set a mock user_id
$_SESSION['user_id'] = 2; // Let's try 2 or other values
$user_id = 2;
$today = date('Y-m-d');
$bulanIni = date('Y-m');

$conn = require 'backend/koneksi.php';

// Profil Dokter
$qProfil = mysqli_query($conn, "
    SELECT u.username, u.email, d.*
    FROM   users u
    LEFT JOIN dokter d ON d.user_id = u.id
    WHERE  u.id = $user_id
    LIMIT  1
");
$profil = mysqli_fetch_assoc($qProfil);
$dokter_id = (int)($profil['id'] ?? 0);

$queries = [
    "qProfil" => "SELECT u.username, u.email, d.* FROM users u LEFT JOIN dokter d ON d.user_id = u.id WHERE u.id = $user_id LIMIT 1",
    "qStatHari" => "SELECT COUNT(*) AS total, SUM(status = 'Selesai') AS selesai, SUM(status IN ('Menunggu','Berlangsung','Terjadwal')) AS menunggu FROM jadwal WHERE dokter_id = $dokter_id AND tanggal = '$today'",
    "qTotalPasien" => "SELECT COUNT(DISTINCT pasien_id) AS total FROM rekam_medis WHERE dokter_id = $dokter_id",
    "qStatBulan" => "SELECT COUNT(*) AS total_rm, SUM(treatment = 'Facelift') AS facelift, SUM(treatment = 'Rhinoplasty') AS rhinoplasty, SUM(treatment = 'Blepharoplasty') AS blepharoplasty FROM rekam_medis WHERE dokter_id = $dokter_id AND DATE_FORMAT(tanggal, '%Y-%m') = '$bulanIni'",
    "qJadwal" => "SELECT j.*, p.nama AS nama_pasien, p.usia, p.jenis_kelamin, p.keluhan, p.id AS pasien_id FROM jadwal j JOIN pasien p ON p.id = j.pasien_id WHERE j.dokter_id = $dokter_id AND j.tanggal = '$today' ORDER BY j.jam_mulai ASC",
    "qPasien" => "SELECT p.*, j.treatment, j.jam_mulai, j.status AS status_jadwal, COUNT(rm.id) AS total_kunjungan FROM pasien p LEFT JOIN jadwal j ON j.pasien_id = p.id AND j.dokter_id = $dokter_id AND j.tanggal = '$today' LEFT JOIN rekam_medis rm ON rm.pasien_id = p.id AND rm.dokter_id = $dokter_id WHERE j.dokter_id = $dokter_id OR rm.dokter_id = $dokter_id GROUP BY p.id, j.id ORDER BY j.jam_mulai ASC",
    "qRM" => "SELECT rm.*, p.nama AS nama_pasien, p.no_pasien AS no_pasien, rm.ruangan FROM rekam_medis rm JOIN pasien p ON p.id = rm.pasien_id WHERE rm.dokter_id = $dokter_id ORDER BY rm.tanggal DESC, rm.id DESC LIMIT 20",
    "qPasienDropdown" => "SELECT DISTINCT p.id, p.nama, p.no_rekam FROM pasien p JOIN jadwal j ON j.pasien_id = p.id WHERE j.dokter_id = $dokter_id ORDER BY p.nama ASC",
];

foreach ($queries as $name => $sql) {
    echo "Running query: $name...\n";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        echo "❌ ERROR: " . mysqli_error($conn) . "\n\n";
    } else {
        echo "✅ SUCCESS\n\n";
    }
}
