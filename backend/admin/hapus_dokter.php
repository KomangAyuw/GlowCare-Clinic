<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/Signin.php'); exit;
}
$conn = require '../config/koneksi.php';

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: ../../pages/admin/dashboard.php?panel=dokter&error='.urlencode('ID tidak valid.')); exit;
}

try {
    $stmtRow = $conn->prepare("SELECT nama FROM dokter WHERE id = ?");
    $stmtRow->execute([$id]);
    $row = $stmtRow->fetch();
    $nama = $row['nama'] ?? 'Tidak diketahui';

    $stmt = $conn->prepare("DELETE FROM dokter WHERE id = ?");
    $ok = $stmt->execute([$id]);

    if ($ok) {
        $uid  = (int)$_SESSION['user_id'];
        $tipe = 'Dokter';
        $judul = 'Dokter Dihapus';
        $desk  = "$nama dihapus oleh admin.";
        $log  = $conn->prepare("INSERT INTO log_aktivitas (user_id,tipe,judul,deskripsi,referensi_tabel) VALUES (?,?,?,?,'dokter')");
        $log->execute([$uid, $tipe, $judul, $desk]);
        $param = 'success='.urlencode("Dokter $nama berhasil dihapus.");
    } else {
        $param = 'error='.urlencode('Gagal menghapus dokter. Mungkin ada jadwal atau appointment terkait.');
    }
} catch (Exception $e) {
    $param = 'error='.urlencode('Gagal menghapus dokter: ' . $e->getMessage());
}

header("Location: ../../pages/admin/dashboard.php?panel=dokter&$param");
exit;