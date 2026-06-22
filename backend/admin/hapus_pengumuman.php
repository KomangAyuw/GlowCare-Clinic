<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/Signin.php');
    exit;
}

$conn = require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];

    // Ambil info pengumuman untuk log sebelum dihapus
    $qInfo = mysqli_query($conn, "SELECT judul FROM pengumuman WHERE id = $id LIMIT 1");
    $info = mysqli_fetch_assoc($qInfo);
    $judul = $info ? $info['judul'] : '';

    $query = "DELETE FROM pengumuman WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        // Catat ke log aktivitas
        $user_id = (int)$_SESSION['user_id'];
        $judul_log = "Hapus Pengumuman";
        $desk_log = mysqli_real_escape_string($conn, "Pengumuman '$judul' berhasil dihapus");
        mysqli_query($conn, "INSERT INTO log_aktivitas (user_id, tipe, judul, deskripsi, referensi_tabel) VALUES ($user_id, 'Pengumuman', '$judul_log', '$desk_log', 'pengumuman')");

        header('Location: ../../pages/admin/dashboard.php?panel=pengumuman&success=Pengumuman+berhasil+dihapus');
    } else {
        header('Location: ../../pages/admin/dashboard.php?panel=pengumuman&error=Gagal+menghapus+pengumuman');
    }
} else {
    header('Location: ../../pages/admin/dashboard.php?panel=pengumuman');
}
mysqli_close($conn);
