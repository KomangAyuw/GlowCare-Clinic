<?php
session_start();
$old = $_SESSION['old_input'] ?? [];
$old_nama = htmlspecialchars($old['nama'] ?? '');
$old_telp = htmlspecialchars($old['telp'] ?? '');
$old_email = htmlspecialchars($old['email'] ?? '');
$old_pesan = htmlspecialchars($old['pesan'] ?? '');
unset($_SESSION['old_input']);
?>
<!DOCTYPE html>
<html class="scroll-smooth" lang="en" style="">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Contact Us - GlowCare</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&amp;family=Playfair+Display:wght@500;600;700&amp;display=swap"
        rel="stylesheet">
    <script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                "colors": {
                    "secondary-container": "#bbece8",
                    "surface-container-highest": "#e4e2dd",
                    "surface-container-lowest": "#ffffff",
                    "primary-fixed-dim": "#e1c198",
                    "secondary-fixed": "#bbece8",
                    "on-primary": "#ffffff",
                    "on-surface-variant": "#4e453c",
                    "primary-container": "#e0c097",
                    "surface-tint": "#735a39",
                    "surface-dim": "#dbdad5",
                    "on-error-container": "#93000a",
                    "surface-container-high": "#eae8e3",
                    "on-background": "#1b1c19",
                    "secondary-fixed-dim": "#a0cfcc",
                    "on-surface": "#1b1c19",
                    "surface-container-low": "#f5f3ee",
                    "inverse-surface": "#30312e",
                    "secondary": "#386663",
                    "on-tertiary-fixed": "#161d1f",
                    "on-error": "#ffffff",
                    "tertiary-fixed-dim": "#c1c8ca",
                    "on-primary-fixed-variant": "#594323",
                    "inverse-on-surface": "#f2f1ec",
                    "error-container": "#ffdad6",
                    "primary": "#735a39",
                    "tertiary-fixed": "#dde4e6",
                    "on-tertiary-container": "#4b5355",
                    "surface-variant": "#e4e2dd",
                    "on-primary-fixed": "#291800",
                    "background": "#fbf9f4",
                    "outline-variant": "#d1c4b8",
                    "on-tertiary": "#ffffff",
                    "on-secondary": "#ffffff",
                    "on-tertiary-fixed-variant": "#41484a",
                    "on-secondary-fixed": "#00201f",
                    "surface-container": "#f0eee9",
                    "surface-bright": "#fbf9f4",
                    "tertiary-container": "#bfc6c8",
                    "tertiary": "#586062",
                    "on-secondary-container": "#3e6c69",
                    "on-primary-container": "#654d2d",
                    "inverse-primary": "#e1c198",
                    "surface": "#fbf9f4",
                    "primary-fixed": "#ffddb2",
                    "outline": "#7f756a",
                    "on-secondary-fixed-variant": "#1e4e4c",
                    "error": "#ba1a1a"
                },
                "borderRadius": {
                    "DEFAULT": "0.25rem",
                    "lg": "0.5rem",
                    "xl": "0.75rem",
                    "full": "9999px",
                    "2xl": "1rem",
                    "3xl": "1.5rem"
                },
                "spacing": {
                    "margin-desktop": "64px",
                    "lg": "48px",
                    "margin-mobile": "16px",
                    "xs": "4px",
                    "base": "8px",
                    "sm": "12px",
                    "gutter": "24px",
                    "md": "24px",
                    "xl": "80px",
                    "2xl": "120px"
                },
                "fontFamily": {
                    "display-lg": ["Playfair Display"],
                    "headline-md": ["Playfair Display"],
                    "headline-lg": ["Playfair Display"],
                    "body-md": ["Inter"],
                    "label-md": ["Inter"],
                    "body-lg": ["Inter"],
                    "label-sm": ["Inter"],
                    "body-sm": ["Inter"],
                    "headline-lg-mobile": ["Playfair Display"]
                },
                "fontSize": {
                    "display-lg": ["48px", {
                        "lineHeight": "1.2",
                        "letterSpacing": "-0.02em",
                        "fontWeight": "700"
                    }],
                    "headline-md": ["24px", {
                        "lineHeight": "1.4",
                        "fontWeight": "500"
                    }],
                    "headline-lg": ["32px", {
                        "lineHeight": "1.3",
                        "fontWeight": "600"
                    }],
                    "body-md": ["16px", {
                        "lineHeight": "1.5",
                        "fontWeight": "400"
                    }],
                    "label-md": ["14px", {
                        "lineHeight": "1.2",
                        "letterSpacing": "0.05em",
                        "fontWeight": "600"
                    }],
                    "body-lg": ["18px", {
                        "lineHeight": "1.6",
                        "fontWeight": "400"
                    }],
                    "label-sm": ["12px", {
                        "lineHeight": "1.2",
                        "fontWeight": "500"
                    }],
                    "body-sm": ["14px", {
                        "lineHeight": "1.5",
                        "fontWeight": "400"
                    }],
                    "headline-lg-mobile": ["24px", {
                        "lineHeight": "1.3",
                        "fontWeight": "600"
                    }]
                },
                "boxShadow": {
                    "ambient": "0 4px 20px rgba(115, 90, 57, 0.05)"
                }
            }
        }
    }
    </script>
    <style>
    body {
        background-color: theme('colors.background');
        color: theme('colors.on-background');
        font-family: 'Inter', sans-serif;
    }

    .lux-input {
        width: 100%;
        background-color: transparent;
        border: none;
        border-bottom: 1px solid theme('colors.outline-variant');
        padding: theme('spacing.sm') 0;
        font-family: theme('fontFamily.body-md');
        font-size: 16px;
        color: theme('colors.on-surface');
        transition: all 0.3s ease;
    }

    .lux-input:focus {
        outline: none;
        border-bottom-color: theme('colors.primary');
        box-shadow: 0 1px 0 0 theme('colors.primary');
    }

    .lux-input::placeholder {
        color: theme('colors.outline');
        opacity: 0.7;
    }
    </style>
