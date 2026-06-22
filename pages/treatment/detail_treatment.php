<?php
session_start();
$conn = require_once '../../backend/config/koneksi.php';

// Get treatment by ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: treatment.php'); exit; }

$stmt = $conn->prepare("SELECT * FROM treatment WHERE id=? AND status='Aktif'");
$stmt->execute([$id]);
$tr = $stmt->fetch();

if (!$tr) { header('Location: treatment.php'); exit; }

$nama     = htmlspecialchars($tr['nama']);
$kategori = htmlspecialchars($tr['kategori']);
$durasi   = htmlspecialchars($tr['durasi'] ?? '60 Menit');
$desc     = htmlspecialchars($tr['deskripsi_panjang'] ?? $tr['deskripsi'] ?? '');
$img      = $tr['gambar_url'] ?? '';
if ($img && strpos($img, 'http') !== 0) {
    if (strpos($img, 'asset/') === 0) {
        $img = '../../' . $img;
    } else {
        $img = '../../backend/uploads/' . $img;
    }
}
$img      = htmlspecialchars($img);

// Map categories to icons & benefit data
$category_data = [
    'Acne & Clear Skin' => [
        'badge' => 'Dermatology Care',
        'science_title' => 'The Science of Clear Skin',
        'science_p1' => 'Our acne treatments use clinically-proven protocols to address the root causes of breakouts — from excess sebum production and clogged pores to bacterial overgrowth and inflammation. Each treatment is customized based on your skin type, acne severity, and lifestyle factors.',
        'science_p2' => 'At GlowCare, we go beyond surface-level solutions. Our practitioners combine medical-grade actives with advanced technology to not only clear existing acne but also repair texture damage and prevent future flare-ups, restoring confidence in your skin.',
        'benefits' => [
            ['icon' => 'healing', 'title' => 'Clears Active Acne', 'desc' => 'Targets inflammation and bacteria to rapidly reduce active breakouts and prevent new ones.'],
            ['icon' => 'grain', 'title' => 'Refines Pore Size', 'desc' => 'Unclogs and minimizes pores for a smoother, more refined skin surface.'],
            ['icon' => 'dermatology', 'title' => 'Reduces Scarring', 'desc' => 'Promotes cellular renewal to fade post-inflammatory marks and textural irregularities.'],
            ['icon' => 'shield', 'title' => 'Prevents Recurrence', 'desc' => 'Strengthens the skin barrier and regulates oil production for long-term clarity.'],
        ],
        'why_title' => 'Why Choose This Treatment?',
        'results' => 'Visible improvement',
        'results_desc' => 'Results vary by individual.',
        'side_effects' => 'Potential redness',
        'side_effects_desc' => 'Temporary mild irritation.',
        'step1' => 'A thorough skin analysis and mapping of active acne areas to define targets.',
        'step2' => 'Careful application of medical-grade peels or extraction by certified specialists.',
        'step3' => 'Soothing barrier repair creams and post-care guidelines to avoid irritation.'
    ],
    'Anti-Aging' => [
        'badge' => 'Aesthetic Excellence',
        'science_title' => 'The Science of Softness',
        'science_p1' => 'Advanced anti-aging treatments work at the cellular level to stimulate collagen production, restore volume loss, and relax dynamic muscle movements that create expression lines. Each protocol is designed around the natural architecture of your face.',
        'science_p2' => 'At GlowCare, our approach is rooted in anatomical precision. We focus on the delicate balance between softening expression lines and maintaining the natural mobility of your face, ensuring you look like a refreshed version of yourself, never "frozen."',
        'benefits' => [
            ['icon' => 'auto_awesome', 'title' => 'Diminishes Fine Lines', 'desc' => 'Rapidly targets forehead lines, crow\'s feet, and expression lines for a smoother complexion.'],
            ['icon' => 'shield', 'title' => 'Prevents Deep Wrinkles', 'desc' => 'Proactive treatment slows the development of permanent creases by reducing muscle tension.'],
            ['icon' => 'timer', 'title' => 'Minimal Downtime', 'desc' => 'Often called a "lunchtime procedure," most patients return to normal activities immediately.'],
            ['icon' => 'spa', 'title' => 'Natural Results', 'desc' => 'Precision dosing ensures you maintain your unique expressions with a youthful lift.'],
        ],
        'why_title' => 'Why Choose Botox?',
        'results' => '3-7 days onset',
        'results_desc' => 'Peak effects at 14 days.',
        'side_effects' => 'Minor redness',
        'side_effects_desc' => 'Potential mild bruising.',
        'step1' => 'A bespoke analysis of your facial anatomy and goals to create a tailored treatment plan.',
        'step2' => 'A brief, precise series of injections using ultra-fine needles for maximum comfort.',
        'step3' => 'Simple guidance for the first 24 hours to optimize your settling results.'
    ],
    'Brightening' => [
        'badge' => 'Radiance Protocol',
        'science_title' => 'The Science of Luminosity',
        'science_p1' => 'Brightening treatments target melanin overproduction and uneven pigmentation at the dermal level. Using high-potency antioxidants, advanced light technology, and medical-grade actives, we break down existing dark spots while inhibiting future hyperpigmentation.',
        'science_p2' => 'Our protocols are designed to deliver a luminous, even-toned complexion — the coveted "glass skin" effect. Each session works synergistically with your skin\'s natural renewal cycle for cumulative, lasting radiance.',
        'benefits' => [
            ['icon' => 'light_mode', 'title' => 'Instant Radiance', 'desc' => 'Delivers visible brightening from the very first session with cumulative improvement.'],
            ['icon' => 'palette', 'title' => 'Even Skin Tone', 'desc' => 'Targets dark spots, melasma, and post-inflammatory hyperpigmentation effectively.'],
            ['icon' => 'water_drop', 'title' => 'Deep Hydration', 'desc' => 'Infuses potent moisture and nutrients for a plump, dewy finish.'],
            ['icon' => 'verified', 'title' => 'Long-lasting Glow', 'desc' => 'Results build over time with proper skincare routine maintenance.'],
        ],
        'why_title' => 'Why Choose This Treatment?',
        'results' => 'Instant Glow',
        'results_desc' => 'Cumulative radiance.',
        'side_effects' => 'None',
        'side_effects_desc' => 'No downtime involved.',
        'step1' => 'Analysis of pigmentation depth and tone mapping to determine correct levels.',
        'step2' => 'Precision infusion of high-potency antioxidants and laser toning procedures.',
        'step3' => 'Hydration boost and sun protection protocol instructions for a lasting glow.'
    ],
    'Hair & Body Care' => [
        'badge' => 'Body Aesthetics',
        'science_title' => 'The Science of Restoration',
        'science_p1' => 'Our hair and body treatments use evidence-based protocols combining bio-stimulatory infusions, light therapy, and advanced laser systems. These technologies work at the follicular and cellular level to stimulate natural growth cycles and restore vitality.',
        'science_p2' => 'Whether addressing hair thinning, unwanted hair, or body contouring, our medical-grade approach ensures safe, effective results with treatments customized to your unique physiology and aesthetic goals.',
        'benefits' => [
            ['icon' => 'spa', 'title' => 'Stimulates Growth', 'desc' => 'Bio-stimulatory protocols activate dormant hair follicles and promote healthy regrowth.'],
            ['icon' => 'bolt', 'title' => 'Advanced Technology', 'desc' => 'Medical-grade lasers and light therapy for precise, comfortable treatments.'],
            ['icon' => 'self_improvement', 'title' => 'Full Body Care', 'desc' => 'Comprehensive solutions for hair, skin, and body aesthetic concerns.'],
            ['icon' => 'science', 'title' => 'Evidence-Based', 'desc' => 'All protocols backed by clinical research and proven outcomes.'],
        ],
        'why_title' => 'Why Choose This Treatment?',
        'results' => 'Progressive',
        'results_desc' => 'Visible over 2-3 months.',
        'side_effects' => 'None to mild',
        'side_effects_desc' => 'Safe for all skin types.',
        'step1' => 'Detailed assessment of target body contour areas or scalp follicular density.',
        'step2' => 'Comfortable application of clinical laser systems or bio-stimulatory infusions.',
        'step3' => 'Follow-up schedule review and simple post-session care instructions.'
    ],
];

