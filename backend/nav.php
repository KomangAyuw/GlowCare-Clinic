<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<header>
    <a href="/GlowCare-Clinic/index.php" class="logo" style="text-decoration:none;">GlowCare Clinic</a>
    <nav>
        <a href="/GlowCare-Clinic/index.php">Beranda</a>
        <a href="/GlowCare-Clinic/tentang.php">Tentang kami</a>
        <a href="/GlowCare-Clinic/treatment.php">Treatment</a>
        <a href="/GlowCare-Clinic/spesialis.php">Spesialis</a>
        <a href="/GlowCare-Clinic/jadwal.php">Jadwal</a>
        <a href="/GlowCare-Clinic/kontak.php">Kontak</a>

        <?php if (isset($_SESSION['username'])): 
            // Ambil huruf pertama dan jadikan kapital
            $initial = strtoupper(substr($_SESSION['username'], 0, 1)); 
        ?>

            <div class="profile-container">
                <?php
                $dashboard_link = "pages/user/dashboarduser.php";
                if (isset($_SESSION['role'])) {
                    if ($_SESSION['role'] === 'admin') {
                        $dashboard_link = "pages/admin/dashboard.php";
                    } elseif ($_SESSION['role'] === 'dokter') {
                        $dashboard_link = "pages/dokter/dashboardDokter.php";
                    }
                }
                ?>
                <a href="<?php echo $dashboard_link; ?>" class="profile-link" title="<?php echo htmlspecialchars($_SESSION['username']); ?>">
                    <div class="user-avatar">
                        <?php echo $initial; ?>
                    </div>
                </a>
            </div>
            
        <?php else: ?>
            <a href="pages/auth/SignUp.php" class="btn">Sign Up</a>
        <?php endif; ?>
    </nav>
</header>