</head>

<body class="min-h-screen flex flex-col antialiased bg-background text-on-background font-body-md text-body-md">
    <!-- TopAppBar -->
    <header
        class="fixed w-full z-50 bg-surface/80 backdrop-blur-md shadow-sm transition-all duration-300 ease-in-out docked full-width top-0">
        <div
            class="flex justify-between items-center w-full px-margin-mobile md:px-margin-desktop py-base max-w-[1200px] mx-auto">
            <!-- Brand Logo -->
            <a class="font-headline-lg text-headline-lg text-primary inline-flex items-center" href="../index.php"><span
                    class="material-symbols-outlined text-primary text-3xl mr-2 align-middle"
                    style="font-variation-settings: &quot;FILL&quot; 1;">spa</span>GlowCare</a>
            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center gap-sm">
                <!-- Inactive Items -->
                <a class="font-label-md text-label-md px-4 py-2 text-on-surface-variant font-medium hover:text-primary hover:bg-primary-container/20 rounded-lg transition-all duration-300 ease-in-out"
                    href="../index.php">Home</a>
                <a class="font-label-md text-label-md px-4 py-2 text-on-surface-variant font-medium hover:text-primary hover:bg-primary-container/20 rounded-lg transition-all duration-300 ease-in-out"
                    href="about.php">About Us</a>
                <a class="font-label-md text-label-md px-4 py-2 text-on-surface-variant font-medium hover:text-primary hover:bg-primary-container/20 rounded-lg transition-all duration-300 ease-in-out"
                    href="treatment/treatment.php">Services</a>
                <a class="font-label-md text-label-md px-4 py-2 text-on-surface-variant font-medium hover:text-primary hover:bg-primary-container/20 rounded-lg transition-all duration-300 ease-in-out"
                    href="spesialis.php">Doctors</a>
                <!-- Active Item (Contact) -->
                <a class="font-label-md text-label-md px-4 py-2 text-primary font-bold border-b-2 border-primary pb-1 hover:bg-primary-container/20 rounded-t-lg transition-all duration-300 ease-in-out"
                    href="kontak.php">Contact</a>
            </nav>
            <!-- Actions -->
            <div class="flex items-center gap-sm">
                <?php if (isset($_SESSION['user_id'])): 
    $dashboard_url = 'user/dashboarduser.php';
    if (isset($_SESSION['role'])) {
        if ($_SESSION['role'] === 'admin') {
            $dashboard_url = 'admin/dashboard.php';
        } elseif ($_SESSION['role'] === 'dokter') {
            $dashboard_url = 'dokter/dashboardDokter.php';
        }
    }
