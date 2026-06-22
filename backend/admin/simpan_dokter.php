<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/Signin.php'); exit;
}
$conn = require '../config/koneksi.php';

$id           = (int)($_POST['id'] ?? 0);
$nama         = trim($_POST['nama'] ?? '');
$no_str       = trim($_POST['no_str'] ?? '');
$spesialisasi = $_POST['spesialisasi'] ?? 'Other';
$telepon      = trim($_POST['telepon'] ?? '');
$email        = trim($_POST['email'] ?? '');
$pengalaman   = (int)($_POST['pengalaman'] ?? 0);
$rating       = (float)($_POST['rating'] ?? 5.0);
$status       = $_POST['status'] ?? 'Aktif';
$bio          = trim($_POST['bio'] ?? '');

if ($nama === '') {
    header('Location: ../../pages/admin/dashboard.php?panel=dokter&error='.urlencode('Nama dokter wajib diisi.')); exit;
}
$rating = min(5.0, max(0.0, $rating));

$foto_val = $_POST['current_foto'] ?? '';

// Check if a new photo is uploaded
if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../uploads/dokter/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($file_ext, $allowed)) {
            $filename = 'doctor_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $filename)) {
                $foto_val = 'dokter/' . $filename;
            } else {
                header('Location: ../../pages/admin/dashboard.php?panel=dokter&error='.urlencode('Gagal menyimpan file foto dokter.')); exit;
            }
        } else {
            header('Location: ../../pages/admin/dashboard.php?panel=dokter&error='.urlencode('Format foto harus JPG, JPEG, PNG, GIF, atau WEBP.')); exit;
        }
    } else {
        header('Location: ../../pages/admin/dashboard.php?panel=dokter&error='.urlencode('Error mengunggah foto. Code: ' . $_FILES['foto']['error'])); exit;
    }
}

if (empty($foto_val)) {
    header('Location: ../../pages/admin/dashboard.php?panel=dokter&error='.urlencode('Foto dokter wajib diunggah.')); exit;
}

if ($id > 0) {
    $stmt = mysqli_prepare($conn,
        "UPDATE dokter SET nama=?,no_str=?,spesialisasi=?,telepon=?,email=?,pengalaman=?,rating=?,status=?,bio=?,foto=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'sssssidsssi',
        $nama,$no_str,$spesialisasi,$telepon,$email,$pengalaman,$rating,$status,$bio,$foto_val,$id);
    $ok  = mysqli_stmt_execute($stmt);
    $msg = $ok ? 'Data dokter berhasil diperbarui.' : mysqli_error($conn);
    $judul = 'Dokter Diperbarui';
    $desk  = "$nama diperbarui oleh admin.";
} else {
    $stmt = mysqli_prepare($conn,
        "INSERT INTO dokter (nama,no_str,spesialisasi,telepon,email,pengalaman,rating,status,bio,foto) VALUES (?,?,?,?,?,?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, 'sssssidsss',
        $nama,$no_str,$spesialisasi,$telepon,$email,$pengalaman,$rating,$status,$bio,$foto_val);
    $ok  = mysqli_stmt_execute($stmt);
    $msg = $ok ? 'Dokter baru berhasil ditambahkan.' : mysqli_error($conn);
    $judul = 'Dokter Baru';
    $desk  = "$nama ditambahkan oleh admin.";
}

if ($ok) {
    $uid  = (int)$_SESSION['user_id'];
    $tipe = 'Dokter';
    $log  = mysqli_prepare($conn, "INSERT INTO log_aktivitas (user_id,tipe,judul,deskripsi,referensi_tabel) VALUES (?,?,?,?,'dokter')");
    mysqli_stmt_bind_param($log, 'isss', $uid, $tipe, $judul, $desk);
    mysqli_stmt_execute($log);
}

$param = $ok ? 'success='.urlencode($msg) : 'error='.urlencode($msg);
header("Location: ../../pages/admin/dashboard.php?panel=dokter&$param");
exit;