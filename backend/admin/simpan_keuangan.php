<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/Signin.php'); exit;
}
$conn = require '../config/koneksi.php';

$id          = (int)($_POST['id'] ?? 0);
$tanggal     = $_POST['tanggal']    ?? date('Y-m-d');
$jenis       = $_POST['jenis']      ?? 'Pemasukan';
$kategori    = trim($_POST['kategori']    ?? '');
$keterangan  = trim($_POST['keterangan']  ?? '');
$jumlah      = (float)str_replace(['.', ','], ['', '.'], $_POST['jumlah'] ?? 0);
$metode      = trim($_POST['metode']      ?? 'Tunai');
$referensi   = trim($_POST['referensi']   ?? '');
$catatan     = trim($_POST['catatan']     ?? '');
$uid         = (int)$_SESSION['user_id'];

// Validasi wajib
if (!$tanggal || !$kategori || !$keterangan || $jumlah <= 0) {
    header('Location: ../../pages/admin/dashboard.php?panel=keuangan&error=' . urlencode('Semua kolom wajib diisi dan jumlah harus lebih dari 0.'));
    exit;
}

if (!in_array($jenis, ['Pemasukan', 'Pengeluaran'])) $jenis = 'Pemasukan';

if ($id > 0) {
    // UPDATE
    $stmt = mysqli_prepare($conn, "UPDATE keuangan SET tanggal=?,jenis=?,kategori=?,keterangan=?,jumlah=?,metode=?,referensi=?,catatan=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'ssssdsssi', $tanggal, $jenis, $kategori, $keterangan, $jumlah, $metode, $referensi, $catatan, $id);
    $ok     = mysqli_stmt_execute($stmt);
    $aksi   = 'Transaksi Diperbarui';
    $desk   = "$jenis '$keterangan' diperbarui. Jumlah: Rp " . number_format($jumlah, 0, ',', '.');
} else {
    // INSERT
    $stmt = mysqli_prepare($conn, "INSERT INTO keuangan (tanggal,jenis,kategori,keterangan,jumlah,metode,referensi,catatan,dibuat_oleh) VALUES (?,?,?,?,?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, 'ssssdsssi', $tanggal, $jenis, $kategori, $keterangan, $jumlah, $metode, $referensi, $catatan, $uid);
    $ok     = mysqli_stmt_execute($stmt);
    $aksi   = 'Transaksi Dicatat';
    $desk   = "$jenis '$keterangan' sebesar Rp " . number_format($jumlah, 0, ',', '.') . " dicatat.";
}

if ($ok) {
    // Log aktivitas
    $log = mysqli_prepare($conn, "INSERT INTO log_aktivitas (user_id,tipe,judul,deskripsi,referensi_tabel) VALUES (?,'Keuangan',?,?,'keuangan')");
    mysqli_stmt_bind_param($log, 'iss', $uid, $aksi, $desk);
    mysqli_stmt_execute($log);
    header('Location: ../../pages/admin/dashboard.php?panel=keuangan&success=' . urlencode('Transaksi berhasil disimpan.'));
} else {
    header('Location: ../../pages/admin/dashboard.php?panel=keuangan&error=' . urlencode('Gagal menyimpan transaksi: ' . mysqli_error($conn)));
}
exit;
?>
