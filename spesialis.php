<?php session_start(); require_once 'backend/koneksi.php'; ?>
<!DOCTYPE html><html lang="en" style=""><head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>Meet Our Doctors - GlowCare Clinical Aesthetics</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com" rel="preconnect">
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&amp;family=Playfair+Display:wght@500;600;700&amp;display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "surface-bright": "#fbf9f4",
                        "on-tertiary-fixed-variant": "#41484a",
                        "on-primary-fixed-variant": "#594323",
                        "inverse-on-surface": "#f2f1ec",
                        "surface-container-low": "#f5f3ee",
                        "on-secondary-fixed": "#00201f",
                        "surface-container-high": "#eae8e3",
                        "surface": "#fbf9f4",
                        "on-error": "#ffffff",
                        "surface-container-highest": "#e4e2dd",
                        "secondary": "#386663",
                        "on-secondary": "#ffffff",
                        "on-primary-container": "#654d2d",
                        "primary-fixed-dim": "#e1c198",
                        "surface-dim": "#dbdad5",
                        "on-tertiary-container": "#4b5355",
                        "on-surface-variant": "#4e453c",
                        "surface-container-lowest": "#ffffff",
                        "surface-container": "#f0eee9",
                        "inverse-surface": "#30312e",
                        "secondary-container": "#bbece8",
                        "outline-variant": "#d1c4b8",
                        "primary-fixed": "#ffddb2",
                        "primary": "#735a39",
                        "secondary-fixed-dim": "#a0cfcc",
                        "secondary-fixed": "#bbece8",
                        "on-tertiary-fixed": "#161d1f",
                        "on-tertiary": "#ffffff",
                        "surface-variant": "#e4e2dd",
                        "outline": "#7f756a",
                        "on-primary": "#ffffff",
                        "on-surface": "#1b1c19",
                        "inverse-primary": "#e1c198",
                        "surface-tint": "#735a39",
                        "on-secondary-fixed-variant": "#1e4e4c",
                        "tertiary-fixed-dim": "#c1c8ca",
                        "tertiary": "#586062",
                        "on-primary-fixed": "#291800",
                        "error-container": "#ffdad6",
                        "tertiary-fixed": "#dde4e6",
                        "tertiary-container": "#bfc6c8",
                        "on-background": "#1b1c19",
                        "background": "#fbf9f4",
                        "primary-container": "#e0c097",
                        "on-error-container": "#93000a",
                        "on-secondary-container": "#3e6c69",
                        "error": "#ba1a1a"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "xs": "4px",
                        "margin-desktop": "64px",
                        "margin-mobile": "16px",
                        "sm": "12px",
                        "base": "8px",
                        "gutter": "24px",
                        "lg": "48px",
                        "md": "24px",
                        "xl": "80px"
                    },
                    "fontFamily": {
                        "headline-lg": ["Playfair Display"],
                        "headline-md": ["Playfair Display"],
                        "label-md": ["Inter"],
                        "body-md": ["Inter"],
                        "body-lg": ["Inter"],
                        "body-sm": ["Inter"],
                        "headline-lg-mobile": ["Playfair Display"],
                        "display-lg": ["Playfair Display"],
                        "label-sm": ["Inter"]
                    },
                    "fontSize": {
                        "headline-lg": ["32px", { "lineHeight": "1.3", "fontWeight": "600" }],
                        "headline-md": ["24px", { "lineHeight": "1.4", "fontWeight": "500" }],
                        "label-md": ["14px", { "lineHeight": "1.2", "letterSpacing": "0.05em", "fontWeight": "600" }],
                        "body-md": ["16px", { "lineHeight": "1.5", "fontWeight": "400" }],
                        "body-lg": ["18px", { "lineHeight": "1.6", "fontWeight": "400" }],
                        "body-sm": ["14px", { "lineHeight": "1.5", "fontWeight": "400" }],
                        "headline-lg-mobile": ["24px", { "lineHeight": "1.3", "fontWeight": "600" }],
                        "display-lg": ["48px", { "lineHeight": "1.2", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                        "label-sm": ["12px", { "lineHeight": "1.2", "fontWeight": "500" }]
                    }
                }
            }
        }
    </script>
<style>
        .clinical-shadow {
            box-shadow: 0 10px 30px -10px rgba(115, 90, 57, 0.08);
        }
        .clinical-shadow-hover:hover {
            box-shadow: 0 20px 40px -10px rgba(115, 90, 57, 0.12);
            transform: translateY(-4px);
        }
    </style>
