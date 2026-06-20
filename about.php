<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - GlowCare</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Design System Configuration -->
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "surface": "#fbf9f4",
                        "surface-dim": "#dbdad5",
                        "surface-bright": "#fbf9f4",
                        "surface-container-lowest": "#ffffff",
                        "surface-container-low": "#f5f3ee",
                        "surface-container": "#f0eee9",
                        "surface-container-high": "#eae8e3",
                        "surface-container-highest": "#e4e2dd",
                        "on-surface": "#1b1c19",
                        "on-surface-variant": "#4e453c",
                        "inverse-surface": "#30312e",
                        "inverse-on-surface": "#f2f1ec",
                        "outline": "#7f756a",
                        "outline-variant": "#d1c4b8",
                        "surface-tint": "#735a39",
                        "primary": "#735a39",
                        "on-primary": "#ffffff",
                        "primary-container": "#e0c097",
                        "on-primary-container": "#654d2d",
                        "inverse-primary": "#e1c198",
                        "secondary": "#386663",
                        "on-secondary": "#ffffff",
                        "secondary-container": "#bbece8",
                        "on-secondary-container": "#3e6c69",
                        "tertiary": "#586062",
                        "on-tertiary": "#ffffff",
                        "tertiary-container": "#bfc6c8",
                        "on-tertiary-container": "#4b5355",
                        "error": "#ba1a1a",
                        "on-error": "#ffffff",
                        "error-container": "#ffdad6",
                        "on-error-container": "#93000a",
                        "primary-fixed": "#ffddb2",
                        "primary-fixed-dim": "#e1c198",
                        "on-primary-fixed": "#291800",
                        "on-primary-fixed-variant": "#594323",
                        "secondary-fixed": "#bbece8",
                        "secondary-fixed-dim": "#a0cfcc",
                        "on-secondary-fixed": "#00201f",
                        "on-secondary-fixed-variant": "#1e4e4c",
                        "tertiary-fixed": "#dde4e6",
                        "tertiary-fixed-dim": "#c1c8ca",
                        "on-tertiary-fixed": "#161d1f",
                        "on-tertiary-fixed-variant": "#41484a",
                        "background": "#fbf9f4",
                        "on-background": "#1b1c19",
                        "surface-variant": "#e4e2dd"
                    },
                    borderRadius: {
                        "sm": "0.25rem",
                        "DEFAULT": "0.5rem",
                        "md": "0.75rem",
                        "lg": "1rem",
                        "xl": "1.5rem",
                        "full": "9999px"
                    },
                    spacing: {
                        "base": "8px",
                        "xs": "4px",
                        "sm": "12px",
                        "md": "24px",
                        "lg": "48px",
                        "xl": "80px",
                        "gutter": "24px",
                        "margin-mobile": "16px",
                        "margin-desktop": "64px"
                    },
                    fontFamily: {
                        "display-lg": ["Playfair Display"],
                        "headline-lg": ["Playfair Display"],
                        "headline-lg-mobile": ["Playfair Display"],
                        "headline-md": ["Playfair Display"],
                        "body-lg": ["Inter"],
                        "body-md": ["Inter"],
                        "body-sm": ["Inter"],
                        "label-md": ["Inter"],
                        "label-sm": ["Inter"]
                    },
                    fontSize: {
                        "display-lg": ["48px", { lineHeight: "1.2", letterSpacing: "-0.02em", fontWeight: "700" }],
                        "headline-lg": ["32px", { lineHeight: "1.3", fontWeight: "600" }],
                        "headline-lg-mobile": ["24px", { lineHeight: "1.3", fontWeight: "600" }],
                        "headline-md": ["24px", { lineHeight: "1.4", fontWeight: "500" }],
                        "body-lg": ["18px", { lineHeight: "1.6", fontWeight: "400" }],
                        "body-md": ["16px", { lineHeight: "1.5", fontWeight: "400" }],
                        "body-sm": ["14px", { lineHeight: "1.5", fontWeight: "400" }],
                        "label-md": ["14px", { lineHeight: "1.2", letterSpacing: "0.05em", fontWeight: "600" }],
                        "label-sm": ["12px", { lineHeight: "1.2", fontWeight: "500" }]
                    }
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 24;
        }
        /* Custom warm ambient shadow as per style guide */
        .shadow-ambient {
            box-shadow: 0 8px 32px rgba(115, 90, 57, 0.06);
        }
    </style>
