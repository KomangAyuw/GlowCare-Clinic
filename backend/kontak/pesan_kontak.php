<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../config/koneksi.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../index.php');
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
    try {
        $stmt = $conn->prepare(
            "INSERT INTO pesan_kontak (nama, telepon, email, pesan)
             VALUES (?, ?, ?, ?)"
        );

        if ($stmt->execute([$nama, $telp, $email, $pesan])) {
            $_SESSION['sukses'] = true;
            $conn = null;
            header('Location: ../../pages/kontak.php');
            exit;
        } else {
            $errors[] = 'Gagal menyimpan pesan.';
        }
    } catch (Exception $e) {
        $errors[] = 'Gagal menyimpan pesan: ' . $e->getMessage();
    }
}

$conn = null;

$_SESSION['errors']    = $errors;
$_SESSION['old_input'] = compact('nama', 'telp', 'email', 'pesan');

header('Location: ../../pages/kontak.php');
exit;