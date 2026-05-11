<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'koneksi.php';

// DEBUG - hapus setelah selesai
/*echo '<pre>';
echo 'POST data: ';
var_dump($_POST);
echo 'Koneksi: ';
var_dump($conn);
echo '</pre>';
die();*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$errors = [];

$nama  = trim($_POST['nama']  ?? '');
$telp  = trim($_POST['telp']  ?? '');
$email = trim($_POST['email'] ?? '');
$pesan = trim($_POST['pesan'] ?? '');

if (empty($nama)) {
    $errors[] = 'Nama lengkap wajib diisi.';
}
if (empty($telp)) {
    $errors[] = 'Nomor telepon wajib diisi.';
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Email tidak valid.';
}
if (empty($pesan)) {
    $errors[] = 'Pesan wajib diisi.';
}

if (empty($errors)) {
    $stmt = mysqli_prepare($conn,
        "INSERT INTO pesan_kontak (nama, telepon, email, pesan)
         VALUES (?, ?, ?, ?)"
    );

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ssss', $nama, $telp, $email, $pesan);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['sukses'] = true;
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            header('Location: ../index.php#kontak'); // ← sudah benar
            exit;
        } else {
            $errors[] = 'Gagal menyimpan pesan: ' . mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
        }
    } else {
        $errors[] = 'Terjadi kesalahan pada server.';
    }
}

mysqli_close($conn);

$_SESSION['errors']    = $errors;
$_SESSION['old_input'] = compact('nama', 'telp', 'email', 'pesan');

header('Location: ../index.php#kontak'); // ← sudah benar
exit;