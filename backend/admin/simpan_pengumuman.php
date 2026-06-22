<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/Signin.php');
    exit;
}

$conn = require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $judul_raw = trim($_POST['judul']);
    $target_raw = trim($_POST['target']);
    $konten_raw = trim($_POST['konten']);

    $judul = mysqli_real_escape_string($conn, $judul_raw);
    $target = mysqli_real_escape_string($conn, $target_raw);
    $konten = mysqli_real_escape_string($conn, $konten_raw);

    if (empty($judul) || empty($konten) || empty($target)) {
        header('Location: ../../pages/admin/dashboard.php?panel=pengumuman&error=Semua+field+harus+diisi');
        exit;
    }

    if ($id > 0) {
        $query = "UPDATE pengumuman SET judul='$judul', target='$target', konten='$konten' WHERE id=$id";
        $success_msg = "Pengumuman berhasil diperbarui";
        $judul_log = "Edit Pengumuman";
        $desk_log = mysqli_real_escape_string($conn, "Pengumuman '$judul_raw' diperbarui oleh admin");
    } else {
        $query = "INSERT INTO pengumuman (judul, target, konten) VALUES ('$judul', '$target', '$konten')";
        $success_msg = "Pengumuman berhasil disimpan";
        $judul_log = "Tambah Pengumuman";
        $desk_log = mysqli_real_escape_string($conn, "Pengumuman baru: '$judul_raw' untuk target '$target_raw'");
    }

    if (mysqli_query($conn, $query)) {
        // Catat ke log aktivitas
        $user_id = (int)$_SESSION['user_id'];
        mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, tipe, judul, deskripsi, referensi_tabel) VALUES ($user_id, 'Pengumuman', '$judul_log', '$desk_log', 'pengumuman')");

        header('Location: ../../pages/admin/dashboard.php?panel=pengumuman&success=' . urlencode($success_msg));
    } else {
        header('Location: ../../pages/admin/dashboard.php?panel=pengumuman&error=Gagal+menyimpan+pengumuman');
    }
} else {
    header('Location: ../../pages/admin/dashboard.php?panel=pengumuman');
}
mysqli_close($conn);
