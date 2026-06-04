<?php
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html><html lang="en" style=""><head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>Sign Up - GlowCare</title>
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com" rel="preconnect">
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&amp;family=Playfair+Display:wght@500;600;700&amp;display=swap" rel="stylesheet">
<!-- Material Symbols -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
                    "surface-container-high": "#eae8e3",
                    "on-tertiary-fixed": "#161d1f",
                    "surface-container": "#f0eee9",
                    "secondary-fixed": "#bbece8",
                    "on-error-container": "#93000a",
                    "on-surface-variant": "#4e453c",
                    "surface-tint": "#064e3b",
                    "on-primary-container": "#654d2d",
                    "inverse-surface": "#30312e",
                    "tertiary-fixed": "#dde4e6",
                    "primary-fixed-dim": "#e1c198",
                    "on-primary-fixed-variant": "#022c22",
                    "surface-container-low": "#f5f3ee",
                    "surface-container-highest": "#e4e2dd",
                    "outline-variant": "#a7f3d0",
                    "tertiary": "#586062",
                    "on-tertiary": "#ffffff",
                    "surface-dim": "#dbdad5",
                    "tertiary-fixed-dim": "#c1c8ca",
                    "on-secondary-fixed": "#00201f",
                    "on-secondary-fixed-variant": "#1e4e4c",
                    "surface": "#fbf9f4",
                    "on-secondary": "#ffffff",
                    "error": "#ba1a1a",
                    "on-tertiary-fixed-variant": "#41484a",
                    "on-surface": "#1b1c19",
                    "on-secondary-container": "#3e6c69",
                    "on-primary": "#ffffff",
                    "on-error": "#ffffff",
                    "on-background": "#1b1c19",
                    "secondary-container": "#bbece8",
                    "primary": "#064e3b",
                    "surface-bright": "#fbf9f4",
                    "on-tertiary-container": "#4b5355",
                    "tertiary-container": "#bfc6c8",
                    "secondary-fixed-dim": "#a0cfcc",
                    "surface-container-lowest": "#ffffff",
                    "inverse-primary": "#e1c198",
                    "error-container": "#ffdad6",
                    "background": "#fbf9f4",
                    "surface-variant": "#e4e2dd",
                    "inverse-on-surface": "#f2f1ec",
                    "primary-container": "#e0c097",
                    "primary-fixed": "#ffddb2",
                    "on-primary-fixed": "#291800",
                    "secondary": "#386663",
                    "outline": "#7f756a"
            },
            "borderRadius": {
                    "DEFAULT": "0.25rem",
                    "lg": "0.5rem",
                    "xl": "0.75rem",
                    "full": "9999px"
            },
            "spacing": {
                    "base": "8px",
                    "xl": "80px",
                    "gutter": "24px",
                    "md": "24px",
                    "sm": "12px",
                    "xs": "4px",
                    "lg": "48px",
                    "margin-mobile": "16px",
                    "margin-desktop": "64px"
            },
            "fontFamily": {
                    "headline-lg-mobile": ["Playfair Display"],
                    "label-md": ["Inter"],
                    "display-lg": ["Playfair Display"],
                    "body-sm": ["Inter"],
                    "body-lg": ["Inter"],
                    "label-sm": ["Inter"],
                    "headline-lg": ["Playfair Display"],
                    "headline-md": ["Playfair Display"],
                    "body-md": ["Inter"]
            },
            "fontSize": {
                    "headline-lg-mobile": ["24px", {"lineHeight": "1.3", "fontWeight": "600"}],
                    "label-md": ["14px", {"lineHeight": "1.2", "letterSpacing": "0.05em", "fontWeight": "600"}],
                    "display-lg": ["48px", {"lineHeight": "1.2", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                    "body-sm": ["14px", {"lineHeight": "1.5", "fontWeight": "400"}],
                    "body-lg": ["18px", {"lineHeight": "1.6", "fontWeight": "400"}],
                    "label-sm": ["12px", {"lineHeight": "1.2", "fontWeight": "500"}],
                    "headline-lg": ["32px", {"lineHeight": "1.3", "fontWeight": "600"}],
                    "headline-md": ["24px", {"lineHeight": "1.4", "fontWeight": "500"}],
                    "body-md": ["16px", {"lineHeight": "1.5", "fontWeight": "400"}]
            }
          }
        }
      }
    </script>
