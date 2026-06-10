<?php
session_start();
$conn = require_once '../koneksi.php';
$r = mysqli_fetch_assoc(mysqli_query($conn, "SELECT NOW() AS n"));
$_SESSION['last_view_log_time'] = $r['n'];
header('Content-Type: application/json');
echo json_encode(['success' => true]);
exit;
