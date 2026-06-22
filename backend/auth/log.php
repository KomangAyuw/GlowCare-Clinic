<?php
$conn = require '../config/koneksi.php';
session_start(); // ← pindah ke atas, sebelum header apapun

$alterTable = "ALTER TABLE users ADD COLUMN IF NOT EXISTS role VARCHAR(50) NOT NULL DEFAULT 'user'";
mysqli_query($conn, $alterTable);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../pages/auth/Signin.php'); // ← 'I' kapital
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    header('Location: ../../pages/auth/Signin.php?error=' . urlencode('Email dan password harus diisi.')); // ← fix
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../../pages/auth/Signin.php?error=' . urlencode('Email tidak valid.')); // ← fix
    exit;
}

$email = mysqli_real_escape_string($conn, $email);

$query  = "SELECT id, username, password, role FROM users WHERE email = '$email' LIMIT 1";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) === 1) {
    $user = mysqli_fetch_assoc($result);

    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email']    = $email;
        $role = $user['role'] ?? 'user';
        $_SESSION['role']     = $role;

        switch ($role) {
            case 'dokter':
                header('Location: ../../pages/dokter/dashboardDokter.php');
                break;
            case 'admin':
                header('Location: ../../pages/admin/dashboard.php');
                break;
            default:
                header('Location: ../../pages/user/dashboarduser.php');
        }
        exit;
    }
}

header('Location: ../../pages/auth/Signin.php?error=' . urlencode('Email atau password salah.')); // ← fix
exit;
?>