</head>
<body class="bg-surface text-on-surface font-body-md antialiased min-h-screen flex flex-col selection:bg-primary-container selection:text-on-primary-container">
<!-- TopNavBar -->
<header class="bg-surface/80 backdrop-blur-md dark:bg-surface-dim/80 fixed top-0 w-full z-50 shadow-sm dark:shadow-none transition-all duration-300">
<div class="flex justify-between items-center w-full px-margin-mobile md:px-margin-desktop py-base max-w-[1200px] mx-auto">
<a class="font-headline-lg text-headline-lg text-primary inline-flex items-center" href="index.php"><span class="material-symbols-outlined text-primary text-3xl mr-2 align-middle" style="font-variation-settings: &quot;FILL&quot; 1;">spa</span>GlowCare</a>
<nav class="hidden md:flex items-center gap-sm">
<a class="font-label-md text-label-md px-4 py-2 text-on-surface-variant font-medium hover:text-primary hover:bg-primary-container/20 rounded-lg transition-all duration-300 ease-in-out" href="index.php">Home</a>
<a class="font-label-md text-label-md px-4 py-2 text-on-surface-variant font-medium hover:text-primary hover:bg-primary-container/20 rounded-lg transition-all duration-300 ease-in-out" href="about.php">About Us</a>
<a class="font-label-md text-label-md px-4 py-2 text-on-surface-variant font-medium hover:text-primary hover:bg-primary-container/20 rounded-lg transition-all duration-300 ease-in-out" href="treatment.php">Services</a>
<a class="font-label-md text-label-md px-4 py-2 text-primary font-bold border-b-2 border-primary pb-1 hover:bg-primary-container/20 rounded-t-lg transition-all duration-300 ease-in-out" href="spesialis.php">Doctors</a>
<a class="font-label-md text-label-md px-4 py-2 text-on-surface-variant font-medium hover:text-primary hover:bg-primary-container/20 rounded-lg transition-all duration-300 ease-in-out" href="kontak.php">Contact</a>
</nav>
<div class="flex items-center gap-sm">
<?php if (isset($_SESSION['user_id'])): 
    $dashboard_url = 'pages/user/dashboarduser.php';
    if (isset($_SESSION['role'])) {
        if ($_SESSION['role'] === 'admin') {
            $dashboard_url = 'pages/admin/dashboard.php';
        } elseif ($_SESSION['role'] === 'dokter') {
            $dashboard_url = 'pages/dokter/dashboardDokter.php';
        }
    }
?>
    <a href="<?= $dashboard_url ?>" class="font-label-md text-label-md text-primary hover:bg-primary-container/20 px-4 py-2 rounded-lg transition-all duration-300 ease-in-out inline-flex items-center justify-center">Dashboard</a>
    <a href="backend/logout.php" class="font-label-md text-label-md bg-error text-on-error px-6 py-2 rounded-lg hover:opacity-90 shadow-sm transition-all duration-300 ease-in-out inline-flex items-center justify-center">Logout</a>
<?php else: ?>
    <a href="pages/auth/Signin.php" class="font-label-md text-label-md text-primary hover:bg-primary-container/20 px-4 py-2 rounded-lg transition-all duration-300 ease-in-out inline-flex items-center justify-center">Login</a>
    <a href="pages/auth/SignUp.php" class="font-label-md text-label-md bg-primary text-on-primary px-6 py-2 rounded-lg hover:bg-on-primary-fixed-variant shadow-sm transition-all duration-300 ease-in-out inline-flex items-center justify-center">Register</a>
<?php endif; ?>
</div>
</div>
</header>
<main class="flex-grow pt-[100px] pb-xl">
<!-- Header Section -->
<section class="max-w-[1200px] mx-auto px-margin-mobile md:px-margin-desktop py-xl text-center">
<span class="inline-block px-4 py-1 rounded-full bg-primary-container text-on-primary-container font-label-sm text-label-sm mb-6 tracking-widest uppercase">The GlowCare Team</span>
<h1 class="font-display-lg text-display-lg text-primary mb-6">Our Medical Experts</h1>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl mx-auto">
                A team of world-class aesthetic physicians dedicated to artistic precision and clinical excellence.
            </p>
