<?php
session_start();
require 'koneksi.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$consultation_id = (int)($_GET['consultation_id'] ?? 0);
if (!$consultation_id) {
    echo json_encode(['status' => 'error', 'message' => 'Consultation ID missing']);
    exit;
}

$q = mysqli_query($conn, "
    SELECT m.*,
        CASE
            WHEN m.sender_type = 'Dokter' THEN d.nama
            WHEN m.sender_type = 'Pasien' THEN p.nama
            ELSE 'Sistem'
        END AS sender_name
    FROM messages m
    LEFT JOIN dokter d ON m.sender_type = 'Dokter' AND m.sender_id = d.id
    LEFT JOIN pasien p ON m.sender_type = 'Pasien' AND m.sender_id = p.id
    WHERE m.consultation_id = $consultation_id
    ORDER BY m.created_at ASC
");

$messages = [];
while ($row = mysqli_fetch_assoc($q)) {
    $row['time'] = date('H:i', strtotime($row['created_at']));
    $messages[]  = $row;
}

echo json_encode(['status' => 'success', 'data' => $messages]);
