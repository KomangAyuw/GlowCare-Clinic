<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../pages/auth/Signin.php');
    exit;
}

$conn = require '../config/koneksi.php';

$user_id = (int)$_SESSION['user_id'];

// Ambil ID pasien berdasarkan user_id
$qPasien = $conn->prepare("SELECT id, nama FROM pasien WHERE user_id = :user_id LIMIT 1");
$qPasien->execute(['user_id' => $user_id]);
$pasien = $qPasien->fetch();

if (!$pasien) {
    header('Location: ../../pages/user/dashboarduser.php?error=' . urlencode('Profil pasien tidak ditemukan.'));
    exit;
}
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
$qCekKonflik = $conn->prepare("SELECT id FROM appointment WHERE dokter_id = :dokter_id AND tanggal = :tanggal AND jam = :jam AND status != 'Dibatalkan'");
$qCekKonflik->execute([
    'dokter_id' => $dokter_id,
    'tanggal' => $tanggal,
    'jam' => $jam
]);
if ($qCekKonflik->fetch()) {
    header('Location: ../../pages/user/dashboarduser.php?error=' . urlencode('Maaf, jadwal tersebut sudah dipesan oleh pasien lain. Silakan pilih jam atau tanggal berbeda.'));
    exit;
}

// 1. Dapatkan nama treatment
$nama_treatment = 'Konsultasi Umum';
if ($treatment_id !== null) {
    $qTreatment = $conn->prepare("SELECT nama FROM treatment WHERE id = :treatment_id LIMIT 1");
    $qTreatment->execute(['treatment_id' => $treatment_id]);
    if ($tRow = $qTreatment->fetch()) {
        $nama_treatment = $tRow['nama'];
    }
}

// 2. Simpan ke tabel appointment (untuk admin & pasien dashboard)
$stmt1 = $conn->prepare("INSERT INTO appointment (pasien_id, dokter_id, treatment_id, tanggal, jam, status) VALUES (:pasien_id, :dokter_id, :treatment_id, :tanggal, :jam, 'Terjadwal')");
$ok1 = $stmt1->execute([
    'pasien_id' => $pasien_id,
    'dokter_id' => $dokter_id,
    'treatment_id' => $treatment_id,
    'tanggal' => $tanggal,
    'jam' => $jam
]);
$appt_id = $conn->lastInsertId();

// 3. Simpan ke tabel jadwal (untuk dokter dashboard)
$jam_selesai = date('H:i:s', strtotime($jam) + 3600); // 1 jam kemudian
$stmt2 = $conn->prepare("INSERT INTO jadwal (pasien_id, dokter_id, tanggal, jam_mulai, jam_selesai, treatment, status) VALUES (:pasien_id, :dokter_id, :tanggal, :jam, :jam_selesai, :treatment, 'Terjadwal')");
$ok2 = $stmt2->execute([
    'pasien_id' => $pasien_id,
    'dokter_id' => $dokter_id,
    'tanggal' => $tanggal,
    'jam' => $jam,
    'jam_selesai' => $jam_selesai,
    'treatment' => $nama_treatment
]);

// 4. Buat pembayaran otomatis (Lunas/Belum Lunas - Default Belum Lunas)
$stmtPay = $conn->prepare("INSERT INTO pembayaran (appointment_id, jumlah, status) VALUES (:appt_id, 150000.00, 'Belum Lunas')");
$stmtPay->execute(['appt_id' => $appt_id]);

// 5. Update keluhan pasien jika ada
if ($keluhan !== '') {
    $stmtUpdatePasien = $conn->prepare("UPDATE pasien SET keluhan = :keluhan, catatan_medis = :alergi WHERE id = :pasien_id");
    $stmtUpdatePasien->execute([
        'keluhan' => $keluhan,
        'alergi' => $alergi,
        'pasien_id' => $pasien_id
    ]);
}

if ($ok1 && $ok2) {
    // Log Aktivitas
    $judulLog = 'Booking Baru';
    $deskLog = "$nama_pasien membuat janji temu baru.";
    $log = $conn->prepare("INSERT INTO log_aktivitas (user_id, tipe, judul, deskripsi, referensi_tabel) VALUES (:user_id, 'Janji Temu', :judul, :deskripsi, 'appointment')");
    $log->execute([
        'user_id' => $user_id,
        'judul' => $judulLog,
        'deskripsi' => $deskLog
    ]);

    // Ambil nama dokter
    $qD = $conn->prepare("SELECT nama FROM dokter WHERE id = :dokter_id LIMIT 1");
    $qD->execute(['dokter_id' => $dokter_id]);
    $nama_dokter = ($dRow = $qD->fetch()) ? $dRow['nama'] : 'Dokter';

    $antrian = 'A-00' . rand(1, 9);

    header('Location: ../../pages/user/dashboarduser.php?success_booking=1&dokter=' . urlencode($nama_dokter) . '&tanggal=' . urlencode($tanggal) . '&jam=' . urlencode($jam) . '&antrian=' . urlencode($antrian));
} else {
    header('Location: ../../pages/user/dashboarduser.php?error=' . urlencode('Gagal memproses pendaftaran.'));
}
exit;
?>