<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../pages/auth/Signin.php');
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$conn = require_once '../../backend/koneksi.php';

// 1. Ambil Profil Pasien
$qPasien = mysqli_query($conn, "SELECT * FROM pasien WHERE user_id = $user_id LIMIT 1");
if (!$qPasien || mysqli_num_rows($qPasien) === 0) {
    // Buat pasien record jika belum ada
    $noPasien = 'P-' . rand(1000, 9999);
    $noRekam = 'RM-' . rand(1000, 9999);
    $nama = mysqli_real_escape_string($conn, $_SESSION['username']);
    $email = mysqli_real_escape_string($conn, $_SESSION['email']);
    
    // Ambil telepon dari users table jika ada
    $qUserTel = mysqli_query($conn, "SELECT telepon FROM users WHERE id = $user_id LIMIT 1");
    $userTelRow = $qUserTel ? mysqli_fetch_assoc($qUserTel) : null;
    $telepon = mysqli_real_escape_string($conn, $userTelRow['telepon'] ?? '');
    
    mysqli_query($conn, "INSERT INTO pasien (user_id, nama, no_pasien, no_rekam, email, telepon, no_telp, status) VALUES ($user_id, '$nama', '$noPasien', '$noRekam', '$email', '$telepon', '$telepon', 'Aktif')");
    $qPasien = mysqli_query($conn, "SELECT * FROM pasien WHERE user_id = $user_id LIMIT 1");
}
$pasien = mysqli_fetch_assoc($qPasien);
$pasien_id = (int)$pasien['id'];

// 2. Statistik
$total_kunjungan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM rekam_medis WHERE pasien_id = $pasien_id"))['n'];
$jadwal_mendatang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM appointment WHERE pasien_id = $pasien_id AND status = 'Terjadwal' AND tanggal >= CURDATE()"))['n'];

