<?php
$conn = require 'koneksi.php';

// Create consultations table
$sql1 = "CREATE TABLE IF NOT EXISTS `consultations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `appointment_id` INT NOT NULL,
  `pasien_id` INT NOT NULL,
  `dokter_id` INT NOT NULL,
  `status` VARCHAR(50) DEFAULT 'ONGOING',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `closed_at` TIMESTAMP NULL,
  FOREIGN KEY (`appointment_id`) REFERENCES `appointment` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`pasien_id`) REFERENCES `pasien` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`dokter_id`) REFERENCES `dokter` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

// Create messages table
$sql2 = "CREATE TABLE IF NOT EXISTS `messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `consultation_id` INT NOT NULL,
  `sender_type` VARCHAR(50) NOT NULL COMMENT 'pasien or dokter',
  `sender_id` INT NOT NULL,
  `message` TEXT NULL,
  `image_url` VARCHAR(255) NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`consultation_id`) REFERENCES `consultations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if (mysqli_query($conn, $sql1)) {
    echo "Table 'consultations' created or already exists.<br>";
} else {
    echo "Error creating 'consultations': " . mysqli_error($conn) . "<br>";
}

if (mysqli_query($conn, $sql2)) {
    echo "Table 'messages' created or already exists.<br>";
} else {
    echo "Error creating 'messages': " . mysqli_error($conn) . "<br>";
}

echo "Database update complete.";
?>
