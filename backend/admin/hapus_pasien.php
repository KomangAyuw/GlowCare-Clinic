<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/Signin.php'); exit;
}
$conn = require '../config/koneksi.php';

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: ../../pages/admin/dashboard.php?panel=pasien&error='.urlencode('ID tidak valid.')); exit;
}

try {
    // Ambil nama dulu untuk log
    $stmtRow = $conn->prepare("SELECT nama FROM pasien WHERE id = ?");
    $stmtRow->execute([$id]);
    $row = $stmtRow->fetch();
    $nama = $row['nama'] ?? 'Tidak diketahui';

    $stmt = $conn->prepare("DELETE FROM pasien WHERE id = ?");
    $ok = $stmt->execute([$id]);

    if ($ok) {
        $uid = (int)$_SESSION['user_id'];
        $judul = 'Pasien Dihapus';
        $desk  = "$nama dihapus oleh admin.";
        $tipe  = 'Pasien';
        $log = $conn->prepare("INSERT INTO log_aktivitas (user_id,tipe,judul,deskripsi,referensi_tabel) VALUES (?,?,?,?,'pasien')");
        $log->execute([$uid,$tipe,$judul,$desk]);
        $param = 'success='.urlencode("Pasien $nama berhasil dihapus.");
    } else {
        $param = 'error='.urlencode('Gagal menghapus pasien. Mungkin ada data terkait (appointment).');
    }
} catch (Exception $e) {
    $param = 'error='.urlencode('Gagal menghapus pasien: ' . $e->getMessage());
}

header("Location: ../../pages/admin/dashboard.php?panel=pasien&$param");
exit;
?>