// Cek pengumuman baru dalam 7 hari terakhir
$qRecentPengumuman = mysqli_query($conn, "SELECT COUNT(*) AS n FROM pengumuman WHERE target IN ('Semua', 'Pasien') AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$recent_pengumuman = $qRecentPengumuman ? mysqli_fetch_assoc($qRecentPengumuman)['n'] : 0;
$notif_baru = ($jadwal_mendatang > 0 || $recent_pengumuman > 0) ? 1 : 0;

// Query pengumuman untuk ditampilkan
$qPengumuman = mysqli_query($conn, "SELECT * FROM pengumuman WHERE target IN ('Semua', 'Pasien') ORDER BY created_at DESC LIMIT 20");
$pengumuman_list = [];
if ($qPengumuman) {
    while ($p = mysqli_fetch_assoc($qPengumuman)) {
        $pengumuman_list[] = $p;
    }
}

// 3. Jadwal Mendatang Pertama
$qNextAppt = mysqli_query($conn, "
    SELECT a.*, d.nama AS nama_dokter, d.spesialisasi, t.nama AS nama_treatment 
    FROM appointment a 
    JOIN dokter d ON a.dokter_id = d.id 
    LEFT JOIN treatment t ON a.treatment_id = t.id 
    WHERE a.pasien_id = $pasien_id AND a.tanggal >= CURDATE() AND a.status = 'Terjadwal' 
    ORDER BY a.tanggal ASC, a.jam ASC 
    LIMIT 1
");
$nextAppt = $qNextAppt ? mysqli_fetch_assoc($qNextAppt) : null;

// 4. Dokter List
$qDokter = mysqli_query($conn, "SELECT * FROM dokter WHERE status='Aktif' ORDER BY nama ASC");
$dokter_list = [];
while ($d = mysqli_fetch_assoc($qDokter)) {
    if (!empty($d['foto'])) {
        if (strpos($d['foto'], 'http') !== 0) {
            if (strpos($d['foto'], 'asset/') === 0) {
                $d['foto'] = '../../' . $d['foto'];
            } else {
                $d['foto'] = '../../backend/uploads/' . $d['foto'];
            }
        }
    } else {
        $d['foto'] = 'https://ui-avatars.com/api/?name=' . urlencode($d['nama']) . '&background=064e3b&color=fff&size=500';
    }
    $dokter_list[] = $d;
}

// Fetch Doctor Schedules for the table
$qJadwal = mysqli_query($conn, "SELECT * FROM jadwal_dokter WHERE status='Aktif'");
$jadwal_list = [];
if ($qJadwal) {
    while ($j = mysqli_fetch_assoc($qJadwal)) {
        $jadwal_list[$j['dokter_id']][$j['hari']] = substr($j['jam_mulai'], 0, 5) . ' - ' . substr($j['jam_selesai'], 0, 5);
    }
}

// 5. Treatment List untuk Dropdown Booking
$qTreatment = mysqli_query($conn, "SELECT * FROM treatment WHERE status='Aktif' ORDER BY urutan ASC");
$treatment_list = [];
while ($t = mysqli_fetch_assoc($qTreatment)) {
    $treatment_list[] = $t;
}

// 6. Riwayat Kunjungan Pasien
$qRiwayat = mysqli_query($conn, "
    SELECT a.id, a.tanggal, a.jam, a.status, d.nama AS nama_dokter, d.spesialisasi, t.nama AS nama_treatment, rm.anamnesis, rm.pemeriksaan, rm.tindak_lanjut, rm.ruangan
    FROM appointment a
    JOIN dokter d ON a.dokter_id = d.id
    LEFT JOIN treatment t ON a.treatment_id = t.id
    LEFT JOIN rekam_medis rm ON rm.pasien_id = a.pasien_id AND rm.dokter_id = a.dokter_id AND rm.tanggal = a.tanggal
    WHERE a.pasien_id = $pasien_id
    ORDER BY a.tanggal DESC, a.jam DESC
");
$riwayat_list = [];
while ($r = mysqli_fetch_assoc($qRiwayat)) {
    $riwayat_list[] = $r;
}

$initial = strtoupper(substr($pasien['nama'], 0, 1));
$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GlowCare — Dashboard Pasien</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@300;400;500&display=swap">
    <link rel="stylesheet" href="../../asset/css/user.css?v=11">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
            background: #f7f6f3;
        }

        /* ══ SIDEBAR ══ */
        .sidebar {
            width: 260px;
            background: #4a321f;
            border-right: 1px solid #3d2716;
            min-height: 100vh;
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            z-index: 100;
            box-shadow: 2px 0 16px rgba(0,0,0,0.06);
        }

        .sidebar-logo {
            padding: 28px 28px 22px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .sidebar-logo .brand {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-style: italic;
            color: #ffffff;
            letter-spacing: 1px;
        }

        .sidebar-logo .role {
            font-size: 9px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #a89a8a;
            margin-top: 4px;
        }

        /* Patient profile mini card in sidebar */
        .sidebar-patient {
            padding: 18px 28px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-pat-avatar {
            width: 44px; height: 44px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
        }

        .sidebar-pat-avatar .avatar-circle {
            width: 100%; height: 100%;
            background: linear-gradient(135deg, #735a39 0%, #a47e58 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-family: 'Playfair Display', serif;
            color: #fff;
        }

        .sidebar-pat-info .pat-name {
            font-size: 13px;
            color: #ffffff;
            font-weight: 500;
            font-family: 'Playfair Display', serif;
        }

        .sidebar-pat-info .pat-status {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 9px;
            color: #e0c097;
            margin-top: 4px;
        }

        .sidebar-pat-info .pat-status::before {
            content: "";
            width: 6px; height: 6px;
            border-radius: 50%;
            background: #e0c097;
        }

        .sidebar-nav {
            flex: 1;
            padding: 20px 0;
            overflow-y: auto;
        }

        .nav-section-label {
            font-size: 8px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.4);
            padding: 14px 28px 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 28px;
            font-size: 13px;
            color: rgba(255,255,255,0.8);
            cursor: pointer;
            transition: all 0.18s;
            border-left: 3px solid transparent;
            text-decoration: none;
            background: transparent;
            border-top: none;
            border-right: none;
            border-bottom: none;
            width: 100%;
            text-align: left;
            font-family: 'DM Sans', sans-serif;
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.06);
            color: #ffffff;
        }

        .nav-item.active {
            background: rgba(255,255,255,0.12);
            color: #ffffff;
            border-left-color: #e0c097;
            font-weight: 500;
        }

        .nav-badge {
            margin-left: auto;
            background: #e0c097;
            color: #4a321f;
            font-size: 9px;
            padding: 2px 7px;
            border-radius: 20px;
            font-weight: 600;
        }

        .sidebar-footer {
            padding: 16px 28px;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .logout-btn-sidebar {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 12px;
            color: #ffffff;
            background: #e05050;
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            justify-content: center;
            width: 100%;
            border: none;
        }

        .logout-btn-sidebar:hover { background: #ba1a1a; color: #ffffff; }

        /* ══ MAIN ══ */
        .main {
            margin-left: 260px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ══ TOPBAR ══ */
        .topbar {
            background: #4a321f;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            padding: 0 40px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar-title {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            font-weight: 400;
            color: #ffffff;
        }

        .topbar-bc {
            font-size: 11px;
            color: rgba(255,255,255,0.6);
            letter-spacing: 0.5px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .topbar-date {
            font-size: 12px;
            color: #f5f3ee;
            font-weight: 300;
            padding: 6px 16px;
            background: rgba(255,255,255,0.08);
            border-radius: 50px;
            border: 1px solid rgba(255,255,255,0.15);
        }

        .notif-btn {
            width: 38px; height: 38px;
            border-radius: 50%;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            font-size: 16px;
            position: relative;
            transition: background 0.2s;
        }

        .notif-btn:hover { background: rgba(255,255,255,0.15); }

        .notif-dot {
            position: absolute;
            top: 6px; right: 7px;
            width: 7px; height: 7px;
            border-radius: 50%;
            background: #e0c097;
            border: 2px solid #4a321f;
        }

        /* ══ CARD OVERRIDES ══ */
        .card {
            background: #ffffff !important;
            border: 1px solid #efebe4 !important;
            box-shadow: 0 4px 16px rgba(0,0,0,0.04) !important;
        }
        .card:hover {
            box-shadow: 0 8px 24px rgba(0,0,0,0.06) !important;
        }

        /* ══ CONTENT AREA OVERRIDES & ACCESSIBILITY READABILITY ══ */
        .content-area {
            max-width: 100%;
            margin: 0;
            padding: 36px 40px;
        }

        /* High contrast colors for main content area to make text more readable */
        .main {
            color: #2b2621 !important; /* Default dark charcoal-brown */
        }

        .hero-banner {
            background: #faf6ee !important;
            padding: 36px 40px;
            border-bottom: 1px solid #efebe4 !important;
        }
        .hero-greeting {
            color: #5c432d !important; /* Dark warm brown */
            font-weight: 500 !important;
        }
        .hero-name {
            color: #4a321f !important;
        }
        .hero-name em {
            color: #5c432d !important;
        }
        .hero-sub {
            color: #4a3c31 !important; /* Darker charcoal-brown */
        }
        .hero-stat-val {
            color: #4a321f !important;
        }
        .hero-stat-lbl {
            color: #7a5e41 !important; /* Darker gold-brown */
            font-weight: 500 !important;
        }
        .hero-divider-v {
            background-color: #efebe4 !important;
        }

        /* Make small labels, ratings, and subtexts in content area darker for better readability */
        .main p, 
        .main .card-title,
        .main .section-title,
        .main .dokter-name,
        .main .pat-name,
        .main .notif-title,
        .main .td-name,
        .main .cal-month,
        .main .akun-section-title,
        .main .modal-title {
            color: #2b2621 !important;
        }

        .main .card-title em,
        .main .section-title em,
        .main .modal-title em {
            color: #5c432d !important;
            -webkit-text-fill-color: initial !important;
            background: none !important;
        }

        .main .form-label,
        .main .notif-desc,
        .main .notif-time,
        .main .td-sub,
        .main .dokter-spec,
        .main .dokter-rating,
        .main .section-sub,
        .main .step-label,
        .main .confirm-row span:first-child,
        .main .modal-sub,
        .main .success-desc {
            color: #4a3e36 !important;
            font-weight: 400 !important;
        }

        .main .data-table thead th {
            color: #4a3e36 !important;
            font-weight: 600 !important;
        }
        .main .data-table tbody td {
            color: #2b2621 !important;
        }

        .main .step-num {
            border-color: #9c8a7c !important;
            color: #4a3e36 !important;
        }
        .main .step.active .step-num {
            background: #735a39 !important;
            border-color: #735a39 !important;
            color: #ffffff !important;
        }

        /* Systematically target inline styles in the main content area that force low contrast */
        .main [style*="color:#7d6756"],
        .main [style*="color: #7d6756"] {
            color: #4a3e36 !important;
        }
        .main [style*="color:#735a39"],
        .main [style*="color: #735a39"] {
            color: #5c432d !important;
        }
        .main [style*="color:#a47e58"],
        .main [style*="color: #a47e58"] {
            color: #735a39 !important;
        }
        .main [style*="color:#9a8f87"],
        .main [style*="color: #9a8f87"] {
            color: #5c524a !important;
        }
        .main [style*="color:#b5a695"],
        .main [style*="color: #b5a695"] {
            color: #736455 !important;
        }
        .main [style*="color:#64748b"],
        .main [style*="color: #64748b"] {
            color: #475569 !important;
        }

        /* slot button */
        .slot-btn {
            padding: 10px 14px;
            border: 1px solid #c4a882;
            border-radius: 8px;
            text-align: center;
            background: #fff5f0;
            color: #735a39;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
        }
        .slot-btn:hover {
            background: #f7ede5;
        }
        .slot-btn.selected {
            background: #735a39;
            color: #fff;
            border-color: #735a39;
        }
        .slot-btn.full {
            background: #f7ede5;
            color: #a89a8a;
            border-color: #f7ede5;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <!-- ══ SIDEBAR ══ -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="brand">GlowCare Clinic</div>
            <div class="role">Portal Pasien</div>
        </div>

        <div class="sidebar-patient">
            <div class="sidebar-pat-avatar">
                <div class="avatar-circle"><?= $initial ?></div>
            </div>
            <div class="sidebar-pat-info">
                <div class="pat-name"><?= htmlspecialchars($pasien['nama']) ?></div>
                <div class="pat-status">Pasien Aktif</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-label">Menu Utama</div>
            <button class="nav-item active" onclick="showPage('beranda', this)">
                Beranda
            </button>
            <button class="nav-item" onclick="showPage('jadwal-dokter', this)">
                Jadwal Dokter
            </button>
            <button class="nav-item" onclick="showPage('daftar-konsul', this)">
                Daftar Pendaftaran
            </button>
            <button class="nav-item" onclick="showPage('riwayat', this)">
                Riwayat Medis
            </button>

            <div class="nav-section-label" style="margin-top:8px">Pusat Pesan</div>
            <a class="nav-item" href="chat.php">
                Chat Dokter
            </a>
            <div class="nav-section-label" style="margin-top:8px">Akun</div>
            <button class="nav-item" onclick="showPage('akun', this)">
                Profil Saya
            </button>
        </nav>
    </aside>

    <!-- ══ MAIN CONTAINER ══ -->
    <div class="main">
        <!-- TOPBAR -->
        <div class="topbar" style="justify-content: flex-end;">
            <div class="topbar-right">
                <button class="notif-btn-text" style="position: relative; border: 1px solid rgba(255,255,255,0.35); border-radius: 20px; padding: 6px 16px; background: none; font-family: 'DM Sans', sans-serif; font-size: 11px; cursor: pointer; color: #ffffff; font-weight: 500; display: inline-flex; align-items: center; gap: 4px;" onclick="showPage('notifikasi', this)">
                    Notifikasi
                    <?php if ($notif_baru > 0): ?>
                        <span class="notif-dot" style="display: inline-block; width: 6px; height: 6px; background: #e0c097; border-radius: 50%;"></span>
                    <?php endif; ?>
                </button>
                <button class="logout-btn-sidebar" style="width: auto; padding: 6px 16px;" onclick="showLogoutModal(); return false;">
                    Logout
                </button>
            </div>
        </div>


    <!-- ALERT SUCCESS/ERROR -->
    <?php if (($_GET['page'] ?? '') !== 'akun'): ?>
        <?php if ($error): ?>
            <div class="alert-notification alert-danger" style="margin: 20px 48px -10px; padding: 12px 20px; background: #fce4df; border: 1px solid #e8b5ac; border-radius: 8px; color: #6b3a2d; font-size: 13px; transition: opacity 0.5s ease;">
                Peringatan: <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert-notification alert-success" style="margin: 20px 48px -10px; padding: 12px 20px; background: #e8f5d8; border: 1px solid #c5daa8; border-radius: 8px; color: #5a6f2d; font-size: 13px; transition: opacity 0.5s ease;">
                Sukses: <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- ══════════════════════════════════════════
        PAGE: BERANDA
    ══════════════════════════════════════════ -->
    <div class="page active" id="page-beranda">

        <!-- Hero banner -->
        <div class="hero-banner">
            <div class="hero-greeting"><?= ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'][date('w')] ?>, <?= date('d M Y') ?></div>
            <div class="hero-name">Halo, <em><?= htmlspecialchars($pasien['nama']) ?></em></div>
            <div class="hero-sub">Selamat datang kembali di GlowCare Clinic. Yuk, jaga kecantikan alami Anda.</div>
            <div class="hero-stats">
                <div class="hero-stat-item">
                    <div class="hero-stat-val"><?= $total_kunjungan ?></div>
                    <div class="hero-stat-lbl">Total Kunjungan</div>
                </div>
                <div class="hero-divider-v"></div>
                <div class="hero-stat-item">
                    <div class="hero-stat-val"><?= $jadwal_mendatang ?></div>
                    <div class="hero-stat-lbl">Jadwal Mendatang</div>
                </div>
                <div class="hero-divider-v"></div>
                <div class="hero-stat-item">
                    <div class="hero-stat-val"><?= $notif_baru ?></div>
                    <div class="hero-stat-lbl">Pemberitahuan</div>
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

                        <div style="padding: 0 26px 26px">
                            <?php if ($nextAppt): ?>
                                <div style="background:linear-gradient(135deg,#594323,#a47e58); border-radius:12px; padding:22px; color:#fff; position:relative; overflow:hidden">
                                    <div style="font-size:9px; letter-spacing:3px; text-transform:uppercase; color:rgba(255,255,255,0.5); margin-bottom:10px">JADWAL BERIKUTNYA</div>
                                    <div style="font-family:'Playfair Display',serif; font-size:20px; margin-bottom:4px"><?= htmlspecialchars($nextAppt['nama_dokter']) ?></div>
                                    <div style="font-size:10px; letter-spacing:1.5px; text-transform:uppercase; color:#d8b39b; margin-bottom:14px"><?= htmlspecialchars($nextAppt['nama_treatment'] ?: 'Konsultasi') ?></div>
                                    <div style="display:flex; gap:20px">
                                        <div>
                                            <div style="font-size:9px; color:rgba(255,255,255,0.45); letter-spacing:1px; text-transform:uppercase; margin-bottom:2px">Tanggal</div>
                                            <div style="font-size:13px"><?= date('d M Y', strtotime($nextAppt['tanggal'])) ?></div>
                                        </div>
                                        <div>
                                            <div style="font-size:9px; color:rgba(255,255,255,0.45); letter-spacing:1px; text-transform:uppercase; margin-bottom:2px">Jam</div>
                                            <div style="font-size:13px"><?= substr($nextAppt['jam'], 0, 5) ?> WIB</div>
                                        </div>
                                        <div>
                                            <div style="font-size:9px; color:rgba(255,255,255,0.45); letter-spacing:1px; text-transform:uppercase; margin-bottom:2px">Ruangan</div>
                                            <div style="font-size:13px">Ruang A-1</div>
                                        </div>
                                    </div>
                                    <div style="margin-top:16px; display:flex; gap:8px">
                                        <button onclick="window.location.href='chat.php?appt_id=<?= $nextAppt['id'] ?>'" style="background:#fff; color:#735a39; border:none; padding:7px 16px; border-radius:50px; font-size:10px; font-weight:600; letter-spacing:1px; text-transform:uppercase; font-family:'DM Sans',sans-serif; cursor:pointer; box-shadow:0 4px 10px rgba(0,0,0,0.1)">Chat Dokter</button>
                                        <button onclick="openModalBatal(<?= $nextAppt['id'] ?>, '<?= htmlspecialchars(addslashes($nextAppt['nama_dokter'])) ?>', '<?= $nextAppt['tanggal'] ?>', '<?= substr($nextAppt['jam'], 0, 5) ?>', '<?= htmlspecialchars(addslashes($nextAppt['nama_treatment'])) ?>')" style="background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.25); color:#fff; padding:7px 16px; border-radius:50px; font-size:10px; letter-spacing:1px; text-transform:uppercase; font-family:'DM Sans',sans-serif; cursor:pointer;">Batalkan</button>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div style="padding:24px; text-align:center; color:#7d6756; font-size:13px; background:#f7ede5; border-radius:8px">
                                    Tidak ada jadwal konsultasi mendatang.<br>
                                    <button class="btn-primary" style="margin-top:12px; font-size:11px; padding:6px 14px;" onclick="showPage('daftar-konsul', document.querySelector('[onclick*=daftar-konsul]'))">Booking Online Sekarang</button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Rekomendasi dokter -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Dokter <em>Kecantikan</em></div>
                            <button class="card-action" onclick="showPage('jadwal-dokter', document.querySelector('[onclick*=jadwal-dokter]'))">Semua →</button>
                        </div>
                        <div style="padding:0 26px 26px; display:flex; flex-direction:column; gap:12px">
                            <?php foreach (array_slice($dokter_list, 0, 2) as $d): ?>
                                <div class="dokter-card">
                                    <div class="dokter-avatar">
                                        <img src="<?= htmlspecialchars($d['foto']) ?>">
                                    </div>
                                    <div style="flex:1">
                                        <div class="dokter-name"><?= htmlspecialchars($d['nama']) ?></div>
                                        <div class="dokter-spec"><?= htmlspecialchars($d['spesialisasi']) ?></div>
                                        <div class="dokter-rating">★ <?= number_format($d['rating'], 1) ?> · <?= htmlspecialchars($d['bio']) ?></div>
                                    </div>
                                    <button class="btn-primary btn-sm" onclick="startBookingDokter(<?= $d['id'] ?>)">Daftar</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Notif ringkas + Quick CTA -->
                <div style="display:flex; flex-direction:column; gap:20px">

                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Notifikasi <em>Terbaru</em></div>
                        </div>
                        <div>
                            <?php if ($nextAppt): ?>
                                <div class="notif-item unread">
                                    <div class="notif-icon pink">!</div>
                                    <div>
                                        <div class="notif-title">Pengingat Jadwal</div>
                                        <div class="notif-desc">Konsultasi Anda dengan <?= htmlspecialchars($nextAppt['nama_dokter']) ?> terjadwal pada <?= date('d M Y', strtotime($nextAppt['tanggal'])) ?> pukul <?= substr($nextAppt['jam'], 0, 5) ?> WIB.</div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="notif-item">
                                <div class="notif-icon green">✓</div>
                                <div>
                                    <div class="notif-title">Akun Aktif</div>
                                    <div class="notif-desc">Selamat! Akun GlowCare Clinic Anda telah aktif sepenuhnya.</div>
                                </div>
                            </div>
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
            <div class="hero-sub">Lihat ketersediaan dokter dan lakukan konsultasi sesuai kebutuhan kulit Anda.</div>
        </div>

        <div class="content-area">
            <div class="card" style="overflow-x: auto; border-radius: 16px; border: 1px solid #efebe4;">
                <table class="data-table" style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#faf6ee;">
                            <th style="padding: 16px 20px; font-family:'Playfair Display', serif; font-weight:600; color:#4a321f; font-size:14px; text-align:left; border-bottom:1px solid #efebe4;">Dokter</th>
                            <th style="padding: 16px 20px; font-weight:500; color:#735a39; font-size:12px; text-align:center; border-bottom:1px solid #efebe4;">Senin</th>
                            <th style="padding: 16px 20px; font-weight:500; color:#735a39; font-size:12px; text-align:center; border-bottom:1px solid #efebe4;">Selasa</th>
                            <th style="padding: 16px 20px; font-weight:500; color:#735a39; font-size:12px; text-align:center; border-bottom:1px solid #efebe4;">Rabu</th>
                            <th style="padding: 16px 20px; font-weight:500; color:#735a39; font-size:12px; text-align:center; border-bottom:1px solid #efebe4;">Kamis</th>
                            <th style="padding: 16px 20px; font-weight:500; color:#735a39; font-size:12px; text-align:center; border-bottom:1px solid #efebe4;">Jumat</th>
                            <th style="padding: 16px 20px; font-weight:500; color:#735a39; font-size:12px; text-align:center; border-bottom:1px solid #efebe4;">Sabtu</th>
                            <th style="padding: 16px 20px; font-weight:500; color:#735a39; font-size:12px; text-align:center; border-bottom:1px solid #efebe4;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dokter_list as $d): ?>
                            <tr style="border-bottom:1px solid #efebe4;">
                                <td style="padding: 18px 20px; display:flex; align-items:center; gap:14px; text-align:left;">
                                    <div class="dokter-avatar" style="width:48px; height:48px; border-radius:50%; overflow:hidden; flex-shrink:0;">
                                        <img src="<?= htmlspecialchars($d['foto']) ?>" style="width:100%; height:100%; object-fit:cover;">
                                    </div>
                                    <div>
                                        <div style="font-weight:500; color:#4a321f; font-size:14px;"><?= htmlspecialchars($d['nama']) ?></div>
                                        <div style="font-size:11px; color:#735a39; margin-top:2px;"><?= htmlspecialchars($d['spesialisasi']) ?></div>
                                        <div style="font-size:10px; color:#9a8f87; margin-top:2px;">★ <?= number_format($d['rating'], 1) ?> · <?= htmlspecialchars($d['pengalaman']) ?>+ Thn</div>
                                    </div>
                                </td>
                                <td style="padding: 18px 20px; font-size:12px; color:#4a321f; text-align:center;"><?= $jadwal_list[$d['id']]['Senin'] ?? '<span style="color:#b5a695; font-style:italic;">Libur</span>' ?></td>
                                <td style="padding: 18px 20px; font-size:12px; color:#4a321f; text-align:center;"><?= $jadwal_list[$d['id']]['Selasa'] ?? '<span style="color:#b5a695; font-style:italic;">Libur</span>' ?></td>
                                <td style="padding: 18px 20px; font-size:12px; color:#4a321f; text-align:center;"><?= $jadwal_list[$d['id']]['Rabu'] ?? '<span style="color:#b5a695; font-style:italic;">Libur</span>' ?></td>
                                <td style="padding: 18px 20px; font-size:12px; color:#4a321f; text-align:center;"><?= $jadwal_list[$d['id']]['Kamis'] ?? '<span style="color:#b5a695; font-style:italic;">Libur</span>' ?></td>
                                <td style="padding: 18px 20px; font-size:12px; color:#4a321f; text-align:center;"><?= $jadwal_list[$d['id']]['Jumat'] ?? '<span style="color:#b5a695; font-style:italic;">Libur</span>' ?></td>
                                <td style="padding: 18px 20px; font-size:12px; color:#4a321f; text-align:center;"><?= $jadwal_list[$d['id']]['Sabtu'] ?? '<span style="color:#b5a695; font-style:italic;">Libur</span>' ?></td>
                                <td style="padding: 18px 20px; text-align:center;">
                                    <button class="btn-primary" style="padding: 8px 16px; font-size: 11px; border-radius: 50px; cursor:pointer;" onclick="startBookingDokter(<?= $d['id'] ?>)">Pilih & Daftar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
            <div class="card" style="padding: 20px 32px; margin-bottom: 24px; border-radius: 16px;">
                <div class="steps" id="step-indicator" style="margin-bottom: 0;">
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
            </div>

            <!-- Booking Form Wrapper -->
            <form method="POST" action="../../backend/user/simpan_booking.php" id="booking-form">
                <input type="hidden" name="dokter_id" id="booking-dokter-id" value="<?= $dokter_list[0]['id'] ?? 0 ?>">
                <input type="hidden" name="tanggal" id="booking-tanggal" value="<?= date('Y-m-d') ?>">
                <input type="hidden" name="jam" id="booking-jam" value="09:00">

                <!-- Step content wrapper -->
                <div id="step-content">

                    <!-- STEP 1: Pilih Dokter -->
                    <div id="step-panel-1">
                        <div class="card" style="margin-bottom:20px">
                            <div class="card-header"><div class="card-title">Pilih <em>Dokter</em></div></div>
                            <div style="padding:0 26px 26px; display:flex; flex-direction:column; gap:12px">
                                <?php 
                                $idx = 0;
                                foreach ($dokter_list as $d): 
                                    $idx++;
                                    $border = $idx === 1 ? 'border:2px solid #735a39' : '';
                                    $badge = $idx === 1 ? 'badge-green' : 'badge-gray';
                                    $badgeTxt = $idx === 1 ? 'Terpilih ✓' : 'Pilih';
                                ?>
                                    <div class="dokter-card" id="dokter-option-<?= $d['id'] ?>" onclick="selectDokterCard(<?= $d['id'] ?>, '<?= htmlspecialchars(addslashes($d['nama'])) ?>')" style="<?= $border ?>">
                                        <div class="dokter-avatar" style="width:52px;height:52px">
                                            <img src="<?= htmlspecialchars($d['foto']) ?>">
                                        </div>
                                        <div style="flex:1">
                                            <div class="dokter-name"><?= htmlspecialchars($d['nama']) ?></div>
                                            <div class="dokter-spec"><?= htmlspecialchars($d['spesialisasi']) ?></div>
                                            <div class="dokter-rating">★ <?= number_format($d['rating'], 1) ?> · <?= htmlspecialchars($d['bio']) ?></div>
                                        </div>
                                        <span class="badge <?= $badge ?>"><?= $badgeTxt ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div style="display:flex; justify-content:flex-end">
                            <button type="button" class="btn-primary" onclick="nextStep(2)">Lanjut: Pilih Jadwal →</button>
                        </div>
                    </div>

                    <!-- STEP 2: Pilih Jadwal -->
                    <div id="step-panel-2" style="display:none">
                        <div class="two-col" style="margin-bottom:20px">
                            <div class="card">
                                <div class="card-header"><div class="card-title">Pilih <em>Tanggal</em></div></div>
                                <div style="padding:26px">
                                    <label class="form-label" style="margin-bottom:8px; display:block">Tanggal Konsultasi</label>
                                    <input class="form-input" type="date" id="booking-date-picker" min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>" style="background:#fff; font-size:14px;" onchange="updateTanggalBooking(this.value)">
                                    <p style="font-size:11px; color:#7d6756; margin-top:10px">Silakan pilih tanggal kerja klinik (Senin s.d Sabtu).</p>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header"><div class="card-title">Pilih <em>Jam</em></div></div>
                                <div class="slot-section">
                                    <div class="slot-date-title" id="slot-preview-header">Hari ini · Dokter Terpilih</div>
                                    <div class="slot-grid">
                                        <div class="slot-btn selected" onclick="selectSlotBtn(this, '09:00')">09:00</div>
                                        <div class="slot-btn" onclick="selectSlotBtn(this, '10:00')">10:00</div>
                                        <div class="slot-btn" onclick="selectSlotBtn(this, '11:00')">11:00</div>
                                        <div class="slot-btn" onclick="selectSlotBtn(this, '13:00')">13:00</div>
                                        <div class="slot-btn" onclick="selectSlotBtn(this, '14:00')">14:00</div>
                                        <div class="slot-btn" onclick="selectSlotBtn(this, '15:00')">15:00</div>
                                        <div class="slot-btn" onclick="selectSlotBtn(this, '16:00')">16:00</div>
                                    </div>
                                    <div style="margin-top:16px; background:#f7ede5; border-radius:8px; padding:12px 14px; font-size:12px; color:#735a39; font-weight:300; line-height:1.7">
                                         Janji temu berdurasi 45-60 menit di <strong style="color:#4a321f">GlowCare Clinic Mataram</strong>.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="display:flex; justify-content:space-between">
                            <button type="button" class="btn-outline" onclick="nextStep(1)">← Kembali</button>
                            <button type="button" class="btn-primary" onclick="nextStep(3)">Lanjut: Isi Keluhan →</button>
                        </div>
                    </div>

                    <!-- STEP 3: Isi Keluhan -->
                    <div id="step-panel-3" style="display:none">
                        <div class="card" style="margin-bottom:20px">
                            <div class="card-header"><div class="card-title">Isi <em>Keluhan & Layanan</em></div></div>
                            <div style="padding:0 26px 26px">
                                <div class="form-row-2">
                                    <div class="form-group">
                                        <label class="form-label">Layanan / Treatment yang Diinginkan</label>
                                        <select class="form-select" name="treatment_id" id="booking-treatment-select" onchange="updateTreatmentPreview(this)">
                                            <option value="">Konsultasi Estetika Umum</option>
                                            <?php foreach ($treatment_list as $t): ?>
                                                <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nama']) ?> (<?= htmlspecialchars($t['kategori']) ?>)</option>
                                            <?php endforeach; ?>
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
                                    <label class="form-label">Keluhan Utama & Keinginan</label>
                                    <textarea class="form-textarea" name="keluhan" placeholder="Ceritakan keluhan kulit atau bagian wajah yang ingin dikonsultasikan secara singkat..." required></textarea>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Riwayat Alergi / Kondisi Khusus</label>
                                    <input class="form-input" name="alergi" placeholder="Contoh: alergi penisilin, riwayat keloid, dsb. (kosongkan jika tidak ada)" value="Tidak ada" required>
                                </div>
                            </div>
                        </div>
                        <div style="display:flex; justify-content:space-between">
                            <button type="button" class="btn-outline" onclick="nextStep(2)">← Kembali</button>
                            <button type="button" class="btn-primary" onclick="validateStep3AndNext()">Lanjut: Konfirmasi →</button>
                        </div>
                    </div>

                    <!-- STEP 4: Konfirmasi -->
                    <div id="step-panel-4" style="display:none">
                        <div class="card" style="margin-bottom:20px">
                            <div class="card-header"><div class="card-title">Konfirmasi <em>Pendaftaran</em></div></div>
                            <div style="padding:0 26px 26px">
                                <div class="confirm-box">
                                    <div class="confirm-row"><span>Pasien</span><span><?= htmlspecialchars($pasien['nama']) ?> (#<?= htmlspecialchars($pasien['no_pasien']) ?>)</span></div>
                                    <div class="confirm-row"><span>Dokter</span><span id="sum-dokter">dr. Anisa Putri</span></div>
                                    <div class="confirm-row"><span>Treatment</span><span id="sum-treatment">Konsultasi Estetika Umum</span></div>
                                    <div class="confirm-row"><span>Tanggal</span><span id="sum-tanggal"><?= date('Y-m-d') ?></span></div>
                                    <div class="confirm-row"><span>Jam</span><span id="sum-jam">09:00 WIB</span></div>
                                    <div class="confirm-row"><span>Ruangan</span><span>Ruang A-1</span></div>
                                </div>
                                <div style="background:#f5e7dc; border-radius:8px; padding:14px 16px; font-size:12px; color:#735a39; line-height:1.7; margin-bottom:20px">
                                    Jadwal tersedia. Setelah klik <strong>Konfirmasi Pendaftaran</strong>, sistem akan menyimpan janji temu Anda.
                                </div>
                                <label style="display:flex; align-items:flex-start; gap:10px; font-size:12px; color:#735a39; font-weight:300; cursor:pointer; margin-bottom:8px">
                                    <input type="checkbox" checked required style="margin-top:2px"> Saya menyetujui syarat &amp; ketentuan layanan GlowCare Clinic
                                </label>
                            </div>
                        </div>
                        <div style="display:flex; justify-content:space-between">
                            <button type="button" class="btn-outline" onclick="nextStep(3)">← Kembali</button>
                            <button type="submit" class="btn-primary">✓ Konfirmasi Pendaftaran</button>
                        </div>
                    </div>

                </div>
            </form>
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
            <div class="card">
                <table class="data-table">
                    <thead>
                        <tr><th>Tanggal</th><th>Dokter</th><th>Treatment</th><th>Ruangan</th><th>Status</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($riwayat_list)): ?>
                            <tr><td colspan="6" style="text-align:center; color:#7d6756; padding:24px">Belum ada riwayat kunjungan.</td></tr>
                        <?php else: ?>
                            <?php foreach ($riwayat_list as $r): ?>
                                <tr>
                                    <td>
                                        <div style="font-size:13px; font-weight:400"><?= date('d M Y', strtotime($r['tanggal'])) ?></div>
                                        <div class="td-sub"><?= substr($r['jam'], 0, 5) ?> WIB</div>
                                    </td>
                                    <td>
                                        <span class="td-name"><?= htmlspecialchars($r['nama_dokter']) ?></span>
                                        <span class="td-sub"><?= htmlspecialchars($r['spesialisasi']) ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($r['nama_treatment'] ?: 'Konsultasi Umum') ?></td>
                                    <td><?= htmlspecialchars($r['ruangan'] ?: 'Ruang A-1') ?></td>
                                    <td>
                                        <span class="badge <?= $r['status'] === 'Selesai' ? 'badge-green' : ($r['status'] === 'Dibatalkan' ? 'badge-gray' : 'badge-yellow') ?>">
                                            <?= htmlspecialchars($r['status']) ?>
                                        </span>
                                    </td>
                                    <td style="display: flex; gap: 4px;">
                                        <button class="btn-outline btn-sm" onclick="openModalDetailRM('<?= date('d M Y', strtotime($r['tanggal'])) ?>', '<?= htmlspecialchars(addslashes($r['nama_dokter'])) ?>', '<?= htmlspecialchars(addslashes($r['nama_treatment'] ?: 'Konsultasi')) ?>', '<?= htmlspecialchars(addslashes($r['ruangan'] ?: 'Ruang A-1')) ?>', '<?= htmlspecialchars(addslashes($r['anamnesis'] ?: 'Tidak ada keluhan tertulis.')) ?>', '<?= htmlspecialchars(addslashes($r['pemeriksaan'] ?: 'Hasil pemeriksaan atau resep obat belum diinput oleh dokter.')) ?>', '<?= htmlspecialchars(addslashes($r['tindak_lanjut'] ?: 'Tidak ada instruksi khusus.')) ?>', <?= $r['status'] === 'Selesai' ? 'true' : 'false' ?>, <?= (int)$r['id'] ?>)">Detail</button>
                                        <?php if ($r['status'] !== 'Dibatalkan'): ?>
                                            <button class="btn-primary btn-sm" onclick="window.location.href='chat.php?appt_id=<?= (int)$r['id'] ?>'" style="padding: 6px 12px; font-size: 11px;">Chat</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
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
            <div class="card" style="padding: 24px;">
                <!-- Announcements -->
                <?php foreach ($pengumuman_list as $p): ?>
                    <div style="padding: 16px; background:#fbf8f3; border:1px solid #efebe4; border-radius:10px; margin-bottom:12px; display:flex; gap:15px; align-items:flex-start;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#735a39; flex-shrink: 0; margin-top: 2px;"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                        <div style="flex: 1;">
                            <div style="font-weight:500; color:#4a321f; font-family: 'Playfair Display', serif; font-size: 15px;"><?= htmlspecialchars($p['judul']) ?></div>
                            <div style="font-size:12px; color:#7d6756; margin-top:4px; line-height: 1.5;"><?= nl2br(htmlspecialchars($p['konten'])) ?></div>
                            <div style="font-size:10px; color:#a89a8a; margin-top:8px;"><?= date('d M Y, H:i', strtotime($p['created_at'])) ?> WIB</div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if ($nextAppt): ?>
                    <div style="padding: 16px; background:#f8ede3; border:1px solid #f7ede5; border-radius:10px; margin-bottom:12px; display:flex; gap:15px; align-items:center;">
                        <span style="font-size:24px; font-weight:bold; color:#e05050;">!</span>
                        <div>
                            <div style="font-weight:500; color:#4a321f;">Pengingat Janji Temu Estetika</div>
                            <div style="font-size:12px; color:#735a39; margin-top:3px;">Anda memiliki konsultasi medis terjadwal dengan <strong><?= htmlspecialchars($nextAppt['nama_dokter']) ?></strong> pada <?= date('d M Y', strtotime($nextAppt['tanggal'])) ?> pukul <?= substr($nextAppt['jam'], 0, 5) ?> WIB.</div>
                        </div>
                    </div>
                <?php endif; ?>
                <div style="padding: 16px; background:#f8ede3; border:1px solid #f7ede5; border-radius:10px; display:flex; gap:15px; align-items:center;">
                    <span style="font-size:24px; font-weight:bold; color:#735a39;">✓</span>
                    <div>
                        <div style="font-weight:500; color:#4a321f;">Selamat Bergabung!</div>
                        <div style="font-size:12px; color:#735a39; margin-top:3px;">Pendaftaran akun Anda di sistem klinik kecantikan GlowCare Clinic Mataram berhasil. Nikmati layanan premium kami!</div>
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
            <div class="hero-greeting">Pengaturan Profil</div>
            <div class="hero-name">Informasi <em>Akun</em></div>
            <div class="hero-sub">Kelola informasi kontak medis Anda agar memudahkan dokter dalam mendiagnosa.</div>
        </div>

        <div class="content-area">
            <?php if (($_GET['page'] ?? '') === 'akun'): ?>
                <?php if ($error): ?>
                    <div class="alert-notification alert-danger" style="margin-bottom: 20px; padding: 12px 20px; background: #fce4df; border: 1px solid #e8b5ac; border-radius: 8px; color: #6b3a2d; font-size: 13px; transition: opacity 0.5s ease;">
                        Peringatan: <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert-notification alert-success" style="margin-bottom: 20px; padding: 12px 20px; background: #e8f5d8; border: 1px solid #c5daa8; border-radius: 8px; color: #5a6f2d; font-size: 13px; transition: opacity 0.5s ease;">
                        Sukses: <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="two-col">

                <!-- Data Pribadi -->
                <div class="akun-section">
                    <div class="akun-section-header">
                        <div class="akun-section-title">Data Diri Medis</div>
                    </div>
                    <form method="POST" action="../../backend/user/update_profil_user.php">
                        <input type="hidden" name="action" value="update_diri">
                        <div class="akun-section-body">
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap</label>
                                <input class="form-input" name="nama" value="<?= htmlspecialchars($pasien['nama']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">No Pasien / Rekam</label>
                                <input class="form-input" value="<?= htmlspecialchars($pasien['no_pasien']) ?> (<?= htmlspecialchars($pasien['no_rekam']) ?>)" disabled style="background:#fdfaf5">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tanggal Lahir</label>
                                <input class="form-input" type="date" name="tanggal_lahir" value="<?= $pasien['tanggal_lahir'] ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Jenis Kelamin</label>
                                <select class="form-select" name="jenis_kelamin">
                                    <option <?= $pasien['jenis_kelamin'] === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                                    <option <?= $pasien['jenis_kelamin'] === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Alamat Lengkap</label>
                                <textarea class="form-textarea" name="alamat" style="min-height:70px" required><?= htmlspecialchars($pasien['alamat'] ?? '') ?></textarea>
                            </div>
                            <button type="submit" class="btn-primary" style="width:100%; margin-top:10px;">Simpan Perubahan Data Diri</button>
                        </div>
                    </form>
                </div>

                <!-- Kontak & Keamanan -->
                <div style="display:flex; flex-direction:column; gap:20px">
                    <div class="akun-section">
                        <div class="akun-section-header">
                            <div class="akun-section-title">Kontak Medis</div>
                        </div>
                        <form method="POST" action="../../backend/user/update_profil_user.php">
                            <input type="hidden" name="action" value="update_kontak">
                            <input type="hidden" name="nama" value="<?= htmlspecialchars($pasien['nama']) ?>">
                            <div class="akun-section-body">
                                <div class="form-group">
                                    <label class="form-label">No. Telepon / WhatsApp</label>
                                    <input class="form-input" name="telepon" value="<?= htmlspecialchars($pasien['telepon'] ?? '') ?>" placeholder="+62 8..." required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Email Aktif</label>
                                    <input class="form-input" type="email" value="<?= htmlspecialchars($pasien['email']) ?>" disabled style="background:#fdfaf5">
                                </div>
                                <button type="submit" class="btn-primary" style="width:100%; margin-top:10px;">Simpan Perubahan Kontak</button>
                            </div>
                        </form>
                    </div>

                    <div class="akun-section">
                        <div class="akun-section-header">
                            <div class="akun-section-title">Keamanan Akun</div>
                        </div>
                        <form method="POST" action="../../backend/user/update_profil_user.php">
                            <div class="akun-section-body">
                                <div class="form-group">
                                    <label class="form-label">Password Saat Ini</label>
                                    <input class="form-input" type="password" name="password_lama" placeholder="••••••••" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Password Baru</label>
                                    <input class="form-input" type="password" name="password_baru" placeholder="Minimal 8 karakter" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Konfirmasi Password Baru</label>
                                    <input class="form-input" type="password" name="konfirmasi" placeholder="Ulangi password baru" required>
                                </div>
                                <button type="submit" class="btn-primary" style="width:100%">Perbarui Password</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ══ MODAL: BATALKAN ══ -->
    <div class="modal-overlay" id="modal-batalkan" onclick="closeModalOutside(event,'modal-batalkan')">
        <div class="modal">
            <h3 class="modal-title">Batalkan <em>Konsultasi?</em></h3>
            <p class="modal-sub">Anda yakin ingin membatalkan janji temu berikut?</p>
            <form method="POST" action="../../backend/user/batal_booking.php">
                <input type="hidden" name="appointment_id" id="batal-appt-id">
                <div class="confirm-box" style="margin-bottom:20px">
                    <div class="confirm-row"><span>Dokter</span><span id="batal-dokter-preview">-</span></div>
                    <div class="confirm-row"><span>Tanggal</span><span id="batal-tanggal-preview">-</span></div>
                    <div class="confirm-row"><span>Jam</span><span id="batal-jam-preview">-</span></div>
                    <div class="confirm-row"><span>Layanan</span><span id="batal-layanan-preview">-</span></div>
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label">Alasan Pembatalan</label>
                    <select class="form-select" name="alasan">
                        <option>Ada halangan mendadak</option>
                        <option>Ingin ganti jadwal tanggal lain</option>
                        <option>Kondisi tubuh kurang fit</option>
                        <option>Lainnya</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-outline" onclick="closeModal('modal-batalkan')">Batal</button>
                    <button type="submit" style="background:#735a39; color:#fff; border:none; padding:10px 24px; border-radius:50px; font-size:11px; letter-spacing:1.5px; text-transform:uppercase; font-family:'DM Sans',sans-serif; cursor:pointer;">Ya, Batalkan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ══ MODAL: DETAIL RIWAYAT ══ -->
    <div class="modal-overlay" id="modal-detail-riwayat" onclick="closeModalOutside(event,'modal-detail-riwayat')">
        <div class="modal" style="width: 550px;">
            <h3 class="modal-title">Detail <em>Kunjungan Medis</em></h3>
            <p class="modal-sub" id="det-tgl-dokter">-</p>
            <div class="confirm-box" style="margin-bottom:20px">
                <div class="confirm-row"><span>Dokter</span><span id="det-dokter">-</span></div>
                <div class="confirm-row"><span>Layanan</span><span id="det-layanan">-</span></div>
                <div class="confirm-row"><span>Ruangan</span><span id="det-ruangan">-</span></div>
            </div>
            <div style="background:#f7ede5; border-radius:10px; padding:16px; margin-bottom:15px">
                <div style="font-size:9px; letter-spacing:2px; text-transform:uppercase; color:#7d6756; margin-bottom:4px; font-weight:500;">Anamnesis / Keluhan Medis</div>
                <div style="font-size:12px; color:#735a39; font-weight:300; line-height:1.7" id="det-anamnesis">
                    -
                </div>
            </div>
            <div style="background:#f7ede5; border-radius:10px; padding:16px; margin-bottom:20px">
                <div style="font-size:9px; letter-spacing:2px; text-transform:uppercase; color:#7d6756; margin-bottom:4px; font-weight:500;">Diagnosa &amp; Hasil Pemeriksaan Dokter</div>
                <div style="font-size:12px; color:#735a39; font-weight:300; line-height:1.7" id="det-pemeriksaan">
                    -
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-outline" onclick="closeModal('modal-detail-riwayat')">Tutup</button>
                <button type="button" class="btn-primary" id="btn-beri-ulasan" onclick="triggerReviewModal()">Beri Ulasan</button>
            </div>
        </div>
    </div>

    <!-- ══ MODAL: ULASAN ══ -->
    <div class="modal-overlay" id="modal-ulasan" onclick="closeModalOutside(event,'modal-ulasan')">
        <div class="modal">
            <h3 class="modal-title">Beri <em>Ulasan &amp; Rating</em></h3>
            <p class="modal-sub" id="ulasan-dokter-sub">Berikan tanggapan untuk Dokter</p>
            <form method="POST" action="../../backend/user/simpan_ulasan.php">
                <input type="hidden" name="dokter_id" id="ulasan-dokter-id">
                <div style="text-align:center; margin-bottom:24px">
                    <div style="font-size:13px; color:#735a39; font-weight:300; margin-bottom:14px">Pilih Rating Bintang:</div>
                    <select name="rating" class="form-select" style="max-width:240px; margin:0 auto; font-size:15px; text-align:center;">
                        <option value="5">⭐⭐⭐⭐⭐ 5 / 5 (Sangat Puas)</option>
                        <option value="4">⭐⭐⭐⭐ 4 / 5 (Puas)</option>
                        <option value="3">⭐⭐⭐ 3 / 5 (Cukup)</option>
                        <option value="2">⭐⭐ 2 / 5 (Kurang)</option>
                        <option value="1">⭐ 1 / 5 (Sangat Kurang)</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label">Tulis Ulasan Anda</label>
                    <textarea class="form-textarea" name="ulasan" placeholder="Ceritakan pengalaman perawatan kulit Anda..." required style="min-height:80px;"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-outline" onclick="closeModal('modal-ulasan')">Batal</button>
                    <button type="submit" class="btn-primary">Kirim Ulasan Medis</button>
                </div>
            </form>
        </div>
        </div>
    </div>

    <!-- ══ MODAL: SUKSES PENDAFTARAN ══ -->
    <?php if (isset($_GET['success_booking'])): ?>
        <div class="modal-overlay open" id="modal-sukses" onclick="closeModal('modal-sukses')">
            <div class="modal">
                <div class="success-card">
                    <div class="success-icon" style="font-size:48px; text-align:center; margin-bottom:10px;">✅</div>
                    <div class="success-title" style="font-family:'Playfair Display',serif; font-size:24px; color:#4a321f; text-align:center; margin-bottom:10px;">Pendaftaran Berhasil!</div>
                    <div class="success-desc" style="font-size:13px; color:#735a39; text-align:center; line-height:1.7; margin-bottom:20px;">Jadwal konsultasi estetika Anda telah terdaftar. Detail janji temu Anda:</div>
                    <div class="confirm-box" style="background:#f7ede5; padding:16px; border-radius:10px; margin-bottom:20px;">
                        <div class="confirm-row" style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:8px;"><span>Dokter</span><span style="font-weight:500; color:#4a321f;"><?= htmlspecialchars($_GET['dokter']) ?></span></div>
                        <div class="confirm-row" style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:8px;"><span>Tanggal</span><span style="font-weight:500; color:#4a321f;"><?= htmlspecialchars($_GET['tanggal']) ?></span></div>
                        <div class="confirm-row" style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:8px;"><span>Jam</span><span style="font-weight:500; color:#4a321f;"><?= htmlspecialchars($_GET['jam']) ?> WIB</span></div>
                        <div class="confirm-row" style="display:flex; justify-content:space-between; font-size:12px;"><span>No. Antrian</span><span style="color:#735a39; font-weight:600;"><?= htmlspecialchars($_GET['antrian']) ?></span></div>
                    </div>
                    <button class="btn-primary" style="width:100%" onclick="closeModal('modal-sukses')">Kembali ke Beranda</button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Toast -->
    <div class="toast" id="toast"><span id="toast-msg"></span></div>

    <script src="../../asset/js/user.js?v=3"></script>
    <script>
        // Set variables for booking wizard
        let selectedDocId = <?= $dokter_list[0]['id'] ?? 0 ?>;
        let selectedDocName = "<?= htmlspecialchars(addslashes($dokter_list[0]['nama'] ?? '')) ?>";
        let selectedDate = "<?= date('Y-m-d') ?>";
        let selectedJam = "09:00";
        let selectedTreatment = "Konsultasi Estetika Umum";
        
        let reviewDocId = 0;
        let reviewDocName = "";

        function selectDokterCard(id, name) {
            selectedDocId = id;
            selectedDocName = name;
            document.getElementById('booking-dokter-id').value = id;

            // Highlight card
            document.querySelectorAll('.dokter-card').forEach(c => {
                c.style.border = '1px solid #c4a882';
                const b = c.querySelector('.badge');
                if (b) {
                    b.className = 'badge badge-gray';
                    b.textContent = 'Pilih';
                }
            });

            const card = document.getElementById('dokter-option-' + id);
            if (card) {
                card.style.border = '2px solid #735a39';
                const b = card.querySelector('.badge');
                if (b) {
                    b.className = 'badge badge-green';
                    b.textContent = 'Terpilih ✓';
                }
            }
        }

        function startBookingDokter(docId) {
            const d = <?= json_encode($dokter_list) ?>;
            const doc = d.find(x => x.id == docId);
            if (doc) {
                selectDokterCard(docId, doc.nama);
                showPage('daftar-konsul', document.querySelector('[onclick*=daftar-konsul]'));
                nextStep(2);
            }
        }

        function updateTanggalBooking(val) {
            selectedDate = val;
            document.getElementById('booking-tanggal').value = val;
            document.getElementById('slot-preview-header').textContent = val + " · " + selectedDocName;
        }

        function selectSlotBtn(el, time) {
            selectedJam = time;
            document.getElementById('booking-jam').value = time;
            
            document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('selected'));
            el.classList.add('selected');
        }

        function updateTreatmentPreview(el) {
            if (el.selectedIndex === 0) {
                selectedTreatment = "Konsultasi Estetika Umum";
            } else {
                selectedTreatment = el.options[el.selectedIndex].text;
            }
        }

        function generateSummary() {
            document.getElementById('sum-dokter').textContent = selectedDocName;
            document.getElementById('sum-treatment').textContent = selectedTreatment;
            document.getElementById('sum-tanggal').textContent = selectedDate;
            document.getElementById('sum-jam').textContent = selectedJam + " WIB";
        }

        function openModalBatal(id, doc, tgl, jam, layanan) {
            document.getElementById('batal-appt-id').value = id;
            document.getElementById('batal-dokter-preview').textContent = doc;
            document.getElementById('batal-tanggal-preview').textContent = tgl;
            document.getElementById('batal-jam-preview').textContent = jam + " WIB";
            document.getElementById('batal-layanan-preview').textContent = layanan;
            openModal('modal-batalkan');
        }

        function openModalDetailRM(tgl, doc, layanan, ruangan, anamnesis, pemeriksaan, tindak_lanjut, isSelesai, id) {
            document.getElementById('det-tgl-dokter').textContent = tgl + " · " + doc + " · " + layanan;
            document.getElementById('det-dokter').textContent = doc;
            document.getElementById('det-layanan').textContent = layanan;
            document.getElementById('det-ruangan').textContent = ruangan;
            document.getElementById('det-anamnesis').innerHTML = anamnesis.replace(/\n/g, '<br>');
            document.getElementById('det-pemeriksaan').innerHTML = pemeriksaan.replace(/\n/g, '<br>') + (tindak_lanjut ? '<br><br><strong>Rencana Lanjut:</strong><br>' + tindak_lanjut.replace(/\n/g, '<br>') : '');
            
            // Cari dokter_id
            const d = <?= json_encode($dokter_list) ?>;
            const docObj = d.find(x => x.nama.toLowerCase() === doc.toLowerCase());
            if (docObj) {
                reviewDocId = docObj.id;
                reviewDocName = docObj.nama;
            }

            const btn = document.getElementById('btn-beri-ulasan');
            if (isSelesai) {
                btn.style.display = 'inline-block';
            } else {
                btn.style.display = 'none';
            }

            openModal('modal-detail-riwayat');
        }

        function triggerReviewModal() {
            closeModal('modal-detail-riwayat');
            document.getElementById('ulasan-dokter-id').value = reviewDocId;
            document.getElementById('ulasan-dokter-sub').textContent = "Berikan ulasan dan rating untuk " + reviewDocName;
            openModal('modal-ulasan');
        }
    </script>
    <!-- LOGOUT CONFIRMATION MODAL -->
    <div id="logout-modal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.45);backdrop-filter:blur(4px);align-items:center;justify-content:center">
        <div style="background:#fff;border-radius:20px;padding:40px 36px;width:360px;text-align:center;box-shadow:0 24px 64px rgba(0,0,0,0.18);animation:logoutFadeIn .25s ease">
            <div style="width:56px;height:56px;background:#f5e7dc;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:22px">&#x23FB;</div>
            <div style="font-family:'Playfair Display',serif;font-size:20px;color:#4a321f;margin-bottom:10px">Yakin ingin <em>keluar</em>?</div>
            <p style="font-size:13px;color:#7d6756;margin-bottom:28px;line-height:1.6">Sesi Anda sebagai <strong>Pasien</strong> akan diakhiri. Anda perlu login kembali untuk mengakses akun.</p>
            <div style="display:flex;gap:12px;justify-content:center">
                <button onclick="hideLogoutModal()" style="flex:1;padding:11px;border:1.5px solid #c4a882;border-radius:50px;background:#fff;color:#7d6756;font-size:12px;font-weight:500;letter-spacing:1px;text-transform:uppercase;cursor:pointer;font-family:'DM Sans',sans-serif">Batal</button>
                <a href="../../backend/logout.php" style="flex:1;padding:11px;border-radius:50px;background:#735a39;color:#fff;font-size:12px;font-weight:500;letter-spacing:1px;text-transform:uppercase;cursor:pointer;font-family:'DM Sans',sans-serif;text-decoration:none;display:flex;align-items:center;justify-content:center">Ya, Keluar</a>
            </div>
        </div>
    </div>
    <style>
    @keyframes logoutFadeIn { from { transform:scale(.92); opacity:0; } to { transform:scale(1); opacity:1; } }
    #logout-modal.open { display:flex !important; }
    </style>
    <script>
    function showLogoutModal() { document.getElementById('logout-modal').classList.add('open'); }
    function hideLogoutModal() { document.getElementById('logout-modal').classList.remove('open'); }
    document.getElementById('logout-modal').addEventListener('click', function(e) { if(e.target===this) hideLogoutModal(); });
    </script>

    <!-- Real-time Notification Polling -->
    <script>
    function pollNotifications() {
        fetch('../../backend/notif_count.php')
            .then(r => r.json())
            .then(data => {
                const btn = document.querySelector('.notif-btn-text');
                if (!btn) return;
                // Hapus dot lama
                const oldDot = btn.querySelector('.notif-dot');
                if (oldDot) oldDot.remove();
                const oldBadge = btn.querySelector('.notif-badge');
                if (oldBadge) oldBadge.remove();

                if (data.count > 0) {
                    const badge = document.createElement('span');
                    badge.className = 'notif-badge';
                    badge.style.cssText = 'background:#735a39; color:#fff; font-size:9px; padding:1px 6px; border-radius:50px; font-weight:600; margin-left:2px;';
                    badge.textContent = data.count;
                    btn.appendChild(badge);
                }
            })
            .catch(() => {});
    }
    // Poll setiap 10 detik
    pollNotifications();
    setInterval(pollNotifications, 10000);
    
    // Parse URL params for routing and wizard pre-selection
    document.addEventListener('DOMContentLoaded', () => {
        const params = new URLSearchParams(window.location.search);
        const page = params.get('page');
        if (page) {
            const btn = document.querySelector('.nav-item[onclick*="' + page + '"]');
            if (btn) {
                showPage(page, btn);
            }
            
            const docId = params.get('dokter_id');
            if (docId && typeof startBookingDokter === 'function') {
                startBookingDokter(docId);
            }
            
            const treatmentId = params.get('treatment_id');
            if (treatmentId) {
                const selectEl = document.getElementById('booking-treatment-select');
                if (selectEl) {
                    selectEl.value = treatmentId;
                    updateTreatmentPreview(selectEl);
                }
            }
        }

        // Auto fadeout alert notifications and clear success/error params from URL
        setTimeout(() => {
            document.querySelectorAll('.alert-notification').forEach(el => {
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            });
        }, 5000);

        const url = new URL(window.location.href);
        if (url.searchParams.has('success') || url.searchParams.has('error')) {
            url.searchParams.delete('success');
            url.searchParams.delete('error');
            window.history.replaceState({}, document.title, url.pathname + url.search);
        }
    });

    function validateStep3AndNext() {
        const keluhanEl = document.getElementsByName('keluhan')[0];
        const alergiEl = document.getElementsByName('alergi')[0];
        if (!keluhanEl || !keluhanEl.value.trim()) {
            showToast('Keluhan Utama & Keinginan wajib diisi!');
            if (keluhanEl) keluhanEl.focus();
            return;
        }
        if (!alergiEl || !alergiEl.value.trim()) {
            showToast('Riwayat Alergi / Kondisi Khusus wajib diisi!');
            if (alergiEl) alergiEl.focus();
            return;
        }
        nextStep(4);
        generateSummary();
    }
    </script>
</body>
</html>