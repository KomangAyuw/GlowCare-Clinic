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
$conn->exec($createTable);

$alterTable = "ALTER TABLE users ADD COLUMN IF NOT EXISTS role VARCHAR(50) NOT NULL DEFAULT 'user'";
$conn->exec($alterTable);

$alterTablePhone = "ALTER TABLE users ADD COLUMN IF NOT EXISTS telepon VARCHAR(50) NULL";
$conn->exec($alterTablePhone);

$stmtCheck = $conn->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
$stmtCheck->execute(['email' => $email]);
if ($stmtCheck->fetch()) {
    header('Location: ../../pages/auth/SignUp.php?error=' . urlencode('Email sudah terdaftar.'));
    exit;
}

$hashPassword = password_hash($password, PASSWORD_DEFAULT);
$role = 'user';

$stmtInsert = $conn->prepare("INSERT INTO users (username, email, telepon, password, role) VALUES (:username, :email, :telepon, :password, :role)");
$inserted = $stmtInsert->execute([
    'username' => $username,
    'email'    => $email,
    'telepon'  => $phone,
    'password' => $hashPassword,
    'role'     => $role
]);

if ($inserted) {
    header('Location: ../../pages/auth/Signin.php?success=' . urlencode('Registrasi berhasil. Silakan masuk.'));
    exit;
}

$errorMessage = 'Terjadi kesalahan saat registrasi. Silakan coba lagi.';
header('Location: ../../pages/auth/SignUp.php?error=' . urlencode($errorMessage));
exit;