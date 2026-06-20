<?php
session_start();
// Jika sudah login, redirect ke dashboard yang sesuai
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'] ?? 'user';
    if ($role === 'admin') {
        header('Location: ../../pages/admin/dashboard.php');
    } elseif ($role === 'dokter') {
        header('Location: ../../pages/dokter/dashboardDokter.php');
    } else {
        header('Location: ../../pages/user/dashboarduser.php');
    }
    exit;
}
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
                    "surface-tint": "#735a39",
                    "on-primary-container": "#654d2d",
                    "inverse-surface": "#30312e",
                    "tertiary-fixed": "#dde4e6",
                    "primary-fixed-dim": "#e1c198",
                    "on-primary-fixed-variant": "#594323",
                    "surface-container-low": "#f5f3ee",
                    "surface-container-highest": "#e4e2dd",
                    "outline-variant": "#d1c4b8",
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
                    "primary": "#735a39",
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
<style>
/* Hide Microsoft Edge / IE native password reveal and clear button */
input::-ms-reveal,
input::-ms-clear {
    display: none;
}
</style>
</head>
<body class="bg-surface text-on-surface antialiased min-h-screen flex selection:bg-primary/20 selection:text-primary">
<!-- Left Screen: Image Canvas (Hidden on Mobile) -->
<div class="hidden lg:block lg:w-1/2 relative bg-surface-container-high">
<div class="absolute inset-0 bg-cover bg-center" style="background-image: url(&quot;../../asset/img/regis.png&quot;);"></div>
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

<div id="error-container" class="<?php echo $error ? '' : 'hidden'; ?> bg-error-container border border-error text-on-error-container px-4 py-3 rounded-lg mb-6 font-body-sm flex items-center gap-2">
    <span class="material-symbols-outlined">error</span>
    <span id="error-message"><?php echo htmlspecialchars($error); ?></span>
</div>

<!-- Form Section -->
<form action="../../backend/Regist.php" class="space-y-6" method="POST" id="signup-form" novalidate>
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
<input autocomplete="tel" class="block w-full px-4 py-3 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant/50 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all duration-200" id="phone" name="phone" required="" type="tel">
</div>
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-xs" for="password">
                            Password
                        </label>
<div class="relative">
<input autocomplete="new-password" class="block w-full pl-4 pr-12 py-3 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant/50 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all duration-200" id="password" name="password" required="" type="password" maxlength="8">
<button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center text-on-surface-variant/70 hover:text-primary transition-colors toggle-password" data-target="password">
    <span class="material-symbols-outlined text-xl">visibility_off</span>
</button>
</div>
</div>
<div>
<label class="block font-label-md text-label-md text-on-surface-variant mb-xs" for="konfirmasi">
                            Confirm Password
                        </label>
<div class="relative">
<input autocomplete="new-password" class="block w-full pl-4 pr-12 py-3 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-on-surface-variant/50 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all duration-200" id="konfirmasi" name="konfirmasi" required="" type="password" maxlength="8">
<button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center text-on-surface-variant/70 hover:text-primary transition-colors toggle-password" data-target="konfirmasi">
    <span class="material-symbols-outlined text-xl">visibility_off</span>
</button>
</div>
</div>
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
<script>
document.getElementById('signup-form').addEventListener('submit', function(e) {
    const username = document.getElementById('username').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('konfirmasi').value;
    const errorContainer = document.getElementById('error-container');
    const errorMessage = document.getElementById('error-message');

    function showError(msg) {
        errorMessage.textContent = msg;
        errorContainer.classList.remove('hidden');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // 1. Check if all fields are empty
    if (!username && !email && !phone && !password && !confirm) {
        e.preventDefault();
        showError('Semua field harus diisi.');
        return;
    }
    // Check each field individually for specific error messages
    if (!username) {
        e.preventDefault();
        showError('Nama Lengkap harus diisi.');
        return;
    }
    if (!email) {
        e.preventDefault();
        showError('Email harus diisi.');
        return;
    }
    if (!phone) {
        e.preventDefault();
        showError('Nomor telepon harus diisi.');
        return;
    }
    if (!password) {
        e.preventDefault();
        showError('Password harus diisi.');
        return;
    }
    if (!confirm) {
        e.preventDefault();
        showError('Konfirmasi password harus diisi.');
        return;
    }



    // 3. Check email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        e.preventDefault();
        showError('Format email tidak valid.');
        return;
    }

    // 4. Check phone number length (kurang dari 11 ATAU lebih dari sama dengan 13)
    if (phone.length < 11 || phone.length >= 13) {
        e.preventDefault();
        showError('Nomor telepon tidak valid (harus 11 atau 12 karakter).');
        return;
    }

    // 5. Check password length (harus 8 karakter)
    if (password.length !== 8) {
        e.preventDefault();
        showError('Password harus terdiri dari 8 karakter.');
        return;
    }

    // 6. Check password confirmation
    if (password !== confirm) {
        e.preventDefault();
        showError('Password dan konfirmasi tidak cocok.');
        return;
    }
});

// Toggle password visibility
document.querySelectorAll('.toggle-password').forEach(button => {
    button.addEventListener('click', function() {
        const targetId = this.getAttribute('data-target');
        const input = document.getElementById(targetId);
        const icon = this.querySelector('.material-symbols-outlined');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = 'visibility';
        } else {
            input.type = 'password';
            icon.textContent = 'visibility_off';
        }
    });
});
</script>
</body></html>