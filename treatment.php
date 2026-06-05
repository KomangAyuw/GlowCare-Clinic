<?php
session_start();
$conn = require_once 'backend/koneksi.php';
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
<a class="font-headline-lg text-headline-lg text-primary inline-flex items-center" href="index.php"><span class="material-symbols-outlined text-primary text-3xl mr-2 align-middle" style="font-variation-settings: &quot;FILL&quot; 1;">spa</span>GlowCare</a>
<!-- Navigation Links -->
<nav class="hidden md:flex items-center gap-sm">
<!-- Inactive Items -->
<a class="font-label-md text-label-md px-4 py-2 text-on-surface-variant font-medium hover:text-primary hover:bg-primary-container/20 rounded-lg transition-all duration-300 ease-in-out" href="index.php">Home</a>
<a class="font-label-md text-label-md px-4 py-2 text-on-surface-variant font-medium hover:text-primary hover:bg-primary-container/20 rounded-lg transition-all duration-300 ease-in-out" href="about.php">About Us</a>
<!-- Active Item -->
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
<main class="pt-[80px]">
<!-- Hero Section -->
<section class="relative w-full h-[50vh] min-h-[400px] flex items-center justify-center overflow-hidden">
<div class="absolute inset-0 z-0">
<img alt="Premium Aesthetic Services Hero" class="w-full h-full object-cover object-center" src="https://lh3.googleusercontent.com/aida/AP1WRLvx9aMlJNEa5OJ5ldw2XnCFFOxAzy94gxGdXsSe1lEdli7kKExY3agiZmi7qEW4xBn9VW-2Zmrm8PUO4OuMO5CGJijvdki2ha6m5LbEoKG9SaMKQRv8rH9GG2GuVLkBs7wjcwvpfNBQIPEOhJ3KyTpEMPT9Y3tJVb6Flt-9zNdoyNFT4SGXMshDnKrF6zE4YhCLixCu9QAdbh8iPaywwCynpZhZEyLN7XGIyHQhxlr1LdFrsL9R4Tl164I">
<div class="absolute inset-0 bg-surface/40 backdrop-blur-[2px]"></div>
<div class="absolute inset-0 bg-gradient-to-t from-background via-transparent to-transparent"></div>
</div>
<div class="relative z-10 text-center max-w-3xl px-margin-desktop mx-auto">
<span class="inline-block px-sm py-xs mb-md backdrop-blur-sm font-label-sm text-label-sm rounded-full tracking-wider uppercase bg-primary text-on-primary">Clinical Excellence</span>
<h1 class="font-display-lg text-display-lg text-on-background mb-md">Our Clinical Treatments</h1>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl mx-auto">Experience the pinnacle of medical-grade aesthetics. Our treatments combine advanced clinical technology with a serene luxury experience, curated by expert practitioners for your skin's health.</p>
</div>
</section>
<!-- Category Filter Bar -->
<section class="sticky top-[72px] z-40 bg-surface/95 backdrop-blur-sm border-b border-outline-variant/30">
<div class="max-w-[1200px] mx-auto px-margin-mobile md:px-margin-desktop overflow-x-auto">
<div class="flex items-center justify-start md:justify-center gap-xs py-4 whitespace-nowrap no-scrollbar" id="filter-container">
<button data-target="all" class="filter-tab active font-label-md text-label-md px-6 py-2 rounded-full transition-all duration-300">All Treatments</button>
<button data-target="acne" class="filter-tab text-on-surface-variant hover:text-primary font-label-md text-label-md px-6 py-2 rounded-full transition-all duration-300">Acne &amp; Clear Skin</button>
<button data-target="anti-aging" class="filter-tab text-on-surface-variant hover:text-primary font-label-md text-label-md px-6 py-2 rounded-full transition-all duration-300">Anti-Aging</button>
<button data-target="brightening" class="filter-tab text-on-surface-variant hover:text-primary font-label-md text-label-md px-6 py-2 rounded-full transition-all duration-300">Brightening</button>
<button data-target="body" class="filter-tab text-on-surface-variant hover:text-primary font-label-md text-label-md px-6 py-2 rounded-full transition-all duration-300">Hair &amp; Body</button>
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
    $durasi = htmlspecialchars($tr['durasi']);
    $nama = htmlspecialchars($tr['nama']);
    $img = htmlspecialchars($tr['gambar_url']);
    $kat = htmlspecialchars($tr['kategori']);
    $desc = htmlspecialchars($tr['deskripsi_panjang']);
