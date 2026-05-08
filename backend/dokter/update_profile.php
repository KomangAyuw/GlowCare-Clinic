<?php
// backend/dokter/update_profil.php
require '../koneksi.php';
require '../guard_dokter.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$user_id          = $_SESSION['user_id'];
$nama_lengkap     = mysqli_real_escape_string($conn, trim($_POST['nama_lengkap'] ?? ''));
$gelar            = mysqli_real_escape_string($conn, trim($_POST['gelar'] ?? ''));
$no_telp          = mysqli_real_escape_string($conn, trim($_POST['no_telp'] ?? ''));
$alamat           = mysqli_real_escape_string($conn, trim($_POST['alamat'] ?? ''));
$spesialisasi     = mysqli_real_escape_string($conn, trim($_POST['spesialisasi'] ?? ''));
$no_str           = mysqli_real_escape_string($conn, trim($_POST['no_str'] ?? ''));
$no_sip           = mysqli_real_escape_string($conn, trim($_POST['no_sip'] ?? ''));
$tahun_pengalaman = (int)($_POST['tahun_pengalaman'] ?? 0);
$bio              = mysqli_real_escape_string($conn, trim($_POST['bio'] ?? ''));

// Cek apakah record dokter sudah ada
$cek = mysqli_query($conn, "SELECT id FROM dokter WHERE user_id = $user_id LIMIT 1");

if (mysqli_num_rows($cek) > 0) {
    // Update
    $sql = "UPDATE dokter SET
        nama_lengkap = '$nama_lengkap',
        gelar = '$gelar',
        no_telp = '$no_telp',
        alamat = '$alamat',
        spesialisasi = '$spesialisasi',
        no_str = '$no_str',
        no_sip = '$no_sip',
        tahun_pengalaman = $tahun_pengalaman,
        bio = '$bio'
    WHERE user_id = $user_id";
} else {
    // Insert pertama kali
    $sql = "INSERT INTO dokter
        (user_id, nama_lengkap, gelar, no_telp, alamat, spesialisasi, no_str, no_sip, tahun_pengalaman, bio)
        VALUES
        ($user_id, '$nama_lengkap', '$gelar', '$no_telp', '$alamat',
         '$spesialisasi', '$no_str', '$no_sip', $tahun_pengalaman, '$bio')";
}

if (mysqli_query($conn, $sql)) {
    header('Location: ../../pages/dokter/dashboard.php?success=' . urlencode('Profil berhasil disimpan.'));
} else {
    header('Location: ../../pages/dokter/dashboard.php?error=' . urlencode('Gagal menyimpan profil.'));
}
exit;
?>