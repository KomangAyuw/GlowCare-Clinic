<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/auth/SignUp.php');
    exit;
}

$username   = trim($_POST['username'] ?? '');
$email      = trim($_POST['email'] ?? '');
$password   = $_POST['password'] ?? '';
$confirm    = $_POST['konfirmasi'] ?? '';

if ($username === '' || $email === '' || $password === '' || $confirm === '') {
    header('Location: ../pages/auth/SignUp.php?error=' . urlencode('Semua field harus diisi.'));
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../pages/auth/SignUp.php?error=' . urlencode('Email tidak valid.'));
    exit;
}

if (strlen($password) < 8) {
    header('Location: ../pages/auth/SignUp.php?error=' . urlencode('Password minimal 8 karakter.'));
    exit;
}

if ($password !== $confirm) {
    header('Location: ../pages/auth/SignUp.php?error=' . urlencode('Password dan konfirmasi tidak cocok.'));
    exit;
}

// membuat tabel users jika belum ada
$createTable = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
mysqli_query($conn, $createTable);

$email = mysqli_real_escape_string($conn, $email);
$username = mysqli_real_escape_string($conn, $username);

$checkEmail = "SELECT id FROM users WHERE email = '$email' LIMIT 1";
$result = mysqli_query($conn, $checkEmail);
if ($result && mysqli_num_rows($result) > 0) {
    header('Location: ../pages/auth/SignUp.php?error=' . urlencode('Email sudah terdaftar.'));
    exit;
}

$hashPassword = password_hash($password, PASSWORD_DEFAULT);
$hashPassword = mysqli_real_escape_string($conn, $hashPassword);
$insertSql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashPassword')";

if (mysqli_query($conn, $insertSql)) {
    header('Location: ../pages/auth/SignIn.php?success=' . urlencode('Registrasi berhasil. Silakan masuk.'));
    exit;
}

$errorMessage = 'Terjadi kesalahan saat registrasi. Silakan coba lagi.';
header('Location: ../pages/auth/SignUp.php?error=' . urlencode($errorMessage));
exit;

