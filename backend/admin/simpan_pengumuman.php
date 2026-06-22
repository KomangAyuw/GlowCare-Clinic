<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/Signin.php');
    exit;
}

$conn = require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $judul = trim($_POST['judul'] ?? '');
    $target = trim($_POST['target'] ?? '');
    $konten = trim($_POST['konten'] ?? '');

    if ($judul === '' || $konten === '' || $target === '') {
        header('Location: ../../pages/admin/dashboard.php?panel=pengumuman&error=Semua+field+harus+diisi');
        exit;
    }

    try {
        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE pengumuman SET judul = ?, target = ?, konten = ? WHERE id = ?");
            $ok = $stmt->execute([$judul, $target, $konten, $id]);
            $success_msg = "Pengumuman berhasil diperbarui";
            $judul_log = "Edit Pengumuman";
            $desk_log = "Pengumuman '$judul' diperbarui oleh admin";
        } else {
            $stmt = $conn->prepare("INSERT INTO pengumuman (judul, target, konten) VALUES (?, ?, ?)");
            $ok = $stmt->execute([$judul, $target, $konten]);
            $success_msg = "Pengumuman berhasil disimpan";
            $judul_log = "Tambah Pengumuman";
            $desk_log = "Pengumuman baru: '$judul' untuk target '$target'";
        }

        if ($ok) {
            // Catat ke log aktivitas
            $user_id = (int)$_SESSION['user_id'];
            $log = $conn->prepare("INSERT INTO log_aktivitas (user_id, tipe, judul, deskripsi, referensi_tabel) VALUES (?, 'Pengumuman', ?, ?, 'pengumuman')");
            $log->execute([$user_id, $judul_log, $desk_log]);

            header('Location: ../../pages/admin/dashboard.php?panel=pengumuman&success=' . urlencode($success_msg));
        } else {
            header('Location: ../../pages/admin/dashboard.php?panel=pengumuman&error=Gagal+menyimpan+pengumuman');
        }
    } catch (Exception $e) {
        header('Location: ../../pages/admin/dashboard.php?panel=pengumuman&error=' . urlencode($e->getMessage()));
    }
} else {
    header('Location: ../../pages/admin/dashboard.php?panel=pengumuman');
}
$conn = null;