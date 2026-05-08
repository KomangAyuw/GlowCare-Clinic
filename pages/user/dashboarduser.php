<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../pages/auth/Signin.php');
    exit;
}
$username = htmlspecialchars($_SESSION['username'] ?? 'User');
$email = htmlspecialchars($_SESSION['email'] ?? '');
$initial = strtoupper(substr($username, 0, 1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GlowCare — Dashboard Pasien</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@300;400;500&display=swap">
    <link rel="stylesheet" href="../../asset/css/user.css">
</head>
<body>
    <!-- ══ TOP NAV ══ -->
    <nav class="topnav">
        <div class="topnav-brand">GlowCare Clinic</div>

        <div class="topnav-menu">
            <button class="topnav-item active" onclick="showPage('beranda', this)">Beranda</button>
            <button class="topnav-item" onclick="showPage('jadwal-dokter', this)">Jadwal Dokter</button>
            <button class="topnav-item" onclick="showPage('daftar-konsul', this)">Daftar Konsultasi</button>
            <button class="topnav-item" onclick="showPage('riwayat', this)">Riwayat</button>
        </div>

        <div class="topnav-right">
            <div class="notif-btn" onclick="showPage('notifikasi', document.querySelector('[onclick*=notifikasi]'))">
                <div class="notif-dot"></div>
            </div>
            <div class="user-chip" onclick="showPage('akun', document.querySelector('[onclick*=akun]'))">
                <div class="user-chip-avatar">S</div>
                <span class="user-chip-name">Siti Rahayu</span>
            </div>
        </div>
    </nav>

    <!-- ══════════════════════════════════════════
        PAGE: BERANDA
    ══════════════════════════════════════════ -->
    <div class="page active" id="page-beranda">

        <!-- Hero banner -->
        <div class="hero-banner">
            <div class="hero-greeting">Sabtu, 02 Mei 2026</div>
            <div class="hero-name">Halo, <em>Siti Rahayu</em></div>
            <div class="hero-sub">Selamat datang kembali di GlowCare Clinic. Yuk, jaga kecantikan alami Anda.</div>
            <div class="hero-stats">
                <div class="hero-stat-item">
                    <div class="hero-stat-val">12</div>
                    <div class="hero-stat-lbl">Total Kunjungan</div>
                </div>
                <div class="hero-divider-v"></div>
                <div class="hero-stat-item">
                    <div class="hero-stat-val">1</div>
                    <div class="hero-stat-lbl">Jadwal Mendatang</div>
                </div>
                <div class="hero-divider-v"></div>
                <div class="hero-stat-item">
                    <div class="hero-stat-val">3</div>
                    <div class="hero-stat-lbl">Notifikasi Baru</div>
                </div>
            </div>
        </div>

        <div class="content-area">

            <!-- Jadwal mendatang + Notif -->
            <div class="two-col">

                <!-- Jadwal mendatang -->
                <div>
                    <div class="card" style="margin-bottom:20px">
                        <div class="card-header">
                            <div class="card-title">Jadwal <em>Mendatang</em></div>
                            <button class="card-action" onclick="showPage('daftar-konsul', document.querySelector('[onclick*=daftar-konsul]'))">Daftar</button>
                        </div>

                        <!-- Active appointment -->
                        <div style="padding: 0 26px 26px">
                            <div style="background:linear-gradient(135deg,#3d1a22,#c55085); border-radius:12px; padding:22px; color:#fff; position:relative; overflow:hidden">
                                <div style="font-size:9px; letter-spacing:3px; text-transform:uppercase; color:rgba(255,255,255,0.5); margin-bottom:10px">JADWAL BERIKUTNYA</div>
                                <div style="font-family:'Playfair Display',serif; font-size:20px; margin-bottom:4px">Dr. Anisa Putri</div>
                                <div style="font-size:10px; letter-spacing:1.5px; text-transform:uppercase; color:#f2c4ce; margin-bottom:14px">Facelift Consultation</div>
                                <div style="display:flex; gap:20px">
                                    <div>
                                        <div style="font-size:9px; color:rgba(255,255,255,0.45); letter-spacing:1px; text-transform:uppercase; margin-bottom:2px">Tanggal</div>
                                        <div style="font-size:13px">Senin, 23 Mei 2026</div>
                                    </div>
                                    <div>
                                        <div style="font-size:9px; color:rgba(255,255,255,0.45); letter-spacing:1px; text-transform:uppercase; margin-bottom:2px">Jam</div>
                                        <div style="font-size:13px">09:00 WIB</div>
                                    </div>
                                    <div>
                                        <div style="font-size:9px; color:rgba(255,255,255,0.45); letter-spacing:1px; text-transform:uppercase; margin-bottom:2px">Ruangan</div>
                                        <div style="font-size:13px">Ruang A-1</div>
                                    </div>
                                </div>
                                <div style="margin-top:16px; display:flex; gap:8px">
                                    <button onclick="openModal('modal-batalkan')" style="background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.25); color:#fff; padding:7px 16px; border-radius:50px; font-size:10px; letter-spacing:1px; text-transform:uppercase; font-family:'DM Sans',sans-serif; cursor:pointer;">Batalkan</button>
                                    <button style="background:#fff; border:none; color:#c55085; padding:7px 16px; border-radius:50px; font-size:10px; letter-spacing:1px; text-transform:uppercase; font-family:'DM Sans',sans-serif; cursor:pointer; font-weight:500;">Lihat Detail</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rekomendasi dokter -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Dokter <em>Favorit</em></div>
                            <button class="card-action" onclick="showPage('jadwal-dokter', document.querySelector('[onclick*=jadwal-dokter]'))">Semua →</button>
                        </div>
                        <div style="padding:0 26px 26px; display:flex; flex-direction:column; gap:12px">
                            <div class="dokter-card">
                                <div class="dokter-avatar">
                                    <img src="https://images.unsplash.com/photo-1651008376811-b90baee60c1f?auto=format&fit=crop&w=200&q=80">
                                </div>
                                <div style="flex:1">
                                    <div class="dokter-name">Dr. Anisa Putri</div>
                                    <div class="dokter-spec">Plastic Surgeon</div>
                                    <div class="dokter-rating">5.0 · 412 pasien</div>
                                    <div class="dokter-slots">
                                        <span class="slot-chip">09:00</span>
                                        <span class="slot-chip">13:00</span>
                                        <span class="slot-chip penuh">15:00 penuh</span>
                                    </div>
                                </div>
                                <button class="btn-primary btn-sm" onclick="showPage('daftar-konsul', document.querySelector('[onclick*=daftar-konsul]'))">Daftar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notif ringkas + Quick CTA -->
                <div style="display:flex; flex-direction:column; gap:20px">

                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Notifikasi <em>Terbaru</em></div>
                            <button class="card-action" onclick="showPage('notifikasi', document.querySelector('[onclick*=notifikasi]'))">Semua →</button>
                        </div>
                        <div>
                            <div class="notif-item unread">
                                <div class="notif-icon pink"></div>
                                <div>
                                    <div class="notif-title">Pengingat Jadwal</div>
                                    <div class="notif-desc">Jadwal konsultasi Anda dengan Dr. Anisa Putri pada 23 Mei 2026 pukul 09:00 WIB.</div>
                                    <div class="notif-time">2 jam lalu</div>
                                </div>
                                <div class="unread-dot"></div>
                            </div>
                            <div class="notif-item unread">
                                <div class="notif-icon green"></div>
                                <div>
                                    <div class="notif-title">Pendaftaran Dikonfirmasi</div>
                                    <div class="notif-desc">Jadwal Konsultasi Anda telah dikonfirmasi oleh pihak klinik.</div>
                                    <div class="notif-time">1 hari lalu</div>
                                </div>
                                <div class="unread-dot"></div>
                            </div>
                            <div class="notif-item unread">
                                <div class="notif-icon yellow"></div>
                                <div>
                                    <div class="notif-title">Beri Ulasan</div>
                                    <div class="notif-desc">Bagaimana kunjungan Anda pada 02 Apr 2026? Beri rating untuk Dr. Anisa Putri.</div>
                                    <div class="notif-time">3 hari lalu</div>
                                </div>
                                <div class="unread-dot"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick action -->
                    <div class="card">
                        <div class="card-header"><div class="card-title">Akses <em>Cepat</em></div></div>
                        <div style="padding:0 26px 26px; display:grid; grid-template-columns:1fr 1fr; gap:10px">
                            <button onclick="showPage('daftar-konsul', document.querySelector('[onclick*=daftar-konsul]'))" style="background:#fde8f2; border:none; border-radius:10px; padding:16px; text-align:center; cursor:pointer; transition:background 0.15s; display:flex; flex-direction:column; align-items:center; gap:6px">
                                <span style="font-size:22px"></span>
                                <span style="font-size:11px; color:#c55085; letter-spacing:0.5px">Daftar Konsultasi</span>
                            </button>
                            <button onclick="showPage('jadwal-dokter', document.querySelector('[onclick*=jadwal-dokter]'))" style="background:#f0f9f5; border:none; border-radius:10px; padding:16px; text-align:center; cursor:pointer; transition:background 0.15s; display:flex; flex-direction:column; align-items:center; gap:6px">
                                <span style="font-size:22px"></span>
                                <span style="font-size:11px; color:#5bab8b; letter-spacing:0.5px">Lihat Jadwal</span>
                            </button>
                            <button onclick="showPage('riwayat', document.querySelector('[onclick*=riwayat]'))" style="background:#f0effe; border:none; border-radius:10px; padding:16px; text-align:center; cursor:pointer; transition:background 0.15s; display:flex; flex-direction:column; align-items:center; gap:6px">
                                <span style="font-size:22px"></span>
                                <span style="font-size:11px; color:#9b7dd4; letter-spacing:0.5px">Riwayat Konsultasi</span>
                            </button>
                            <button onclick="showPage('akun', document.querySelector('[onclick*=akun]'))" style="background:#fff5e8; border:none; border-radius:10px; padding:16px; text-align:center; cursor:pointer; transition:background 0.15s; display:flex; flex-direction:column; align-items:center; gap:6px">
                                <span style="font-size:22px"></span>
                                <span style="font-size:11px; color:#e8956d; letter-spacing:0.5px">Kelola Akun</span>
                            </button>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- ══════════════════════════════════════════
        PAGE: JADWAL DOKTER
    ══════════════════════════════════════════ -->
    <div class="page" id="page-jadwal-dokter">

        <div class="hero-banner" style="padding-bottom:50px">
            <div class="hero-greeting">Jadwal Dokter Real-Time</div>
            <div class="hero-name">Pilih <em>Dokter & Waktu</em></div>
            <div class="hero-sub">Lihat ketersediaan dokter dan pilih jadwal yang sesuai kebutuhan Anda.</div>
        </div>

        <div class="content-area">

            <!-- Filter -->
            <div style="display:flex; gap:10px; margin-bottom:20px; flex-wrap:wrap">
                <select class="form-select" style="width:auto; background:#fff">
                    <option>Semua Spesialisasi</option>
                    <option>Plastic Surgeon</option>
                    <option>Aesthetic Physician</option>
                    <option>Dermatologist</option>
                </select>
                <select class="form-select" style="width:auto; background:#fff">
                    <option>Semua Treatment</option>
                    <option>Facelift</option>
                    <option>Botox & Fillers</option>
                    <option>Laser Treatment</option>
                    <option>Body Contouring</option>
                </select>
                <input class="form-input" type="date" style="width:auto; background:#fff" value="2026-05-23">
            </div>

            <div class="three-col">

                <!-- Dr. Anisa -->
                <div class="card">
                    <div style="background:linear-gradient(135deg,#f9e7ef,#fdf0f5); padding:20px; display:flex; gap:14px; align-items:flex-start">
                        <div class="dokter-avatar" style="width:56px;height:56px">
                            <img src="https://images.unsplash.com/photo-1651008376811-b90baee60c1f?auto=format&fit=crop&w=200&q=80">
                        </div>
                        <div>
                            <div class="dokter-name">Dr. Anisa Putri</div>
                            <div class="dokter-spec">Plastic Surgeon</div>
                            <div class="dokter-rating"> 5.0 · 10+ Tahun</div>
                        </div>
                        <span class="badge badge-green" style="margin-left:auto">Tersedia</span>
                    </div>
                    <div style="padding:14px 20px; font-size:11px; color:#b89098; letter-spacing:0.5px; border-bottom:1px solid #fdf0f5">
                        Keahlian: Facelift · Rhinoplasty · Blepharoplasty
                    </div>
                    <div class="slot-section" style="padding:16px 20px 20px">
                        <div class="slot-date-title">Senin, 23 Mei 2026</div>
                        <div class="slot-grid">
                            <div class="slot-btn" onclick="selectSlot(this)">09:00</div>
                            <div class="slot-btn" onclick="selectSlot(this)">10:00</div>
                            <div class="slot-btn full">11:00<br><span style="font-size:9px">penuh</span></div>
                            <div class="slot-btn" onclick="selectSlot(this)">13:00</div>
                            <div class="slot-btn full">14:00<br><span style="font-size:9px">penuh</span></div>
                            <div class="slot-btn" onclick="selectSlot(this)">15:00</div>
                            <div class="slot-btn" onclick="selectSlot(this)">16:00</div>
                            <div class="slot-btn full">17:00<br><span style="font-size:9px">penuh</span></div>
                        </div>
                        <button class="btn-primary" style="width:100%; margin-top:14px; text-align:center" onclick="showPage('daftar-konsul', document.querySelector('[onclick*=daftar-konsul]'))">Pilih & Daftar</button>
                    </div>
                </div>

                <!-- Dr. Marina -->
                <div class="card">
                    <div style="background:linear-gradient(135deg,#f9e7ef,#fdf0f5); padding:20px; display:flex; gap:14px; align-items:flex-start">
                        <div class="dokter-avatar" style="width:56px;height:56px">
                            <img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=200&q=80">
                        </div>
                        <div>
                            <div class="dokter-name">Dr. Marina Crystine</div>
                            <div class="dokter-spec">Aesthetic Physician</div>
                            <div class="dokter-rating"> 5.0 · 8 Tahun</div>
                        </div>
                        <span class="badge badge-green" style="margin-left:auto">Tersedia</span>
                    </div>
                    <div style="padding:14px 20px; font-size:11px; color:#b89098; letter-spacing:0.5px; border-bottom:1px solid #fdf0f5">
                        Keahlian: Botox · CoolSculpting · Thread Lifts
                    </div>
                    <div class="slot-section" style="padding:16px 20px 20px">
                        <div class="slot-date-title">Senin, 23 Mei 2026</div>
                        <div class="slot-grid">
                            <div class="slot-btn full">09:00<br><span style="font-size:9px">penuh</span></div>
                            <div class="slot-btn" onclick="selectSlot(this)">10:00</div>
                            <div class="slot-btn" onclick="selectSlot(this)">11:00</div>
                            <div class="slot-btn full">13:00<br><span style="font-size:9px">penuh</span></div>
                            <div class="slot-btn" onclick="selectSlot(this)">14:00</div>
                            <div class="slot-btn" onclick="selectSlot(this)">15:00</div>
                            <div class="slot-btn full">16:00<br><span style="font-size:9px">penuh</span></div>
                            <div class="slot-btn" onclick="selectSlot(this)">17:00</div>
                        </div>
                        <button class="btn-primary" style="width:100%; margin-top:14px; text-align:center" onclick="showPage('daftar-konsul', document.querySelector('[onclick*=daftar-konsul]'))">Pilih & Daftar</button>
                    </div>
                </div>

                <!-- Dr. Michael -->
                <div class="card">
                    <div style="background:linear-gradient(135deg,#f9e7ef,#fdf0f5); padding:20px; display:flex; gap:14px; align-items:flex-start">
                        <div class="dokter-avatar" style="width:56px;height:56px">
                            <img src="https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=200&q=80">
                        </div>
                        <div>
                            <div class="dokter-name">Dr. Michael Chen</div>
                            <div class="dokter-spec">Dermatologist</div>
                            <div class="dokter-rating"> 5.0 · 12 Tahun</div>
                        </div>
                        <span class="badge badge-yellow" style="margin-left:auto">Sibuk</span>
                    </div>
                    <div style="padding:14px 20px; font-size:11px; color:#b89098; letter-spacing:0.5px; border-bottom:1px solid #fdf0f5">
                        Keahlian: Laser Treatment · Botox · Fillers
                    </div>
                    <div class="slot-section" style="padding:16px 20px 20px">
                        <div class="slot-date-title">Senin, 23 Mei 2026</div>
                        <div class="slot-grid">
                            <div class="slot-btn full">09:00<br><span style="font-size:9px">penuh</span></div>
                            <div class="slot-btn full">10:00<br><span style="font-size:9px">penuh</span></div>
                            <div class="slot-btn full">11:00<br><span style="font-size:9px">penuh</span></div>
                            <div class="slot-btn full">13:00<br><span style="font-size:9px">penuh</span></div>
                            <div class="slot-btn" onclick="selectSlot(this)">14:00</div>
                            <div class="slot-btn" onclick="selectSlot(this)">15:00</div>
                            <div class="slot-btn" onclick="selectSlot(this)">16:00</div>
                            <div class="slot-btn full">17:00<br><span style="font-size:9px">penuh</span></div>
                        </div>
                        <button class="btn-primary" style="width:100%; margin-top:14px; text-align:center" onclick="showPage('daftar-konsul', document.querySelector('[onclick*=daftar-konsul]'))">Pilih & Daftar</button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
        PAGE: DAFTAR KONSULTASI
    ══════════════════════════════════════════ -->
    <div class="page" id="page-daftar-konsul">

        <div class="hero-banner" style="padding-bottom:50px">
            <div class="hero-greeting">Pendaftaran Online</div>
            <div class="hero-name">Daftar <em>Konsultasi</em></div>
            <div class="hero-sub">Isi formulir pendaftaran berikut untuk memesan jadwal konsultasi Anda.</div>
        </div>

        <div class="content-area">
            <!-- Step indicator -->
            <div class="steps" id="step-indicator">
                <div class="step active" id="step1">
                    <div class="step-num">1</div>
                    <div class="step-label">Pilih Dokter</div>
                </div>
                <div class="step-line"></div>
                <div class="step" id="step2">
                    <div class="step-num">2</div>
                    <div class="step-label">Pilih Jadwal</div>
                </div>
                <div class="step-line"></div>
                <div class="step" id="step3">
                    <div class="step-num">3</div>
                    <div class="step-label">Isi Keluhan</div>
                </div>
                <div class="step-line"></div>
                <div class="step" id="step4">
                    <div class="step-num">4</div>
                    <div class="step-label">Konfirmasi</div>
                </div>
            </div>

            <!-- Step content wrapper -->
            <div id="step-content">

                <!-- STEP 1: Pilih Dokter -->
                <div id="step-panel-1">
                    <div class="card" style="margin-bottom:20px">
                        <div class="card-header"><div class="card-title">Pilih <em>Dokter</em></div></div>
                        <div style="padding:0 26px 26px; display:flex; flex-direction:column; gap:12px">

                            <div class="dokter-card" id="dokter-option-1" onclick="selectDokter(1)" style="border:2px solid #c55085">
                                <div class="dokter-avatar" style="width:52px;height:52px">
                                    <img src="https://images.unsplash.com/photo-1651008376811-b90baee60c1f?auto=format&fit=crop&w=200&q=80">
                                </div>
                                <div style="flex:1">
                                    <div class="dokter-name">Dr. Anisa Putri</div>
                                    <div class="dokter-spec">Plastic Surgeon</div>
                                    <div class="dokter-rating"> 5.0 · Facelift · Rhinoplasty · Blepharoplasty</div>
                                </div>
                                <span class="badge badge-green">Terpilih ✓</span>
                            </div>

                            <div class="dokter-card" id="dokter-option-2" onclick="selectDokter(2)">
                                <div class="dokter-avatar" style="width:52px;height:52px">
                                    <img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&w=200&q=80">
                                </div>
                                <div style="flex:1">
                                    <div class="dokter-name">Dr. Marina Crystine</div>
                                    <div class="dokter-spec">Aesthetic Physician</div>
                                    <div class="dokter-rating"> 5.0 · Botox · CoolSculpting · Thread Lifts</div>
                                </div>
                                <span class="badge badge-gray">Pilih</span>
                            </div>

                            <div class="dokter-card" id="dokter-option-3" onclick="selectDokter(3)">
                                <div class="dokter-avatar" style="width:52px;height:52px">
                                    <img src="https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&w=200&q=80">
                                </div>
                                <div style="flex:1">
                                    <div class="dokter-name">Dr. Michael Chen</div>
                                    <div class="dokter-spec">Dermatologist</div>
                                    <div class="dokter-rating"> 5.0 · Laser Treatment · Botox · Fillers</div>
                                </div>
                                <span class="badge badge-gray">Pilih</span>
                            </div>
                        </div>
                    </div>
                    <div style="display:flex; justify-content:flex-end">
                        <button class="btn-primary" onclick="nextStep(2)">Lanjut: Pilih Jadwal →</button>
                    </div>
                </div>

                <!-- STEP 2: Pilih Jadwal -->
                <div id="step-panel-2" style="display:none">
                    <div class="two-col" style="margin-bottom:20px">
                        <div class="card">
                            <div class="card-header"><div class="card-title">Pilih <em>Tanggal</em></div></div>
                            <div class="cal-header">
                                <div class="cal-month">Mei 2026</div>
                                <div class="cal-nav">
                                    <button class="cal-nav-btn">←</button>
                                    <button class="cal-nav-btn">→</button>
                                </div>
                            </div>
                            <div class="cal-grid">
                                <div class="cal-day-label">Min</div><div class="cal-day-label">Sen</div><div class="cal-day-label">Sel</div><div class="cal-day-label">Rab</div><div class="cal-day-label">Kam</div><div class="cal-day-label">Jum</div><div class="cal-day-label">Sab</div>
                                <div class="cal-day other-month">27</div><div class="cal-day other-month">28</div><div class="cal-day other-month">29</div><div class="cal-day other-month">30</div><div class="cal-day">1</div><div class="cal-day today">2</div><div class="cal-day">3</div>
                                <div class="cal-day">4</div><div class="cal-day">5</div><div class="cal-day">6</div><div class="cal-day">7</div><div class="cal-day">8</div><div class="cal-day">9</div><div class="cal-day">10</div>
                                <div class="cal-day">11</div><div class="cal-day">12</div><div class="cal-day has-appointment">13</div><div class="cal-day">14</div><div class="cal-day">15</div><div class="cal-day">16</div><div class="cal-day">17</div>
                                <div class="cal-day">18</div><div class="cal-day">19</div><div class="cal-day">20</div><div class="cal-day">21</div><div class="cal-day">22</div><div class="cal-day selected" onclick="pickDate(this)">23</div><div class="cal-day">24</div>
                                <div class="cal-day">25</div><div class="cal-day">26</div><div class="cal-day">27</div><div class="cal-day">28</div><div class="cal-day">29</div><div class="cal-day">30</div><div class="cal-day">31</div>
                            </div>
                            <div style="padding:0 20px 16px; font-size:11px; color:#b89098; display:flex; gap:16px">
                                <span style="display:flex; align-items:center; gap:5px"><span style="width:8px;height:8px;border-radius:50%;background:#c55085;display:inline-block"></span>Ada appointment</span>
                                <span style="display:flex; align-items:center; gap:5px"><span style="width:16px;height:16px;border-radius:50%;background:#c55085;display:inline-block;opacity:0.8"></span>Hari ini</span>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header"><div class="card-title">Pilih <em>Jam</em></div></div>
                            <div class="slot-section">
                                <div class="slot-date-title">Senin, 23 Mei 2026 · Dr. Anisa Putri</div>
                                <div class="slot-grid">
                                    <div class="slot-btn selected" onclick="selectSlot(this)">09:00</div>
                                    <div class="slot-btn" onclick="selectSlot(this)">10:00</div>
                                    <div class="slot-btn full">11:00<br><span style="font-size:9px">penuh</span></div>
                                    <div class="slot-btn" onclick="selectSlot(this)">13:00</div>
                                    <div class="slot-btn full">14:00<br><span style="font-size:9px">penuh</span></div>
                                    <div class="slot-btn" onclick="selectSlot(this)">15:00</div>
                                    <div class="slot-btn" onclick="selectSlot(this)">16:00</div>
                                    <div class="slot-btn full">17:00<br><span style="font-size:9px">penuh</span></div>
                                </div>
                                <div style="margin-top:16px; background:#fdf0f5; border-radius:8px; padding:12px 14px; font-size:12px; color:#7a4d5c; font-weight:300; line-height:1.7">
                                     <strong style="color:#3d1a22">Jam 09:00</strong> tersedia · Durasi 60 menit · Ruang A-1
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="display:flex; justify-content:space-between">
                        <button class="btn-outline" onclick="nextStep(1)">← Kembali</button>
                        <button class="btn-primary" onclick="nextStep(3)">Lanjut: Isi Keluhan →</button>
                    </div>
                </div>

                <!-- STEP 3: Isi Keluhan -->
                <div id="step-panel-3" style="display:none">
                    <div class="card" style="margin-bottom:20px">
                        <div class="card-header"><div class="card-title">Isi <em>Keluhan</em></div></div>
                        <div style="padding:0 26px 26px">
                            <div class="form-row-2">
                                <div class="form-group">
                                    <label class="form-label">Treatment yang Diinginkan</label>
                                    <select class="form-select">
                                        <option>Facelift Consultation</option>
                                        <option>Rhinoplasty</option>
                                        <option>Blepharoplasty</option>
                                        <option>Konsultasi Umum</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Pernah Berkunjung Sebelumnya?</label>
                                    <select class="form-select">
                                        <option>Ya, pasien lama</option>
                                        <option>Tidak, kunjungan pertama</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Keluhan Utama</label>
                                <textarea class="form-textarea" placeholder="Ceritakan keluhan atau hal yang ingin Anda konsultasikan secara singkat...">Kulit wajah mulai kendur di area pipi dan leher. Ingin konsultasi mengenai opsi facelift yang tepat untuk usia saya.</textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Riwayat Alergi / Kondisi Khusus</label>
                                <input class="form-input" placeholder="Contoh: alergi penisilin, riwayat keloid, dsb." value="Tidak ada">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Catatan Tambahan</label>
                                <textarea class="form-textarea" style="min-height:60px" placeholder="Pertanyaan atau catatan khusus untuk dokter..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div style="display:flex; justify-content:space-between">
                        <button class="btn-outline" onclick="nextStep(2)">← Kembali</button>
                        <button class="btn-primary" onclick="nextStep(4)">Lanjut: Konfirmasi →</button>
                    </div>
                </div>

                <!-- STEP 4: Konfirmasi -->
                <div id="step-panel-4" style="display:none">
                    <div class="card" style="margin-bottom:20px">
                        <div class="card-header"><div class="card-title">Konfirmasi <em>Pendaftaran</em></div></div>
                        <div style="padding:0 26px 26px">
                            <div class="confirm-box">
                                <div class="confirm-row"><span>Pasien</span><span>Siti Rahayu (#P-0041)</span></div>
                                <div class="confirm-row"><span>Dokter</span><span>Dr. Anisa Putri · Plastic Surgeon</span></div>
                                <div class="confirm-row"><span>Treatment</span><span>Facelift Consultation</span></div>
                                <div class="confirm-row"><span>Tanggal</span><span>Senin, 23 Mei 2026</span></div>
                                <div class="confirm-row"><span>Jam</span><span>09:00 WIB (60 menit)</span></div>
                                <div class="confirm-row"><span>Ruangan</span><span>Ruang A-1</span></div>
                                <div class="confirm-row"><span>Keluhan</span><span style="max-width:280px; text-align:right">Kulit wajah mulai kendur di area pipi dan leher</span></div>
                            </div>
                            <div style="background:#e8f9f1; border-radius:8px; padding:14px 16px; font-size:12px; color:#3dab74; line-height:1.7; margin-bottom:20px">
                                Jadwal tersedia. Setelah klik <strong>Konfirmasi</strong>, sistem akan mengirimkan notifikasi dan pengingat ke email Anda.
                            </div>
                            <label style="display:flex; align-items:flex-start; gap:10px; font-size:12px; color:#7a4d5c; font-weight:300; cursor:pointer; margin-bottom:8px">
                                <input type="checkbox" checked style="margin-top:2px"> Saya menyetujui syarat &amp; ketentuan layanan GlowCare Clinic
                            </label>
                            <label style="display:flex; align-items:flex-start; gap:10px; font-size:12px; color:#7a4d5c; font-weight:300; cursor:pointer">
                                <input type="checkbox" checked style="margin-top:2px"> Kirim pengingat ke email dan SMS saya
                            </label>
                        </div>
                    </div>
                    <div style="display:flex; justify-content:space-between">
                        <button class="btn-outline" onclick="nextStep(3)">← Kembali</button>
                        <button class="btn-primary" onclick="submitPendaftaran()">✓ Konfirmasi Pendaftaran</button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
        PAGE: RIWAYAT
    ══════════════════════════════════════════ -->
    <div class="page" id="page-riwayat">

        <div class="hero-banner" style="padding-bottom:50px">
            <div class="hero-greeting">Rekam Jejak Perawatan</div>
            <div class="hero-name">Riwayat <em>Konsultasi</em></div>
            <div class="hero-sub">Lihat seluruh riwayat kunjungan dan konsultasi Anda di GlowCare Clinic.</div>
        </div>

        <div class="content-area">

            <!-- Filter -->
            <div style="display:flex; gap:10px; margin-bottom:20px; flex-wrap:wrap">
                <input class="form-input" style="width:220px; background:#fff" placeholder="🔍 Cari treatment...">
                <select class="form-select" style="width:auto; background:#fff">
                    <option>Semua Status</option>
                    <option>Selesai</option>
                    <option>Dibatalkan</option>
                </select>
                <select class="form-select" style="width:auto; background:#fff">
                    <option>Semua Tahun</option>
                    <option>2026</option>
                    <option>2025</option>
                </select>
            </div>

            <div class="card">
                <table class="data-table">
                    <thead>
                        <tr><th>Tanggal</th><th>Dokter</th><th>Treatment</th><th>Durasi</th><th>Status</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><div style="font-size:13px; font-weight:400">02 Mei 2026</div><div class="td-sub">09:00 WIB</div></td>
                            <td><span class="td-name">Dr. Anisa Putri</span><span class="td-sub">Plastic Surgeon</span></td>
                            <td>Facelift Consultation</td>
                            <td>60 menit</td>
                            <td><span class="badge badge-green">Selesai</span></td>
                            <td><button class="btn-outline btn-sm" onclick="openModal('modal-detail-riwayat')">Detail</button></td>
                        </tr>
                        <tr>
                            <td><div style="font-size:13px; font-weight:400">02 Apr 2026</div><div class="td-sub">10:00 WIB</div></td>
                            <td><span class="td-name">Dr. Anisa Putri</span><span class="td-sub">Plastic Surgeon</span></td>
                            <td>Botox Forehead</td>
                            <td>30 menit</td>
                            <td><span class="badge badge-green">Selesai</span></td>
                            <td><button class="btn-outline btn-sm" onclick="openModal('modal-detail-riwayat')">Detail</button></td>
                        </tr>
                        <tr>
                            <td><div style="font-size:13px; font-weight:400">12 Mar 2026</div><div class="td-sub">13:00 WIB</div></td>
                            <td><span class="td-name">Dr. Michael Chen</span><span class="td-sub">Dermatologist</span></td>
                            <td>Laser Treatment</td>
                            <td>45 menit</td>
                            <td><span class="badge badge-green">Selesai</span></td>
                            <td><button class="btn-outline btn-sm" onclick="openModal('modal-detail-riwayat')">Detail</button></td>
                        </tr>
                        <tr>
                            <td><div style="font-size:13px; font-weight:400">05 Feb 2026</div><div class="td-sub">09:30 WIB</div></td>
                            <td><span class="td-name">Dr. Marina Crystine</span><span class="td-sub">Aesthetic Physician</span></td>
                            <td>CoolSculpting</td>
                            <td>90 menit</td>
                            <td><span class="badge badge-green">Selesai</span></td>
                            <td><button class="btn-outline btn-sm" onclick="openModal('modal-detail-riwayat')">Detail</button></td>
                        </tr>
                        <tr>
                            <td><div style="font-size:13px; font-weight:400">10 Jan 2026</div><div class="td-sub">15:00 WIB</div></td>
                            <td><span class="td-name">Dr. Anisa Putri</span><span class="td-sub">Plastic Surgeon</span></td>
                            <td>Konsultasi Awal</td>
                            <td>60 menit</td>
                            <td><span class="badge badge-green">Selesai</span></td>
                            <td><button class="btn-outline btn-sm" onclick="openModal('modal-detail-riwayat')">Detail</button></td>
                        </tr>
                        <tr>
                            <td><div style="font-size:13px; font-weight:400">15 Des 2025</div><div class="td-sub">11:00 WIB</div></td>
                            <td><span class="td-name">Dr. Michael Chen</span><span class="td-sub">Dermatologist</span></td>
                            <td>Botox</td>
                            <td>30 menit</td>
                            <td><span class="badge badge-gray">Dibatalkan</span></td>
                            <td><button class="btn-outline btn-sm">Detail</button></td>
                        </tr>
                    </tbody>
                </table>
                <div style="padding:14px 22px; border-top:1px solid #fdf0f5; font-size:12px; color:#b89098; display:flex; justify-content:space-between; align-items:center">
                    <span>Menampilkan 6 dari 12 kunjungan</span>
                    <button class="btn-outline btn-sm">Muat lebih banyak</button>
                </div>
            </div>

        </div>
    </div>

    <!-- ══════════════════════════════════════════
        PAGE: NOTIFIKASI
    ══════════════════════════════════════════ -->
    <div class="page" id="page-notifikasi">

        <div class="hero-banner" style="padding-bottom:50px">
            <div class="hero-greeting">Pusat Pesan</div>
            <div class="hero-name">Notifikasi & <em>Konfirmasi</em></div>
            <div class="hero-sub">Semua pengingat, konfirmasi, dan informasi jadwal konsultasi Anda.</div>
        </div>

        <div class="content-area">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px">
                <div style="display:flex; gap:8px">
                    <button class="btn-primary btn-sm">Semua</button>
                    <button class="btn-outline btn-sm">Belum Dibaca (3)</button>
                    <button class="btn-outline btn-sm">Jadwal</button>
                    <button class="btn-outline btn-sm">Pembayaran</button>
                </div>
                <button class="btn-outline btn-sm" onclick="showToast('Semua notifikasi ditandai dibaca')">Tandai semua dibaca</button>
            </div>

            <div class="card">
                <div class="notif-item unread">
                    <div class="notif-icon pink"></div>
                    <div style="flex:1">
                        <div class="notif-title">Pengingat Jadwal Konsultasi</div>
                        <div class="notif-desc">Jadwal konsultasi Anda dengan <strong>Dr. Anisa Putri</strong> pada Senin, 23 Mei 2026 pukul 09:00 WIB akan segera tiba. Harap datang 15 menit lebih awal untuk registrasi ulang.</div>
                        <div class="notif-time">Hari ini, 08:00</div>
                    </div>
                    <div class="unread-dot"></div>
                </div>
                <div class="notif-item unread">
                    <div class="notif-icon green"></div>
                    <div style="flex:1">
                        <div class="notif-title">Pendaftaran Dikonfirmasi</div>
                        <div class="notif-desc">Pendaftaran konsultasi Anda pada 23 Mei 2026 dengan Dr. Anisa Putri telah <strong>dikonfirmasi</strong> oleh admin klinik. Nomor antrian Anda: <strong>#A-003</strong>.</div>
                        <div class="notif-time">Kemarin, 15:30</div>
                    </div>
                    <div class="unread-dot"></div>
                </div>
                <div class="notif-item unread">
                    <div class="notif-icon yellow"></div>
                    <div style="flex:1">
                        <div class="notif-title">Beri Ulasan untuk Dr. Anisa Putri</div>
                        <div class="notif-desc">Bagaimana kunjungan Anda pada 02 Mei 2026? Bantu pasien lain dengan memberikan ulasan dan rating.</div>
                        <div class="notif-time">2 hari lalu</div>
                        <button class="btn-primary btn-sm" style="margin-top:10px" onclick="openModal('modal-ulasan')">Beri Ulasan</button>
                    </div>
                    <div class="unread-dot"></div>
                </div>
                <div class="notif-item">
                    <div class="notif-icon blue"></div>
                    <div style="flex:1">
                        <div class="notif-title">Pembayaran Berhasil</div>
                        <div class="notif-desc">Pembayaran konsultasi tanggal 02 Mei 2026 sebesar <strong>Rp 350.000</strong> telah berhasil diproses. Terima kasih telah mempercayakan perawatan Anda kepada GlowCare.</div>
                        <div class="notif-time">3 hari lalu</div>
                    </div>
                </div>
                <div class="notif-item">
                    <div class="notif-icon pink"></div>
                    <div style="flex:1">
                        <div class="notif-title">Pengingat Jadwal (Lampau)</div>
                        <div class="notif-desc">Jadwal konsultasi Anda dengan Dr. Anisa Putri pada 02 Mei 2026 pukul 09:00 WIB.</div>
                        <div class="notif-time">4 hari lalu</div>
                    </div>
                </div>
                <div class="notif-item">
                    <div class="notif-icon green"></div>
                    <div style="flex:1">
                        <div class="notif-title">Pendaftaran Dikonfirmasi</div>
                        <div class="notif-desc">Pendaftaran konsultasi Anda pada 02 Mei 2026 telah dikonfirmasi. Nomor antrian: <strong>#A-001</strong>.</div>
                        <div class="notif-time">5 hari lalu</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════
        PAGE: AKUN
    ══════════════════════════════════════════ -->
    <div class="page" id="page-akun">

        <div class="hero-banner" style="padding-bottom:50px">
            <div class="hero-greeting">Kelola Profil</div>
            <div class="hero-name">Pengaturan <em>Akun</em></div>
            <div class="hero-sub">Perbarui data diri dan kelola keamanan akun Anda.</div>
        </div>

        <div class="content-area">

            <!-- Profil strip -->
            <div class="profil-strip">
                <div class="profil-avatar-wrap">
                    <div class="profil-avatar">S</div>
                    <div class="profil-edit-btn"></div>
                </div>
                <div style="flex:1">
                    <div class="profil-name">Siti Rahayu</div>
                    <div class="profil-id">ID Pasien: #P-0041</div>
                    <div class="profil-chips">
                        <span class="profil-chip">Pasien Aktif</span>
                        <span class="profil-chip">12 Kunjungan</span>
                        <span class="profil-chip">Bergabung Jan 2025</span>
                    </div>
                </div>
                <button class="btn-primary" onclick="showToast('Data berhasil disimpan ✓')">Simpan Perubahan</button>
                <a href="../../backend/logout.php" 
                style="background:#e8716d; color:#fff; border:none; padding:10px 24px; border-radius:50px; font-size:11px; letter-spacing:1.5px; text-transform:uppercase; font-family:'DM Sans',sans-serif; cursor:pointer; text-decoration:none; display:inline-block;">
                Keluar
                </a>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px">

                <!-- Data diri -->
                <div class="akun-section">
                    <div class="akun-section-header">
                        <div class="akun-section-title">Data <em style="color:#c55085;font-style:italic">Pribadi</em></div>
                    </div>
                    <div class="akun-section-body">
                        <div class="form-row-2">
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap</label>
                                <input class="form-input" value="Siti Rahayu">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tanggal Lahir</label>
                                <input class="form-input" type="date" value="1998-04-15">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Jenis Kelamin</label>
                                <select class="form-select"><option selected>Perempuan</option><option>Laki-laki</option></select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Golongan Darah</label>
                                <select class="form-select"><option>O+</option><option>A</option><option>B</option><option>AB</option></select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Alamat Lengkap</label>
                            <input class="form-input" value="Jl. Udayana No. 22, Mataram, NTB">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Alergi / Kondisi Khusus</label>
                            <input class="form-input" value="Tidak ada">
                        </div>
                    </div>
                </div>

                <!-- Kontak & keamanan -->
                <div style="display:flex; flex-direction:column; gap:20px">
                    <div class="akun-section">
                        <div class="akun-section-header">
                            <div class="akun-section-title">Kontak</div>
                        </div>
                        <div class="akun-section-body">
                            <div class="form-group">
                                <label class="form-label">No. Telepon</label>
                                <input class="form-input" value="+62 812 1234 5678">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input class="form-input" type="email" value="siti.rahayu@email.com">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Kontak Darurat</label>
                                <input class="form-input" placeholder="Nama dan nomor kontak darurat">
                            </div>
                        </div>
                    </div>

                    <div class="akun-section">
                        <div class="akun-section-header">
                            <div class="akun-section-title">Keamanan <em style="color:#c55085;font-style:italic">Password</em></div>
                        </div>
                        <div class="akun-section-body">
                            <div class="form-group">
                                <label class="form-label">Password Saat Ini</label>
                                <input class="form-input" type="password" placeholder="••••••••">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Password Baru</label>
                                <input class="form-input" type="password" placeholder="Min. 8 karakter">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Konfirmasi Password Baru</label>
                                <input class="form-input" type="password" placeholder="Ulangi password baru">
                            </div>
                            <button class="btn-primary" style="width:100%" onclick="showToast('Password berhasil diubah ✓')">Ubah Password</button>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Preferensi notifikasi -->
            <div class="akun-section" style="margin-top:20px">
                <div class="akun-section-header">
                    <div class="akun-section-title">Preferensi <em style="color:#c55085;font-style:italic">Notifikasi</em></div>
                </div>
                <div class="akun-section-body">
                    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px">
                        <label style="display:flex; align-items:center; justify-content:space-between; font-size:13px; color:#3d1a22; cursor:pointer; padding:12px 16px; background:#fdf0f5; border-radius:8px">
                            Email Pengingat Jadwal
                            <input type="checkbox" checked>
                        </label>
                        <label style="display:flex; align-items:center; justify-content:space-between; font-size:13px; color:#3d1a22; cursor:pointer; padding:12px 16px; background:#fdf0f5; border-radius:8px">
                            SMS / WhatsApp
                            <input type="checkbox" checked>
                        </label>
                        <label style="display:flex; align-items:center; justify-content:space-between; font-size:13px; color:#3d1a22; cursor:pointer; padding:12px 16px; background:#fdf0f5; border-radius:8px">
                            Notifikasi Promosi
                            <input type="checkbox">
                        </label>
                        <label style="display:flex; align-items:center; justify-content:space-between; font-size:13px; color:#3d1a22; cursor:pointer; padding:12px 16px; background:#fdf0f5; border-radius:8px">
                            Konfirmasi Pembayaran
                            <input type="checkbox" checked>
                        </label>
                        <label style="display:flex; align-items:center; justify-content:space-between; font-size:13px; color:#3d1a22; cursor:pointer; padding:12px 16px; background:#fdf0f5; border-radius:8px">
                            Pengingat Follow-up
                            <input type="checkbox" checked>
                        </label>
                        <label style="display:flex; align-items:center; justify-content:space-between; font-size:13px; color:#3d1a22; cursor:pointer; padding:12px 16px; background:#fdf0f5; border-radius:8px">
                            Ulasan & Rating
                            <input type="checkbox" checked>
                        </label>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ══ MODAL: BATALKAN ══ -->
    <div class="modal-overlay" id="modal-batalkan" onclick="closeModalOutside(event,'modal-batalkan')">
        <div class="modal">
            <h3 class="modal-title">Batalkan <em>Konsultasi?</em></h3>
            <p class="modal-sub">Anda akan membatalkan jadwal berikut:</p>
            <div class="confirm-box" style="margin-bottom:20px">
                <div class="confirm-row"><span>Dokter</span><span>Dr. Anisa Putri</span></div>
                <div class="confirm-row"><span>Tanggal</span><span>Senin, 23 Mei 2026</span></div>
                <div class="confirm-row"><span>Jam</span><span>09:00 WIB</span></div>
                <div class="confirm-row"><span>Treatment</span><span>Facelift Consultation</span></div>
            </div>
            <div style="background:#fef9e7; border-radius:8px; padding:12px 14px; font-size:12px; color:#c9970e; margin-bottom:20px">
                 Pembatalan kurang dari 24 jam sebelum jadwal mungkin dikenakan biaya administrasi.
            </div>
            <div class="form-group">
                <label class="form-label">Alasan Pembatalan</label>
                <select class="form-select">
                    <option>Pilih alasan...</option>
                    <option>Ada halangan mendadak</option>
                    <option>Ingin ganti jadwal</option>
                    <option>Kondisi kesehatan</option>
                    <option>Lainnya</option>
                </select>
            </div>
            <div class="modal-footer">
                <button class="btn-outline" onclick="closeModal('modal-batalkan')">Batal</button>
                <button style="background:#e8716d; color:#fff; border:none; padding:10px 24px; border-radius:50px; font-size:11px; letter-spacing:1.5px; text-transform:uppercase; font-family:'DM Sans',sans-serif; cursor:pointer;" onclick="closeModal('modal-batalkan'); showToast('Jadwal berhasil dibatalkan')">Ya, Batalkan</button>
            </div>
        </div>
    </div>

    <!-- ══ MODAL: DETAIL RIWAYAT ══ -->
    <div class="modal-overlay" id="modal-detail-riwayat" onclick="closeModalOutside(event,'modal-detail-riwayat')">
        <div class="modal">
            <h3 class="modal-title">Detail <em>Kunjungan</em></h3>
            <p class="modal-sub">02 Mei 2026 · Dr. Anisa Putri · Facelift Consultation</p>
            <div class="confirm-box" style="margin-bottom:20px">
                <div class="confirm-row"><span>Dokter</span><span>Dr. Anisa Putri, Sp.BP-RE</span></div>
                <div class="confirm-row"><span>Treatment</span><span>Facelift Consultation</span></div>
                <div class="confirm-row"><span>Durasi</span><span>60 menit</span></div>
                <div class="confirm-row"><span>Ruangan</span><span>Ruang A-1</span></div>
                <div class="confirm-row"><span>Status</span><span><span class="badge badge-green">Selesai</span></span></div>
            </div>
            <div style="background:#fdf0f5; border-radius:10px; padding:16px; margin-bottom:20px">
                <div style="font-size:9px; letter-spacing:2px; text-transform:uppercase; color:#b89098; margin-bottom:8px">Catatan Dokter</div>
                <div style="font-size:13px; color:#7a4d5c; font-weight:300; line-height:1.8">
                    Konsultasi mengenai rencana mini facelift. Direncanakan prosedur SMAS technique untuk mengencangkan area mid-face dan cervical. Follow-up pre-operasi dijadwalkan 23 Mei 2026.
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-outline" onclick="closeModal('modal-detail-riwayat')">Tutup</button>
                <button class="btn-primary" onclick="closeModal('modal-detail-riwayat'); openModal('modal-ulasan')"> Beri Ulasan</button>
            </div>
        </div>
    </div>

    <!-- ══ MODAL: ULASAN ══ -->
    <div class="modal-overlay" id="modal-ulasan" onclick="closeModalOutside(event,'modal-ulasan')">
        <div class="modal">
            <h3 class="modal-title">Beri <em>Ulasan</em></h3>
            <p class="modal-sub">Kunjungan 02 Mei 2026 · Dr. Anisa Putri</p>
            <div style="text-align:center; margin-bottom:24px">
                <div style="font-size:13px; color:#7a4d5c; font-weight:300; margin-bottom:14px">Bagaimana pengalaman konsultasi Anda?</div>
                <div style="font-size:36px; letter-spacing:8px; cursor:pointer" id="star-rating"></div>
                <div style="font-size:11px; color:#c55085; margin-top:6px; letter-spacing:1px">5 / 5 — Sangat Puas</div>
            </div>
            <div class="form-group">
                <label class="form-label">Ulasan Anda</label>
                <textarea class="form-textarea" placeholder="Ceritakan pengalaman konsultasi Anda...">Dr. Anisa sangat profesional dan detail dalam menjelaskan rencana treatment. Saya merasa sangat nyaman berkonsultasi. Sangat direkomendasikan!</textarea>
            </div>
            <div class="modal-footer">
                <button class="btn-outline" onclick="closeModal('modal-ulasan')">Batal</button>
                <button class="btn-primary" onclick="closeModal('modal-ulasan'); showToast('Ulasan berhasil dikirim ✓ Terima kasih!')">Kirim Ulasan</button>
            </div>
        </div>
    </div>

    <!-- ══ MODAL: SUKSES PENDAFTARAN ══ -->
    <div class="modal-overlay" id="modal-sukses">
        <div class="modal">
            <div class="success-card">
                <div class="success-icon"></div>
                <div class="success-title">Pendaftaran Berhasil!</div>
                <div class="success-desc">Jadwal konsultasi Anda telah berhasil didaftarkan. Konfirmasi dan pengingat akan segera dikirim ke email Anda.</div>
                <div class="confirm-box">
                    <div class="confirm-row"><span>Dokter</span><span>Dr. Anisa Putri</span></div>
                    <div class="confirm-row"><span>Tanggal</span><span>Senin, 23 Mei 2026</span></div>
                    <div class="confirm-row"><span>Jam</span><span>09:00 WIB</span></div>
                    <div class="confirm-row"><span>No. Antrian</span><span style="color:#c55085; font-weight:500">#A-004</span></div>
                </div>
                <button class="btn-primary" style="width:100%" onclick="closeModal('modal-sukses'); showPage('beranda', document.querySelector('[onclick*=beranda]'))">Kembali ke Beranda</button>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div class="toast" id="toast"><span id="toast-msg"></span></div>

    <script src="../../asset/js/user.js"></script>
</body>
</html>