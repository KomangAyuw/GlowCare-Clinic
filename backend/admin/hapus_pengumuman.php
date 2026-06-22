<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/Signin.php');
    exit;
}

$conn = require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];

    try {
        // Ambil info pengumuman untuk log sebelum dihapus
        $stmtInfo = $conn->prepare("SELECT judul FROM pengumuman WHERE id = ? LIMIT 1");
        $stmtInfo->execute([$id]);
        $info = $stmtInfo->fetch();
        $judul = $info ? $info['judul'] : '';

        $stmt = $conn->prepare("DELETE FROM pengumuman WHERE id = ?");
        $ok = $stmt->execute([$id]);

        if ($ok) {
            // Catat ke log aktivitas
            $user_id = (int)$_SESSION['user_id'];
            $judul_log = "Hapus Pengumuman";
            $desk_log = "Pengumuman '$judul' berhasil dihapus";
            $log = $conn->prepare("INSERT INTO log_aktivitas (user_id, tipe, judul, deskripsi, referensi_tabel) VALUES (?, 'Pengumuman', ?, ?, 'pengumuman')");
            $log->execute([$user_id, $judul_log, $desk_log]);

            header('Location: ../../pages/admin/dashboard.php?panel=pengumuman&success=Pengumuman+berhasil+dihapus');
        } else {
            header('Location: ../../pages/admin/dashboard.php?panel=pengumuman&error=Gagal+menghapus+pengumuman');
        }
    } catch (Exception $e) {
        header('Location: ../../pages/admin/dashboard.php?panel=pengumuman&error=' . urlencode($e->getMessage()));
    }
} else {
    header('Location: ../../pages/admin/dashboard.php?panel=pengumuman');
}
$conn = null;
