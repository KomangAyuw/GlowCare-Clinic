<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - GlowCare Clinic</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;1,400&family=DM+Sans:wght@300;400;500&display=swap">
    <link rel="stylesheet" href="../../asset/css/style.css">
</head>
<body class="page-auth">

    <section class="kiri-SignIn">
        <div class="kiri-overlay"></div>
        <div class="kiri-teks">
            <p class="kiri-tag">GlowCare Clinic</p>
            <h2 class="kiri-judul">Selamat <em>Datang Kembali</em></h2>
            <p class="kiri-desc">Masuk untuk melihat jadwal dan mengelola booking perawatan kamu.</p>
        </div>
    </section>

    <section class="kanan">
        <a href="../../index.php" class="back-home">Back to Home</a>
        <div class="k-logo">GlowCare Clinic</div>
        <h1 class="k-judul">Masuk ke <em>Akun</em></h1>
        

        <form action="../../backend/log.php" method="POST">
            <div class="grup">
                <label class="label">Email Address</label>
                <input type="email" name="email" class="input" placeholder="contoh@email.com" required>
            </div>
            <div class="grup">
                <label class="label">Password</label>
                <input type="password" name="password" class="input" placeholder="Masukkan kata sandi" required>
            </div>
            <button type="submit" class="btn-primary" style="width:100%;">Sign In</button>
        </form>

        <p class="form-subtitle">
            Belum punya akun? <a href="SignUp.php">Daftar di sini</a>
        </p>
    </section>
    <script src="../../asset/js/auth.js"></script>

</body>
</html>