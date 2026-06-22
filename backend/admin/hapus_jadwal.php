<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/Signin.php'); exit;
}
$conn = require '../config/koneksi.php';

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: ../../pages/admin/dashboard.php?panel=jadwal&error='.urlencode('ID tidak valid.')); exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM jadwal_dokter WHERE id = ?");
    $ok = $stmt->execute([$id]);

    if ($ok) {
        $uid  = (int)$_SESSION['user_id'];
        $tipe = 'Jadwal'; $judul = 'Jadwal Dihapus'; $desk = "Jadwal ID #$id dihapus oleh admin.";
        $log  = $conn->prepare("INSERT INTO log_aktivitas (user_id,tipe,judul,deskripsi,referensi_tabel) VALUES (?,?,?,?,'jadwal_dokter')");
        $log->execute([$uid, $tipe, $judul, $desk]);
        $param = 'success='.urlencode('Jadwal berhasil dihapus.');
    } else {
        $param = 'error='.urlencode('Gagal menghapus jadwal.');
    }
} catch (Exception $e) {
    $param = 'error='.urlencode('Gagal menghapus jadwal: ' . $e->getMessage());
}

header("Location: ../../pages/admin/dashboard.php?panel=jadwal&$param");
exit;