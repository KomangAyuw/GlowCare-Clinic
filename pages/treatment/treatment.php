<?php
session_start();
$conn = require_once '../../backend/config/koneksi.php';
$treatments = [];
$q = mysqli_query($conn, "SELECT * FROM treatment WHERE status='Aktif' ORDER BY urutan ASC");
while($r = mysqli_fetch_assoc($q)) {
    $treatments[] = $r;
}
?>
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
        .filter-tab.active {
            color: #735a39 !important;
            border-bottom: 2px solid #735a39;
            background-color: rgba(224, 192, 151, 0.2);
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
                        "on-primary-fixed-variant": "#594323",
                        "outline": "#7f756a",
                        "inverse-surface": "#30312e",
                        "on-error": "#ffffff",
                        "outline-variant": "#d1c4b8",
                        "on-tertiary": "#ffffff",
                        "surface-container-high": "#eae8e3",
                        "surface-bright": "#fbf9f4",
                        "on-tertiary-fixed": "#161d1f",
                        "tertiary-fixed": "#dde4e6",
                        "on-secondary": "#ffffff",
                        "secondary": "#386663",
                        "primary-fixed": "#ffddb2",
                        "tertiary-fixed-dim": "#c1c8ca",
                        "surface-tint": "#735a39",
                        "surface-container-highest": "#e4e2dd",
                        "on-surface": "#1b1c19",
                        "surface-container-low": "#f5f3ee",
                        "on-secondary-fixed": "#00201f",
                        "primary": "#735a39",
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
<header class="fixed w-full z-50 bg-surface/80 backdrop-blur-md shadow-sm transition-all duration-300 ease-in-out docked full-width top-0">
<div class="flex justify-between items-center w-full px-margin-mobile md:px-margin-desktop py-base max-w-[1200px] mx-auto">
<!-- Brand Logo -->
<a class="font-headline-lg text-headline-lg text-primary inline-flex items-center" href="../../index.php"><span class="material-symbols-outlined text-primary text-3xl mr-2 align-middle" style="font-variation-settings: &quot;FILL&quot; 1;">spa</span>GlowCare</a>
<!-- Navigation Links -->
<nav class="hidden md:flex items-center gap-sm">
<!-- Inactive Items -->
<a class="font-label-md text-label-md px-4 py-2 text-on-surface-variant font-medium hover:text-primary hover:bg-primary-container/20 rounded-lg transition-all duration-300 ease-in-out" href="../../index.php">Home</a>
<a class="font-label-md text-label-md px-4 py-2 text-on-surface-variant font-medium hover:text-primary hover:bg-primary-container/20 rounded-lg transition-all duration-300 ease-in-out" href="../about.php">About Us</a>
<!-- Active Item -->
<a class="font-label-md text-label-md px-4 py-2 text-primary font-bold border-b-2 border-primary pb-1 hover:bg-primary-container/20 rounded-t-lg transition-all duration-300 ease-in-out" href="treatment.php">Services</a>
<a class="font-label-md text-label-md px-4 py-2 text-on-surface-variant font-medium hover:text-primary hover:bg-primary-container/20 rounded-lg transition-all duration-300 ease-in-out" href="../spesialis.php">Doctors</a>
<a class="font-label-md text-label-md px-4 py-2 text-on-surface-variant font-medium hover:text-primary hover:bg-primary-container/20 rounded-lg transition-all duration-300 ease-in-out" href="../kontak.php">Contact</a>
</nav>
<!-- Actions -->
<div class="flex items-center gap-sm">
<?php if (isset($_SESSION['user_id'])): 
    $dashboard_url = '../user/dashboarduser.php';
    if (isset($_SESSION['role'])) {
        if ($_SESSION['role'] === 'admin') {
            $dashboard_url = '../admin/dashboard.php';
        } elseif ($_SESSION['role'] === 'dokter') {
            $dashboard_url = '../dokter/dashboardDokter.php';
        }
    }
?>
    <a href="<?= $dashboard_url ?>" class="font-label-md text-label-md text-primary hover:bg-primary-container/20 px-4 py-2 rounded-lg transition-all duration-300 ease-in-out inline-flex items-center justify-center">Dashboard</a>
    <a href="../../backend/auth/logout.php" class="font-label-md text-label-md bg-error text-on-error px-6 py-2 rounded-lg hover:opacity-90 shadow-sm transition-all duration-300 ease-in-out inline-flex items-center justify-center">Logout</a>
<?php else: ?>
    <a href="../auth/Signin.php" class="font-label-md text-label-md text-primary hover:bg-primary-container/20 px-4 py-2 rounded-lg transition-all duration-300 ease-in-out inline-flex items-center justify-center">Login</a>
    <a href="../auth/SignUp.php" class="font-label-md text-label-md bg-primary text-on-primary px-6 py-2 rounded-lg hover:bg-on-primary-fixed-variant shadow-sm transition-all duration-300 ease-in-out inline-flex items-center justify-center">Register</a>
<?php endif; ?>
</div>
</div>
</header>
<main class="pt-[80px]">
<!-- Hero Section -->
<section class="relative w-full h-[50vh] min-h-[400px] flex items-center justify-center overflow-hidden">
<div class="absolute inset-0 z-0">
<img alt="Premium Aesthetic Services Hero" class="w-full h-full object-cover object-center brightness-[0.85] contrast-[1.05]" src="../../asset/img/glow_infusion.png">
<div class="absolute inset-0 bg-surface/50 backdrop-blur-[1px]"></div>
<div class="absolute inset-0 bg-gradient-to-t from-background via-background/10 to-transparent"></div>
</div>
<div class="relative z-10 text-center max-w-3xl px-margin-desktop mx-auto">
<span class="inline-block px-4 py-1.5 mb-md backdrop-blur-md font-label-sm text-label-sm rounded-full tracking-widest uppercase bg-primary/10 text-primary border border-primary/25 shadow-sm">Clinical Excellence</span>
<h1 class="font-display-lg text-display-lg md:text-[42px] text-on-background mb-md font-serif leading-tight">Our Clinical Treatments</h1>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl mx-auto leading-relaxed">Experience the pinnacle of medical-grade aesthetics. Our treatments combine advanced clinical technology with a serene luxury experience, curated by expert practitioners for your skin's health.</p>
</div>
</section>

<!-- Search & Category Filter Bar -->
<section class="sticky top-[72px] z-40 bg-surface/90 backdrop-blur-md border-b border-outline-variant/30 py-4 shadow-sm">
<div class="max-w-[1200px] mx-auto px-margin-mobile md:px-margin-desktop flex flex-col md:flex-row md:items-center justify-between gap-md">
<!-- Category Filter Tabs -->
<div class="flex items-center gap-xs overflow-x-auto whitespace-nowrap no-scrollbar pb-2 md:pb-0" id="filter-container">
<button data-target="all" class="filter-tab active font-label-md text-label-md px-5 py-2.5 rounded-full transition-all duration-300">All Treatments</button>
<button data-target="acne" class="filter-tab text-on-surface-variant hover:text-primary font-label-md text-label-md px-5 py-2.5 rounded-full transition-all duration-300">Acne &amp; Clear Skin</button>
<button data-target="anti-aging" class="filter-tab text-on-surface-variant hover:text-primary font-label-md text-label-md px-5 py-2.5 rounded-full transition-all duration-300">Anti-Aging</button>
<button data-target="brightening" class="filter-tab text-on-surface-variant hover:text-primary font-label-md text-label-md px-5 py-2.5 rounded-full transition-all duration-300">Brightening</button>
<button data-target="body" class="filter-tab text-on-surface-variant hover:text-primary font-label-md text-label-md px-5 py-2.5 rounded-full transition-all duration-300">Hair &amp; Body</button>
</div>
<!-- Premium Search Input -->
<div class="relative w-full md:w-80">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant/50 pointer-events-none text-xl">search</span>
<input type="text" id="search-input" placeholder="Search treatments..." class="w-full pl-10 pr-10 py-2.5 bg-surface-container-low border border-outline-variant/50 rounded-full text-body-md text-on-surface placeholder:text-on-surface-variant/40 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/30 transition-all duration-300 shadow-inner">
<button id="clear-search" class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant/40 hover:text-primary hidden transition-colors duration-200">
<span class="material-symbols-outlined text-[18px]">close</span>
</button>
</div>
</div>
</section>

<!-- Treatments Grid -->
<section class="py-xl px-margin-mobile md:px-margin-desktop max-w-[1200px] mx-auto">
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-gutter lg:gap-lg" id="treatments-grid">
<?php foreach($treatments as $tr): 
    $cat_class = strtolower(explode(' ', $tr['kategori'])[0]); 
    if ($cat_class == 'hair' || $cat_class == 'body') $cat_class = 'body'; 
    else if ($cat_class == 'anti-aging') $cat_class = 'anti-aging';
    else if ($cat_class == 'acne') $cat_class = 'acne';
    else if ($cat_class == 'brightening') $cat_class = 'brightening';
    else $cat_class = 'other';
    $durasi = htmlspecialchars($tr['durasi'] ?? '60 Menit');
    $nama = htmlspecialchars($tr['nama']);
    $img = $tr['gambar_url'] ?? '';
    if ($img && strpos($img, 'http') !== 0 && strpos($img, 'asset/') !== 0) {
        $img = '../../backend/uploads/' . $img;
    }
    $img = htmlspecialchars($img);
    $kat = htmlspecialchars($tr['kategori']);
    $desc = htmlspecialchars($tr['deskripsi_panjang'] ?? $tr['deskripsi'] ?? '');
?>
<div data-category="<?= $cat_class ?>" data-name="<?= strtolower($nama) ?>" data-desc="<?= strtolower($desc) ?>" class="treatment-card bg-surface-container-lowest rounded-2xl border border-outline-variant/30 overflow-hidden shadow-sm hover:shadow-[0_12px_30px_rgba(115,90,57,0.12)] hover:-translate-y-1.5 transition-all duration-500 flex flex-col group">
<div class="relative h-[240px] w-full overflow-hidden">
<img alt="<?= $nama ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" src="<?= $img ?>">
<span class="absolute top-4 left-4 bg-surface/90 backdrop-blur-md text-primary font-label-sm text-label-sm px-3.5 py-1.5 rounded-full border border-primary/20 shadow-sm font-semibold"><?= $kat ?></span>
</div>
<div class="p-md flex flex-col flex-grow">
<div class="flex justify-between items-start mb-3">
<h3 class="font-headline-md text-headline-md text-on-background leading-tight font-serif font-semibold group-hover:text-primary transition-colors duration-300"><?= $nama ?></h3>
<div class="flex items-center text-on-surface-variant/70 shrink-0 bg-surface-container-low px-2.5 py-1 rounded-md ml-2 border border-outline-variant/20">
<span class="material-symbols-outlined text-sm mr-1" style="font-size: 16px;">schedule</span>
<span class="font-label-sm text-label-sm font-medium"><?= $durasi ?></span>
</div>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant/90 mb-lg flex-grow leading-relaxed"><?= $desc ?></p>
<a href="detail_treatment.php?id=<?= $tr['id'] ?>" class="w-full py-3 bg-surface-container-high text-primary font-label-md text-label-md rounded-xl border border-primary/25 hover:bg-primary hover:text-on-primary hover:border-transparent transition-all duration-300 uppercase tracking-widest text-center block inline-flex items-center justify-center gap-2 font-semibold shadow-sm hover:shadow-md"><span class="material-symbols-outlined" style="font-size:18px;">visibility</span> Lihat Detail</a>
</div>
</div>
<?php endforeach; ?>
</div>
</section>
<!-- Call to Action -->
<section class="py-xl px-margin-mobile md:px-margin-desktop max-w-[1200px] mx-auto">
<div class="grid grid-cols-1 lg:grid-cols-2 gap-lg items-start">
<!-- Left Content -->
<div class="flex flex-col gap-md">
<h2 class="font-headline-lg text-headline-lg text-primary">Certified Clinical Technology</h2>
<p class="font-body-lg text-body-lg text-on-surface-variant leading-relaxed">
        Setiap perawatan kami didukung oleh teknologi medis tercanggih yang telah tersertifikasi internasional, memastikan hasil maksimal dengan tingkat keamanan tertinggi bagi pasien kami.
      </p>
<div class="grid grid-cols-2 gap-md mt-lg">
<div class="flex items-center gap-sm">
<div class="w-10 h-10 rounded-full bg-secondary-container flex items-center justify-center">
<span class="material-symbols-outlined text-secondary" style="font-variation-settings: &quot;FILL&quot; 1;">verified</span>
</div>
<span class="font-label-md text-label-md text-on-background">FDA Approved</span>
</div>
<div class="flex items-center gap-sm">
<div class="w-10 h-10 rounded-full bg-secondary-container flex items-center justify-center">
<span class="material-symbols-outlined text-secondary" style="font-variation-settings: &quot;FILL&quot; 1;">clinical_notes</span>
</div>
<span class="font-label-md text-label-md text-on-background">Clinical Research</span>
</div>
<div class="flex items-center gap-sm">
<div class="w-10 h-10 rounded-full bg-secondary-container flex items-center justify-center">
<span class="material-symbols-outlined text-secondary" style="font-variation-settings: &quot;FILL&quot; 1;">precision_manufacturing</span>
</div>
<span class="font-label-md text-label-md text-on-background">Precision Tools</span>
</div>
<div class="flex items-center gap-sm">
<div class="w-10 h-10 rounded-full bg-secondary-container flex items-center justify-center">
<span class="material-symbols-outlined text-secondary" style="font-variation-settings: &quot;FILL&quot; 1;">badge</span>
</div>
<span class="font-label-md text-label-md text-on-background">Certified Staff</span>
</div>
</div>
</div>
<!-- Right Content: Testimonial Image -->
<div class="relative rounded-2xl overflow-hidden shadow-lg">
<img alt="Clinical Technology" class="w-full h-full object-cover" src="../../asset/img/Laser.jpg">
<div class="absolute bottom-6 left-6 right-6 bg-surface/90 backdrop-blur-md p-md rounded-xl border border-outline-variant/30">
<p class="font-body-sm italic text-on-surface-variant mb-2">
          "Kami mengutamakan presisi medis dan kenyamanan pasien di setiap detik prosedur."
        </p>
<div class="flex items-center gap-2">
<div class="w-8 h-8 rounded-full bg-primary-container flex items-center justify-center text-[10px] font-bold text-on-primary-container">GC</div>
<span class="font-label-sm text-label-sm text-primary">GlowCare Medical Standards</span>
</div>
</div>
</div>
</div>
</section><section class="py-xl px-margin-mobile md:px-margin-desktop max-w-[1000px] mx-auto text-center mb-xl">
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
<a href="<?= isset($_SESSION['user_id']) ? '../user/dashboarduser.php?page=daftar-konsul' : '../auth/Signin.php' ?>" class="inline-block relative z-10 bg-primary text-on-primary font-label-md text-label-md px-xl py-4 rounded-lg hover:-translate-y-1 hover:shadow-lg transition-all duration-300 uppercase tracking-wider">
    Booking Now
  </a>
<!-- Subtle Bottom Accent -->
<div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-32 h-1 bg-primary-container/40 rounded-t-full"></div>
</div>
</section>
</main>
<!-- JSON Component: Footer -->
<footer class="bg-surface-container-highest w-full py-lg px-margin-mobile md:px-margin-desktop grid grid-cols-1 md:grid-cols-4 items-start gap-lg full-width bottom mt-xl" id="contact">
<!-- Branding & Social -->
<div class="col-span-1 md:col-span-1 space-y-md">
<a class="font-headline-lg text-headline-lg text-primary" href="#">GlowCare</a>
<p class="font-body-sm text-body-sm text-on-surface-variant">Klinik kecantikan tepercaya untuk kulit sehat alami Anda.</p>
<div class="flex gap-sm"><a class="text-primary hover:text-on-primary-fixed-variant transition-colors" href="https://www.instagram.com" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"></path></svg></a><a class="text-primary hover:text-on-primary-fixed-variant transition-colors" href="https://www.youtube.com" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"></path></svg></a><a class="text-primary hover:text-on-primary-fixed-variant transition-colors" href="https://www.facebook.com" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"></path></svg></a></div>
</div>
<!-- Contact Info -->
<div class="col-span-1 md:col-span-1 space-y-sm">
<h4 class="font-label-md text-label-md text-on-background mb-xs">Hubungi Kami</h4>
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-secondary text-sm">call</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">+62 811-1234-5678</span>
</div>
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-secondary text-sm">mail</span>
<span class="font-body-sm text-body-sm text-on-surface-variant">hello@glowcare.clinic</span>
</div>
<div class="flex items-start gap-2 mt-2">
<span class="material-symbols-outlined text-secondary text-sm mt-1">schedule</span>
<div>
<div class="font-body-sm text-body-sm text-on-surface-variant font-bold">Jam Operasional:</div>
<div class="font-body-sm text-body-sm text-on-surface-variant">Senin - Sabtu: 09.00 - 20.00</div>
</div>
</div>
</div>
<!-- Links -->
<div class="col-span-1 md:col-span-1 space-y-sm flex flex-col">
<h4 class="font-label-md text-label-md text-on-background mb-xs">Tautan Cepat</h4>
<a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary transition-colors" href="../../index.php">Beranda</a>
<a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary transition-colors" href="treatment.php">Layanan</a>
<a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary transition-colors" href="../spesialis.php">Dokter</a>
<a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary transition-colors" href="../kontak.php">Contact</a>
</div>
<!-- Map Placeholder -->
<div class="col-span-1 md:col-span-1">
<h4 class="font-label-md text-label-md text-on-background mb-xs">Lokasi Kami</h4>
<div class="w-full h-32 rounded-lg bg-surface-container-high overflow-hidden relative">
<!-- Google Maps Embed Placeholder -->
<iframe allowfullscreen="" class="w-full h-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63123.123456789!2d116.1165!3d-8.5833!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dcdbf406173076b%3A0x4030bf45e4b27a0!2sMataram%2C%20Kota%20Mataram%2C%20Nusa%20Tenggara%20Bar.!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid"></iframe>
</div>
</div>
<!-- Copyright Line -->
<div class="col-span-1 md:col-span-4 pt-md border-t border-outline-variant/30 text-center mt-md">
<span class="font-body-sm text-body-sm text-on-surface-variant">© 2026 GlowCare Clinical Aesthetics. All rights reserved.</span>
</div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Read the category from URL
    const params = new URLSearchParams(window.location.search);
    const categoryQuery = params.get('category');
    
    // Map URL parameter to data-category
    const paramMap = {
        'Acne': 'acne',
        'Anti-Aging': 'anti-aging',
        'Brightening': 'brightening',
        'Body': 'body'
    };
    
    let activeCategory = 'all';
    if (categoryQuery && paramMap[categoryQuery]) {
        activeCategory = paramMap[categoryQuery];
    }
    
    const filterTabs = document.querySelectorAll('.filter-tab');
    const treatmentCards = document.querySelectorAll('.treatment-card');
    const searchInput = document.getElementById('search-input');
    const clearSearchBtn = document.getElementById('clear-search');
    
    let searchQuery = '';
    
    function filterCards() {
        treatmentCards.forEach(card => {
            const matchesCategory = (activeCategory === 'all' || card.dataset.category === activeCategory);
            const cardName = card.dataset.name || '';
            const cardDesc = card.dataset.desc || '';
            const matchesSearch = !searchQuery || cardName.includes(searchQuery) || cardDesc.includes(searchQuery);
            
            if (matchesCategory && matchesSearch) {
                card.style.display = 'flex';
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0) scale(1)';
                }, 10);
            } else {
                card.style.opacity = '0';
                card.style.transform = 'translateY(10px) scale(0.98)';
                card.style.display = 'none';
            }
        });
    }
    
    // Setup event listeners for tabs
    filterTabs.forEach(tab => {
        tab.addEventListener('click', (e) => {
            activeCategory = e.target.dataset.target;
            filterCards();
            
            // Update tabs styling
            filterTabs.forEach(t => {
                if (t.dataset.target === activeCategory) {
                    t.classList.add('active');
                    t.classList.remove('text-on-surface-variant');
                } else {
                    t.classList.remove('active');
                    t.classList.add('text-on-surface-variant');
                }
            });
            
            // Optionally update URL without reloading
            const readableParam = Object.keys(paramMap).find(key => paramMap[key] === activeCategory);
            const newUrl = activeCategory === 'all' 
                ? window.location.pathname 
                : `${window.location.pathname}?category=${readableParam}`;
            window.history.pushState({path: newUrl}, '', newUrl);
        });
    });
    
    // Search input event
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            searchQuery = e.target.value.toLowerCase().trim();
            if (searchQuery) {
                clearSearchBtn.classList.remove('hidden');
            } else {
                clearSearchBtn.classList.add('hidden');
            }
            filterCards();
        });
    }
    
    // Clear search
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', () => {
            searchInput.value = '';
            searchQuery = '';
            clearSearchBtn.classList.add('hidden');
            filterCards();
            searchInput.focus();
        });
    }
    
    // Update tabs styling initially based on activeCategory
    filterTabs.forEach(t => {
        if (t.dataset.target === activeCategory) {
            t.classList.add('active');
            t.classList.remove('text-on-surface-variant');
        } else {
            t.classList.remove('active');
            t.classList.add('text-on-surface-variant');
        }
    });
    
    // Initial filtering
    filterCards();
});
</script>
</body></html>
