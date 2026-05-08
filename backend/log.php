<?php
// backend/login.php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/auth/Signin.php');
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    header('Location: ../pages/auth/Signin.php?error=' . urlencode('Email dan password harus diisi.'));
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../pages/auth/Signin.php?error=' . urlencode('Email tidak valid.'));
    exit;
}

$email = mysqli_real_escape_string($conn, $email);

$query = "SELECT id, username, password, role FROM users WHERE email = '$email' LIMIT 1";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) === 1) {
    $user = mysqli_fetch_assoc($result);

    if (password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email']    = $email;
        $_SESSION['role']     = $user['role'];

        // Arahkan sesuai role
        switch ($user['role']) {
            case 'dokter':
                header('Location: ../pages/dokter/dashboard.php');
                break;
            case 'admin':
                header('Location: ../pages/admin/dashboard.php');
                break;
            default:
                header('Location: ../pages/user/dashboarduser.php');
        }
        exit;
    }
}

header('Location: ../pages/auth/Signin.php?error=' . urlencode('Email atau password salah.'));
exit;
?>