<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/Signin.php');
    exit;
}

$conn = require_once '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    $query = "INSERT INTO pengumuman (judul, target, konten) VALUES ('$judul', '$target', '$konten')";
    if (mysqli_query($conn, $query)) {
        // Catat ke log aktivitas
        $judul_log = "Tambah Pengumuman";
        $desk_log = mysqli_real_escape_string($conn, "Pengumuman baru: '$judul_raw' untuk target '$target_raw'");
        mysqli_query($conn, "INSERT INTO log_aktivitas (judul, deskripsi) VALUES ('$judul_log', '$desk_log')");

        header('Location: ../../pages/admin/dashboard.php?panel=pengumuman&success=Pengumuman+berhasil+disimpan');
    } else {
        header('Location: ../../pages/admin/dashboard.php?panel=pengumuman&error=Gagal+menyimpan+pengumuman');
    }
} else {
    header('Location: ../../pages/admin/dashboard.php?panel=pengumuman');
}
mysqli_close($conn);