</head>
<body class="bg-background text-on-background antialiased selection:bg-primary-container selection:text-on-primary-container font-body-md text-body-md pt-24">

    <!-- Header -->
    <header class="fixed w-full z-50 bg-surface/80 backdrop-blur-md shadow-sm transition-all duration-300 ease-in-out top-0">
        <div class="flex justify-between items-center w-full px-margin-mobile md:px-margin-desktop py-base max-w-[1200px] mx-auto">
            <!-- Brand Logo -->
            <a class="font-headline-lg text-headline-lg text-primary inline-flex items-center" href="index.php">
                <span class="material-symbols-outlined text-primary text-3xl mr-2 align-middle" style="font-variation-settings: 'FILL' 1;">spa</span>GlowCare
            </a>
            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center gap-sm">
                <a class="font-label-md text-label-md px-4 py-2 text-on-surface-variant font-medium hover:text-primary hover:bg-primary-container/20 rounded-lg transition-all duration-300 ease-in-out" href="index.php#home">Home</a>
                <a class="font-label-md text-label-md px-4 py-2 text-primary font-bold border-b-2 border-primary pb-1 hover:bg-primary-container/20 rounded-t-lg transition-all duration-300 ease-in-out" href="about.php">About Us</a>
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

    <!-- Main Content -->
    <main class="w-full">
        
        <!-- Our Story Section -->
        <section class="max-w-[1200px] mx-auto px-margin-mobile md:px-margin-desktop py-xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-gutter items-center">
                <div class="order-2 md:order-1 pr-0 md:pr-lg">
                    <h2 class="font-headline-lg text-headline-lg text-primary mb-md">Our Story</h2>
                    <p class="font-body-md text-body-md text-on-surface-variant mb-4">
                        Founded on the principle that true beauty is an expression of deep, holistic well-being, GlowCare has redefined aesthetic medicine. We blend clinical precision with luxurious tranquility to reveal your most radiant self.
                    </p>
                </div>
                <div class="order-1 md:order-2">
                    <div class="rounded-lg overflow-hidden h-[400px] md:h-[500px]">
                        <img src="asset/img/detail_treatment.png" alt="GlowCare Treatment" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>
        </section>

        <!-- Mission & Vision Section -->
        <section class="max-w-[1200px] mx-auto px-margin-mobile md:px-margin-desktop py-xl">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-gutter">
                <div class="md:col-span-2 bg-surface-container-lowest rounded-lg p-lg shadow-ambient">
                    <h2 class="font-headline-md text-headline-md text-primary mb-md">Mission & Vision</h2>
                    <p class="font-body-md text-body-md text-on-surface-variant mb-6">
                        To empower individuals through personalized, cutting-edge aesthetic treatments that enhance natural beauty and foster profound self-confidence.
                    </p>
                    <p class="font-body-md text-body-md text-on-surface-variant">
                        We envision a world where aesthetic care is a seamless integration of advanced medical science and deeply restorative personal care, setting a new standard for luxury wellness.
                    </p>
                </div>
                <div class="md:col-span-1 bg-primary-container rounded-lg p-lg flex flex-col items-center justify-center text-center shadow-ambient">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center text-primary mb-md">
                        <span class="material-symbols-outlined text-4xl">verified</span>
                    </div>
                    <h3 class="font-headline-md text-headline-md text-on-primary-container mb-xs">Excellence</h3>
                    <p class="font-body-sm text-body-sm text-on-primary-container">In every detail, every procedure.</p>
                </div>
            </div>
        </section>

        <!-- Our Core Values Section -->
        <section class="max-w-[1200px] mx-auto px-margin-mobile md:px-margin-desktop py-xl">
            <div class="text-center max-w-2xl mx-auto mb-xl">
                <h2 class="font-headline-lg text-headline-lg text-primary mb-xs">Our Core Values</h2>
                <p class="font-body-md text-body-md text-on-surface-variant">
                    The pillars that uphold our commitment to you and guide every interaction at GlowCare.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-gutter">
                <!-- Value 1 -->
                <div class="bg-surface-container-lowest p-md rounded-lg shadow-ambient">
                    <div class="w-10 h-10 rounded-full bg-secondary-container text-on-secondary-container flex items-center justify-center mb-md">
                        <span class="material-symbols-outlined text-sm">psychology</span>
                    </div>
                    <h3 class="font-headline-md text-headline-md text-on-background mb-sm">Personalized Care</h3>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">
                        We recognize that every face is unique. Our approach is entirely bespoke, tailored to your specific anatomy and desires.
                    </p>
                </div>
                <!-- Value 2 -->
                <div class="bg-surface-container-lowest p-md rounded-lg shadow-ambient">
                    <div class="w-10 h-10 rounded-full bg-secondary-container text-on-secondary-container flex items-center justify-center mb-md">
                        <span class="material-symbols-outlined text-sm">science</span>
                    </div>
                    <h3 class="font-headline-md text-headline-md text-on-background mb-sm">Innovation</h3>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">
                        We continually invest in the most advanced, clinically proven technologies to ensure optimal safety and exceptional results.
                    </p>
                </div>
                <!-- Value 3 -->
                <div class="bg-surface-container-lowest p-md rounded-lg shadow-ambient">
                    <div class="w-10 h-10 rounded-full bg-secondary-container text-on-secondary-container flex items-center justify-center mb-md">
                        <span class="material-symbols-outlined text-sm">verified_user</span>
                    </div>
                    <h3 class="font-headline-md text-headline-md text-on-background mb-sm">Integrity</h3>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">
                        Honesty in consultation is our hallmark. We only recommend treatments that will genuinely benefit you, prioritizing your long-term wellness.
                    </p>
                </div>
            </div>
        </section>

        <!-- Meet Our Top Specialists Section -->
        <section class="max-w-[1200px] mx-auto px-margin-mobile md:px-margin-desktop py-xl">
            <div class="mb-lg">
                <h2 class="font-headline-lg text-headline-lg text-primary mb-xs">Meet Our Top Specialists</h2>
                <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl">
                    A team of renowned aesthetic physicians dedicated to artistic precision and clinical safety.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-gutter">
                <!-- Specialist 1 -->
                <div class="bg-surface-container-lowest rounded-lg overflow-hidden shadow-ambient">
                    <img src="asset/img/doctor1.png" alt="Dr. Amanda Hayes" class="w-full h-64 object-cover object-top">
                    <div class="p-md text-center">
                        <h3 class="font-headline-md text-headline-md text-on-background mb-1">Dr. Amanda Hayes</h3>
                        <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider mb-3">Medical Director, Dermatologist</p>
                        <div class="flex justify-center text-primary-fixed-dim mb-4">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1; font-size: 16px;">star</span>
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1; font-size: 16px;">star</span>
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1; font-size: 16px;">star</span>
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1; font-size: 16px;">star</span>
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1; font-size: 16px;">star</span>
                        </div>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">
                            Specializing in holistic facial rejuvenation and advanced laser therapies, Dr. Hayes brings over 15 years of clinical excellence to GlowCare.
                        </p>
                    </div>
                </div>
                <!-- Specialist 2 -->
                <div class="bg-surface-container-lowest rounded-lg overflow-hidden shadow-ambient">
                    <img src="asset/img/doctor2.png" alt="Dr. Marcus Chen" class="w-full h-64 object-cover object-top">
                    <div class="p-md text-center">
                        <h3 class="font-headline-md text-headline-md text-on-background mb-1">Dr. Marcus Chen</h3>
                        <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider mb-3">Aesthetic Physician</p>
                        <div class="flex justify-center text-primary-fixed-dim mb-4">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1; font-size: 16px;">star</span>
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1; font-size: 16px;">star</span>
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1; font-size: 16px;">star</span>
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1; font-size: 16px;">star</span>
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1; font-size: 16px;">star</span>
                        </div>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">
                            Known for his meticulous approach to dermal fillers and neuromodulators, Dr. Chen focuses on restoring youthful contours naturally.
                        </p>
                    </div>
                </div>
                <!-- Specialist 3 -->
                <div class="bg-surface-container-lowest rounded-lg overflow-hidden shadow-ambient">
                    <img src="asset/img/doctor3.png" alt="Dr. Sarah Jenkins" class="w-full h-64 object-cover object-top">
                    <div class="p-md text-center">
                        <h3 class="font-headline-md text-headline-md text-on-background mb-1">Dr. Sarah Jenkins</h3>
                        <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider mb-3">Plastic Surgeon</p>
                        <div class="flex justify-center text-primary-fixed-dim mb-4">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1; font-size: 16px;">star</span>
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1; font-size: 16px;">star</span>
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1; font-size: 16px;">star</span>
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1; font-size: 16px;">star</span>
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1; font-size: 16px;">star</span>
                        </div>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">
                            Dr. Jenkins blends surgical expertise with minimally invasive techniques, offering a comprehensive spectrum of transformative care.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-surface-container-highest w-full py-lg px-margin-mobile md:px-margin-desktop grid grid-cols-1 md:grid-cols-4 items-start gap-lg full-width bottom mt-xl" id="contact">
        <!-- Branding & Social -->
        <div class="col-span-1 md:col-span-1 space-y-md">
            <a class="font-headline-lg text-headline-lg text-primary" href="#">GlowCare</a>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Klinik kecantikan tepercaya untuk kulit sehat alami Anda.</p>
            <div class="flex gap-sm">
                <a class="text-primary hover:text-on-primary-fixed-variant transition-colors" href="https://www.instagram.com" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"></path></svg></a>
                <a class="text-primary hover:text-on-primary-fixed-variant transition-colors" href="https://www.youtube.com" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"></path></svg></a>
                <a class="text-primary hover:text-on-primary-fixed-variant transition-colors" href="https://www.facebook.com" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"><path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"></path></svg></a>
            </div>
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

</body>
</html>