// Fallback for unknown categories
$cat_info = $category_data[$kategori] ?? [
    'badge' => 'Clinical Treatment',
    'science_title' => 'The Clinical Approach',
    'science_p1' => 'This treatment leverages advanced medical-grade technology and clinical expertise to deliver exceptional results safely and effectively.',
    'science_p2' => 'At GlowCare, every protocol is personalized to your unique needs, ensuring optimal outcomes with the highest standard of care.',
    'benefits' => [
        ['icon' => 'verified', 'title' => 'Medical Grade', 'desc' => 'Precision formulation for clinical results.'],
        ['icon' => 'medical_services', 'title' => 'Expert-Led', 'desc' => 'Performed by certified practitioners.'],
        ['icon' => 'timer', 'title' => 'Quick Recovery', 'desc' => 'Minimal downtime for busy lifestyles.'],
        ['icon' => 'face', 'title' => 'Natural Look', 'desc' => 'Subtle enhancement that looks like you.'],
    ],
    'why_title' => 'Why Choose This Treatment?',
    'results' => 'Visible improvement',
    'results_desc' => 'Results vary by individual.',
    'side_effects' => 'None to mild',
    'side_effects_desc' => 'Minimal downtime.',
    'step1' => 'A comprehensive consultation to analyze your concerns and goals.',
    'step2' => 'The clinical procedure performed with precision by certified practitioners.',
    'step3' => 'Detailed post-care instructions to maintain and optimize your results.'
];
?>
<!DOCTYPE html>
<html class="scroll-smooth" lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title><?= $nama ?> | GlowCare Medical Aesthetics</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&amp;family=Inter:wght@400;500;600&amp;display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet">
    <script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                "colors": {
                    "on-primary-container": "#654d2d",
                    "on-error-container": "#93000a",
                    "inverse-on-surface": "#f2f1ec",
                    "on-secondary-container": "#3e6c69",
                    "surface-variant": "#e4e2dd",
                    "surface-dim": "#dbdad5",
                    "secondary-fixed": "#bbece8",
                    "secondary-fixed-dim": "#a0cfcc",
                    "tertiary": "#586062",
                    "inverse-primary": "#e1c198",
                    "on-tertiary-fixed": "#161d1f",
                    "on-background": "#1b1c19",
                    "surface-container-highest": "#e4e2dd",
                    "on-primary-fixed-variant": "#594323",
                    "tertiary-fixed": "#dde4e6",
                    "on-tertiary": "#ffffff",
                    "inverse-surface": "#30312e",
                    "primary-container": "#e0c097",
                    "tertiary-fixed-dim": "#c1c8ca",
                    "on-tertiary-container": "#4b5355",
                    "on-secondary": "#ffffff",
                    "on-error": "#ffffff",
                    "background": "#fbf9f4",
                    "surface-bright": "#fbf9f4",
                    "primary": "#735a39",
                    "error-container": "#ffdad6",
                    "error": "#ba1a1a",
                    "on-primary-fixed": "#291800",
                    "secondary-container": "#bbece8",
                    "primary-fixed": "#ffddb2",
                    "outline-variant": "#d1c4b8",
                    "outline": "#7f756a",
                    "surface-container-low": "#f5f3ee",
                    "surface-container-lowest": "#ffffff",
                    "on-surface-variant": "#4e453c",
                    "on-secondary-fixed": "#00201f",
                    "surface-container": "#f0eee9",
                    "primary-fixed-dim": "#e1c198",
                    "surface-tint": "#735a39",
                    "on-tertiary-fixed-variant": "#41484a",
                    "tertiary-container": "#bfc6c8",
                    "on-surface": "#1b1c19",
                    "surface-container-high": "#eae8e3",
                    "on-secondary-fixed-variant": "#1e4e4c",
                    "surface": "#fbf9f4",
                    "secondary": "#386663",
                    "on-primary": "#ffffff"
                },
                "borderRadius": {
                    "DEFAULT": "0.25rem",
                    "lg": "0.5rem",
                    "xl": "0.75rem",
                    "full": "9999px"
                },
                "spacing": {
                    "margin-mobile": "16px",
                    "xl": "80px",
                    "md": "24px",
                    "gutter": "24px",
                    "xs": "4px",
                    "base": "8px",
                    "margin-desktop": "64px",
                    "lg": "48px",
                    "sm": "12px"
                },
                "fontFamily": {
                    "body-md": ["Inter"],
                    "headline-lg": ["Playfair Display"],
                    "label-sm": ["Inter"],
                    "body-lg": ["Inter"],
                    "display-lg": ["Playfair Display"],
                    "body-sm": ["Inter"],
                    "label-md": ["Inter"],
                    "headline-md": ["Playfair Display"],
                    "headline-lg-mobile": ["Playfair Display"]
                },
                "fontSize": {
                    "body-md": ["16px", {
                        "lineHeight": "1.5",
                        "fontWeight": "400"
                    }],
                    "headline-lg": ["32px", {
                        "lineHeight": "1.3",
                        "fontWeight": "600"
                    }],
                    "label-sm": ["12px", {
                        "lineHeight": "1.2",
                        "fontWeight": "500"
                    }],
                    "body-lg": ["18px", {
                        "lineHeight": "1.6",
                        "fontWeight": "400"
                    }],
                    "display-lg": ["48px", {
                        "lineHeight": "1.2",
                        "letterSpacing": "-0.02em",
                        "fontWeight": "700"
                    }],
                    "body-sm": ["14px", {
                        "lineHeight": "1.5",
                        "fontWeight": "400"
                    }],
                    "label-md": ["14px", {
                        "lineHeight": "1.2",
                        "letterSpacing": "0.05em",
                        "fontWeight": "600"
                    }],
                    "headline-md": ["24px", {
                        "lineHeight": "1.4",
                        "fontWeight": "500"
                    }],
                    "headline-lg-mobile": ["24px", {
                        "lineHeight": "1.3",
                        "fontWeight": "600"
                    }]
                }
            },
        },
    }
    </script>
    <style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL'0, 'wght'400, 'GRAD'0, 'opsz'24;
    }

    .luxury-shadow {
        box-shadow: 0 10px 40px -10px rgba(115, 90, 57, 0.08);
    }

    .hero-overlay {
        background: linear-gradient(to right, rgba(251, 249, 244, 0.95) 0%, rgba(251, 249, 244, 0.4) 50%, rgba(251, 249, 244, 0) 100%);
    }
    </style>
