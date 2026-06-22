<?php
// backend/admin/kelola_pesan.php
// Tandai baca | hapus | balas pesan — dipanggil dari dashboard admin

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/Signin.php'); exit;
}
$conn = require '../config/koneksi.php';

// Pastikan kolom balasan ada (auto-migrate, aman dipanggil berkali-kali)
try {
    $_db = $conn->query("SELECT DATABASE() AS db")->fetch()['db'];
    $_cols_exist = [];
    $_chk = $conn->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = :db AND TABLE_NAME='pesan_kontak'
        AND COLUMN_NAME IN ('balasan','dibalas_at','dibalas_oleh')");
    $_chk->execute(['db' => $_db]);
    while ($_r = $_chk->fetch()) $_cols_exist[] = $_r['COLUMN_NAME'];
    if (!in_array('balasan',    $_cols_exist)) $conn->exec("ALTER TABLE `pesan_kontak` ADD COLUMN `balasan` TEXT NULL AFTER `sudah_baca`");
    if (!in_array('dibalas_at', $_cols_exist)) $conn->exec("ALTER TABLE `pesan_kontak` ADD COLUMN `dibalas_at` TIMESTAMP NULL AFTER `balasan`");
    if (!in_array('dibalas_oleh',$_cols_exist)) $conn->exec("ALTER TABLE `pesan_kontak` ADD COLUMN `dibalas_oleh` VARCHAR(100) NULL AFTER `dibalas_at`");
    unset($_db, $_cols_exist, $_chk, $_r);
} catch (Exception $e) {
    // Abaikan error migrasi jika kolom/tabel belum siap
}

$aksi = $_POST['aksi'] ?? '';
$id   = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    header('Location: ../../pages/admin/dashboard.php?panel=pesan&error=' . urlencode('ID tidak valid.')); exit;
}

try {
    // Ambil data pesan
    $qPesan = $conn->prepare("SELECT * FROM pesan_kontak WHERE id=? LIMIT 1");
    $qPesan->execute([$id]);
    $pesan = $qPesan->fetch();

    if (!$pesan) {
        header('Location: ../../pages/admin/dashboard.php?panel=pesan&error=' . urlencode('Pesan tidak ditemukan.')); exit;
    }

    if ($aksi === 'baca') {
        $stmt = $conn->prepare("UPDATE pesan_kontak SET sudah_baca=1 WHERE id=?");
        $ok = $stmt->execute([$id]);
        $msg = $ok ? 'Pesan ditandai sudah dibaca.' : 'Gagal memperbarui status.';
        $p   = $ok ? 'success=' . urlencode($msg) : 'error=' . urlencode($msg);

    } elseif ($aksi === 'balas') {
        $teks_balasan = trim($_POST['balasan'] ?? '');
        if ($teks_balasan === '') {
            header('Location: ../../pages/admin/dashboard.php?panel=pesan&error=' . urlencode('Isi balasan tidak boleh kosong.')); exit;
        }

        $admin_name = $_SESSION['username'] ?? 'Admin';
        $now        = date('Y-m-d H:i:s');

        $stmt = $conn->prepare("UPDATE pesan_kontak SET sudah_baca=1, balasan=?, dibalas_at=?, dibalas_oleh=? WHERE id=?");
        $ok = $stmt->execute([$teks_balasan, $now, $admin_name, $id]);

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
        $stmt = $conn->prepare("DELETE FROM pesan_kontak WHERE id=?");
        $ok = $stmt->execute([$id]);
        $msg = $ok ? 'Pesan berhasil dihapus.' : 'Gagal menghapus pesan.';
        $p   = $ok ? 'success=' . urlencode($msg) : 'error=' . urlencode($msg);

    } else {
        $p = 'error=' . urlencode('Aksi tidak dikenali.');
    }
} catch (Exception $e) {
    $p = 'error=' . urlencode('Gagal memproses pesan: ' . $e->getMessage());
}

header("Location: ../../pages/admin/dashboard.php?panel=pesan&$p");
exit;
