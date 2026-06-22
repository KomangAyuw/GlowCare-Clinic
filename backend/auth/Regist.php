<?php
require '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/auth/SignUp.php');
    exit;
}

$username   = trim($_POST['username'] ?? '');
$email      = trim($_POST['email'] ?? '');
$phone      = trim($_POST['phone'] ?? '');
$password   = $_POST['password'] ?? '';
$confirm    = $_POST['konfirmasi'] ?? '';

if ($username === '' || $email === '' || $phone === '' || $password === '' || $confirm === '') {
    header('Location: ../../pages/auth/SignUp.php?error=' . urlencode('Semua field harus diisi.'));
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../../pages/auth/SignUp.php?error=' . urlencode('Format email tidak valid.'));
    exit;
}

if (strlen($phone) < 11 || strlen($phone) >= 13) {
    header('Location: ../../pages/auth/SignUp.php?error=' . urlencode('Nomor telepon tidak valid (harus 11 atau 12 karakter).'));
    exit;
}

if (strlen($password) !== 8) {
    header('Location: ../../pages/auth/SignUp.php?error=' . urlencode('Password harus terdiri dari 8 karakter.'));
    exit;
}

if ($password !== $confirm) {
    header('Location: ../../pages/auth/SignUp.php?error=' . urlencode('Password dan konfirmasi tidak cocok.'));
    exit;
}

// membuat tabel users jika belum ada
$createTable = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    telepon VARCHAR(50) NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
mysqli_query($conn, $createTable);

$alterTable = "ALTER TABLE users ADD COLUMN IF NOT EXISTS role VARCHAR(50) NOT NULL DEFAULT 'user'";
mysqli_query($conn, $alterTable);

$alterTablePhone = "ALTER TABLE users ADD COLUMN IF NOT EXISTS telepon VARCHAR(50) NULL";
mysqli_query($conn, $alterTablePhone);

$email = mysqli_real_escape_string($conn, $email);
$username = mysqli_real_escape_string($conn, $username);
$phone = mysqli_real_escape_string($conn, $phone);

$checkEmail = "SELECT id FROM users WHERE email = '$email' LIMIT 1";
$result = mysqli_query($conn, $checkEmail);
if ($result && mysqli_num_rows($result) > 0) {
    header('Location: ../../pages/auth/SignUp.php?error=' . urlencode('Email sudah terdaftar.'));
    exit;
}

$hashPassword = password_hash($password, PASSWORD_DEFAULT);
$hashPassword = mysqli_real_escape_string($conn, $hashPassword);
$role = 'user';
$role = mysqli_real_escape_string($conn, $role);
$insertSql = "INSERT INTO users (username, email, telepon, password, role) VALUES ('$username', '$email', '$phone', '$hashPassword', '$role')";

if (mysqli_query($conn, $insertSql)) {
    header('Location: ../../pages/auth/Signin.php?success=' . urlencode('Registrasi berhasil. Silakan masuk.'));
    exit;
}

$errorMessage = 'Terjadi kesalahan saat registrasi. Silakan coba lagi.';
header('Location: ../../pages/auth/SignUp.php?error=' . urlencode($errorMessage));
exit;