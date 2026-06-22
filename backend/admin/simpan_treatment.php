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

try {
    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE treatment SET nama=?,kategori=?,durasi=?,deskripsi=?,deskripsi_panjang=?,gambar_url=?,link_halaman=?,urutan=?,status=? WHERE id=?");
        $ok = $stmt->execute([$nama,$kategori,$durasi,$deskripsi,$deskripsi_panjang,$gambar_url,$link_halaman,$urutan,$status,$id]);
        $msg = 'Treatment berhasil diperbarui.';
        $judul = 'Treatment Diperbarui'; $desk = "$nama diperbarui.";
    } else {
        $stmt = $conn->prepare("INSERT INTO treatment (nama,kategori,durasi,deskripsi,deskripsi_panjang,gambar_url,link_halaman,urutan,status) VALUES (?,?,?,?,?,?,?,?,?)");
        $ok = $stmt->execute([$nama,$kategori,$durasi,$deskripsi,$deskripsi_panjang,$gambar_url,$link_halaman,$urutan,$status]);
        $msg = 'Treatment baru berhasil ditambahkan.';
        $judul = 'Treatment Baru'; $desk = "$nama ditambahkan.";
    }

    if ($ok) {
        $uid  = (int)$_SESSION['user_id'];
        $tipe = 'Treatment';
        $log  = $conn->prepare("INSERT INTO log_aktivitas (user_id,tipe,judul,deskripsi,referensi_tabel) VALUES (?,?,?,?,'treatment')");
        $log->execute([$uid, $tipe, $judul, $desk]);
    }
} catch (Exception $e) {
    $ok = false;
    $msg = $e->getMessage();
}

$param = $ok ? 'success='.urlencode($msg) : 'error='.urlencode($msg);
header("Location: ../../pages/admin/dashboard.php?panel=treatment&$param");
exit;