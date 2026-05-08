<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<header>
    <div class="logo">GlowCare Clinic</div>
    <nav>
        <a href="#beranda">Beranda</a>
        <a href="#tentang">Tentang kami</a>
        <a href="#services">Treatment</a>
        <a href="#spesialis">Spesialis</a>
        <a href="jadwal.php">Jadwal</a>
        <a href="#kontak">Kontak</a>

        <?php if (isset($_SESSION['username'])): 
            // Ambil huruf pertama dan jadikan kapital
            $initial = strtoupper(substr($_SESSION['username'], 0, 1)); 
        ?>

            <div class="profile-container">
                <a href="pages/user/dashboarduser.php" class="profile-link" title="<?php echo htmlspecialchars($_SESSION['username']); ?>">
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