<?php
session_start();
$conn = require_once '../config/koneksi.php';
$r = $conn->query("SELECT NOW() AS n")->fetch();
$_SESSION['last_view_log_time'] = $r['n'];
header('Content-Type: application/json');
echo json_encode(['success' => true]);
exit;