?>
<div data-category="<?= $cat_class ?>" class="treatment-card bg-surface-container-lowest rounded-2xl border border-outline-variant/40 overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 flex flex-col group">
<div class="relative h-[240px] w-full overflow-hidden">
<img alt="<?= $nama ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" src="<?= $img ?>">
<span class="absolute top-4 left-4 bg-surface/90 backdrop-blur-sm text-primary font-label-sm text-label-sm px-3 py-1 rounded-full border border-primary/20"><?= $kat ?></span>
</div>
<div class="p-md flex flex-col flex-grow">
<div class="flex justify-between items-start mb-2">
<h3 class="font-headline-md text-headline-md text-on-background leading-tight"><?= $nama ?></h3>
<div class="flex items-center text-on-surface-variant/70">
<span class="material-symbols-outlined text-sm mr-1" style="font-size: 16px;">schedule</span>
<span class="font-label-sm text-label-sm"><?= $durasi ?></span>
</div>
</div>
<p class="font-body-sm text-body-sm text-on-surface-variant mb-lg flex-grow"><?= $desc ?></p>
<a href="detail_treatment.php?id=<?= $tr['id'] ?>" class="w-full py-3 bg-surface-container-high text-primary font-label-md text-label-md rounded-lg border border-primary/20 hover:bg-primary hover:text-on-primary transition-all duration-300 uppercase tracking-wider text-center block inline-flex items-center justify-center gap-2"><span class="material-symbols-outlined" style="font-size:18px;">visibility</span> Lihat Detail</a>
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
<img alt="Clinical Technology" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAlkcdxmr1qSlDHS7Jt4Kdu_6B6RqHYNm-0RXfw7qR5-DzVwvq5M4yBiUMQLMfer6uonWbiqScRyPXZw846WOAJ-EzduAvO-cAcRUD83y4y0YPN2NIaL5WgvoHFsqkwlxXD34rWcjaHd4aYWcqEQKbRnPuSozWqgNtOwWI0nYcNGHu80TYUadBoJ2e0cLXuR0xsrhQs--OJwp56LWvbK56ctD7NjLpxLt6ZBWJB-8WhafPCyfGprxuBXTBy5aSCEOKCoRuFR0rQK-M">
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
<a href="jadwal.php" class="inline-block relative z-10 bg-primary text-on-primary font-label-md text-label-md px-xl py-4 rounded-lg hover:-translate-y-1 hover:shadow-lg transition-all duration-300 uppercase tracking-wider">
    Schedule Consultation
  </a>
<!-- Subtle Bottom Accent -->
<div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-32 h-1 bg-primary-container/40 rounded-t-full"></div>
</div>
</section>
</main>
<!-- Footer -->
<footer class="w-full bg-surface-container-low border-t border-outline-variant flat no shadows">
<div class="w-full px-margin-desktop py-xl flex flex-col md:flex-row justify-between items-start gap-lg max-w-[1200px] mx-auto">
<!-- Brand Logo -->
<div class="font-headline-md text-headline-md text-primary">
                GlowCare
            </div>
<!-- Links -->
<ul class="flex flex-col md:flex-row flex-wrap gap-md items-start md:items-center">
<li class=""><a class="text-on-surface-variant hover:text-primary transition-colors duration-200 font-label-sm text-label-sm" href="#">Privacy Policy</a></li>
<li class=""><a class="text-on-surface-variant hover:text-primary transition-colors duration-200 font-label-sm text-label-sm" href="#">Terms of Service</a></li>
<li class=""><a class="text-on-surface-variant hover:text-primary transition-colors duration-200 font-label-sm text-label-sm" href="#">Patient Rights</a></li>
<li class=""><a class="text-on-surface-variant hover:text-primary transition-colors duration-200 font-label-sm text-label-sm" href="#">Careers</a></li>
<li class=""><a class="text-on-surface-variant hover:text-primary transition-colors duration-200 font-label-sm text-label-sm" href="#">Contact Us</a></li>
</ul>
<!-- Copyright -->
<div class="text-secondary font-body-sm text-body-sm">
                © 2026 GlowCare Aesthetic Clinic. All rights reserved.
            </div>
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
    
    function filterCards(cat) {
        // Update cards visibility
        treatmentCards.forEach(card => {
            if (cat === 'all' || card.dataset.category === cat) {
                card.style.display = 'flex'; // Use flex because the card uses flex layout
            } else {
                card.style.display = 'none';
            }
        });
        
        // Update tabs styling
        filterTabs.forEach(tab => {
            if (tab.dataset.target === cat) {
                tab.classList.add('active');
                tab.classList.remove('text-on-surface-variant');
            } else {
                tab.classList.remove('active');
                tab.classList.add('text-on-surface-variant');
            }
        });
    }
    
    // Setup event listeners for tabs
    filterTabs.forEach(tab => {
        tab.addEventListener('click', (e) => {
            const targetCat = e.target.dataset.target;
            filterCards(targetCat);
            
            // Optionally update URL without reloading
            const readableParam = Object.keys(paramMap).find(key => paramMap[key] === targetCat);
            const newUrl = targetCat === 'all' 
                ? window.location.pathname 
                : `${window.location.pathname}?category=${readableParam}`;
            window.history.pushState({path: newUrl}, '', newUrl);
        });
    });
    
    // Initial filtering
    filterCards(activeCategory);
});
</script>
</body></html>
