<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - GlowCare Clinic</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;1,400&family=DM+Sans:wght@300;400;500&display=swap">
    <link rel="stylesheet" href="../../asset/css/style.css">
</head>
<body class="page-auth">

    <section class="kiri-SignUp">
        <div class="kiri-overlay"></div>
        <div class="kiri-teks">
            <p class="kiri-tag">Premium Beauty Clinic</p>
            <h2 class="kiri-judul">Kulit Sehat, <em>Tampil Percaya Diri</em></h2>
            <p class="kiri-desc">Daftarkan diri dan temukan perawatan kulit terbaik yang dirancang khusus untukmu.</p>
        </div>
    </section>

    <div class="kanan" style="overflow-y: auto;">
        <a href="../../index.php" class="back-home">Back to Home</a>
        <div class="k-logo">GlowCare Clinic</div>
        <h1 class="k-judul">Buat <em>Akun Baru</em></h1>

        <form method="POST" action="../../backend/Regist.php">
            <div class="grup">
                <label class="label">Username</label>
                <input type="text" name="username" class="input" placeholder="Masukkan username Anda" required>
            </div>
            <div class="grup">
                <label class="label">Email Address</label>
                <input type="email" name="email" class="input" placeholder="contoh@email.com" required>
            </div>
            <div class="grup">
                <label class="label">Password</label>
                <input type="password" name="password" class="input" placeholder="Kata sandi minimal 8 karakter" required>
            </div>
            <div class="grup">
                <label class="label">Konfirmasi Password</label>
                <input type="password" name="konfirmasi" class="input" placeholder="Ulangi kata sandi Anda" required>
            </div>
            <button type="submit" class="btn-primary" style="width:100%;">Daftar Sekarang</button>
        </form>

        <p class="form-subtitle">
            Sudah punya akun? <a href="SignIn.php">Masuk di sini</a>
        </p>
    </section>
    <script src="../../asset/js/auth.js"></script>

</body>
</html>