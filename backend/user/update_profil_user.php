<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../pages/auth/Signin.php');
    exit;
}

$conn = require '../config/koneksi.php';

$user_id = (int)$_SESSION['user_id'];

// Ambil ID pasien
$qPasien = $conn->prepare("SELECT id FROM pasien WHERE user_id = :user_id LIMIT 1");
$qPasien->execute(['user_id' => $user_id]);
$pasien = $qPasien->fetch();
if (!$pasien) {
    header('Location: ../../pages/user/dashboarduser.php?page=akun&error=' . urlencode('Profil pasien tidak ditemukan.'));
    exit;
}
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

    $qUser = $conn->prepare("SELECT password FROM users WHERE id = :user_id LIMIT 1");
    $qUser->execute(['user_id' => $user_id]);
    $user = $qUser->fetch();

    if (!$user || !password_verify($password_lama, $user['password'])) {
        header('Location: ../../pages/user/dashboarduser.php?page=akun&error=' . urlencode('Password lama salah.'));
        exit;
    }

    $hash = password_hash($password_baru, PASSWORD_DEFAULT);
    $stmtUpdatePass = $conn->prepare("UPDATE users SET password = :password WHERE id = :user_id");
    $ok = $stmtUpdatePass->execute(['password' => $hash, 'user_id' => $user_id]);

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

        $stmt = $conn->prepare("UPDATE pasien SET telepon = :telepon, no_telp = :no_telp WHERE id = :pasien_id");
        $ok = $stmt->execute([
            'telepon'   => $telepon,
            'no_telp'   => $telepon,
            'pasien_id' => $pasien_id
        ]);

        if ($ok) {
            header('Location: ../../pages/user/dashboarduser.php?page=akun&success=' . urlencode('Kontak berhasil diperbarui.'));
        } else {
            header('Location: ../../pages/user/dashboarduser.php?page=akun&error=' . urlencode('Gagal memperbarui kontak.'));
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

        $stmt = $conn->prepare("UPDATE pasien SET nama = :nama, tanggal_lahir = :tanggal_lahir, usia = :usia, jenis_kelamin = :jenis_kelamin, alamat = :alamat WHERE id = :pasien_id");
        $ok = $stmt->execute([
            'nama'          => $nama,
            'tanggal_lahir' => $tanggal_lahir,
            'usia'          => $usia,
            'jenis_kelamin' => $jenis_kelamin,
            'alamat'        => $alamat,
            'pasien_id'     => $pasien_id
        ]);

        if ($ok) {
            // Update username di session
            $_SESSION['username'] = $nama;
            header('Location: ../../pages/user/dashboarduser.php?page=akun&success=' . urlencode('Profil berhasil diperbarui.'));
        } else {
            header('Location: ../../pages/user/dashboarduser.php?page=akun&error=' . urlencode('Gagal memperbarui profil.'));
        }
    }
}
exit;
?>