<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/Signin.php'); exit;
}
$conn = require '../config/koneksi.php';

$id               = (int)($_POST['id'] ?? 0);
$nama             = trim($_POST['nama'] ?? '');
$kategori         = $_POST['kategori'] ?? 'Other';
$deskripsi        = trim($_POST['deskripsi'] ?? '');
$deskripsi_panjang= trim($_POST['deskripsi_panjang'] ?? '');
$gambar_url       = trim($_POST['gambar_url_lama'] ?? '');

// Check if a new treatment image is uploaded
if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../uploads/treatment/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($file_ext, $allowed)) {
            $filename = 'treatment_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_dir . $filename)) {
                $gambar_url = 'treatment/' . $filename; // Just save the filename (consistent with doctor logic)
            } else {
                header('Location: ../../pages/admin/dashboard.php?panel=treatment&error='.urlencode('Gagal menyimpan file gambar treatment.')); exit;
            }
        } else {
            header('Location: ../../pages/admin/dashboard.php?panel=treatment&error='.urlencode('Format gambar harus JPG, JPEG, PNG, GIF, atau WEBP.')); exit;
        }
    } else {
        header('Location: ../../pages/admin/dashboard.php?panel=treatment&error='.urlencode('Error mengunggah gambar. Code: ' . $_FILES['gambar']['error'])); exit;
    }
}

$link_halaman     = trim($_POST['link_halaman'] ?? '');
$durasi           = trim($_POST['durasi'] ?? '60 Menit');
$urutan           = (int)($_POST['urutan'] ?? 99);
$status           = $_POST['status'] ?? 'Aktif';

if ($nama === '') {
    header('Location: ../../pages/admin/dashboard.php?panel=treatment&error='.urlencode('Nama treatment wajib diisi.')); exit;
}

if ($id > 0) {
    $stmt = mysqli_prepare($conn,
        "UPDATE treatment SET nama=?,kategori=?,deskripsi=?,deskripsi_panjang=?,gambar_url=?,link_halaman=?,urutan=?,status=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'sssssssii',
        $nama,$kategori,$deskripsi,$deskripsi_panjang,$gambar_url,$link_halaman,$urutan,$status,$id);
    // fix: i for urutan, s for status, i for id
    mysqli_stmt_close($stmt);
    $stmt = mysqli_prepare($conn,
        "UPDATE treatment SET nama=?,kategori=?,durasi=?,deskripsi=?,deskripsi_panjang=?,gambar_url=?,link_halaman=?,urutan=?,status=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'sssssssisi',
        $nama,$kategori,$durasi,$deskripsi,$deskripsi_panjang,$gambar_url,$link_halaman,$urutan,$status,$id);
    $ok  = mysqli_stmt_execute($stmt);
    $msg = $ok ? 'Treatment berhasil diperbarui.' : mysqli_error($conn);
    $judul = 'Treatment Diperbarui'; $desk = "$nama diperbarui.";
} else {
    $stmt = mysqli_prepare($conn,
        "INSERT INTO treatment (nama,kategori,durasi,deskripsi,deskripsi_panjang,gambar_url,link_halaman,urutan,status) VALUES (?,?,?,?,?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, 'sssssssis',
        $nama,$kategori,$durasi,$deskripsi,$deskripsi_panjang,$gambar_url,$link_halaman,$urutan,$status);
    $ok  = mysqli_stmt_execute($stmt);
    $msg = $ok ? 'Treatment baru berhasil ditambahkan.' : mysqli_error($conn);
    $judul = 'Treatment Baru'; $desk = "$nama ditambahkan.";
}

if ($ok) {
    $uid  = (int)$_SESSION['user_id'];
    $tipe = 'Treatment';
    $log  = mysqli_prepare($conn, "INSERT INTO log_aktivitas (user_id,tipe,judul,deskripsi,referensi_tabel) VALUES (?,?,?,?,'treatment')");
    mysqli_stmt_bind_param($log, 'isss', $uid, $tipe, $judul, $desk);
    mysqli_stmt_execute($log);
}

$param = $ok ? 'success='.urlencode($msg) : 'error='.urlencode($msg);
header("Location: ../../pages/admin/dashboard.php?panel=treatment&$param");
exit;
