<?php
require '../guard_dokter.php';
$conn = require '../koneksi.php';

$user_id = (int)$_SESSION['user_id'];

// Ambil ID dokter berdasarkan user_id
$qDokter = mysqli_query($conn, "SELECT id FROM dokter WHERE user_id = $user_id LIMIT 1");
if (!$qDokter || mysqli_num_rows($qDokter) === 0) {
    header('Location: ../../pages/dokter/dashboardDokter.php?error=' . urlencode('Profil dokter tidak ditemukan.'));
    exit;
}
$dokter = mysqli_fetch_assoc($qDokter);
$dokter_id = (int)$dokter['id'];

// Ambil data POST
$nama_lengkap     = isset($_POST['nama_lengkap']) ? trim($_POST['nama_lengkap']) : null;
$gelar            = isset($_POST['gelar']) ? trim($_POST['gelar']) : null;
$no_telp          = isset($_POST['no_telp']) ? trim($_POST['no_telp']) : null;
$alamat           = isset($_POST['alamat']) ? trim($_POST['alamat']) : null;

$spesialisasi     = isset($_POST['spesialisasi']) ? trim($_POST['spesialisasi']) : null;
$no_str           = isset($_POST['no_str']) ? trim($_POST['no_str']) : null;
$no_sip           = isset($_POST['no_sip']) ? trim($_POST['no_sip']) : null;
$tahun_pengalaman = isset($_POST['tahun_pengalaman']) ? (int)$_POST['tahun_pengalaman'] : null;
$bio              = isset($_POST['bio']) ? trim($_POST['bio']) : null;

if ($nama_lengkap !== null) {
    // Form data pribadi
    $stmt = mysqli_prepare($conn, "UPDATE dokter SET nama_lengkap=?, nama=?, gelar=?, no_telp=?, telepon=?, alamat=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'ssssssi', $nama_lengkap, $nama_lengkap, $gelar, $no_telp, $no_telp, $alamat, $dokter_id);
    $ok = mysqli_stmt_execute($stmt);
} else {
    // Form data profesional
    $stmt = mysqli_prepare($conn, "UPDATE dokter SET spesialisasi=?, no_str=?, no_sip=?, tahun_pengalaman=?, pengalaman=?, bio=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'ssssiis', $spesialisasi, $no_str, $no_sip, $tahun_pengalaman, $tahun_pengalaman, $bio, $dokter_id);
    $ok = mysqli_stmt_execute($stmt);
}

if ($ok) {
    header('Location: ../../pages/dokter/dashboardDokter.php?page=profil&success=' . urlencode('Profil berhasil diperbarui.'));
} else {
    header('Location: ../../pages/dokter/dashboardDokter.php?page=profil&error=' . urlencode('Gagal memperbarui profil: ' . mysqli_error($conn)));
}
exit;
?>
