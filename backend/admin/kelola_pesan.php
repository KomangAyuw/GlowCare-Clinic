<?php
// backend/admin/kelola_pesan.php
// Tandai baca | hapus | balas pesan — dipanggil dari dashboard admin

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/Signin.php'); exit;
}
$conn = require '../koneksi.php';

// Pastikan kolom balasan ada (auto-migrate, aman dipanggil berkali-kali)
@mysqli_query($conn, "ALTER TABLE `pesan_kontak` ADD COLUMN `balasan` TEXT NULL AFTER `sudah_baca`");
@mysqli_query($conn, "ALTER TABLE `pesan_kontak` ADD COLUMN `dibalas_at` TIMESTAMP NULL AFTER `balasan`");
@mysqli_query($conn, "ALTER TABLE `pesan_kontak` ADD COLUMN `dibalas_oleh` VARCHAR(100) NULL AFTER `dibalas_at`");

$aksi = $_POST['aksi'] ?? '';
$id   = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    header('Location: ../../pages/admin/dashboard.php?panel=pesan&error=' . urlencode('ID tidak valid.')); exit;
}

// Ambil data pesan
$qPesan = mysqli_prepare($conn, "SELECT * FROM pesan_kontak WHERE id=? LIMIT 1");
mysqli_stmt_bind_param($qPesan, 'i', $id);
mysqli_stmt_execute($qPesan);
$pesan = mysqli_fetch_assoc(mysqli_stmt_get_result($qPesan));

if (!$pesan) {
    header('Location: ../../pages/admin/dashboard.php?panel=pesan&error=' . urlencode('Pesan tidak ditemukan.')); exit;
}

if ($aksi === 'baca') {
    $stmt = mysqli_prepare($conn, "UPDATE pesan_kontak SET sudah_baca=1 WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    $ok  = mysqli_stmt_execute($stmt);
    $msg = $ok ? 'Pesan ditandai sudah dibaca.' : 'Gagal memperbarui status.';
    $p   = $ok ? 'success=' . urlencode($msg) : 'error=' . urlencode($msg);

} elseif ($aksi === 'balas') {
    $teks_balasan = trim($_POST['balasan'] ?? '');
    if ($teks_balasan === '') {
        header('Location: ../../pages/admin/dashboard.php?panel=pesan&error=' . urlencode('Isi balasan tidak boleh kosong.')); exit;
    }

    $admin_name = $_SESSION['username'] ?? 'Admin';
    $now        = date('Y-m-d H:i:s');

    $stmt = mysqli_prepare($conn, "UPDATE pesan_kontak SET sudah_baca=1, balasan=?, dibalas_at=?, dibalas_oleh=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'sssi', $teks_balasan, $now, $admin_name, $id);
    $ok = mysqli_stmt_execute($stmt);

    // Kirim email (jika server mendukung mail())
    if ($ok && !empty($pesan['email'])) {
        $to      = $pesan['email'];
        $subject = '=?UTF-8?B?' . base64_encode('Balasan dari GlowCare Clinic') . '?=';
        $body    = "Halo " . $pesan['nama'] . ",\r\n\r\n"
                 . "Terima kasih telah menghubungi GlowCare Clinic. Berikut balasan kami:\r\n\r\n"
                 . "---\r\n"
                 . $teks_balasan . "\r\n"
                 . "---\r\n\r\n"
                 . "Pesan Anda sebelumnya:\r\n\"" . $pesan['pesan'] . "\"\r\n\r\n"
                 . "Salam,\r\nTim GlowCare Clinic\r\n";
        $headers = "From: noreply@glowcareclinic.com\r\nContent-Type: text/plain; charset=UTF-8";
        @mail($to, $subject, $body, $headers);
    }

    $msg = $ok ? 'Balasan berhasil dikirim.' : 'Gagal menyimpan balasan.';
    $p   = $ok ? 'success=' . urlencode($msg) : 'error=' . urlencode($msg);

} elseif ($aksi === 'hapus') {
    $stmt = mysqli_prepare($conn, "DELETE FROM pesan_kontak WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    $ok  = mysqli_stmt_execute($stmt);
    $msg = $ok ? 'Pesan berhasil dihapus.' : 'Gagal menghapus pesan.';
    $p   = $ok ? 'success=' . urlencode($msg) : 'error=' . urlencode($msg);

} else {
    $p = 'error=' . urlencode('Aksi tidak dikenali.');
}

header("Location: ../../pages/admin/dashboard.php?panel=pesan&$p");
exit;