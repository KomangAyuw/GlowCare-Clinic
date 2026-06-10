<?php
$conn = require_once __DIR__ . '/koneksi.php';

echo "Updating doctors...\n";
$doctors = [
    1 => 'asset/img/doctor1.png',
    2 => 'asset/img/doctor3.png',
    3 => 'asset/img/doctor2.png'
];
foreach ($doctors as $id => $foto) {
    $stmt = mysqli_prepare($conn, "UPDATE dokter SET foto=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'si', $foto, $id);
    if (mysqli_stmt_execute($stmt)) {
        echo "Updated doctor ID {$id} with {$foto}\n";
    } else {
        echo "Failed to update doctor ID {$id}: " . mysqli_error($conn) . "\n";
    }
}

echo "\nUpdating treatments...\n";
$treatments = [
    5 => 'asset/img/acne_peel.png',
    6 => 'asset/img/Laser.jpg',
    7 => 'asset/img/glow_infusion.png',
    8 => 'asset/img/treatmen3.png',
    9 => 'asset/img/treatment5.png',
    10 => 'asset/img/haircareBody.png',
    11 => 'asset/img/treatment7.png',
    12 => 'asset/img/acneTreatment.png',
    13 => 'asset/img/treatment8.png'
];
foreach ($treatments as $id => $img) {
    $stmt = mysqli_prepare($conn, "UPDATE treatment SET gambar_url=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'si', $img, $id);
    if (mysqli_stmt_execute($stmt)) {
        echo "Updated treatment ID {$id} with {$img}\n";
    } else {
        echo "Failed to update treatment ID {$id}: " . mysqli_error($conn) . "\n";
    }
}

echo "\nUpdate complete!\n";
?>