</head>
<body class="bg-surface text-on-surface antialiased min-h-screen flex selection:bg-primary/20 selection:text-primary">
<!-- Left Screen: Image Canvas (Hidden on Mobile) -->
<div class="hidden lg:block lg:w-1/2 relative bg-surface-container-high">
<div class="absolute inset-0 bg-cover bg-center" style="background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuAMI_wUHlqYASn0iMv9p1Wx9GMJCPobviaEa3kFCY5OuSM_g15vrVOmwYKJvocIx44EniHghkilf5WRkm-ygWMXpbbvoF7M7j57fwePJwZofWA2WyfceS8ibU3FV-q9FAMlhGJ4WFF6KQ8r7Nqo7cxGm5wfs8ORhwb536Xe3LLUQROXOm3b8McGyJzOifda9gqcrvyICbyLm3ZwotCQQVf8wDytZIhKep8LGdZ3khQh9daESUTWRk-zrmNDaAErOlsi17d_oETpIWY&quot;);"></div>
<!-- Optional ambient overlay to ensure image doesn't clash with pure clinical feel -->
</div>
<!-- Right Screen: Form Canvas -->
<div class="w-full lg:w-1/2 flex flex-col items-center justify-center px-margin-mobile sm:px-12 lg:px-margin-desktop py-12 relative z-10 bg-surface min-h-screen overflow-y-auto">
<div class="max-w-md w-full my-auto">
<!-- Header Section -->
<div class="mb-8">
<!-- Brand Anchor -->
<div class="flex items-center gap-2 mb-12"><span class="material-symbols-outlined text-primary text-3xl" style="font-variation-settings: &quot;FILL&quot; 1;">spa</span><span class="font-headline-md text-headline-md text-primary tracking-tight">GlowCare</span></div>
<h1 class="font-headline-lg text-headline-lg text-on-surface mb-2">
                    Create your account
                </h1>
<p class="font-body-md text-body-md text-on-surface-variant">
                    Join the GlowCare community and start your journey to healthy, radiant skin.
                </p>
</div>

<?php if ($error): ?>
    <div class="bg-error-container border border-error text-on-error-container px-4 py-3 rounded-lg mb-6 font-body-sm flex items-center gap-2">
        <span class="material-symbols-outlined">error</span>
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<!-- Form Section -->
<form action="../../backend/Regist.php" class="space-y-6" method="POST">
<div class="space-y-sm">
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-xs" for="username">
                            Full Name
                        </label>
<input class="block w-full px-4 py-3 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant/50 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all duration-200" id="username" name="username" required="" type="text">
</div>
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-xs" for="email">
                            Email Address
                        </label>
<input autocomplete="email" class="block w-full px-4 py-3 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant/50 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all duration-200" id="email" name="email" required="" type="email">
</div>
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-xs" for="phone">
                            Phone Number
                        </label>
<input autocomplete="tel" class="block w-full px-4 py-3 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant/50 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all duration-200" id="phone" name="phone" type="tel">
</div>
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-xs" for="password">
                            Password
                        </label>
<input autocomplete="new-password" class="block w-full px-4 py-3 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant/50 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all duration-200" id="password" name="password" required="" type="password">
</div>
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-xs" for="konfirmasi">
                            Confirm Password
                        </label>
<input autocomplete="new-password" class="block w-full px-4 py-3 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant/50 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all duration-200" id="konfirmasi" name="konfirmasi" required="" type="password">
</div>
</div>
<!-- Terms & Conditions -->
<div class="flex items-center">
<input class="h-4 w-4 rounded border-outline-variant text-primary focus:ring-primary bg-surface-container-lowest cursor-pointer transition-colors" id="terms" name="terms" required="" type="checkbox">
<label class="ml-3 block font-body-sm text-body-sm text-on-surface-variant cursor-pointer" for="terms">
                        I agree to the <a class="text-primary hover:text-surface-tint underline decoration-primary/30 underline-offset-2 transition-colors" href="#">Terms of Service</a> and <a class="text-primary hover:text-surface-tint underline decoration-primary/30 underline-offset-2 transition-colors" href="#">Privacy Policy</a>.
                    </label>
</div>
<!-- Action Button -->
<div>
<button class="w-full flex justify-center py-4 px-4 border border-transparent rounded-lg shadow-sm font-label-md text-label-md text-on-primary bg-primary hover:bg-surface-tint focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary focus:ring-offset-surface transform active:scale-[0.98] transition-all duration-200" type="submit">
                        Register Now
                    </button>
</div>
</form>
<!-- Footer / Login Link -->
<div class="text-center font-body-sm text-body-sm text-on-surface-variant pt-md">
                Already have an account? 
                <a class="font-medium text-primary hover:text-surface-tint underline decoration-primary/50 hover:decoration-primary underline-offset-4 transition-all" href="Signin.php">
                    Log In
                </a>
</div>
</div>
<div class="absolute bottom-6 left-0 right-0 text-center lg:text-left lg:pl-margin-desktop w-full">
    <a href="../../index.php" class="inline-flex items-center gap-2 font-label-md text-primary hover:text-on-primary-fixed-variant transition-colors">
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        Back to Home
    </a>
</div>
</div>


</body></html>