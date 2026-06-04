<?php require_once 'backend/koneksi.php'; ?>
<!DOCTYPE html><html class="light" lang="en" style=""><head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>Premium Aesthetic Services | GlowCare</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com" rel="preconnect">
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&amp;family=Playfair+Display:wght@500;600;700&amp;display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
<style>
        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined';
            font-weight: normal;
            font-style: normal;
            font-size: 24px;
            line-height: 1;
            letter-spacing: normal;
            text-transform: none;
            display: inline-block;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            -webkit-font-feature-settings: 'liga';
            -webkit-font-smoothing: antialiased;
        }
    </style>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "secondary-container": "#bbece8",
                        "on-surface-variant": "#4e453c",
                        "background": "#fbf9f4",
                        "tertiary-container": "#bfc6c8",
                        "on-background": "#1b1c19",
                        "surface-variant": "#e4e2dd",
                        "on-secondary-container": "#3e6c69",
                        "primary-fixed-dim": "#e1c198",
                        "secondary-fixed": "#bbece8",
                        "on-primary-container": "#654d2d",
                        "surface": "#fbf9f4",
                        "surface-dim": "#dbdad5",
                        "primary-container": "#e0c097",
                        "error": "#ba1a1a",
                        "on-secondary-fixed-variant": "#1e4e4c",
                        "on-error-container": "#93000a",
                        "inverse-on-surface": "#f2f1ec",
                        "tertiary": "#586062",
                        "secondary-fixed-dim": "#a0cfcc",
                        "on-tertiary-container": "#4b5355",
                        "inverse-primary": "#e1c198",
                        "error-container": "#ffdad6",
                        "surface-container": "#f0eee9",
                        "on-tertiary-fixed-variant": "#41484a",
                        "on-primary": "#ffffff",
                        "on-primary-fixed": "#291800",
                        "on-primary-fixed-variant": "#022c22",
                        "outline": "#7f756a",
                        "inverse-surface": "#30312e",
                        "on-error": "#ffffff",
                        "outline-variant": "#a7f3d0",
                        "on-tertiary": "#ffffff",
                        "surface-container-high": "#eae8e3",
                        "surface-bright": "#fbf9f4",
                        "on-tertiary-fixed": "#161d1f",
                        "tertiary-fixed": "#dde4e6",
                        "on-secondary": "#ffffff",
                        "secondary": "#386663",
                        "primary-fixed": "#ffddb2",
                        "tertiary-fixed-dim": "#c1c8ca",
                        "surface-tint": "#064e3b",
                        "surface-container-highest": "#e4e2dd",
                        "on-surface": "#1b1c19",
                        "surface-container-low": "#f5f3ee",
                        "on-secondary-fixed": "#00201f",
                        "primary": "#064e3b",
                        "surface-container-lowest": "#ffffff"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "sm": "12px",
                        "base": "8px",
                        "margin-desktop": "64px",
                        "xl": "80px",
                        "md": "24px",
                        "lg": "48px",
                        "xs": "4px",
                        "margin-mobile": "16px",
                        "gutter": "24px"
                    },
                    "fontFamily": {
                        "body-lg": ["Inter"],
                        "label-md": ["Inter"],
                        "headline-lg-mobile": ["Playfair Display"],
                        "body-sm": ["Inter"],
                        "headline-lg": ["Playfair Display"],
                        "body-md": ["Inter"],
                        "display-lg": ["Playfair Display"],
                        "label-sm": ["Inter"],
                        "headline-md": ["Playfair Display"]
                    },
                    "fontSize": {
                        "body-lg": ["18px", { "lineHeight": "1.6", "fontWeight": "400" }],
                        "label-md": ["14px", { "lineHeight": "1.2", "letterSpacing": "0.05em", "fontWeight": "600" }],
                        "headline-lg-mobile": ["24px", { "lineHeight": "1.3", "fontWeight": "600" }],
                        "body-sm": ["14px", { "lineHeight": "1.5", "fontWeight": "400" }],
                        "headline-lg": ["32px", { "lineHeight": "1.3", "fontWeight": "600" }],
                        "body-md": ["16px", { "lineHeight": "1.5", "fontWeight": "400" }],
                        "display-lg": ["48px", { "lineHeight": "1.2", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                        "label-sm": ["12px", { "lineHeight": "1.2", "fontWeight": "500" }],
                        "headline-md": ["24px", { "lineHeight": "1.4", "fontWeight": "500" }]
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-background text-on-background antialiased selection:bg-primary-container selection:text-on-primary-container">
<!-- Top Navigation Bar -->
<header class="sticky w-full z-50 bg-surface shadow-sm transition-all duration-300 ease-in-out top-0">
<div class="flex justify-between items-center w-full px-margin-mobile md:px-margin-desktop py-4 max-w-[1200px] mx-auto">
<!-- Brand Logo -->
<a class="font-headline-lg text-headline-lg text-primary inline-flex items-center" href="index.php"><span class="material-symbols-outlined text-primary text-3xl mr-2 align-middle" style="font-variation-settings: &quot;FILL&quot; 1;">spa</span>GlowCare</a>
<!-- Navigation Links -->
<nav class="hidden md:flex items-center gap-sm">
<!-- Active Item -->
<a class="font-label-md text-label-md px-4 py-2 text-on-surface-variant font-medium hover:text-primary hover:bg-primary-container/20 rounded-lg transition-all duration-300 ease-in-out" href="index.php">Home</a>
<!-- Inactive Items -->
<a class="font-label-md text-label-md px-4 py-2 text-on-surface-variant font-medium hover:text-primary hover:bg-primary-container/20 rounded-lg transition-all duration-300 ease-in-out" href="about.php">About Us</a>
<a class="font-label-md text-label-md px-4 py-2 text-primary font-bold border-b-2 border-primary pb-1 hover:bg-primary-container/20 rounded-t-lg transition-all duration-300 ease-in-out" href="treatment.php">Services</a>
<a class="font-label-md text-label-md px-4 py-2 text-on-surface-variant font-medium hover:text-primary hover:bg-primary-container/20 rounded-lg transition-all duration-300 ease-in-out" href="spesialis.php">Doctors</a>
<a class="font-label-md text-label-md px-4 py-2 text-on-surface-variant font-medium hover:text-primary hover:bg-primary-container/20 rounded-lg transition-all duration-300 ease-in-out" href="kontak.php">Contact</a>
</nav>
<!-- Actions -->
<div class="flex items-center gap-sm">
<a href="pages/auth/Signin.php" class="font-label-md text-label-md text-primary hover:bg-primary-container/20 px-4 py-2 rounded-lg transition-all duration-300 ease-in-out inline-flex items-center justify-center">Login</a>
<a href="pages/auth/SignUp.php" class="font-label-md text-label-md bg-primary text-on-primary px-6 py-2 rounded-lg hover:bg-on-primary-fixed-variant shadow-sm transition-all duration-300 ease-in-out inline-flex items-center justify-center">Register</a>
</div>
</div>
</header>
<main class="flex-grow">
<!-- Hero Section -->
<section class="relative w-full h-[80vh] min-h-[600px] flex items-center justify-center overflow-hidden">
<div class="absolute inset-0 z-0">
<img alt="Premium Aesthetic Services Hero" class="w-full h-full object-cover object-center" src="https://lh3.googleusercontent.com/aida/AP1WRLvx9aMlJNEa5OJ5ldw2XnCFFOxAzy94gxGdXsSe1lEdli7kKExY3agiZmi7qEW4xBn9VW-2Zmrm8PUO4OuMO5CGJijvdki2ha6m5LbEoKG9SaMKQRv8rH9GG2GuVLkBs7wjcwvpfNBQIPEOhJ3KyTpEMPT9Y3tJVb6Flt-9zNdoyNFT4SGXMshDnKrF6zE4YhCLixCu9QAdbh8iPaywwCynpZhZEyLN7XGIyHQhxlr1LdFrsL9R4Tl164I">
<div class="absolute bottom-0 w-full h-3/4 bg-gradient-to-t from-surface to-transparent"></div>
</div>
<div class="relative z-10 text-center max-w-3xl px-margin-desktop mx-auto">
<span class="inline-block px-sm py-xs mb-md font-label-sm text-label-sm rounded-full tracking-wider uppercase bg-primary text-on-primary">Clinical Excellence</span>
<h1 class="font-display-lg text-display-lg text-on-background mb-md [text-shadow:0_0_30px_rgba(255,255,255,1),_0_0_10px_rgba(255,255,255,1)]">Premium Aesthetic Services</h1>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl mx-auto [text-shadow:0_0_20px_rgba(255,255,255,1)]">Discover GlowCare's holistic approach to beauty. We combine advanced medical technology with a serene, luxurious environment to deliver transformative results tailored to your unique skin profile.</p>
</div>
</section>
<!-- Treatments Overview Grid -->
<section class="py-xl px-margin-mobile md:px-margin-desktop max-w-[1200px] mx-auto bg-surface">
<div class="text-center mb-xl">
<h2 class="font-headline-lg text-headline-lg text-on-background mb-sm">Our Signature Protocols</h2>
<p class="font-body-md text-body-md text-on-surface-variant max-w-xl mx-auto">Curated treatment plans designed by dermatological experts to address your specific skin concerns with precision and care.</p>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-lg lg:gap-xl">
<?php
$qTreatment = mysqli_query($conn, "SELECT * FROM treatment WHERE status='Aktif' ORDER BY urutan ASC");
if (mysqli_num_rows($qTreatment) > 0) {
    while ($t = mysqli_fetch_assoc($qTreatment)) {
        $img = !empty($t['gambar_url']) ? $t['gambar_url'] : 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?w=700&auto=format&fit=crop&q=80';
?>
<!-- Dynamic Service Card -->
<div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/40 overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300 flex flex-col group">
<div class="relative h-[250px] sm:h-[300px] w-full overflow-hidden">
<img alt="<?= htmlspecialchars($t['nama']) ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" src="<?= htmlspecialchars($img) ?>">
</div>
<div class="p-md md:p-lg flex flex-col flex-grow">
<span class="inline-block px-sm py-xs mb-md bg-surface-variant text-on-surface-variant font-label-sm text-label-sm rounded-full w-max"><?= htmlspecialchars($t['kategori']) ?></span>
<h3 class="font-headline-md text-headline-md text-on-background mb-sm"><?= htmlspecialchars($t['nama']) ?></h3>
<p class="font-body-md text-body-md text-on-surface-variant mb-lg flex-grow"><?= htmlspecialchars($t['deskripsi_panjang']) ?></p>
<div class="space-y-sm mt-auto pt-md border-t border-outline-variant/30">
<a aria-label="Book <?= htmlspecialchars($t['nama']) ?>" class="bg-surface-container-low p-sm rounded-lg flex justify-between items-center group/item hover:bg-surface-container transition-colors text-primary font-label-md" href="pages/user/dashboarduser.php">
<span>Book this treatment</span>
<span class="material-symbols-outlined text-[20px] group-hover/item:translate-x-1 transition-transform" data-icon="arrow_forward">arrow_forward</span>
</a>
</div>
</div>
</div>
<?php 
    }
} else {
    echo "<p class='text-on-surface-variant'>No treatments available at the moment.</p>";
}
?>
</div>
</section>
<!-- Technology Section (Bento Grid) -->
<section class="py-xl px-margin-mobile md:px-margin-desktop max-w-[1200px] mx-auto bg-surface-container-low rounded-2xl my-xl">
<div class="text-center mb-xl">
<span class="material-symbols-outlined text-primary text-[32px] mb-sm" data-icon="verified">verified</span>
<h2 class="font-headline-lg text-headline-lg text-on-background mb-sm">Certified Clinical Technology</h2>
<p class="font-body-md text-body-md text-on-surface-variant max-w-xl mx-auto">GlowCare exclusively employs FDA-cleared, industry-leading technology to ensure the highest standards of safety, efficacy, and patient comfort.</p>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-md">
<div class="bg-surface p-lg rounded-xl border border-outline-variant/20 shadow-sm flex flex-col items-center text-center">
<span class="material-symbols-outlined text-primary mb-md" data-icon="precision_manufacturing" style="font-size: 40px;">precision_manufacturing</span>
<h3 class="font-label-md text-label-md text-on-background mb-xs">Precision Lasers</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant">State-of-the-art fractional and picosecond lasers for targeted pigment and texture correction.</p>
</div>
<div class="bg-surface p-lg rounded-xl border border-outline-variant/20 shadow-sm flex flex-col items-center text-center">
<span class="material-symbols-outlined text-primary mb-md" data-icon="science" style="font-size: 40px;">science</span>
<h3 class="font-label-md text-label-md text-on-background mb-xs">Medical-Grade Products</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant">Exclusive partnerships with top-tier dermatological skincare lines for profound cellular impact.</p>
</div>
<div class="bg-surface p-lg rounded-xl border border-outline-variant/20 shadow-sm flex flex-col items-center text-center">
<span class="material-symbols-outlined text-primary mb-md" data-icon="health_and_safety" style="font-size: 40px;">health_and_safety</span>
<h3 class="font-label-md text-label-md text-on-background mb-xs">Rigorous Safety Protocols</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant">Our facility adheres to strict clinical hygiene and safety standards, providing peace of mind.</p>
</div>
</div>
</section>
<!-- Call to Action -->
<section class="py-xl px-margin-mobile md:px-margin-desktop max-w-[1000px] mx-auto text-center mb-xl">
<div class="bg-surface-container-low p-xl rounded-2xl relative overflow-hidden border border-primary-container/30 shadow-sm flex flex-col items-center text-center">
<!-- Decorative Background Elements -->
<div class="absolute -right-20 -top-20 w-80 h-80 bg-primary-container/10 rounded-full blur-3xl"></div>
<div class="absolute -left-20 -bottom-20 w-80 h-80 bg-primary-container/10 rounded-full blur-3xl"></div>
<!-- Minimalist Decorative Icon -->
<div class="relative z-10 mb-md">
<span class="material-symbols-outlined text-primary opacity-40" style="font-size: 48px;">spa</span>
</div>
<!-- Enhanced Typography -->
<h2 class="font-display-lg text-display-lg text-primary mb-md relative z-10 max-w-2xl tracking-tight">
    Begin Your Aesthetic Journey
  </h2>
<!-- Refined Body Text -->
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-xl mx-auto mb-lg relative z-10 leading-relaxed">
    Schedule a comprehensive consultation with our clinical experts to develop a personalized treatment plan tailored to your unique goals.
  </p>
<!-- High-Contrast Primary Button -->
<a href="jadwal.php" class="inline-block relative z-10 bg-primary text-on-primary font-label-md text-label-md px-xl py-sm rounded-lg hover:-translate-y-1 hover:shadow-lg transition-all duration-300 uppercase tracking-wider">
    Schedule Consultation
</a>
<!-- Subtle Bottom Accent -->
<div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-32 h-1 bg-primary-container/40 rounded-t-full"></div>
</div>
</section>
</main>
<!-- Footer -->
<footer class="w-full bg-surface-container-low dark:bg-surface-container-lowest border-t border-outline-variant dark:border-outline flat no shadows">
<div class="w-full px-margin-desktop py-xl flex flex-col md:flex-row justify-between items-start gap-lg max-w-[1200px] mx-auto">
<!-- Brand Logo -->
<div class="font-headline-md text-headline-md text-primary dark:text-primary-fixed-dim">
                GlowCare
            </div>
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