</head>

<body class="bg-background text-on-surface font-body-md overflow-x-hidden">
    <!-- TopNavBar -->
    <nav class="fixed top-0 w-full z-50 bg-background/80 backdrop-blur-md shadow-sm">
        <div class="max-w-full px-6 md:px-12 lg:px-16 flex justify-between items-center h-20">
            <div class="flex items-center gap-sm">
                <a href="treatment.php"
                    class="flex items-center gap-xs text-on-surface-variant font-label-md text-label-md hover:text-primary transition-colors duration-300">
                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                    <span class="">Back to Treatments</span>
                </a>
            </div>
            <a class="font-headline-lg text-headline-lg text-primary inline-flex items-center" href="../../index.php">
                <span class="material-symbols-outlined text-primary text-3xl mr-2 align-middle"
                    style="font-variation-settings: 'FILL' 1;">spa</span>GlowCare
            </a>
        </div>
    </nav>
    <main class="pt-20">
        <!-- Hero Banner -->
        <section class="relative h-[600px] w-full overflow-hidden flex items-center">
            <div class="absolute inset-0 z-0">
                <img alt="<?= $nama ?> Banner" class="w-full h-full object-cover" src="<?= $img ?>">
            </div>
            <div class="absolute inset-0 z-10 hero-overlay hidden md:block"></div>
            <!-- Mobile Overlay -->
            <div class="absolute inset-0 z-10 bg-background/60 md:hidden"></div>
            <div class="relative z-20 max-w-full px-6 md:px-12 lg:px-16 w-full">
                <div class="max-w-2xl">
                    <span
                        class="inline-block bg-secondary-container text-on-secondary-container px-sm py-1 rounded-full font-label-sm text-label-sm mb-base uppercase tracking-widest"><?= htmlspecialchars($cat_info['badge']) ?></span>
                    <h1 class="font-display-lg text-display-lg text-on-surface mb-md"><?= $nama ?></h1>
                    <p class="font-body-lg text-body-lg text-on-surface-variant max-w-lg mb-lg"><?= $desc ?></p>
                    <div class="flex gap-md">
                        <a href="<?= isset($_SESSION['user_id']) ? '../user/dashboarduser.php?page=daftar-konsul&treatment_id=' . $tr['id'] : '../auth/Signin.php' ?>"
                            class="bg-primary text-on-primary px-lg py-md rounded-lg font-label-md text-label-md luxury-shadow hover:scale-[1.02] transition-transform inline-block">
                            Book This Treatment
                        </a>
                    </div>
                </div>
            </div>
        </section>
        <!-- Deep Dive Description -->
        <section class="py-xl bg-surface-container-lowest">
            <div
                class="max-w-7xl mx-auto px-margin-mobile md:px-margin-desktop grid grid-cols-1 md:grid-cols-2 gap-xl items-center">
                <div>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface mb-md">
                        <?= htmlspecialchars($cat_info['science_title']) ?></h2>
                    <div class="space-y-md text-on-surface-variant font-body-md text-body-md">
                        <p><?= htmlspecialchars($cat_info['science_p1']) ?></p>
                        <p><?= htmlspecialchars($cat_info['science_p2']) ?></p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-md">
                    <div
                        class="bg-surface p-lg rounded-xl luxury-shadow border border-outline-variant/30 flex flex-col items-center text-center">
                        <span class="material-symbols-outlined text-primary text-[40px] mb-base">biotech</span>
                        <h4 class="font-label-md text-label-md text-primary mb-xs">FDA Approved</h4>
                        <p class="font-body-sm text-body-sm">Gold standard safety profile.</p>
                    </div>
                    <div
                        class="bg-surface p-lg rounded-xl luxury-shadow border border-outline-variant/30 flex flex-col items-center text-center">
                        <span class="material-symbols-outlined text-primary text-[40px] mb-base">verified</span>
                        <h4 class="font-label-md text-label-md text-primary mb-xs">Medical Grade</h4>
                        <p class="font-body-sm text-body-sm">Precision formulation.</p>
                    </div>
                    <div
                        class="bg-surface p-lg rounded-xl luxury-shadow border border-outline-variant/30 flex flex-col items-center text-center">
                        <span class="material-symbols-outlined text-primary text-[40px] mb-base">medical_services</span>
                        <h4 class="font-label-md text-label-md text-primary mb-xs">Practitioner Led</h4>
                        <p class="font-body-sm text-body-sm">Expert clinical care.</p>
                    </div>
                    <div
                        class="bg-surface p-lg rounded-xl luxury-shadow border border-outline-variant/30 flex flex-col items-center text-center">
                        <span class="material-symbols-outlined text-primary text-[40px] mb-base">face</span>
                        <h4 class="font-label-md text-label-md text-primary mb-xs">Natural Look</h4>
                        <p class="font-body-sm text-body-sm">Subtle enhancement.</p>
                    </div>
                </div>
            </div>
        </section>
        <!-- Key Benefits -->
        <section class="py-xl bg-background">
            <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin-desktop">
                <div class="text-center mb-lg">
                    <h2 class="font-headline-lg text-headline-lg text-on-surface">
                        <?= htmlspecialchars($cat_info['why_title']) ?></h2>
                    <p class="font-body-md text-body-md text-on-surface-variant">The dual benefit of immediate
                        correction and long-term prevention.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-gutter">
                    <?php foreach ($cat_info['benefits'] as $b): ?>
                    <div
                        class="p-lg bg-surface-container rounded-xl border border-outline-variant/20 hover:border-primary transition-colors">
                        <span class="material-symbols-outlined text-secondary text-[32px] mb-md"
                            data-icon="<?= htmlspecialchars($b['icon']) ?>"><?= htmlspecialchars($b['icon']) ?></span>
                        <h3 class="font-headline-md text-headline-md text-on-surface mb-sm">
                            <?= htmlspecialchars($b['title']) ?></h3>
                        <p class="font-body-sm text-body-sm text-on-surface-variant"><?= htmlspecialchars($b['desc']) ?>
                        </p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <!-- Step-by-Step Procedure -->
        <section class="py-xl bg-surface-container-high">
            <div class="max-w-7xl mx-auto px-margin-mobile md:px-margin-desktop">
                <h2 class="font-headline-lg text-headline-lg text-on-surface mb-xl text-center">Your Treatment Journey
                </h2>
                <div class="relative">
                    <!-- Connection Line (Desktop) -->
                    <div class="hidden md:block absolute top-10 left-0 w-full h-px bg-primary/20 z-0"></div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-xl relative z-10">
                        <!-- Step 1 -->
                        <div class="text-center group">
                            <div
                                class="w-20 h-20 bg-surface text-primary border-2 border-primary/20 rounded-full flex items-center justify-center mx-auto mb-md luxury-shadow group-hover:border-primary transition-all duration-300">
                                <span class="font-headline-md text-headline-md">01</span>
                            </div>
                            <h3 class="font-headline-md text-headline-md text-on-surface mb-sm">Consultation</h3>
                            <p class="font-body-md text-body-md text-on-surface-variant px-md leading-relaxed">
                                <?= htmlspecialchars($cat_info['step1']) ?></p>
                        </div>
                        <!-- Step 2 -->
                        <div class="text-center group">
                            <div
                                class="w-20 h-20 bg-surface text-primary border-2 border-primary/20 rounded-full flex items-center justify-center mx-auto mb-md luxury-shadow group-hover:border-primary transition-all duration-300">
                                <span class="font-headline-md text-headline-md">02</span>
                            </div>
                            <h3 class="font-headline-md text-headline-md text-on-surface mb-sm">Treatment</h3>
                            <p class="font-body-md text-body-md text-on-surface-variant px-md leading-relaxed">
                                <?= htmlspecialchars($cat_info['step2']) ?></p>
                        </div>
                        <!-- Step 3 -->
                        <div class="text-center group">
                            <div
                                class="w-20 h-20 bg-surface text-primary border-2 border-primary/20 rounded-full flex items-center justify-center mx-auto mb-md luxury-shadow group-hover:border-primary transition-all duration-300">
                                <span class="font-headline-md text-headline-md">03</span>
                            </div>
                            <h3 class="font-headline-md text-headline-md text-on-surface mb-sm">Post-Care</h3>
                            <p class="font-body-md text-body-md text-on-surface-variant px-md leading-relaxed">
                                <?= htmlspecialchars($cat_info['step3']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Clinical Cautions & Info -->
        <section class="py-xl bg-background">
            <div class="max-w-4xl mx-auto px-margin-mobile">
                <div
                    class="bg-surface-container-highest p-lg rounded-2xl luxury-shadow border border-outline-variant/30">
                    <div class="flex items-center gap-sm mb-lg border-b border-outline-variant/30 pb-md"><span
                            class="material-symbols-outlined text-primary text-[28px]">info</span>
                        <h2 class="font-headline-md text-headline-md text-on-surface uppercase tracking-widest">Good to
                            Know</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-gutter">
                        <div
                            class="flex flex-col gap-xs p-md bg-surface/40 rounded-xl border border-outline-variant/20">
                            <p class="font-label-sm text-label-sm text-primary uppercase tracking-wider">Duration</p>
                            <p class="font-headline-md text-headline-md text-on-surface"><?= $durasi ?></p>
                            <p class="font-body-sm text-body-sm text-on-surface-variant italic">Inclusive of
                                consultation.</p>
                        </div>
                        <div
                            class="flex flex-col gap-xs p-md bg-surface/40 rounded-xl border border-outline-variant/20">
                            <p class="font-label-sm text-label-sm text-primary uppercase tracking-wider">Results</p>
                            <p class="font-headline-md text-headline-md text-on-surface">
                                <?= htmlspecialchars($cat_info['results']) ?></p>
                            <p class="font-body-sm text-body-sm text-on-surface-variant italic">
                                <?= htmlspecialchars($cat_info['results_desc']) ?></p>
                        </div>
                        <div
                            class="flex flex-col gap-xs p-md bg-surface/40 rounded-xl border border-outline-variant/20">
                            <p class="font-label-sm text-label-sm text-primary uppercase tracking-wider">Side Effects
                            </p>
                            <p class="font-headline-md text-headline-md text-on-surface">
                                <?= htmlspecialchars($cat_info['side_effects']) ?></p>
                            <p class="font-body-sm text-body-sm text-on-surface-variant italic">
                                <?= htmlspecialchars($cat_info['side_effects_desc']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Main CTA -->
        <section class="py-xl text-center bg-background">
            <div class="max-w-2xl mx-auto px-margin-mobile">
                <h2 class="font-display-lg text-display-lg mb-md">Ready to Refresh?</h2>
                <p class="font-body-lg text-body-lg text-on-surface-variant mb-xl">Join hundreds of satisfied clients
                    who trust GlowCare for their aesthetic journey.</p>
                <a href="<?= isset($_SESSION['user_id']) ? '../user/dashboarduser.php?page=daftar-konsul&treatment_id=' . $tr['id'] : '../auth/Signin.php' ?>"
                    class="bg-primary text-on-primary px-lg py-md rounded-lg font-label-md text-label-md luxury-shadow hover:bg-on-primary-fixed-variant transition-all hover:scale-[1.05] active:scale-95 inline-block">
                    Book This Treatment
                </a>
            </div>
        </section>
    </main>
    <!-- Footer -->
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
                href="../../index.php">Beranda</a>
            <a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary transition-colors"
                href="treatment.php">Layanan</a>
            <a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary transition-colors"
                href="../spesialis.php">Dokter</a>
            <a class="font-body-sm text-body-sm text-on-surface-variant hover:text-primary transition-colors"
                href="../kontak.php">Contact</a>
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
    <script>
    // Micro-interactions for procedure steps
    document.querySelectorAll('.group').forEach(item => {
        item.addEventListener('mouseenter', () => {
            const icon = item.querySelector('.w-20');
            if (icon) icon.classList.add('bg-primary-container', 'text-on-primary-container');
        });
        item.addEventListener('mouseleave', () => {
            const icon = item.querySelector('.w-20');
            if (icon) icon.classList.remove('bg-primary-container', 'text-on-primary-container');
        });
    });
    </script>
</body>

</html>