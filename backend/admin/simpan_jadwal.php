<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/Signin.php'); exit;
}
$conn = require '../config/koneksi.php';

$id           = (int)($_POST['id'] ?? 0);
$dokter_id    = (int)($_POST['dokter_id'] ?? 0);
$hari         = $_POST['hari'] ?? '';
$jam_mulai    = $_POST['jam_mulai'] ?? '09:00';
$jam_selesai  = $_POST['jam_selesai'] ?? '17:00';
$max_pasien   = (int)($_POST['max_pasien'] ?? 8);
$treatment_id = $_POST['treatment_id'] !== '' ? (int)$_POST['treatment_id'] : null;
$status       = $_POST['status'] ?? 'Aktif';

if ($dokter_id <= 0 || $hari === '') {
    header('Location: ../../pages/admin/dashboard.php?panel=jadwal&error='.urlencode('Dokter dan hari wajib dipilih.')); exit;
}

if ($id > 0) {
    $stmt = mysqli_prepare($conn,
        "UPDATE jadwal_dokter SET dokter_id=?,hari=?,jam_mulai=?,jam_selesai=?,max_pasien=?,treatment_id=?,status=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'isssissi',
        $dokter_id,$hari,$jam_mulai,$jam_selesai,$max_pasien,$treatment_id,$status,$id);
    $ok  = mysqli_stmt_execute($stmt);
    $msg = $ok ? 'Jadwal berhasil diperbarui.' : mysqli_error($conn);
    $judul = 'Jadwal Diperbarui'; $desk = "Jadwal $hari diperbarui.";
} else {
    $stmt = mysqli_prepare($conn,
        "INSERT INTO jadwal_dokter (dokter_id,hari,jam_mulai,jam_selesai,max_pasien,treatment_id,status) VALUES (?,?,?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, 'isssiss',
        $dokter_id,$hari,$jam_mulai,$jam_selesai,$max_pasien,$treatment_id,$status);
    $ok  = mysqli_stmt_execute($stmt);
    $msg = $ok ? 'Jadwal baru berhasil ditambahkan.' : mysqli_error($conn);
    $judul = 'Jadwal Baru'; $desk = "Jadwal $hari ditambahkan.";
}

if ($ok) {
    $uid  = (int)$_SESSION['user_id'];
    $tipe = 'Jadwal';
    $log  = mysqli_prepare($conn, "INSERT INTO log_aktivitas (user_id,tipe,judul,deskripsi,referensi_tabel) VALUES (?,?,?,?,'jadwal_dokter')");
    mysqli_stmt_bind_param($log, 'isss', $uid, $tipe, $judul, $desk);
    mysqli_stmt_execute($log);
}

$param = $ok ? 'success='.urlencode($msg) : 'error='.urlencode($msg);
header("Location: ../../pages/admin/dashboard.php?panel=jadwal&$param");
exit;