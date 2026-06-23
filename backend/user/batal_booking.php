<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../pages/auth/Signin.php');
    exit;
}

// Hanya pasien (role 'user') yang boleh membatalkan booking
if (isset($_SESSION['role']) && $_SESSION['role'] !== 'user') {
    header('Location: ../../index.php?error=' . urlencode('Akses ditolak. Hanya pasien yang dapat membatalkan booking.'));
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
$qApp = $conn->prepare("SELECT * FROM appointment WHERE id = :appt_id LIMIT 1");
$qApp->execute(['appt_id' => $appt_id]);
$app = $qApp->fetch();
if (!$app) {
    header('Location: ../../pages/user/dashboarduser.php?error=' . urlencode('Janji temu tidak ditemukan.'));
    exit;
}
$pasien_id = (int)$app['pasien_id'];
$dokter_id = (int)$app['dokter_id'];
$tanggal   = $app['tanggal'];
$jam       = $app['jam'];

// Batalkan appointment
$stmt1 = $conn->prepare("UPDATE appointment SET status = 'Dibatalkan' WHERE id = :appt_id");
$ok1 = $stmt1->execute(['appt_id' => $appt_id]);

// Batalkan jadwal dokter
$stmt2 = $conn->prepare("UPDATE jadwal SET status = 'Dibatalkan' WHERE pasien_id = :pasien_id AND dokter_id = :dokter_id AND tanggal = :tanggal AND jam_mulai = :jam");
$ok2 = $stmt2->execute([
    'pasien_id' => $pasien_id,
    'dokter_id' => $dokter_id,
    'tanggal' => $tanggal,
    'jam' => $jam
]);

if ($ok1 && $ok2) {
    // Log Aktivitas
    $qPasien = $conn->prepare("SELECT nama FROM pasien WHERE id = :pasien_id LIMIT 1");
    $qPasien->execute(['pasien_id' => $pasien_id]);
    $namaPasien = ($pRow = $qPasien->fetch()) ? $pRow['nama'] : 'Pasien';
    $judulLog = 'Booking Dibatalkan';
    $deskLog = "$namaPasien membatalkan janji temu tanggal $tanggal ($alasan).";
    
    $log = $conn->prepare("INSERT INTO log_aktivitas (user_id, tipe, judul, deskripsi, referensi_tabel) VALUES (:user_id, 'Janji Temu', :judul, :deskripsi, 'appointment')");
    $log->execute([
        'user_id' => $user_id,
        'judul' => $judulLog,
        'deskripsi' => $deskLog
    ]);

    header('Location: ../../pages/user/dashboarduser.php?success=' . urlencode('Jadwal berhasil dibatalkan.'));
} else {
    header('Location: ../../pages/user/dashboarduser.php?error=' . urlencode('Gagal membatalkan jadwal.'));
}
exit;
?>