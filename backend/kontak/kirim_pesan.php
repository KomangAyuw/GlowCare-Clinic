<?php

$conn = require '../config/koneksi.php';

$nama   = trim($_POST['nama']  ?? '');
$telp   = trim($_POST['telp']  ?? '');
$email  = trim($_POST['email'] ?? '');
$pesan  = trim($_POST['pesan'] ?? '');

// Validasi dasar
if ($nama === '' || $pesan === '') {
    header('Location: ../../index.php?error=' . urlencode('Nama dan pesan wajib diisi.'));
    exit;
}

if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../../index.php?error=' . urlencode('Format email tidak valid.'));
    exit;
}

try {
    // Simpan ke tabel pesan_kontak
    $stmt = $conn->prepare("INSERT INTO pesan_kontak (nama, telepon, email, pesan) VALUES (?, ?, ?, ?)");
    $ok = $stmt->execute([$nama, $telp, $email, $pesan]);

    if ($ok) {
        header('Location: ../../index.php?success=' . urlencode('Pesan kamu berhasil terkirim! Kami akan menghubungi kamu segera.'));
    } else {
        header('Location: ../../index.php?error=' . urlencode('Gagal mengirim pesan. Silakan coba lagi.'));
    }
} catch (Exception $e) {
    header('Location: ../../index.php?error=' . urlencode('Gagal mengirim pesan: ' . $e->getMessage()));
}
exit;