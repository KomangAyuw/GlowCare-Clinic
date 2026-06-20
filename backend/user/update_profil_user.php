<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../pages/auth/Signin.php');
    exit;
}

$conn = require '../koneksi.php';

$user_id = (int)$_SESSION['user_id'];

// Ambil ID pasien
$qPasien = mysqli_query($conn, "SELECT id FROM pasien WHERE user_id = $user_id LIMIT 1");
if (!$qPasien || mysqli_num_rows($qPasien) === 0) {
    header('Location: ../../pages/user/dashboarduser.php?page=akun&error=' . urlencode('Profil pasien tidak ditemukan.'));
    exit;
}
$pasien = mysqli_fetch_assoc($qPasien);
$pasien_id = (int)$pasien['id'];

// Check which form was submitted (Password update vs Profile update)
if (isset($_POST['password_lama'])) {
    // Form update password
    $password_lama = $_POST['password_lama'] ?? '';
    $password_baru = $_POST['password_baru'] ?? '';
    $konfirmasi    = $_POST['konfirmasi'] ?? '';

    if ($password_lama === '' || $password_baru === '' || $konfirmasi === '') {
        header('Location: ../../pages/user/dashboarduser.php?page=akun&error=' . urlencode('Semua kolom password wajib diisi.'));
        exit;
    }

    if ($password_baru !== $konfirmasi) {
        header('Location: ../../pages/user/dashboarduser.php?page=akun&error=' . urlencode('Password baru tidak cocok dengan konfirmasi.'));
        exit;
    }

    if (strlen($password_baru) < 8) {
        header('Location: ../../pages/user/dashboarduser.php?page=akun&error=' . urlencode('Password baru minimal 8 karakter.'));
        exit;
    }

    $qUser = mysqli_query($conn, "SELECT password FROM users WHERE id = $user_id LIMIT 1");
    $user = mysqli_fetch_assoc($qUser);

    if (!password_verify($password_lama, $user['password'])) {
        header('Location: ../../pages/user/dashboarduser.php?page=akun&error=' . urlencode('Password lama salah.'));
        exit;
    }

    $hash = password_hash($password_baru, PASSWORD_DEFAULT);
    $ok = mysqli_query($conn, "UPDATE users SET password = '" . mysqli_real_escape_string($conn, $hash) . "' WHERE id = $user_id");

    if ($ok) {
        header('Location: ../../pages/user/dashboarduser.php?page=akun&success=' . urlencode('Password berhasil diperbarui.'));
    } else {
        header('Location: ../../pages/user/dashboarduser.php?page=akun&error=' . urlencode('Gagal memperbarui password.'));
    }
} else {
    // Check which form is submitted
    $action = $_POST['action'] ?? '';

    if ($action === 'update_kontak') {
        $telepon = trim($_POST['telepon'] ?? '');

        if ($telepon === '') {
            header('Location: ../../pages/user/dashboarduser.php?page=akun&error=' . urlencode('Nomor telepon / WhatsApp wajib diisi.'));
            exit;
        }

        $stmt = mysqli_prepare($conn, "UPDATE pasien SET telepon=?, no_telp=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'ssi', $telepon, $telepon, $pasien_id);
        $ok = mysqli_stmt_execute($stmt);

        if ($ok) {
            header('Location: ../../pages/user/dashboarduser.php?page=akun&success=' . urlencode('Kontak berhasil diperbarui.'));
        } else {
            header('Location: ../../pages/user/dashboarduser.php?page=akun&error=' . urlencode('Gagal memperbarui kontak: ' . mysqli_error($conn)));
        }
    } else {
        // Default: update_diri
        $nama          = trim($_POST['nama'] ?? '');
        $tanggal_lahir = $_POST['tanggal_lahir'] ?? null;
        $jenis_kelamin = $_POST['jenis_kelamin'] ?? 'Perempuan';
        $alamat        = trim($_POST['alamat'] ?? '');

        if ($nama === '' || empty($tanggal_lahir) || $jenis_kelamin === '' || $alamat === '') {
            header('Location: ../../pages/user/dashboarduser.php?page=akun&error=' . urlencode('Semua data diri wajib diisi (Nama, Tanggal Lahir, Jenis Kelamin, dan Alamat).'));
            exit;
        }

        $usia = null;
        if (!empty($tanggal_lahir)) {
            $usia = date('Y') - date('Y', strtotime($tanggal_lahir));
        }

        $stmt = mysqli_prepare($conn, "UPDATE pasien SET nama=?, tanggal_lahir=?, usia=?, jenis_kelamin=?, alamat=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'ssissi', $nama, $tanggal_lahir, $usia, $jenis_kelamin, $alamat, $pasien_id);
        $ok = mysqli_stmt_execute($stmt);

        if ($ok) {
            // Update username di session
            $_SESSION['username'] = $nama;
            header('Location: ../../pages/user/dashboarduser.php?page=akun&success=' . urlencode('Profil berhasil diperbarui.'));
        } else {
            header('Location: ../../pages/user/dashboarduser.php?page=akun&error=' . urlencode('Gagal memperbarui profil: ' . mysqli_error($conn)));
        }
    }
}
exit;
?>
