<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['count' => 0]);
    exit;
}

require 'koneksi.php';

$user_id = (int)$_SESSION['user_id'];
$role = strtolower($_SESSION['role'] ?? 'user');

$count = 0;

if ($role === 'user' || $role === 'pasien') {
    // Cek appointment baru yang statusnya berubah (Disetujui, Dibatalkan) 
    $q = mysqli_query($conn, "
        SELECT COUNT(*) as cnt FROM appointment a
        JOIN pasien p ON a.pasien_id = p.id
        WHERE p.user_id = $user_id 
        AND a.status IN ('Disetujui','Dibatalkan','Selesai')
        AND a.updated_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
    ");
    if ($q && $row = mysqli_fetch_assoc($q)) {
        $count += (int)$row['cnt'];
    }

    // Cek pesan chat baru yang belum dibaca
    $q2 = mysqli_query($conn, "
        SELECT COUNT(*) as cnt FROM messages m
        JOIN consultations c ON m.consultation_id = c.id
        JOIN pasien p ON c.pasien_id = p.id
        WHERE p.user_id = $user_id 
        AND m.sender_type = 'Dokter'
        AND m.is_read = 0
    ");
    if ($q2 && $row2 = mysqli_fetch_assoc($q2)) {
        $count += (int)$row2['cnt'];
    }

    // Cek pengumuman baru dalam 24 jam terakhir
    $qp = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM pengumuman WHERE target IN ('Semua', 'Pasien') AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    if ($qp && $rowp = mysqli_fetch_assoc($qp)) {
        $count += (int)$rowp['cnt'];
    }

} elseif ($role === 'dokter') {
    // Cek appointment baru masuk
    $q = mysqli_query($conn, "
        SELECT COUNT(*) as cnt FROM appointment a
        JOIN dokter d ON a.dokter_id = d.id
        WHERE d.user_id = $user_id
        AND a.status = 'Menunggu'
        AND a.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
    ");
    if ($q && $row = mysqli_fetch_assoc($q)) {
        $count += (int)$row['cnt'];
    }

    // Cek pesan chat baru
    $q2 = mysqli_query($conn, "
        SELECT COUNT(*) as cnt FROM messages m
        JOIN consultations c ON m.consultation_id = c.id
        JOIN dokter d ON c.dokter_id = d.id
        WHERE d.user_id = $user_id
        AND m.sender_type = 'Pasien'
        AND m.is_read = 0
    ");
    if ($q2 && $row2 = mysqli_fetch_assoc($q2)) {
        $count += (int)$row2['cnt'];
    }

    // Cek pengumuman baru dalam 24 jam terakhir
    $qp = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM pengumuman WHERE target IN ('Semua', 'Dokter') AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    if ($qp && $rowp = mysqli_fetch_assoc($qp)) {
        $count += (int)$rowp['cnt'];
    }
}

echo json_encode(['count' => $count]);
