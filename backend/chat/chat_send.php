<?php
session_start();
require '../config/koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'pasien';

if ($role === 'dokter') {
    $stmt = $conn->prepare("SELECT id FROM dokter WHERE user_id = :user_id LIMIT 1");
    $stmt->execute(['user_id' => $user_id]);
    $dokter = $stmt->fetch();
    $sender_id = $dokter['id'] ?? 0;
    $sender_type = 'Dokter';
} else {
    $stmt = $conn->prepare("SELECT id FROM pasien WHERE user_id = :user_id LIMIT 1");
    $stmt->execute(['user_id' => $user_id]);
    $pasien = $stmt->fetch();
    $sender_id = $pasien['id'] ?? 0;
    $sender_type = 'Pasien';
}

$consultation_id = (int)($_POST['consultation_id'] ?? 0);
$message = $_POST['message'] ?? '';

if (!$consultation_id) {
    echo json_encode(['status' => 'error', 'message' => 'Consultation ID missing']);
    exit;
}

$image_url = null;

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../../backend/uploads/chat/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (in_array($file_ext, $allowed)) {
        $filename = 'chat_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
            $image_url = 'backend/uploads/chat/' . $filename;
        }
    }
}

if ($message === '' && $image_url === null) {
    echo json_encode(['status' => 'error', 'message' => 'Pesan kosong']);
    exit;
}

$stmtInsert = $conn->prepare("INSERT INTO messages (consultation_id, sender_type, sender_id, message, image_url, created_at) VALUES (:consultation_id, :sender_type, :sender_id, :message, :image_url, NOW())");
$inserted = $stmtInsert->execute([
    'consultation_id' => $consultation_id,
    'sender_type'      => $sender_type,
    'sender_id'        => $sender_id,
    'message'          => $message,
    'image_url'        => $image_url
]);

if ($inserted) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan pesan ke database.']);
}