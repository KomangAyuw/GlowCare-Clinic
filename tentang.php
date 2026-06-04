<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Tentang Kami - GlowCare Clinic</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;1,400&family=DM+Sans:wght@300;400;500&display=swap">
        <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css?v=4" />
        <link rel="stylesheet" href="asset/css/style.css?v=4">
        <style>
            body { padding-top: 80px; }
            .page-header-banner {
                position: relative;
                padding: 120px 56px;
                text-align: center;
                color: #ffffff;
                background-image: url('asset/img/teal_beauty.png');
                background-size: cover;
                background-position: center;
                z-index: 1;
            }
            .page-header-banner::before {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(135deg, rgba(20, 40, 50, 0.85) 0%, rgba(20, 40, 50, 0.6) 100%);
                z-index: -1;
            }
            .page-header-banner h1 {
                font-family: 'Playfair Display', serif;
                font-size: 48px;
                font-weight: 400;
                margin-bottom: 16px;
            }
            .page-header-banner h1 em { color: #dce8e8; }
            .page-header-banner p {
                font-family: 'DM Sans', sans-serif;
                font-size: 16px;
                font-weight: 300;
                opacity: 0.9;
                max-width: 600px;
                margin: 0 auto;
            }
            .tentang-detail-section {
                padding: 80px 56px;
                max-width: 1100px;
                margin: 0 auto;
                display: flex;
                flex-direction: column;
                gap: 60px;
            }
            .tentang-row {
                display: flex;
                gap: 60px;
                align-items: center;
            }
            .tentang-row.reverse {
                flex-direction: row-reverse;
            }
            .tentang-row img {
                width: 50%;
                border-radius: 16px;
                box-shadow: 0 16px 40px rgba(69, 139, 139, 0.15);
            }
            .tentang-text h3 {
                font-family: 'Playfair Display', serif;
                font-size: 32px;
                color: #2c3e50;
                margin-bottom: 20px;
            }
            .tentang-text p {
                font-size: 15px;
                line-height: 1.8;
                color: #596b6b;
                margin-bottom: 16px;
            }
        </style>
    </head>
    <body>
        <?php include 'backend/nav.php'; ?>

        <div class="page-header-banner" data-aos="fade-down" data-aos-duration="900">
            <h1>Tentang <em>GlowCare Clinic</em></h1>
            <p>Mendedikasikan diri untuk kecantikan dan kesehatan kulit Anda melalui teknologi modern dan tenaga ahli bersertifikat.</p>
        </div>

        <main class="tentang-detail-section">
            <div class="tentang-row" data-aos="fade-up">
                <img src="asset/img/spa.jpg" alt="Fasilitas GlowCare Clinic">
                <div class="tentang-text">
                    <h3>Visi & Misi Kami</h3>
                    <p>Kami hadir sebagai klinik kecantikan premium yang berdedikasi untuk memberikan perawatan terbaik. Visi kami adalah menjadi pelopor inovasi di bidang estetika medis di Indonesia dengan mengutamakan keselamatan dan hasil yang tampak alami.</p>
                    <p>Misi kami meliputi penggunaan teknologi medis mutakhir yang telah disetujui FDA, memberdayakan pasien dengan pengetahuan tentang kulit mereka, serta memberikan pengalaman layanan yang berfokus pada kenyamanan holistik.</p>
                </div>
            </div>

            <div class="tentang-row reverse" data-aos="fade-up">
                <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=800&q=80" alt="Tim GlowCare">
                <div class="tentang-text">
                    <h3>Pendekatan & Standar Kualitas</h3>
                    <p>Kami tidak sekadar merawat permukaan kulit; kami mendengarkan dan menganalisis kebutuhan unik setiap individu. Standar kebersihan dan sterilisasi kami mengacu pada protokol medis internasional yang ketat.</p>
                    <p>Setiap produk dan alat yang kami gunakan dipilih secara selektif oleh tim ahli dermatologi, memastikan efektivitas tinggi dan efek samping yang minimal.</p>
                </div>
            </div>
        </main>

        <?php include 'backend/footer.php'; ?>

        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js?v=2"></script>
        <script>
            AOS.init({ duration: 800, once: true });
        </script>
    </body>
</html>
