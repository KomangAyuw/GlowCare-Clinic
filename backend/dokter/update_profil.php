<?php
require '../auth/guard_dokter.php';
$conn = require '../config/koneksi.php';

$user_id = (int)$_SESSION['user_id'];

// Ambil ID dokter berdasarkan user_id
$qDokter = $conn->prepare("SELECT id FROM dokter WHERE user_id = :user_id LIMIT 1");
$qDokter->execute(['user_id' => $user_id]);
$dokter = $qDokter->fetch();
if (!$dokter) {
    header('Location: ../../pages/dokter/dashboardDokter.php?error=' . urlencode('Profil dokter tidak ditemukan.'));
    exit;
}
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
    $stmt = $conn->prepare("UPDATE dokter SET nama_lengkap = :nama_lengkap, nama = :nama, gelar = :gelar, no_telp = :no_telp, telepon = :telepon, alamat = :alamat WHERE id = :id");
    $ok = $stmt->execute([
        'nama_lengkap' => $nama_lengkap,
        'nama'         => $nama_lengkap,
        'gelar'        => $gelar,
        'no_telp'      => $no_telp,
        'telepon'      => $no_telp,
        'alamat'       => $alamat,
        'id'           => $dokter_id
    ]);
} else {
    // Form data profesional
    $stmt = $conn->prepare("UPDATE dokter SET spesialisasi = :spesialisasi, no_str = :no_str, no_sip = :no_sip, tahun_pengalaman = :tahun_pengalaman, pengalaman = :pengalaman, bio = :bio WHERE id = :id");
    $ok = $stmt->execute([
        'spesialisasi'     => $spesialisasi,
        'no_str'           => $no_str,
        'no_sip'           => $no_sip,
        'tahun_pengalaman' => $tahun_pengalaman,
        'pengalaman'       => $tahun_pengalaman,
        'bio'              => $bio,
        'id'               => $dokter_id
    ]);
}

if ($ok) {
    header('Location: ../../pages/dokter/dashboardDokter.php?page=profil&success=' . urlencode('Profil berhasil diperbarui.'));
} else {
    header('Location: ../../pages/dokter/dashboardDokter.php?page=profil&error=' . urlencode('Gagal memperbarui profil.'));
}
exit;
?>