<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/Signin.php'); exit;
}
$conn = require '../config/koneksi.php';

$lama      = $_POST['password_lama'] ?? '';
$baru      = $_POST['password_baru'] ?? '';
$konfirmasi= $_POST['konfirmasi'] ?? '';

if ($baru !== $konfirmasi) {
    header('Location: ../../pages/admin/dashboard.php?panel=profil&error='.urlencode('Password baru dan konfirmasi tidak cocok.')); exit;
}
if (strlen($baru) < 8) {
    header('Location: ../../pages/admin/dashboard.php?panel=profil&error='.urlencode('Password minimal 8 karakter.')); exit;
}

$uid  = (int)$_SESSION['user_id'];
try {
    $stmtUser = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmtUser->execute([$uid]);
    $user = $stmtUser->fetch();

    if (!$user || !password_verify($lama, $user['password'])) {
        header('Location: ../../pages/admin/dashboard.php?panel=profil&error='.urlencode('Password lama salah.')); exit;
    }

    $hash = password_hash($baru, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
    $ok = $stmt->execute([$hash, $uid]);

    if ($ok) {
        $tipe = 'Sistem'; $judul = 'Password Diubah'; $desk = "Admin mengubah password akun.";
        $log  = $conn->prepare("INSERT INTO log_aktivitas (user_id,tipe,judul,deskripsi) VALUES (?,?,?,?)");
        $log->execute([$uid, $tipe, $judul, $desk]);
        $param = 'success='.urlencode('Password berhasil diubah.');
    } else {
        $param = 'error='.urlencode('Gagal mengubah password.');
    }
} catch (Exception $e) {
    $param = 'error='.urlencode('Gagal mengubah password: ' . $e->getMessage());
}

header("Location: ../../pages/admin/dashboard.php?panel=profil&$param");
exit;