</section>
<!-- Doctor Grid Section (Bento-inspired) -->
<section class="max-w-[1200px] mx-auto px-margin-mobile md:px-margin-desktop pb-xl">
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-gutter">
<?php
$qDokter = mysqli_query($conn, "SELECT * FROM dokter WHERE status='Aktif' ORDER BY nama ASC");
if (mysqli_num_rows($qDokter) > 0) {
    while ($d = mysqli_fetch_assoc($qDokter)) {
        $foto = !empty($d['foto']) ? 'backend/uploads/' . $d['foto'] : 'https://ui-avatars.com/api/?name=' . urlencode($d['nama']) . '&background=064e3b&color=fff&size=500';
?>
<article class="bg-surface-container-lowest rounded-[16px] overflow-hidden clinical-shadow clinical-shadow-hover transition-all duration-300 flex flex-col border border-surface-variant">
<div class="relative h-[350px] overflow-hidden group">
<div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent z-10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
<img alt="Portrait of <?= htmlspecialchars($d['nama']) ?>" class="w-full h-full object-cover object-top transition-transform duration-700 group-hover:scale-105" src="<?= htmlspecialchars($foto) ?>">
</div>
<div class="p-8 flex flex-col flex-grow">
<h2 class="font-headline-md text-headline-md text-primary mb-1"><?= htmlspecialchars($d['nama_lengkap'] ?: $d['nama']) ?></h2>
<p class="font-label-md text-label-md text-secondary mb-4 tracking-wide uppercase"><?= htmlspecialchars($d['spesialisasi']) ?></p>
<p class="font-body-sm text-body-sm text-on-surface-variant mb-6 flex-grow leading-relaxed">
    <?= htmlspecialchars($d['bio'] ?? 'Dedicated to providing exceptional aesthetic care.') ?>
</p>
<a href="pages/user/dashboarduser.php" class="w-full py-3 px-6 border border-secondary text-secondary font-label-md text-label-md rounded-lg hover:bg-secondary hover:text-on-secondary transition-colors duration-300 flex items-center justify-center gap-2 group mt-auto">
    Book Consultation
    <span class="material-symbols-outlined text-[18px] group-hover:translate-x-1 transition-transform" style="font-variation-settings: 'FILL' 0;">arrow_forward</span>
</a>
</div>
</article>
<?php 
    }
} else {
    echo "<p class='text-on-surface-variant col-span-full text-center'>No doctors available at the moment.</p>";
}
?>
</div>
</section>
<!-- Philosophy Section -->
<section class="max-w-[1000px] mx-auto px-margin-mobile md:px-margin-desktop py-xl">
<div class="bg-surface-container-low rounded-2xl p-8 md:p-12 text-center relative overflow-hidden">
<div class="absolute top-0 right-0 p-8 opacity-5">
<span class="material-symbols-outlined text-[120px]" style="font-variation-settings: &quot;FILL&quot; 1;">spa</span>
</div>
<h3 class="font-headline-md text-headline-md text-primary mb-6 relative z-10">Our Clinical Philosophy</h3>
<p class="font-body-lg text-body-lg text-on-surface-variant leading-relaxed relative z-10 max-w-3xl mx-auto">
                    At GlowCare, we believe true aesthetic enhancement requires a delicate balance of medical rigor and artistic vision. Our commitment is rooted in unparalleled patient safety, achieving consistently natural-looking results, and designing personalized care plans that respect the unique anatomy and aspirations of every individual.
                </p>
</div>
</section>
</main>
<!-- Footer -->
<footer class="w-full bg-surface-container-low dark:bg-surface-container-lowest border-t border-outline-variant dark:border-outline flat no shadows">
<div class="w-full px-margin-desktop py-xl flex flex-col md:flex-row justify-between items-start gap-lg max-w-[1200px] mx-auto">
<!-- Brand Logo -->
<a href="index.php" class="font-headline-md text-headline-md text-primary dark:text-primary-fixed-dim">
                GlowCare
            </a>
<!-- Links -->
<ul class="flex flex-col md:flex-row flex-wrap gap-md items-start md:items-center">
<li class=""><a class="text-on-surface-variant dark:text-on-surface-variant hover:text-primary dark:hover:text-primary-fixed-dim transition-colors duration-200 font-label-sm text-label-sm" href="#">Privacy Policy</a></li>
<li class=""><a class="text-on-surface-variant dark:text-on-surface-variant hover:text-primary dark:hover:text-primary-fixed-dim transition-colors duration-200 font-label-sm text-label-sm" href="#">Terms of Service</a></li>
<li class=""><a class="text-on-surface-variant dark:text-on-surface-variant hover:text-primary dark:hover:text-primary-fixed-dim transition-colors duration-200 font-label-sm text-label-sm" href="#">Patient Rights</a></li>
<li class=""><a class="text-on-surface-variant dark:text-on-surface-variant hover:text-primary dark:hover:text-primary-fixed-dim transition-colors duration-200 font-label-sm text-label-sm" href="#">Careers</a></li>
<li class=""><a class="text-on-surface-variant dark:text-on-surface-variant hover:text-primary dark:hover:text-primary-fixed-dim transition-colors duration-200 font-label-sm text-label-sm" href="#">Contact Us</a></li>
</ul>
<!-- Copyright -->
<div class="text-secondary dark:text-secondary-fixed font-body-sm text-body-sm">
                © 2026 GlowCare Aesthetic Clinic. All rights reserved.
            </div>
</div>
</footer>
</body></html>
