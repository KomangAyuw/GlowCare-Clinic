<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/Signin.php'); exit;
}
$conn = require '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appt_id   = (int)($_POST['appointment_id'] ?? 0);
    $jumlah    = (float)($_POST['jumlah'] ?? 0);
    $metode    = $_POST['metode'] ?? 'Tunai';
    $referensi = trim($_POST['referensi'] ?? '');

    if ($appt_id <= 0 || $jumlah <= 0) {
        header('Location: ../../pages/admin/dashboard.php?panel=appointment&error='.urlencode('Nominal pembayaran harus lebih besar dari 0.')); exit;
    }

    try {
        // 1. Ambil detail appointment untuk log & keuangan
        $stmtInfo = $conn->prepare("
            SELECT a.pasien_id, a.dokter_id, a.treatment_id, p.nama AS nama_pasien, d.nama AS nama_dokter, t.nama AS nama_treatment 
            FROM appointment a 
            JOIN pasien p ON a.pasien_id=p.id 
            JOIN dokter d ON a.dokter_id=d.id 
            LEFT JOIN treatment t ON a.treatment_id=t.id 
            WHERE a.id=? 
            LIMIT 1
        ");
        $stmtInfo->execute([$appt_id]);
        $info = $stmtInfo->fetch();
        if (!$info) {
            header('Location: ../../pages/admin/dashboard.php?panel=appointment&error='.urlencode('Data janji temu tidak ditemukan.')); exit;
        }

        $nama_pasien = $info['nama_pasien'];
        $nama_dokter = $info['nama_dokter'];
        $treatment   = $info['nama_treatment'] ?: 'Konsultasi Umum';
        $kategori    = $info['treatment_id'] !== null ? 'Pendapatan Treatment' : 'Pendapatan Konsultasi';

        // 2. Update status pembayaran ke Lunas & set jumlah riil
        $stmtPay = $conn->prepare("UPDATE pembayaran SET status='Lunas', jumlah=? WHERE appointment_id=?");
        $okPay = $stmtPay->execute([$jumlah, $appt_id]);

        if ($okPay) {
            // 3. Update status appointment ke Selesai secara otomatis
            $stmtAppt = $conn->prepare("UPDATE appointment SET status='Selesai' WHERE id=?");
            $stmtAppt->execute([$appt_id]);

            // 4. Catat transaksi ke Keuangan
            $keterangan = "Pembayaran " . ($info['nama_treatment'] ? "treatment $treatment" : "konsultasi") . " pasien $nama_pasien (dokter $nama_dokter)";
            $uid = (int)$_SESSION['user_id'];
            $stmtKeu = $conn->prepare("INSERT INTO keuangan (tanggal, jenis, kategori, keterangan, jumlah, metode, referensi, dibuat_oleh) VALUES (CURDATE(), 'Pemasukan', ?, ?, ?, ?, ?, ?)");
            $stmtKeu->execute([$kategori, $keterangan, $jumlah, $metode, $referensi, $uid]);

            // 5. Catat ke log aktivitas
            $tipe = 'Sistem'; $judul = 'Pembayaran Lunas'; $desk = "Menerima pembayaran Rp " . number_format($jumlah, 0, ',', '.') . " untuk janji temu $nama_pasien.";
            $log = $conn->prepare("INSERT INTO log_aktivitas (user_id, tipe, judul, deskripsi, referensi_tabel) VALUES (?, ?, ?, ?, 'pembayaran')");
            $log->execute([$uid, $tipe, $judul, $desk]);

            header('Location: ../../pages/admin/dashboard.php?panel=appointment&success='.urlencode('Pembayaran berhasil diproses dan dicatat ke keuangan.'));
        } else {
            header('Location: ../../pages/admin/dashboard.php?panel=appointment&error='.urlencode('Gagal memproses pembayaran.'));
        }
    } catch (Exception $e) {
        header('Location: ../../pages/admin/dashboard.php?panel=appointment&error='.urlencode($e->getMessage()));
    }
} else {
    header('Location: ../../pages/admin/dashboard.php?panel=appointment');
}
exit;
?>
