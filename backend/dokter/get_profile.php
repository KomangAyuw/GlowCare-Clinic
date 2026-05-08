<?php
// backend/dokter/get_profil.php
require '../koneksi.php';
require '../guard_dokter.php';

$user_id = $_SESSION['user_id'];

$query = "
    SELECT u.username, u.email, d.*
    FROM users u
    LEFT JOIN dokter d ON d.user_id = u.id
    WHERE u.id = $user_id
    LIMIT 1
";
$result = mysqli_query($conn, $query);
$profil = $result ? mysqli_fetch_assoc($result) : null;

// Kembalikan sebagai JSON (untuk fetch dari JS) atau set ke variable
header('Content-Type: application/json');
echo json_encode($profil);
?>