<?php
session_start();
$conn = require_once '../../backend/config/koneksi.php';
$q = $conn->query("SELECT * FROM treatment WHERE status='Aktif' ORDER BY urutan ASC");
$treatments = $q->fetchAll();
?>
