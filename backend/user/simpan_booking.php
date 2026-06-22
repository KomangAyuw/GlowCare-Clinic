<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../pages/auth/Signin.php');
    exit;
}

$conn = require '../config/koneksi.php';

$user_id = (int)$_SESSION['user_id'];

// Ambil ID pasien berdasarkan user_id
$qPasien = mysqli_query($conn, "SELECT id, nama FROM pasien WHERE user_id = $user_id LIMIT 1");
if (!$qPasien || mysqli_num_rows($qPasien) === 0) {
    header('Location: ../../pages/user/dashboarduser.php?error=' . urlencode('Profil pasien tidak ditemukan.'));
    exit;
}
$pasien = mysqli_fetch_assoc($qPasien);
$pasien_id = (int)$pasien['id'];
$nama_pasien = $pasien['nama'];

// Ambil data POST
$dokter_id    = (int)($_POST['dokter_id'] ?? 0);
$tanggal      = $_POST['tanggal'] ?? '';
$jam          = $_POST['jam'] ?? '';
$treatment_id = $_POST['treatment_id'] !== '' ? (int)$_POST['treatment_id'] : null;
$keluhan      = trim($_POST['keluhan'] ?? '');
$alergi       = trim($_POST['alergi'] ?? '');

if ($dokter_id <= 0 || $tanggal === '' || $jam === '') {
    header('Location: ../../pages/user/dashboarduser.php?error=' . urlencode('Dokter, tanggal, dan jam wajib dipilih.'));
    exit;
}

if ($keluhan === '') {
    header('Location: ../../pages/user/dashboarduser.php?error=' . urlencode('Keluhan utama wajib diisi.'));
    exit;
}

if ($alergi === '') {
    header('Location: ../../pages/user/dashboarduser.php?error=' . urlencode('Riwayat alergi / kondisi khusus wajib diisi.'));
    exit;
}

// 0. Validasi Konflik Jadwal (Smart Booking)
$qCekKonflik = mysqli_query($conn, "SELECT id FROM appointment WHERE dokter_id = $dokter_id AND tanggal = '$tanggal' AND jam = '$jam' AND status != 'Dibatalkan'");
if ($qCekKonflik && mysqli_num_rows($qCekKonflik) > 0) {
    header('Location: ../../pages/user/dashboarduser.php?error=' . urlencode('Maaf, jadwal tersebut sudah dipesan oleh pasien lain. Silakan pilih jam atau tanggal berbeda.'));
    exit;
}

// 1. Dapatkan nama treatment
$nama_treatment = 'Konsultasi Umum';
if ($treatment_id !== null) {
    $qTreatment = mysqli_query($conn, "SELECT nama FROM treatment WHERE id = $treatment_id LIMIT 1");
    if ($qTreatment && mysqli_num_rows($qTreatment) > 0) {
        $nama_treatment = mysqli_fetch_assoc($qTreatment)['nama'];
    }
}

// 2. Simpan ke tabel appointment (untuk admin & pasien dashboard)
$stmt1 = mysqli_prepare($conn, "INSERT INTO appointment (pasien_id, dokter_id, treatment_id, tanggal, jam, status) VALUES (?, ?, ?, ?, ?, 'Terjadwal')");
mysqli_stmt_bind_param($stmt1, 'iiiss', $pasien_id, $dokter_id, $treatment_id, $tanggal, $jam);
$ok1 = mysqli_stmt_execute($stmt1);
$appt_id = mysqli_insert_id($conn);

// 3. Simpan ke tabel jadwal (untuk dokter dashboard)
$jam_selesai = date('H:i:s', strtotime($jam) + 3600); // 1 jam kemudian
$stmt2 = mysqli_prepare($conn, "INSERT INTO jadwal (pasien_id, dokter_id, tanggal, jam_mulai, jam_selesai, treatment, status) VALUES (?, ?, ?, ?, ?, ?, 'Terjadwal')");
mysqli_stmt_bind_param($stmt2, 'iissss', $pasien_id, $dokter_id, $tanggal, $jam, $jam_selesai, $nama_treatment);
$ok2 = mysqli_stmt_execute($stmt2);

// 4. Buat pembayaran otomatis (Lunas/Belum Lunas - Default Belum Lunas)
mysqli_query($conn, "INSERT INTO pembayaran (appointment_id, jumlah, status) VALUES ($appt_id, 150000.00, 'Belum Lunas')");

// 5. Update keluhan pasien jika ada
if ($keluhan !== '') {
    mysqli_query($conn, "UPDATE pasien SET keluhan = '" . mysqli_real_escape_string($conn, $keluhan) . "', catatan_medis = '" . mysqli_real_escape_string($conn, $alergi) . "' WHERE id = $pasien_id");
}

if ($ok1 && $ok2) {
    // Log Aktivitas
    $judulLog = 'Booking Baru';
    $deskLog = "$nama_pasien membuat janji temu baru.";
    $log = mysqli_prepare($conn, "INSERT INTO log_aktivitas (user_id, tipe, judul, deskripsi, referensi_tabel) VALUES (?, 'Janji Temu', ?, ?, 'appointment')");
    mysqli_stmt_bind_param($log, 'iss', $user_id, $judulLog, $deskLog);
    mysqli_stmt_execute($log);

    // Ambil nama dokter
    $qD = mysqli_query($conn, "SELECT nama FROM dokter WHERE id = $dokter_id LIMIT 1");
    $nama_dokter = $qD ? mysqli_fetch_assoc($qD)['nama'] : 'Dokter';

    $antrian = 'A-00' . rand(1, 9);

    header('Location: ../../pages/user/dashboarduser.php?success_booking=1&dokter=' . urlencode($nama_dokter) . '&tanggal=' . urlencode($tanggal) . '&jam=' . urlencode($jam) . '&antrian=' . urlencode($antrian));
} else {
    header('Location: ../../pages/user/dashboarduser.php?error=' . urlencode('Gagal memproses pendaftaran.'));
}
exit;
?>
