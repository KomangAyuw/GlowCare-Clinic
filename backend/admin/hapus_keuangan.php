<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/Signin.php'); exit;
}
$conn = require '../config/koneksi.php';

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: ../../pages/admin/dashboard.php?panel=keuangan&error=' . urlencode('ID tidak valid.')); exit;
}

try {
    // Ambil info dulu sebelum hapus
    $stmtInfo = $conn->prepare("SELECT jenis,keterangan,jumlah FROM keuangan WHERE id = ?");
    $stmtInfo->execute([$id]);
    $info = $stmtInfo->fetch();

    $stmt = $conn->prepare("DELETE FROM keuangan WHERE id = ?");
    $ok = $stmt->execute([$id]);

    if ($ok && $info) {
        $uid  = (int)$_SESSION['user_id'];
        $desk = "Transaksi '{$info['jenis']} - {$info['keterangan']}' (Rp " . number_format($info['jumlah'], 0, ',', '.') . ") dihapus.";
        $log  = $conn->prepare("INSERT INTO log_aktivitas (user_id,tipe,judul,deskripsi,referensi_tabel) VALUES (?,'Keuangan','Transaksi Dihapus',?,'keuangan')");
        $log->execute([$uid, $desk]);
        header('Location: ../../pages/admin/dashboard.php?panel=keuangan&success=' . urlencode('Transaksi berhasil dihapus.'));
    } else {
        header('Location: ../../pages/admin/dashboard.php?panel=keuangan&error=' . urlencode('Gagal menghapus transaksi.'));
    }
} catch (Exception $e) {
    header('Location: ../../pages/admin/dashboard.php?panel=keuangan&error=' . urlencode('Gagal menghapus transaksi: ' . $e->getMessage()));
}
exit;
?>
