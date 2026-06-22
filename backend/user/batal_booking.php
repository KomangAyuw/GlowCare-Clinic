<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../pages/auth/Signin.php');
    exit;
}

$conn = require '../config/koneksi.php';

$user_id = (int)$_SESSION['user_id'];
$appt_id = (int)($_POST['appointment_id'] ?? 0);
$alasan  = trim($_POST['alasan'] ?? 'Lainnya');

if ($appt_id <= 0) {
    header('Location: ../../pages/user/dashboarduser.php?error=' . urlencode('Janji temu tidak valid.'));
    exit;
}

// Ambil info appointment
$qApp = mysqli_query($conn, "SELECT * FROM appointment WHERE id = $appt_id LIMIT 1");
if (!$qApp || mysqli_num_rows($qApp) === 0) {
    header('Location: ../../pages/user/dashboarduser.php?error=' . urlencode('Janji temu tidak ditemukan.'));
    exit;
}
$app = mysqli_fetch_assoc($qApp);
$pasien_id = (int)$app['pasien_id'];
$dokter_id = (int)$app['dokter_id'];
$tanggal   = $app['tanggal'];
$jam       = $app['jam'];

// Batalkan appointment
$ok1 = mysqli_query($conn, "UPDATE appointment SET status = 'Dibatalkan' WHERE id = $appt_id");

// Batalkan jadwal dokter
$ok2 = mysqli_query($conn, "UPDATE jadwal SET status = 'Dibatalkan' WHERE pasien_id = $pasien_id AND dokter_id = $dokter_id AND tanggal = '$tanggal' AND jam_mulai = '$jam'");

if ($ok1 && $ok2) {
    // Log Aktivitas
    $qPasien = mysqli_query($conn, "SELECT nama FROM pasien WHERE id = $pasien_id LIMIT 1");
    $namaPasien = $qPasien ? mysqli_fetch_assoc($qPasien)['nama'] : 'Pasien';
    $judulLog = 'Booking Dibatalkan';
    $deskLog = "$namaPasien membatalkan janji temu tanggal $tanggal ($alasan).";
    
    $log = mysqli_prepare($conn, "INSERT INTO log_aktivitas (user_id, tipe, judul, deskripsi, referensi_tabel) VALUES (?, 'Janji Temu', ?, ?, 'appointment')");
    mysqli_stmt_bind_param($log, 'iss', $user_id, $judulLog, $deskLog);
    mysqli_stmt_execute($log);

    header('Location: ../../pages/user/dashboarduser.php?success=' . urlencode('Jadwal berhasil dibatalkan.'));
} else {
    header('Location: ../../pages/user/dashboarduser.php?error=' . urlencode('Gagal membatalkan jadwal.'));
}
exit;
?>