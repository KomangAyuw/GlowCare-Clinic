<?php
session_start();
?>
<!DOCTYPE html><html class="scroll-smooth" lang="en" style=""><head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>GlowCare - Clinical Aesthetics</title>
<!-- Google Fonts: Inter & Playfair Display -->
<link href="https://fonts.googleapis.com" rel="preconnect">
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&amp;family=Playfair+Display:wght@500;600;700&amp;display=swap" rel="stylesheet">
<!-- Material Symbols -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<!-- Theme Configuration -->
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
                    "primary-fixed-dim": "#e1c198",
                    "surface-tint": "#735a39",
                    "on-secondary-fixed-variant": "#1e4e4c",
                    "on-background": "#1b1c19",
                    "surface-dim": "#dbdad5",
                    "on-primary-container": "#654d2d",
                    "on-secondary-container": "#3e6c69",
                    "surface-container-lowest": "#ffffff",
                    "on-error": "#ffffff",
                    "tertiary-fixed": "#dde4e6",
                    "on-tertiary-container": "#4b5355",
                    "on-tertiary-fixed": "#161d1f",
                    "tertiary-container": "#bfc6c8",
                    "on-secondary": "#ffffff",
                    "error-container": "#ffdad6",
                    "inverse-on-surface": "#f2f1ec",
                    "background": "#fbf9f4",
                    "surface-bright": "#fbf9f4",
                    "secondary": "#386663",
                    "error": "#ba1a1a",
                    "surface-container-highest": "#e4e2dd",
                    "on-primary-fixed": "#291800",
                    "on-error-container": "#93000a",
                    "on-primary-fixed-variant": "#594323",
                    "primary-container": "#e0c097",
                    "secondary-container": "#bbece8",
                    "outline": "#7f756a",
                    "inverse-surface": "#30312e",
                    "secondary-fixed-dim": "#a0cfcc",
                    "inverse-primary": "#e1c198",
                    "outline-variant": "#d1c4b8",
                    "surface-container": "#f0eee9",
                    "primary-fixed": "#ffddb2",
                    "surface": "#fbf9f4",
                    "tertiary": "#586062",
                    "surface-container-high": "#eae8e3",
                    "primary": "#735a39",
                    "on-surface": "#1b1c19",
                    "surface-container-low": "#f5f3ee",
                    "surface-variant": "#e4e2dd",
                    "on-secondary-fixed": "#00201f",
                    "secondary-fixed": "#bbece8",
                    "on-surface-variant": "#4e453c",
                    "tertiary-fixed-dim": "#c1c8ca",
                    "on-primary": "#ffffff",
                    "on-tertiary": "#ffffff",
                    "on-tertiary-fixed-variant": "#41484a"
            },
            "borderRadius": {
                    "DEFAULT": "0.25rem",
                    "lg": "0.5rem",
                    "xl": "0.75rem",
                    "2xl": "1rem",
                    "full": "9999px"
            },
            "spacing": {
                    "margin-desktop": "64px",
                    "xs": "4px",
                    "gutter": "24px",
                    "sm": "12px",
                    "lg": "48px",
                    "xl": "80px",
                    "md": "24px",
                    "margin-mobile": "16px",
                    "base": "8px"
            },
            "fontFamily": {
                    "headline-lg-mobile": ["Playfair Display"],
                    "label-sm": ["Inter"],
                    "headline-md": ["Playfair Display"],
                    "body-md": ["Inter"],
                    "body-sm": ["Inter"],
                    "body-lg": ["Inter"],
                    "display-lg": ["Playfair Display"],
                    "headline-lg": ["Playfair Display"],
                    "label-md": ["Inter"]
            },
            "fontSize": {
                    "headline-lg-mobile": ["24px", { "lineHeight": "1.3", "fontWeight": "600" }],
                    "label-sm": ["12px", { "lineHeight": "1.2", "fontWeight": "500" }],
                    "headline-md": ["24px", { "lineHeight": "1.4", "fontWeight": "500" }],
                    "body-md": ["16px", { "lineHeight": "1.5", "fontWeight": "400" }],
                    "body-sm": ["14px", { "lineHeight": "1.5", "fontWeight": "400" }],
                    "body-lg": ["18px", { "lineHeight": "1.6", "fontWeight": "400" }],
                    "display-lg": ["48px", { "lineHeight": "1.2", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                    "headline-lg": ["32px", { "lineHeight": "1.3", "fontWeight": "600" }],
                    "label-md": ["14px", { "lineHeight": "1.2", "letterSpacing": "0.05em", "fontWeight": "600" }]
            }
          },
        },
      }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        /* Custom warm ambient shadow as per style guide */
        .shadow-ambient {
            box-shadow: 0 8px 32px rgba(115, 90, 57, 0.06);
        }
        .shadow-ambient-hover {
            box-shadow: 0 16px 48px rgba(115, 90, 57, 0.1);
            transform: translateY(-2px);
        }
        /* Smooth transitions */
        .transition-ambient {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-background text-on-background antialiased selection:bg-primary-container selection:text-on-primary-container font-body-md text-body-md">
<!-- JSON Component: TopNavBar -->
<header class="fixed w-full z-50 bg-surface/80 backdrop-blur-md shadow-sm transition-all duration-300 ease-in-out docked full-width top-0">
<div class="flex justify-between items-center w-full px-margin-mobile md:px-margin-desktop py-base max-w-[1200px] mx-auto">
<!-- Brand Logo -->
<a class="font-headline-lg text-headline-lg text-primary inline-flex items-center" href="index.php"><span class="material-symbols-outlined text-primary text-3xl mr-2 align-middle" style="font-variation-settings: &quot;FILL&quot; 1;">spa</span>GlowCare</a>
<!-- Navigation Links -->
<nav class="hidden md:flex items-center gap-sm">
<!-- Active Item -->
<a class="font-label-md text-label-md px-4 py-2 text-primary font-bold border-b-2 border-primary pb-1 hover:bg-primary-container/20 rounded-t-lg transition-all duration-300 ease-in-out" href="index.php">Home</a>
<!-- Inactive Items -->
<a class="font-label-md text-label-md px-4 py-2 text-on-surface-variant font-medium hover:text-primary hover:bg-primary-container/20 rounded-lg transition-all duration-300 ease-in-out" href="about.php">About Us</a>
<a class="font-label-md text-label-md px-4 py-2 text-on-surface-variant font-medium hover:text-primary hover:bg-primary-container/20 rounded-lg transition-all duration-300 ease-in-out" href="treatment.php">Services</a>
<a class="font-label-md text-label-md px-4 py-2 text-on-surface-variant font-medium hover:text-primary hover:bg-primary-container/20 rounded-lg transition-all duration-300 ease-in-out" href="spesialis.php">Doctors</a>
<a class="font-label-md text-label-md px-4 py-2 text-on-surface-variant font-medium hover:text-primary hover:bg-primary-container/20 rounded-lg transition-all duration-300 ease-in-out" href="kontak.php">Contact</a>
</nav>
<!-- Actions -->
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
<!-- Main Content Canvas -->
<main class="pt-24 md:pt-32 pb-xl space-y-xl md:space-y-[120px]">
<!-- Hero Section -->
<section class="max-w-[1200px] mx-auto px-margin-mobile md:px-margin-desktop" id="home">
<div class="relative rounded-2xl overflow-hidden min-h-[819px] md:min-h-[600px] flex items-center shadow-ambient bg-surface-container-lowest">
<!-- Background Imagery -->
<div class="absolute inset-0 z-0">
<img alt="Luxurious clinic interior" class="w-full h-full object-cover object-[70%_50%]" data-alt="A bright, minimalist, high-end dermatology clinic interior. The space is bathed in natural, soft light coming from large windows. The decor features clean lines, warm white walls, subtle rose-gold metallic accents, and elegant beige stone flooring. A plush, cream-colored treatment chair is visible in the background alongside delicate dried floral arrangements, conveying a sense of luxury, serene tranquility, and rigorous clinical excellence." src="https://lh3.googleusercontent.com/aida-public/AB6AXuCfpoHeV63Iv1D1ZlhEEFmWj4n4zzoqCjKJvQU474pO3D-8f25a3AQezpMahHj0DZsfni_YYVbaL4qWdGAlkIJTbkEHAJ2HLZYyLYbjTpejNPsnCAcCoIbFHSM4LpEyTw7HzlvCj1uJusjJX8g5g7Uh694G8SEFhAb22HPdlLrp6RvYi5fkuzAflI5XAypKz-BmRua_8d-7JDhTJ7vcAN34DQP2K3LwOT50_jiHQm_AAJn7I8lDQlGitWj553LF0q5f84k3CnhKaJ4">
<!-- Soft gradient to ensure text readability -->
<div class="absolute inset-0 bg-gradient-to-r from-surface/95 via-surface/80 to-transparent md:w-2/3"></div>
</div>
<!-- Content Container (Glassmorphism inspired, asymmetrical) -->
<div class="relative z-10 w-full md:w-1/2 p-lg md:p-xl ml-0 md:ml-gutter">
<span class="inline-block px-3 py-1 mb-sm rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-label-sm uppercase tracking-wider">Clinical Aesthetics</span>
<h1 class="font-display-lg text-display-lg text-on-background mb-md">Pancarkan Pesona Kulit Sehat Alami.</h1>
<p class="font-body-lg text-body-lg text-on-surface-variant mb-lg max-w-md">Klinik dermatologi profesional dengan perawatan eksklusif untuk kulit impian Anda.</p>
<div class="flex flex-row items-center gap-md">
<!-- Primary Button (Rose Gold/Primary style) -->
<a href="<?= isset($_SESSION['user_id']) ? 'pages/user/dashboarduser.php?page=daftar-konsul' : 'pages/auth/Signin.php' ?>" class="font-label-md text-label-md bg-primary text-on-primary px-8 py-3 hover:bg-on-primary-fixed-variant transition-colors shadow-sm flex items-center justify-center gap-xs rounded-full">
<span class="">Book Appointment</span>
</a>
<!-- Secondary Button (Teal/Secondary style) -->
<a href="treatment.php" class="font-label-md text-label-md border-2 border-secondary text-secondary px-8 py-3 hover:bg-secondary-container hover:text-on-secondary-container transition-colors flex items-center justify-center rounded-full">Explore Services</a>
</div>
</div>
</div>
</section>
<!-- About Us Section: Asymmetric Text & Image -->
<section class="max-w-[1200px] mx-auto px-margin-mobile md:px-margin-desktop" id="about">
<div class="grid grid-cols-1 md:grid-cols-12 gap-gutter items-center">
<div class="md:col-span-5 order-2 md:order-1 relative rounded-2xl overflow-hidden shadow-ambient h-[400px] md:h-[700px]">
<img alt="Dermatologist examining skin" class="w-full h-full object-cover" data-alt="Close-up of a professional female dermatologist in a crisp white clinical coat examining a patient's face dengan magnifying dermatoscope. The lighting is bright and clinically clean, yet soft enough to feel welcoming. The environment highlights modern, high-tech medical equipment in the soft-focus background, emphasizing precision, care, and trustworthiness in a premium medical setting." src="https://lh3.googleusercontent.com/aida-public/AB6AXuAGm1ofEjZoDRttOV-P3hbmbBj3RUu2IpRT7K4yFxsgT3UIap-bjfh5jh2TOGuxat2CQOGXpfx9XusLqqL8JdlkfjIhh3aUd_QfybLcfCwbVuBvpEP2sDlEuVgBz-ZlftJYJe9G9lHFkGxq98YRqDovj0ZdIQ3WtdzLq42aGkUEElwJoN0wwAstQ1d8mB5bKi7m-LrNtr9tz67909tArV5RTeHh7xp8L7XParRGjdc3J5Et6-5B0x6vP5zWUFuzq4X4dh8tebTH0yQ">
</div>
<div class="md:col-span-6 md:col-start-7 order-1 md:order-2 space-y-md">
<h2 class="font-headline-lg text-headline-lg text-primary">Tentang GlowCare</h2>
<p class="font-body-lg text-body-lg text-on-surface-variant">
                        Visi kami adalah menjadikan setiap individu merasa percaya diri dengan kulit sehat yang natural. Kami memadukan standar medis tertinggi dengan sentuhan perawatan personal.
                    </p>
<p class="font-body-md text-body-md text-on-surface-variant mb-md">
                        Keunggulan Layanan Kami:
                    </p>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-md pt-sm"><!-- Advantage 1 -->
<div class="flex flex-col items-start p-md bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/30 transition-ambient hover:shadow-ambient-hover">
  <div class="w-12 h-12 rounded-full bg-secondary-container flex items-center justify-center mb-4">
    <span class="material-symbols-outlined text-secondary" style="font-size: 28px;">medical_services</span>
  </div>
  <div class="space-y-xs">
    <h4 class="font-headline-md text-label-md text-on-background">Dokter Spesialis Berpengalaman</h4>
    <p class="font-body-sm text-body-sm text-on-surface-variant">Ditangani langsung oleh ahlinya.</p>
  </div>
</div>
<!-- Advantage 2 -->
<div class="flex flex-col items-start p-md bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/30 transition-ambient hover:shadow-ambient-hover">
  <div class="w-12 h-12 rounded-full bg-primary-container/30 flex items-center justify-center mb-4">
    <span class="material-symbols-outlined text-primary" style="font-size: 28px;">verified_user</span>
  </div>
  <div class="space-y-xs">
    <h4 class="font-headline-md text-label-md text-on-background">Bahan &amp; Produk Bersertifikasi Aman</h4>
    <p class="font-body-sm text-body-sm text-on-surface-variant">Standar BPOM dan klinis.</p>
  </div>
</div>
<!-- Advantage 3 -->
<div class="flex flex-col items-start p-md bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/30 transition-ambient hover:shadow-ambient-hover">
  <div class="w-12 h-12 rounded-full bg-primary-container/30 flex items-center justify-center mb-4">
    <span class="material-symbols-outlined text-primary" style="font-size: 28px;">biotech</span>
  </div>
  <div class="space-y-xs">
    <h4 class="font-headline-md text-label-md text-on-background">Teknologi Perawatan Modern</h4>
    <p class="font-body-sm text-body-sm text-on-surface-variant">Inovasi terkini untuk hasil optimal.</p>
  </div>
</div>
<!-- Advantage 4 -->
<div class="flex flex-col items-start p-md bg-surface-container-lowest rounded-xl shadow-sm border border-outline-variant/30 transition-ambient hover:shadow-ambient-hover">
  <div class="w-12 h-12 rounded-full bg-secondary-container flex items-center justify-center mb-4">
    <span class="material-symbols-outlined text-secondary" style="font-size: 28px;">forum</span>
  </div>
  <div class="space-y-xs">
    <h4 class="font-headline-md text-label-md text-on-background">Konsultasi Pasca-Tindakan Gratis</h4>
    <p class="font-body-sm text-body-sm text-on-surface-variant">Layanan chat dukungan pasien.</p>
  </div>
</div></div>
</div>
</div>
</section>
<!-- Services Section: Bento Grid -->
<section class="bg-surface-container-low py-xl" id="services">
<div class="max-w-[1200px] mx-auto px-margin-mobile md:px-margin-desktop">
<div class="text-center mb-lg">
<h2 class="font-headline-lg text-headline-lg text-primary mb-xs">Layanan Kami</h2>
<p class="font-body-md text-body-md text-on-surface-variant max-w-2xl mx-auto">Solusi komprehensif untuk berbagai kebutuhan kulit Anda.</p>
</div>
<div class="space-y-xl md:space-y-[120px]">
<!-- Service 1: Acne Care (Image Left) -->
<div class="grid grid-cols-1 md:grid-cols-12 gap-gutter items-center group">
<div class="md:col-span-7 relative overflow-hidden rounded-2xl shadow-ambient transition-ambient group-hover:shadow-ambient-hover">
<img alt="Acne Care Treatment" class="w-full h-[400px] md:h-[500px] object-cover transition-transform duration-500 group-hover:scale-105" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAvrhAThQxDTgZrCDVovPmwdRJKolhluPDE91UUWSi2Oqe5DXfHZDGqvzC37MeK0nPrAUCXL8AsPAnaaLMf82DtVsq0z1LqRd8dfi8QkXxJXPyXi-W-Jf23o6mH65P430faGYNYfZ22YnMR6pbArgkgqaRXvomeMiuw3PjzfnHaGF56rQYy5PuU85j-jEEuoox5GaLYv5EAd9VQaL3QAUyJ5iRswc_Bpsz6ufFZksAVA5A1dsj43Kqu_Flh2bE7EfJpsyRwy3wdpb0">
<div class="absolute inset-0 bg-primary/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
</div>
<div class="md:col-span-5 md:pl-lg space-y-md">
<span class="inline-block px-3 py-1 rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-label-sm uppercase tracking-wider">Acne &amp; Pores</span>
<h3 class="font-headline-lg text-display-lg text-on-background">Acne Care</h3>
<p class="font-body-lg text-body-lg text-on-surface-variant">Perawatan intensif untuk mengatasi jerawat membandel dan komedo, menenangkan peradangan, dan mencegah bekas luka dengan teknologi terkini.</p>
<a class="inline-flex items-center gap-sm font-label-md text-label-md text-primary hover:text-on-primary-fixed-variant transition-colors group/link" href="treatment.php?category=Acne">Detail Layanan <span class="material-symbols-outlined text-sm transition-transform group-hover/link:translate-x-1">arrow_forward</span></a>
</div>
</div>
<!-- Service 2: Brightening Therapy (Image Right) -->
<div class="grid grid-cols-1 md:grid-cols-12 gap-gutter items-center group">
<div class="md:col-span-5 md:pr-lg order-2 md:order-1 space-y-md">
<span class="inline-block px-3 py-1 rounded-full bg-primary-container/30 text-on-primary-container font-label-sm text-label-sm uppercase tracking-wider">Pigmentation</span>
<h3 class="font-headline-lg text-display-lg text-on-background">Brightening Therapy</h3>
<p class="font-body-lg text-body-lg text-on-surface-variant">Solusi efektif untuk mencerahkan kulit kusam, memudarkan flek hitam, dan meratakan warna kulit untuk kilau alami yang sehat.</p>
<a class="inline-flex items-center gap-sm font-label-md text-label-md text-primary hover:text-on-primary-fixed-variant transition-colors group/link" href="treatment.php?category=Brightening">Detail Layanan <span class="material-symbols-outlined text-sm transition-transform group-hover/link:translate-x-1">arrow_forward</span></a>
</div>
<div class="md:col-span-7 order-1 md:order-2 relative overflow-hidden rounded-2xl shadow-ambient transition-ambient group-hover:shadow-ambient-hover">
<img alt="Brightening Therapy" class="w-full h-[400px] md:h-[500px] object-cover transition-transform duration-500 group-hover:scale-105" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAoqh0qnJ_ZtymedJ_mDDK4ofHRObnvt1AXIV5D6j9_JwwOjN1qRI2AwedAaOnnK91ZZ7q8GOeyf4sbgechDVBpIKF_G4sCXPTd_cHCGOnwdpBmta0ZTofnzX6Mk7PNM8jhzUkWz2FprCWO_kHHUvnbOGFTv1f_IMyL3IFRQzZqxyujQivTi47nUsMMsD3r_iP3O3otaX3NVkdskXaUldXM5lTIgxf0p-bNCpiQ8k3wORggrq47zVrtF_7E3b39OeCjwrxnVzurIjQ">
<div class="absolute inset-0 bg-primary/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
</div>
</div>
<!-- Service 3: Anti-Aging (Image Left) -->
<div class="grid grid-cols-1 md:grid-cols-12 gap-gutter items-center group">
<div class="md:col-span-7 relative overflow-hidden rounded-2xl shadow-ambient transition-ambient group-hover:shadow-ambient-hover">
<img alt="Anti-Aging Treatment" class="w-full h-[400px] md:h-[500px] object-cover transition-transform duration-500 group-hover:scale-105" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBHIyaZtHgB9-8Ma1wRI2qdpQ_mlBW4TikgxWo1v7SyJH2mL2ITRUYHmKl0LLqJl2wCu4IntY92a6WhnHFqhBUGeoCTQ7RcOzho9RhuIgFpZ4eElJgMh8Wx-bL67tXt95vgfRxQL530ZJ8LsJTP6H2Qy6QnjVGyIlylVcY2V1Gq2sJlZ-qzmoAG8vSndsSQMGwLMn89aX9JGN5PmIEddRGB8_KZCnp-xniPpcWoYaue8yID0bsmTa2Zw3h3pZQso8YKNrm53JwGW54">
<div class="absolute inset-0 bg-primary/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
</div>
<div class="md:col-span-5 md:pl-lg space-y-md">
<span class="inline-block px-3 py-1 rounded-full bg-tertiary-container/50 text-on-tertiary-container font-label-sm text-label-sm uppercase tracking-wider">Rejuvenation</span>
<h3 class="font-headline-lg text-display-lg text-on-background">Anti-Aging</h3>
<p class="font-body-lg text-body-lg text-on-surface-variant">Terapi peremajaan kulit premium untuk mengurangi garis halus, mengencangkan kerutan, dan mengembalikan elastisitas kulit muda Anda.</p>
<a class="inline-flex items-center gap-sm font-label-md text-label-md text-primary hover:text-on-primary-fixed-variant transition-colors group/link" href="treatment.php?category=Anti-Aging">Detail Layanan <span class="material-symbols-outlined text-sm transition-transform group-hover/link:translate-x-1">arrow_forward</span></a>
</div>
</div>
<!-- Service 4: Hair & Body Care (Image Right) -->
<div class="grid grid-cols-1 md:grid-cols-12 gap-gutter items-center group">
  <div class="md:col-span-5 md:pr-lg order-2 md:order-1 space-y-md">
    <span class="inline-block px-3 py-1 rounded-full bg-primary-container/30 text-on-primary-container font-label-sm text-label-sm uppercase tracking-wider">Body &amp; Scalp</span>
    <h3 class="font-headline-lg text-display-lg text-on-background">Hair &amp; Body Care</h3>
    <p class="font-body-lg text-body-lg text-on-surface-variant">Perawatan menyeluruh untuk kesehatan rambut dan keindahan tubuh, menggunakan teknik relaksasi premium dan produk klinis terbaik.</p>
    <a class="inline-flex items-center gap-sm font-label-md text-label-md text-primary hover:text-on-primary-fixed-variant transition-colors group/link" href="treatment.php?category=Body">Detail Layanan <span class="material-symbols-outlined text-sm transition-transform group-hover/link:translate-x-1">arrow_forward</span></a>
  </div>
  <div class="md:col-span-7 order-1 md:order-2 relative overflow-hidden rounded-2xl shadow-ambient transition-ambient group-hover:shadow-ambient-hover">
    <img alt="Hair &amp; Body Care Treatment" class="w-full h-[400px] md:h-[500px] object-cover transition-transform duration-500 group-hover:scale-105" src="https://lh3.googleusercontent.com/aida-public/AB6AXuADfMGkA9DhRp6KiHwA0dX8SR6s-dJJLL6IE3_cMpnt4M7Kic6iJ0C1yR8MIsblETp7gmf3xeblZEMAcSsDPQcsJ-NLjOgYlJ_2r50BWs2vWuuB2J8x7BlZLS1OW9AUIqeyAY-O2pGPbLW8MKII-JQE3AsDf2d_0Jc7ejoDQIwjn_Tm2VQ4gcX-KDFYvUmDpJIBocDV4RqL_ERM5PeeN6APaVme9ZHw_lTh84YDc7lkIlzssGfv6-ggHNARwXfj8oz1FpAALngyYwQ">
    <div class="absolute inset-0 bg-primary/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
  </div>
</div></div>
</div>
</section>
<!-- Doctors / Team Section -->
<section class="max-w-[1200px] mx-auto px-margin-mobile md:px-margin-desktop" id="doctors">
<div class="flex flex-col md:flex-row justify-between items-end mb-lg border-b border-outline-variant/30 pb-sm">
<div>
<h2 class="font-headline-lg text-headline-lg text-primary mb-xs">Dokter Spesialis Kami</h2>
<p class="font-body-md text-body-md text-on-surface-variant">Tim medis profesional yang siap memberikan perawatan terbaik.</p>
</div>
<a class="hidden md:flex font-label-md text-label-md text-secondary items-center gap-xs hover:underline mt-sm" href="spesialis.php">Lihat Semua <span class="material-symbols-outlined text-sm">arrow_forward</span></a>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-gutter">
<!-- Profile Card -->
<div class="bg-surface-container-lowest rounded-2xl shadow-ambient text-center group p-md flex flex-col"><div class="w-32 h-32 mx-auto rounded-full overflow-hidden mb-md border-4 border-surface shadow-sm relative"><img alt="dr. Amanda" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" src="asset/img/doctor1.png"></div><h3 class="font-headline-md text-headline-md text-on-background mb-1">dr. Amanda, Sp.DVE</h3><p class="font-label-sm text-label-sm text-secondary mb-4 uppercase tracking-wider">Plastic Surgeon</p><p class="font-body-sm text-body-sm text-on-surface-variant mb-6 leading-relaxed text-left">Berpengalaman lebih dari 10 tahun dalam prosedur bedah wajah dan rekonstruksi estetika.</p><div class="flex flex-wrap gap-2 mt-auto justify-center"><span class="px-3 py-1 rounded-full bg-secondary-container text-on-secondary-container text-xs font-medium">Facelift</span><span class="px-3 py-1 rounded-full bg-secondary-container text-on-secondary-container text-xs font-medium">Rhinoplasty</span><span class="px-3 py-1 rounded-full bg-secondary-container text-on-secondary-container text-xs font-medium">Blepharoplasty</span></div></div>
<!-- Profile Card -->
<div class="bg-surface-container-lowest rounded-2xl shadow-ambient text-center group p-md flex flex-col"><div class="w-32 h-32 mx-auto rounded-full overflow-hidden mb-md border-4 border-surface shadow-sm relative"><img alt="Dr. Chen" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" src="asset/img/doctor2.png"></div><h3 class="font-headline-md text-headline-md text-on-background mb-1">dr. Marcus Chen, Sp.KK</h3><p class="font-label-sm text-label-sm text-secondary mb-4 uppercase tracking-wider">Aesthetic Physician</p><p class="font-body-sm text-body-sm text-on-surface-variant mb-6 leading-relaxed text-left">Spesialis perawatan non-invasif dengan pendekatan personal untuk setiap pasien.</p><div class="flex flex-wrap gap-2 mt-auto justify-center"><span class="px-3 py-1 rounded-full bg-secondary-container text-on-secondary-container text-xs font-medium">CoolSculpting</span><span class="px-3 py-1 rounded-full bg-secondary-container text-on-secondary-container text-xs font-medium">Ultherapy</span><span class="px-3 py-1 rounded-full bg-secondary-container text-on-secondary-container text-xs font-medium">Thread Lifts</span></div></div>
<!-- Profile Card -->
<div class="bg-surface-container-lowest rounded-2xl shadow-ambient text-center group sm:hidden md:block p-md flex flex-col"><div class="w-32 h-32 mx-auto rounded-full overflow-hidden mb-md border-4 border-surface shadow-sm relative"><img alt="Sarah Jenkins" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" src="asset/img/doctor3.png"></div><h3 class="font-headline-md text-headline-md text-on-background mb-1">dr. Sarah Jenkins, Dipl. AAAM</h3><p class="font-label-sm text-label-sm text-secondary mb-4 uppercase tracking-wider">Dermatologist</p><p class="font-body-sm text-body-sm text-on-surface-variant mb-6 leading-relaxed text-left">Ahli dermatologi dengan keahlian khusus dalam perawatan kulit berbasis teknologi laser.</p><div class="flex flex-wrap gap-2 mt-auto justify-center"><span class="px-3 py-1 rounded-full bg-secondary-container text-on-secondary-container text-xs font-medium">Laser Treatment</span><span class="px-3 py-1 rounded-full bg-secondary-container text-on-secondary-container text-xs font-medium">Botox</span><span class="px-3 py-1 rounded-full bg-secondary-container text-on-secondary-container text-xs font-medium">Fillers</span></div></div>
</div>
</section>
<!-- Testimonials & Before-After Section -->
<section class="max-w-[1200px] mx-auto px-margin-mobile md:px-margin-desktop py-xl" id="testimonials">
<div class="text-center mb-lg">
<h2 class="font-headline-lg text-headline-lg text-primary mb-xs">Cerita Pasien Kami</h2>
<p class="font-body-md text-body-md text-on-surface-variant max-w-2xl mx-auto">Pengalaman nyata dari mereka yang telah mempercayakan perawatan kulitnya kepada GlowCare.</p>
</div>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-xl">
<!-- Testimonials Carousel/Slider (Conceptual) -->
<div class="space-y-md relative overflow-hidden">
<div class="flex gap-md overflow-x-auto snap-x snap-mandatory pb-4 hide-scrollbar">
<!-- Testimonial Card 1 -->
<div class="bg-surface-container-lowest p-md rounded-2xl shadow-ambient relative min-w-full md:min-w-[80%] snap-center">
<span class="material-symbols-outlined absolute top-4 right-4 text-primary-container opacity-50" style="font-size: 48px;">format_quote</span>
<div class="flex items-center gap-sm mb-sm">
<div class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center font-headline-md text-primary">E</div>
<div>
<div class="font-label-md text-label-md text-on-background">Elena R.</div>
<div class="font-label-sm text-label-sm text-on-surface-variant">Pasien Brightening</div>
</div>
</div>
<p class="font-body-md text-body-md text-on-surface-variant italic relative z-10">
                                "Perawatan di GlowCare sangat luar biasa. Dokter Amanda sangat detail menjelaskan progres perawatan. Flek hitam saya memudar signifikan dalam 2 bulan."
                            </p>
</div>
<!-- Testimonial Card 2 -->
<div class="bg-surface-container-lowest p-md rounded-2xl shadow-ambient relative min-w-full md:min-w-[80%] snap-center">
<span class="material-symbols-outlined absolute top-4 right-4 text-primary-container opacity-50" style="font-size: 48px;">format_quote</span>
<div class="flex items-center gap-sm mb-sm">
<div class="w-10 h-10 rounded-full bg-secondary-container flex items-center justify-center font-headline-md text-secondary">D</div>
<div>
<div class="font-label-md text-label-md text-on-background">Dina K.</div>
<div class="font-label-sm text-label-sm text-on-surface-variant">Pasien Acne Care</div>
</div>
</div>
<p class="font-body-md text-body-md text-on-surface-variant italic relative z-10">
                                "Setelah berjuang dengan jerawat bertahun-tahun, program Acne Care di sini benar-benar menyelamatkan kulit saya. Sangat merekomendasikan!"
                            </p>
</div>
</div>
<!-- Carousel Controls (Visual only for this edit) -->
<div class="flex justify-center gap-2 mt-sm">
<button class="w-3 h-3 rounded-full bg-primary"></button>
<button class="w-3 h-3 rounded-full bg-outline-variant"></button>
<button class="w-3 h-3 rounded-full bg-outline-variant"></button>
</div>
</div>
<!-- Before-After Gallery -->
<div class="bg-surface-container-low rounded-2xl p-md shadow-ambient">
<h3 class="font-headline-md text-headline-md text-primary mb-md text-center">Progres Perawatan</h3>
<div class="grid grid-cols-2 gap-sm">
<div class="rounded-xl overflow-hidden shadow-sm relative">
<img alt="Before Treatment" class="w-full h-32 object-cover" src="asset/img/before.png">
<span class="absolute bottom-2 left-2 bg-background/80 text-on-background px-2 py-0.5 text-xs rounded">Sebelum</span>
</div>
<div class="rounded-xl overflow-hidden shadow-sm relative">
<img alt="After Treatment" class="w-full h-32 object-cover" src="asset/img/after.png">
<span class="absolute bottom-2 right-2 bg-primary/90 text-on-primary px-2 py-0.5 text-xs rounded">Sesudah</span>
</div>
</div>
<p class="text-center text-xs text-on-surface-variant mt-sm mt-4">*Hasil dapat bervariasi pada setiap individu.</p>
</div>
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
<a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary transition-colors" href="index.php">Beranda</a>
<a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary transition-colors" href="treatment.php">Layanan</a>
<a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary transition-colors" href="spesialis.php">Dokter</a>
<a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary transition-colors" href="kontak.php">Contact</a>
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

</body></html>