?>
                <a href="<?= $dashboard_url ?>"
                    class="font-label-md text-label-md text-primary hover:bg-primary-container/20 px-4 py-2 rounded-lg transition-all duration-300 ease-in-out inline-flex items-center justify-center">Dashboard</a>
                <a href="../backend/auth/logout.php"
                    class="font-label-md text-label-md bg-error text-on-error px-6 py-2 rounded-lg hover:opacity-90 shadow-sm transition-all duration-300 ease-in-out inline-flex items-center justify-center">Logout</a>
                <?php else: ?>
                <a href="auth/Signin.php"
                    class="font-label-md text-label-md text-primary hover:bg-primary-container/20 px-4 py-2 rounded-lg transition-all duration-300 ease-in-out inline-flex items-center justify-center">Login</a>
                <a href="auth/SignUp.php"
                    class="font-label-md text-label-md bg-primary text-on-primary px-6 py-2 rounded-lg hover:bg-on-primary-fixed-variant shadow-sm transition-all duration-300 ease-in-out inline-flex items-center justify-center">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <!-- Main Content -->
    <main class="flex-grow bg-background">
        <!-- Hero Section -->
        <section class="relative w-full min-h-[300px] flex items-center justify-center overflow-hidden h-[450px]">
            <div class="absolute inset-0 z-0">
                <img alt="Serene clinical reception" class="w-full h-full object-cover object-center"
                    src="../asset/img/consultation.png">
                <div class="absolute inset-0 bg-background/70 backdrop-blur-[2px]"></div>
            </div>
            <div class="relative z-10 text-center px-margin-mobile max-w-3xl mx-auto">
                <h1 class="font-display-lg text-display-lg md:text-[56px] text-primary mb-6">Get in Touch</h1>
                <p class="font-body-lg text-body-lg text-on-surface-variant leading-relaxed">Experience bespoke clinical
                    care. Our concierge team is ready to assist you in designing your personalized aesthetic journey.
                </p>
            </div>
        </section>
        <!-- Contact Grid Section -->
        <section class="max-w-[1200px] mx-auto px-margin-mobile md:px-margin-desktop py-xl md:py-xl">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-xl items-start gap-md">
                <div
                    class="lg:col-span-7 bg-surface-container-lowest p-8 md:p-12 rounded-3xl shadow-ambient border border-surface-container-low h-full">
                    <h2 class="font-display-lg text-headline-lg text-on-surface mb-10">Send an Inquiry</h2>

                    <?php if (isset($_SESSION['sukses'])): ?>
                    <div
                        class="bg-secondary-container/40 border border-secondary/50 text-secondary p-4 rounded-2xl mb-8 font-body-md flex items-start gap-3 shadow-ambient">
                        <span class="material-symbols-outlined text-secondary text-2xl mt-0.5"
                            style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        <div>
                            <h4 class="font-bold text-on-secondary-container mb-1">Pesan Terkirim</h4>
                            <p class="text-body-sm text-on-surface-variant leading-relaxed">Terima kasih, pesan Anda
                                telah terkirim dengan sukses. Tim concierge kami akan segera menghubungi Anda kembali.
                            </p>
                        </div>
                    </div>
                    <?php unset($_SESSION['sukses']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['errors'])): ?>
                    <div
                        class="bg-error-container/30 border border-error/50 text-error p-4 rounded-2xl mb-8 font-body-md flex items-start gap-3 shadow-ambient">
                        <span class="material-symbols-outlined text-error text-2xl mt-0.5"
                            style="font-variation-settings: 'FILL' 1;">error</span>
                        <div>
                            <h4 class="font-bold text-on-error-container mb-1">Gagal Mengirim Pesan</h4>
                            <ul class="list-disc list-inside text-body-sm text-on-surface-variant space-y-1">
                                <?php foreach ($_SESSION['errors'] as $_err): ?>
                                <li><?= htmlspecialchars($_err) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <?php unset($_SESSION['errors']); ?>
                    <?php endif; ?>

                    <form action="../backend/kontak/pesan_kontak.php" method="POST" class="space-y-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="flex flex-col gap-2">
                                <label class="font-label-md text-on-surface-variant ml-1" for="name">Full Name *</label>
                                <input
                                    class="w-full bg-surface-container-low border border-outline-variant rounded-xl px-4 py-3 font-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all"
                                    id="name" name="nama" value="<?= $old_nama ?>" placeholder="Enter your full name"
                                    required="" type="text">
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="font-label-md text-on-surface-variant ml-1" for="email">Email Address
                                    *</label>
                                <input
                                    class="w-full bg-surface-container-low border border-outline-variant rounded-xl px-4 py-3 font-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all"
                                    id="email" name="email" value="<?= $old_email ?>" placeholder="email@example.com"
                                    required="" type="email">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="flex flex-col gap-2">
                                <label class="font-label-md text-on-surface-variant ml-1" for="phone">Phone
                                    Number</label>
                                <input
                                    class="w-full bg-surface-container-low border border-outline-variant rounded-xl px-4 py-3 font-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all"
                                    id="phone" name="telp" value="<?= $old_telp ?>" placeholder="+1 (555) 000-0000"
                                    type="tel">
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="font-label-md text-on-surface-variant ml-1" for="message">How can we assist
                                you today? *</label>
                            <textarea
                                class="w-full bg-surface-container-low border border-outline-variant rounded-xl px-4 py-3 font-body-md focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all resize-none"
                                id="message" name="pesan" placeholder="Tell us about your aesthetic goals..."
                                required="" rows="5"><?= $old_pesan ?></textarea>
                        </div>
                        <div class="pt-4">
                            <button
                                class="bg-primary text-on-primary px-12 py-4 rounded-xl font-label-md hover:bg-surface-tint transition-all duration-300 w-full md:w-auto hover:-translate-y-1 shadow-lg shadow-primary/10"
                                type="submit">
                                Submit Inquiry
                            </button>
                        </div>
                    </form>
                </div>
                <!-- Right Column: Clinic Details -->
                <div class="lg:col-span-5 space-y-12 h-full flex flex-col justify-between py-2">
                    <div class="space-y-12">
                        <div class="space-y-8">
                            <div>
                                <h3 class="font-display-lg text-label-md text-primary uppercase tracking-[0.2em] mb-8">
                                    Clinic Location</h3>
                                <div class="flex items-start gap-6">
                                    <div class="p-4 bg-surface-container-low rounded-2xl text-primary">
                                        <span class="material-symbols-outlined"
                                            style="font-variation-settings: 'FILL' 1;">location_on</span>
                                    </div>
                                    <div class="pt-1">
                                        <p class="font-display-lg text-headline-md text-on-surface mb-2">Mataram</p>
                                        <p class="font-body-md text-on-surface-variant leading-relaxed">Mataram, Nusa
                                            Tenggara Barat,<br>Indonesia</p>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h3 class="font-display-lg text-label-md text-primary uppercase tracking-[0.2em] mb-8">
                                    Contact Info</h3>
                                <div class="space-y-6">
                                    <a class="flex items-center gap-6 cursor-pointer w-fit" href="tel:+1234567890">
                                        <div class="p-4 bg-surface-container-low rounded-2xl text-primary">
                                            <span class="material-symbols-outlined"
                                                style="font-variation-settings: 'FILL' 1;">phone</span>
                                        </div>
                                        <span class="font-body-lg text-on-surface">+1 234 567 890</span>
                                    </a>
                                    <a class="flex items-center gap-6 cursor-pointer w-fit"
                                        href="mailto:hello@glowcare.com">
                                        <div class="p-4 bg-surface-container-low rounded-2xl text-primary">
                                            <span class="material-symbols-outlined"
                                                style="font-variation-settings: 'FILL' 1;">mail</span>
                                        </div>
                                        <span class="font-body-lg text-on-surface">hello@glowcare.com</span>
                                    </a>
                                </div>
                            </div>
                            <div>
                                <h3 class="font-display-lg text-label-md text-primary uppercase tracking-[0.2em] mb-8">
                                    Operating Hours</h3>
                                <div class="flex items-start gap-6">
                                    <div class="p-4 bg-surface-container-low rounded-2xl text-primary">
                                        <span class="material-symbols-outlined"
                                            style="font-variation-settings: 'FILL' 1;">schedule</span>
                                    </div>
                                    <div class="space-y-3 w-full max-w-[300px] pt-1">
                                        <div
                                            class="flex justify-between items-center border-b border-surface-container-low pb-3">
                                            <p class="font-body-md text-on-surface">Mon - Fri</p>
                                            <p class="font-body-md text-on-surface-variant">09:00 - 20:00</p>
                                        </div>
                                        <div
                                            class="flex justify-between items-center border-b border-surface-container-low pb-3">
                                            <p class="font-body-md text-on-surface">Saturday</p>
                                            <p class="font-body-md text-on-surface-variant">10:00 - 18:00</p>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <p class="font-body-md text-on-surface">Sunday</p>
                                            <p class="font-body-md text-on-surface-variant">Closed</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pt-8 border-t border-surface-container-low">
                            <h3 class="font-display-lg text-label-md text-primary uppercase tracking-[0.2em] mb-8">
                                Connect With Us</h3>
                            <div class="flex gap-6">
                                <a class="w-14 h-14 rounded-2xl border border-outline-variant flex items-center justify-center text-on-surface-variant hover:border-primary hover:text-primary hover:bg-surface-container-low transition-all shadow-sm"
                                    href="https://www.instagram.com" target="_blank" rel="noopener noreferrer">
                                    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0 3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z">
                                        </path>
                                    </svg>
                                </a>
                                <a class="w-14 h-14 rounded-2xl border border-outline-variant flex items-center justify-center text-on-surface-variant hover:border-primary hover:text-primary hover:bg-surface-container-low transition-all shadow-sm"
                                    href="https://www.facebook.com" target="_blank" rel="noopener noreferrer">
                                    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z">
                                        </path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Map Section -->
        <section class="w-full h-[500px] relative mt-lg md:h-[500px]">
            <div class="absolute inset-0 w-full h-full">
                <img alt="Map location of GlowCare clinic in Mataram" class="w-full h-full object-cover"
                    src="../asset/img/maps.jpg">
            </div>
            <!-- Overlay Card on Map -->
            <div class="absolute inset-0 flex items-center justify-start pointer-events-none">
                <div
                    class="ml-margin-mobile md:ml-margin-desktop bg-surface-container-lowest/95 backdrop-blur-md p-8 rounded-3xl shadow-2xl border border-primary/10 max-w-sm pointer-events-auto">
                    <div class="w-12 h-1 bg-primary mb-6"></div>
                    <h4 class="font-headline-md text-headline-md text-primary mb-4">Visit Our Clinic</h4>
                    <p class="font-body-md text-on-surface-variant mb-8 leading-relaxed">Located in the heart of
                        Mataram, our clinic offers a sanctuary for clinical aesthetics with dedicated private parking
                        for our patients.</p>
                    <a class="inline-flex items-center gap-3 bg-primary text-on-primary px-6 py-3 rounded-xl font-label-md hover:bg-surface-tint transition-all"
                        href="https://maps.google.com/?q=Universitas+Mataram+Lombok+NTB+Indonesia" target="_blank"
                        rel="noopener noreferrer">
                        Get Directions
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>
            </div>
        </section>
    </main>
    <!-- JSON Component: Footer -->
    <footer
        class="bg-surface-container-highest w-full py-lg px-margin-mobile md:px-margin-desktop grid grid-cols-1 md:grid-cols-4 items-start gap-lg full-width bottom mt-xl"
        id="contact">
        <!-- Branding & Social -->
        <div class="col-span-1 md:col-span-1 space-y-md">
            <a class="font-headline-lg text-headline-lg text-primary" href="#">GlowCare</a>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Klinik kecantikan tepercaya untuk kulit sehat
                alami Anda.</p>
            <div class="flex gap-sm"><a class="text-primary hover:text-on-primary-fixed-variant transition-colors"
                    href="https://www.instagram.com" target="_blank" rel="noopener noreferrer"
                    aria-label="Instagram"><svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                        <path
                            d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z">
                        </path>
                    </svg></a><a class="text-primary hover:text-on-primary-fixed-variant transition-colors"
                    href="https://www.youtube.com" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><svg
                        class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                        <path
                            d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z">
                        </path>
                    </svg></a><a class="text-primary hover:text-on-primary-fixed-variant transition-colors"
                    href="https://www.facebook.com" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><svg
                        class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                        <path
                            d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z">
                        </path>
                    </svg></a></div>
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
            <a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary transition-colors"
                href="../index.php">Beranda</a>
            <a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary transition-colors"
                href="treatment/treatment.php">Layanan</a>
            <a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary transition-colors"
                href="spesialis.php">Dokter</a>
            <a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary transition-colors"
                href="kontak.php">Contact</a>
        </div>
        <!-- Map Placeholder -->
        <div class="col-span-1 md:col-span-1">
            <h4 class="font-label-md text-label-md text-on-background mb-xs">Lokasi Kami</h4>
            <div class="w-full h-32 rounded-lg bg-surface-container-high overflow-hidden relative">
                <!-- Google Maps Embed Placeholder -->
                <iframe allowfullscreen="" class="w-full h-full border-0" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63123.123456789!2d116.1165!3d-8.5833!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dcdbf406173076b%3A0x4030bf45e4b27a0!2sMataram%2C%20Kota%20Mataram%2C%20Nusa%20Tenggara%20Bar.!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid"></iframe>
            </div>
        </div>
        <!-- Copyright Line -->
        <div class="col-span-1 md:col-span-4 pt-md border-t border-outline-variant/30 text-center mt-md">
            <span class="font-body-sm text-body-sm text-on-surface-variant">© 2026 GlowCare Clinical Aesthetics. All
                rights reserved.</span>
        </div>
    </footer>


</body>

</html>