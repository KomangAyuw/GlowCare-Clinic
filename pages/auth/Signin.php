<?php
$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>
<!DOCTYPE html><html class="light" lang="en"><head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>GlowCare - Secure Login</title>
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com" rel="preconnect">
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&amp;family=Playfair+Display:wght@500;600;700&amp;display=swap" rel="stylesheet">
<!-- Material Symbols -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<!-- Design System Configuration -->
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
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
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    spacing: {
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
                    fontFamily: {
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
                    fontSize: {
                        "headline-lg-mobile": ["24px", { lineHeight: "1.3", fontWeight: "600" }],
                        "label-sm": ["12px", { lineHeight: "1.2", fontWeight: "500" }],
                        "headline-md": ["24px", { lineHeight: "1.4", fontWeight: "500" }],
                        "body-md": ["16px", { lineHeight: "1.5", fontWeight: "400" }],
                        "body-sm": ["14px", { lineHeight: "1.5", fontWeight: "400" }],
                        "body-lg": ["18px", { lineHeight: "1.6", fontWeight: "400" }],
                        "display-lg": ["48px", { lineHeight: "1.2", letterSpacing: "-0.02em", fontWeight: "700" }],
                        "headline-lg": ["32px", { lineHeight: "1.3", fontWeight: "600" }],
                        "label-md": ["14px", { lineHeight: "1.2", letterSpacing: "0.05em", fontWeight: "600" }]
                    }
                }
            }
        }
    </script>
<style>.material-symbols-outlined {
    font-variation-settings: "FILL" 0, "wght" 300, "GRAD" 0, "opsz" 24
    }</style>
</head>
<body class="bg-surface text-on-surface antialiased selection:bg-primary-container selection:text-on-primary-container">
<div class="flex min-h-screen w-full">
<div class="w-full lg:w-1/2 flex flex-col items-center justify-center px-margin-mobile sm:px-12 lg:px-margin-desktop py-12 relative z-10 bg-surface">
<div class="max-w-md w-full mx-auto">
<div class="flex items-center gap-2 mb-12">
<span class="material-symbols-outlined text-primary text-3xl" style="font-variation-settings: &quot;FILL&quot; 1;">spa</span>
<span class="font-headline-md text-headline-md text-primary tracking-tight">GlowCare</span>
</div>
<div class="mb-8">
<h1 class="font-headline-lg text-headline-lg text-on-surface mb-2">Welcome back</h1>
<p class="font-body-md text-body-md text-on-surface-variant">Enter your credentials to access the secure clinical portal.</p>
</div>

<?php if ($success): ?>
    <div class="bg-surface-container-low border border-primary text-primary px-4 py-3 rounded-lg mb-6 font-body-sm flex items-center gap-2">
        <span class="material-symbols-outlined">check_circle</span>
        <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="bg-error-container border border-error text-on-error-container px-4 py-3 rounded-lg mb-6 font-body-sm flex items-center gap-2">
        <span class="material-symbols-outlined">error</span>
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<form action="../../backend/log.php" class="space-y-6" method="POST">
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-xs" for="email">Email Address</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">mail</span>
<input autocomplete="email" class="w-full pl-10 pr-4 py-3 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface font-body-md focus:ring-2 focus:ring-primary focus:border-primary transition-all shadow-sm placeholder:text-outline-variant" id="email" name="email" placeholder="name@example.com" required="" type="email">
</div>
</div>
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-xs" for="password">Password</label>
<div class="relative">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">lock</span>
<input autocomplete="current-password" class="w-full pl-10 pr-4 py-3 bg-surface-container-lowest border border-outline-variant rounded-lg text-on-surface font-body-md focus:ring-2 focus:ring-primary focus:border-primary transition-all shadow-sm placeholder:text-outline-variant" id="password" name="password" placeholder="••••••••" required="" type="password">
</div>
</div>
<div class="flex items-center justify-between">
<div class="flex items-center">
<input class="h-4 w-4 rounded border-outline-variant text-primary focus:ring-primary bg-surface-container-lowest" id="remember-me" name="remember-me" type="checkbox">
<label class="ml-2 block font-body-sm text-body-sm text-on-surface-variant" for="remember-me">
                                Remember me
                            </label>
</div>
<div class="text-sm">
<a class="font-label-md text-label-md text-primary hover:text-on-primary-fixed-variant transition-colors hover:underline" href="#">
                                Forgot your password?
                            </a>
</div>
</div>
<button class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm font-label-md text-label-md text-on-primary bg-primary hover:bg-on-primary-fixed-variant focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all active:scale-[0.98]" type="submit">
                        Sign In
                    </button>
</form>
<div class="mt-8 pt-6 border-t border-outline-variant/30 text-center">
<p class="font-body-sm text-body-sm text-on-surface-variant">
                        New to GlowCare? 
                        <a class="font-label-md text-label-md text-primary hover:text-on-primary-fixed-variant transition-colors hover:underline ml-1" href="SignUp.php">
                            Register as a New Patient
                        </a>
</p>
</div>
</div>
<div class="absolute bottom-6 left-0 right-0 text-center lg:text-left lg:pl-margin-desktop">
    <a href="../../index.php" class="inline-flex items-center gap-2 font-label-md text-primary hover:text-on-primary-fixed-variant transition-colors">
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        Back to Home
    </a>
</div>
</div>
<div class="hidden lg:block lg:w-1/2 relative bg-surface-container-high"><div class="absolute inset-0 bg-cover bg-center" style="background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuDB10zPN5GpcNtUgSrece2kvyQFWXaIYAnhg7j3j_CudCV31etUXBwQy1rJsYJCkVr77fw8wQCP70gW-RM0QingWQm2bTY-4XpWbywMCNUK1qHxawFc9v1nJaSs5HK9GtoS1gOJ0PXGf59rP47ocnXvEk5OQp1B16C9z8ZJxObCOnegLdW18KBYhOW9BIwgQeAywVvFC98Czjqn9K0rSYzlbtGsC6TDU5Fs1B21DvsvDT-dt8UQGrjDA52_nZWx5ISKyqAIbh4VbmI&quot;);"></div></div>
</div>

</body></html>