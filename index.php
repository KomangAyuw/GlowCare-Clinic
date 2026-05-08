<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Landing_Page</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;1,400&family=DM+Sans:wght@300;400;500&display=swap">
        <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />
        <link rel="stylesheet" href="asset/css/style.css">
    </head>
    <body>
        <?php include 'backend/nav.php'; ?>

        <main>
            <!-- ══ HERO ══ -->
            <article id="beranda" class="hero">
                <section class="hero-left" data-aos="fade-up" data-aos-duration="900">
                    <p class="hero-eyebrow">Premium Beauty Clinic</p>
                    <h1 class="hero-title">
                        Enhance Your
                        <em>Natural Beauty</em>
                    </h1>
                    <div class="hero-divider"><div class="divider-dot"></div></div>
                    <p class="hero-text">
                        Rasakan pengalaman perawatan kecantikan eksklusif yang dirancang untuk menonjolkan
                        kecantikan alami Anda.
                    </p>
                    <div class="hero-buttons">
                        <a href="#jadwal" class="btn-primary">Book Appointment</a>
                        <a href="#treatment" class="btn-secondary">Explore Services</a>
                    </div>
                </section>

                <section class="hero-right">
                    <img src="asset/img/beauty2.png" alt="Beauty"/>
                </section>

                <section class="hero-stats-bar">
                    <div class="stat-item" data-aos="fade-up" data-aos-delay="0">
                        <div class="stat-num">5.000+</div><div class="stat-label">Happy Clients</div>
                    </div>
                    <div class="stat-item" data-aos="fade-up" data-aos-delay="100">
                        <div class="stat-num">15+</div><div class="stat-label">Treatments</div>
                    </div>
                    <div class="stat-item" data-aos="fade-up" data-aos-delay="200">
                        <div class="stat-num">8+</div><div class="stat-label">Specialists</div>
                    </div>
                    <div class="stat-item" data-aos="fade-up" data-aos-delay="300">
                        <div class="stat-num">6 Thn</div><div class="stat-label">Pengalaman</div>
                    </div>
                </section>
            </article>

            <!-- Tentang -->
            <section id="tentang">
                <div class="tentang-wrapper">
                    <div class="tentang-kiri" data-aos="fade-right" data-aos-duration="900">
                        <img src="asset/img/spa.jpg" alt="GlowCare Clinic" class="tentang-img">
                    </div>
                    <div class="tentang-kanan" data-aos="fade-left" data-aos-duration="900" data-aos-delay="150">
                        <h2 class="tentang-judul">Tentang <em>Kami</em></h2>
                        <p class="tentang-desc">Kami adalah klinik kecantikan premium yang berdedikasi untuk memberikan perawatan terbaik. Dengan teknologi modern dan tenaga ahli berpengalaman, kami hadir untuk menonjolkan kecantikan alami Anda.</p>
                        <p class="tentang-approach">Our Approach</p>
                        <ul class="tentang-list">
                            <li>Perawatan personal yang disesuaikan dengan kebutuhan kulit Anda</li>
                            <li>Teknologi terkini dengan hasil yang terbukti klinis</li>
                            <li>Tim dokter dan estetisian bersertifikat internasional</li>
                        </ul>
                        <a href="#treatment" class="btn-primary">Meet Our Specialists</a>
                    </div>
                </div>
            </section>

            <!-- Services -->
            <section id="services">
                <h2 class="section-header" data-aos="fade-up">Pilihan <em>Services</em></h2>
                <p class="section-subheader" data-aos="fade-up" data-aos-delay="100">Perawatan profesional untuk setiap kebutuhan kulit, ditangani oleh tenaga ahli bersertifikat.</p>

                <div class="services-grid">

                    <!-- Facelift -->
                    <div class="service-card" data-aos="fade-up" data-aos-delay="0">
                        <div class="service-img-wrap">
                            <img src="https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?w=700&auto=format&fit=crop&q=80" alt="Facelift Procedures" class="service-img"/>
                            <div class="service-img-overlay"></div>
                        </div>
                        <div class="service-body">
                            <span class="service-tag">Surgical</span>
                            <h3 class="service-name">Facelift Procedures</h3>
                            <p class="service-desc">Teknik bedah canggih untuk memulihkan kontur wajah yang tampak muda.</p>
                            <a href="pages/treatment/facelift.php" class="service-link">Learn more <span class="service-link-arrow">→</span></a>
                        </div>
                    </div>

                    <!-- Botox & Fillers -->
                    <div class="service-card" data-aos="fade-up" data-aos-delay="100">
                        <div class="service-img-wrap">
                            <img src="https://images.unsplash.com/photo-1512290923902-8a9f81dc236c?w=700&auto=format&fit=crop&q=80" alt="Botox & Fillers" class="service-img"/>
                            <div class="service-img-overlay"></div>
                        </div>
                        <div class="service-body">
                            <span class="service-tag">Injectable</span>
                            <h3 class="service-name">Botox & Fillers</h3>
                            <p class="service-desc">Perawatan suntik yang disetujui FDA untuk menghaluskan kerutan wajah secara alami.</p>
                            <a href="pages/treatment/botox.php" class="service-link">Learn more <span class="service-link-arrow">→</span></a>
                        </div>
                    </div>

                    <!-- Laser Treatments -->
                    <div class="service-card" data-aos="fade-up" data-aos-delay="200">
                        <div class="service-img-wrap">
                            <img src="https://images.unsplash.com/photo-1515377905703-c4788e51af15?w=700&auto=format&fit=crop&q=80" alt="Laser Treatments" class="service-img"/>
                            <div class="service-img-overlay"></div>
                        </div>
                        <div class="service-body">
                            <span class="service-tag">Technology</span>
                            <h3 class="service-name">Laser Treatments</h3>
                            <p class="service-desc">Teknologi laser untuk mengatasi pigmentasi, bekas jerawat, dan tanda penuaan.</p>
                            <a href="pages/treatment/laser.php" class="service-link">Learn more <span class="service-link-arrow">→</span></a>
                        </div>
                    </div>

                    <!-- Body Contouring -->
                    <div class="service-card" data-aos="fade-up" data-aos-delay="300">
                        <div class="service-img-wrap">
                            <img src="https://images.unsplash.com/photo-1607619056574-7b8d3ee536b2?w=700&auto=format&fit=crop&q=80" alt="Body Contouring" class="service-img"/>
                            <div class="service-img-overlay"></div>
                        </div>
                        <div class="service-body">
                            <span class="service-tag">Contouring</span>
                            <h3 class="service-name">Body Contouring</h3>
                            <p class="service-desc">Perawatan khusus untuk membentuk dan merampingkan tubuh dengan presisi.</p>
                            <a href="pages/treatment/contouring.php" class="service-link">Learn more <span class="service-link-arrow">→</span></a>
                        </div>
                    </div>

                </div>

                <!-- CTA -->
                <div class="services-cta" data-aos="zoom-in" data-aos-delay="100">
                    <h3 class="services-cta-title"><b>Siap untuk transformasi kecantikan Anda?</b></h3>
                    <a href="#kontak" class="btn-primary">Book a Consultation Now</a>
                </div>
            </section>

            <!-- Spesialis -->
            <section id="spesialis">
                <h2 class="section-header" data-aos="fade-up">Kenali Tim <em>Spesialis Kami</em></h2>
                <p class="section-subheader" data-aos="fade-up" data-aos-delay="100">Dokter dan estetisian bersertifikat kami menggabungkan keahlian medis dengan sentuhan artistik</p>

                <div class="spesialis-grid">

                    <!-- Dr. 1 -->
                    <div class="spesialis-card" data-aos="zoom-in" data-aos-delay="0">
                        <div class="spesialis-img-wrap">
                            <img src="https://images.unsplash.com/photo-1651008376811-b90baee60c1f?auto=format&fit=crop&w=600&q=80" alt="Dr. Anisa Putri" class="spesialis-img">
                            <div class="spesialis-rating">★★★★★ 5.0</div>
                            <div class="spesialis-quote">*</div>
                        </div>
                        <div class="spesialis-body">
                            <h3 class="spesialis-nama">Dr. Anisa Putri</h3>
                            <p class="spesialis-jabatan">Plastic Surgeon</p>
                            <p class="spesialis-desc">Berpengalaman lebih dari 10 tahun dalam prosedur bedah wajah dan rekonstruksi estetika.</p>
                            <div class="spesialis-tags">
                                <span class="spesialis-tag">Facelift</span>
                                <span class="spesialis-tag">Rhinoplasty</span>
                                <span class="spesialis-tag">Blepharoplasty</span>
                            </div>
                        </div>
                    </div>

                    <!-- Dr. 2 -->
                    <div class="spesialis-card" data-aos="zoom-in" data-aos-delay="150">
                        <div class="spesialis-img-wrap">
                            <img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=600&q=80" alt="Dr. Marina Crystine" class="spesialis-img">
                            <div class="spesialis-rating">★★★★★ 5.0</div>
                            <div class="spesialis-quote">*</div>
                        </div>
                        <div class="spesialis-body">
                            <h3 class="spesialis-nama">Dr. Marina Crystine</h3>
                            <p class="spesialis-jabatan">Aesthetic Physician</p>
                            <p class="spesialis-desc">Spesialis perawatan non-invasif dengan pendekatan personal untuk setiap pasien.</p>
                            <div class="spesialis-tags">
                                <span class="spesialis-tag">CoolSculpting</span>
                                <span class="spesialis-tag">Ultherapy</span>
                                <span class="spesialis-tag">Thread Lifts</span>
                            </div>
                        </div>
                    </div>

                    <!-- Dr. 3 -->
                    <div class="spesialis-card" data-aos="zoom-in" data-aos-delay="300">
                        <div class="spesialis-img-wrap">
                            <img src="https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=600&q=80" alt="Dr. Michael Chen" class="spesialis-img">
                            <div class="spesialis-rating">★★★★★ 5.0</div>
                            <div class="spesialis-quote">*</div>
                        </div>
                        <div class="spesialis-body">
                            <h3 class="spesialis-nama">Dr. Michael Chen</h3>
                            <p class="spesialis-jabatan">Dermatologist</p>
                            <p class="spesialis-desc">Ahli dermatologi dengan keahlian khusus dalam perawatan kulit berbasis teknologi laser.</p>
                            <div class="spesialis-tags">
                                <span class="spesialis-tag">Laser Treatment</span>
                                <span class="spesialis-tag">Botox</span>
                                <span class="spesialis-tag">Fillers</span>
                            </div>
                        </div>
                    </div>

                </div>
            </section>

            <!-- Kontak -->
            <section id="kontak">
                <h2 class="section-header" data-aos="fade-up">Hubungi <em>Kami</em></h2>
                <div class="kontak-wrapper">

                    <!-- Kiri: Info -->
                    <div data-aos="fade-right" data-aos-duration="900">
                        <p class="kontak-card-title">Informasi Klinik</p>
                        <table class="kontak-table">
                            <tr>
                                <td class="kontak-td-icon">📍</td>
                                <td class="kontak-td-label">Alamat</td>
                                <td class="kontak-td-value">Jl. Kecantikan No. 12,<br>Mataram, NTB</td>
                            </tr>
                            <tr>
                                <td class="kontak-td-icon">📞</td>
                                <td class="kontak-td-label">Telepon</td>
                                <td class="kontak-td-value">+62 812 3456 7890</td>
                            </tr>
                            <tr>
                                <td class="kontak-td-icon">✉️</td>
                                <td class="kontak-td-label">Email</td>
                                <td class="kontak-td-value">hello@glowcareclinic.com</td>
                            </tr>
                            <tr>
                                <td class="kontak-td-icon">🕐</td>
                                <td class="kontak-td-label">Jam Buka</td>
                                <td class="kontak-td-value">Sen-Sab: 09.00-20.00<br>Minggu: 10.00-17.00</td>
                            </tr>
                        </table>
                        <img src="asset/img/Contact.png" alt="GlowCare Clinic" class="kontak-img">
                    </div>

                    <!-- Kanan: Form -->
                    <div data-aos="fade-left" data-aos-duration="900" data-aos-delay="150">
                        <p class="kontak-card-title">Kirim Pesan</p>
                        <form class="kontak-form">
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-input" name="nama" placeholder="Nama kamu">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Telepon</label>
                                <input type="tel" class="form-input" name="telp" placeholder="+62">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-input" name="email" placeholder="email@contoh.com">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Pesan</label>
                                <textarea class="form-textarea" name="pesan" placeholder="Ceritakan keluhan atau pertanyaan kamu..."></textarea>
                            </div>
                            <button type="submit" class="btn">Kirim Pesan</button>
                        </form>
                    </div>

                </div>
            </section>
        </main>

        <!-- FOOTER -->
        <footer>
            <div class="footer-top">
                <div class="footer-brand" data-aos="fade-up" data-aos-delay="0">
                    <div class="logo">GlowCare Clinic</div>
                    <p class="footer-desc">Klinik kecantikan premium yang berdedikasi untuk menonjolkan kecantikan alami Anda dengan perawatan terkini dan tim ahli berpengalaman.</p>
                    <div class="footer-socials">
                        <a href="#" class="footer-social">f</a>
                        <a href="#" class="footer-social">in</a>
                        <a href="#" class="footer-social">ig</a>
                        <a href="#" class="footer-social">yt</a>
                    </div>
                </div>
                <div class="footer-links" data-aos="fade-up" data-aos-delay="100">
                    <h4 class="footer-heading">Quick Links</h4>
                    <ul>
                        <li><a href="#beranda">Beranda</a></li>
                        <li><a href="#tentang">Tentang Kami</a></li>
                        <li><a href="#services">Treatment</a></li>
                        <li><a href="#spesialis">Spesialis</a></li>
                        <li><a href="jadwal.php">Jadwal</a></li>
                    </ul>
                </div>
                <div class="footer-links" data-aos="fade-up" data-aos-delay="200">
                    <h4 class="footer-heading">Kontak</h4>
                    <ul>
                        <li>📍 Jl. Kecantikan No. 12, Mataram</li>
                        <li>📞 +62 812 3456 7890</li>
                        <li>✉️ hello@glowcareclinic.com</li>
                        <li>🕐 Sen-Sab: 09.00-20.00</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="footer-copy">© 2025 GlowCare Clinic. All rights reserved.</p>
                <div class="footer-legal">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                </div>
            </div>
        </footer>

        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            AOS.init({
                duration: 800,
                easing: 'ease-out-cubic',
                once: false,
                offset: 80
            });
        </script>
        <script src="asset/js/script.js"></script>
    </body>
</html>