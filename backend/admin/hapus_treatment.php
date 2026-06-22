<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/Signin.php'); exit;
}
$conn = require '../config/koneksi.php';

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: ../../pages/admin/dashboard.php?panel=treatment&error='.urlencode('ID tidak valid.')); exit;
}

try {
    $stmtRow = $conn->prepare("SELECT nama FROM treatment WHERE id = ?");
    $stmtRow->execute([$id]);
    $row = $stmtRow->fetch();
    $nama = $row['nama'] ?? 'Tidak diketahui';

    $stmt = $conn->prepare("DELETE FROM treatment WHERE id = ?");
    $ok = $stmt->execute([$id]);

    if ($ok) {
        $uid  = (int)$_SESSION['user_id'];
        $tipe = 'Treatment'; $judul = 'Treatment Dihapus'; $desk = "$nama dihapus oleh admin.";
        $log  = $conn->prepare("INSERT INTO log_aktivitas (user_id,tipe,judul,deskripsi,referensi_tabel) VALUES (?,?,?,?,'treatment')");
        $log->execute([$uid, $tipe, $judul, $desk]);
        $param = 'success='.urlencode("Treatment $nama berhasil dihapus.");
    } else {
        $param = 'error='.urlencode('Gagal menghapus treatment.');
    }
} catch (Exception $e) {
    $param = 'error='.urlencode('Gagal menghapus treatment: ' . $e->getMessage());
}

header("Location: ../../pages/admin/dashboard.php?panel=treatment&$param");
exit;
