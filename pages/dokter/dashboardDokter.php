<?php
// ══════════════════════════════════════════════
//  GlowCare — Dashboard Dokter (Dinamis)
// ══════════════════════════════════════════════
require '../../backend/auth/guard_dokter.php';
require '../../backend/config/koneksi.php';

$user_id = (int)$_SESSION['user_id'];
$today   = date('Y-m-d');

// ── 1. PROFIL DOKTER ──────────────────────────
$qProfil = mysqli_query($conn, "
    SELECT u.username, u.email, d.*
    FROM   users u
    LEFT JOIN dokter d ON d.user_id = u.id
    WHERE  u.id = $user_id
    LIMIT  1
");
$profil = $qProfil ? mysqli_fetch_assoc($qProfil) : [];

$namaLengkap  = htmlspecialchars($profil['nama_lengkap']     ?? $profil['username'] ?? 'Dokter');
$gelar        = htmlspecialchars($profil['gelar']            ?? '');
$spesialisasi = htmlspecialchars($profil['spesialisasi']     ?? '-');
$noStr        = htmlspecialchars($profil['no_str']           ?? '-');
$noSip        = htmlspecialchars($profil['no_sip']           ?? '-');
$noTelp       = htmlspecialchars($profil['no_telp']          ?? '-');
$alamat       = htmlspecialchars($profil['alamat']           ?? '-');
$bio          = htmlspecialchars($profil['bio']              ?? '');
$tahunPengalaman = (int)($profil['tahun_pengalaman']         ?? 0);
$rating       = number_format((float)($profil['rating']      ?? 5.0), 1);
$dokter_id    = (int)($profil['id']                          ?? 0);
$emailDokter  = htmlspecialchars($profil['email']            ?? '');
$fotoUrl      = !empty($profil['foto'])
                ? (strpos($profil['foto'], 'http') === 0 
                    ? htmlspecialchars($profil['foto']) 
                    : (strpos($profil['foto'], 'asset/') === 0 
                        ? '../../' . htmlspecialchars($profil['foto']) 
                        : '../../backend/uploads/' . htmlspecialchars($profil['foto'])))
                : '../../asset/img/doctor1.png';
$namaDisplay  = $gelar ? "$gelar $namaLengkap" : $namaLengkap;

// ── 2. STATISTIK ──────────────────────────────
// Jadwal hari ini
$qStatHari = mysqli_query($conn, "
    SELECT
        COUNT(*) AS total,
        SUM(status = 'Selesai') AS selesai,
        SUM(status IN ('Menunggu','Berlangsung','Terjadwal')) AS menunggu
    FROM jadwal
    WHERE dokter_id = $dokter_id AND tanggal = '$today'
");
$statHari = $qStatHari ? mysqli_fetch_assoc($qStatHari) : ['total'=>0,'selesai'=>0,'menunggu'=>0];

// Total pasien unik dokter ini
$qTotalPasien = mysqli_query($conn, "
    SELECT COUNT(DISTINCT pasien_id) AS total
    FROM rekam_medis
    WHERE dokter_id = $dokter_id
");
$totalPasien = $qTotalPasien ? (int)mysqli_fetch_assoc($qTotalPasien)['total'] : 0;

// Statistik bulan ini
$bulanIni = date('Y-m');
$qStatBulan = mysqli_query($conn, "
    SELECT
        COUNT(*) AS total_rm,
        SUM(treatment = 'Facelift') AS facelift,
        SUM(treatment = 'Rhinoplasty') AS rhinoplasty,
        SUM(treatment = 'Blepharoplasty') AS blepharoplasty
    FROM rekam_medis
    WHERE dokter_id = $dokter_id
      AND DATE_FORMAT(tanggal, '%Y-%m') = '$bulanIni'
");
$statBulan = $qStatBulan ? mysqli_fetch_assoc($qStatBulan) : ['total_rm'=>0,'facelift'=>0,'rhinoplasty'=>0,'blepharoplasty'=>0];

$qJadwal = mysqli_query($conn, "
    SELECT j.*, p.nama AS nama_pasien, p.usia, p.jenis_kelamin,
           p.keluhan, p.id AS pasien_id,
           (SELECT id FROM appointment WHERE pasien_id = j.pasien_id AND dokter_id = j.dokter_id AND tanggal = j.tanggal AND TIME_FORMAT(jam, '%H:%i') = TIME_FORMAT(j.jam_mulai, '%H:%i') LIMIT 1) AS appt_id
    FROM   jadwal j
    JOIN   pasien p ON p.id = j.pasien_id
    WHERE  j.dokter_id = $dokter_id AND j.tanggal = '$today'
    ORDER  BY j.jam_mulai ASC
");
$jadwalHariIni = [];
while ($row = mysqli_fetch_assoc($qJadwal)) {
    $jadwalHariIni[] = $row;
}

// Pasien berikutnya = jadwal pertama yg belum Selesai
$pasienBerikutnya = null;
foreach ($jadwalHariIni as $j) {
    if ($j['status'] !== 'Selesai') {
        $pasienBerikutnya = $j;
        break;
    }
}

$qPasien = mysqli_query($conn, "
    SELECT
        p.*,
        j.treatment,
        j.jam_mulai,
        j.tanggal,
        j.status AS status_jadwal,
        COUNT(rm.id) AS total_kunjungan,
        (SELECT id FROM appointment WHERE pasien_id = p.id AND dokter_id = $dokter_id ORDER BY tanggal DESC, jam DESC LIMIT 1) AS appt_id
    FROM pasien p
    LEFT JOIN jadwal j ON j.pasien_id = p.id
        AND j.dokter_id = $dokter_id
        AND j.tanggal = '$today'
    LEFT JOIN rekam_medis rm ON rm.pasien_id = p.id
        AND rm.dokter_id = $dokter_id
    WHERE j.dokter_id = $dokter_id OR rm.dokter_id = $dokter_id
    GROUP BY p.id, j.id
    ORDER BY j.jam_mulai ASC
");
$daftarPasien = [];
while ($row = mysqli_fetch_assoc($qPasien)) {
    $daftarPasien[] = $row;
}

// ── 5. REKAM MEDIS ────────────────────────────
$qRM = mysqli_query($conn, "
    SELECT rm.*, p.nama AS nama_pasien, p.no_rekam AS no_pasien, rm.ruangan
    FROM   rekam_medis rm
    JOIN   pasien p ON p.id = rm.pasien_id
    WHERE  rm.dokter_id = $dokter_id
    ORDER  BY rm.tanggal DESC, rm.id DESC
    LIMIT  20
");
$daftarRM = [];
while ($row = mysqli_fetch_assoc($qRM)) {
    $daftarRM[] = $row;
}

// Daftar pasien untuk dropdown modal (pasien unik dokter ini)
$qPasienDropdown = mysqli_query($conn, "
    SELECT DISTINCT p.id, p.nama, p.no_rekam
    FROM pasien p
    JOIN jadwal j ON j.pasien_id = p.id
    WHERE j.dokter_id = $dokter_id
    ORDER BY p.nama ASC
");
$pasienDropdown = [];
while ($row = mysqli_fetch_assoc($qPasienDropdown)) {
    $pasienDropdown[] = $row;
}

// ── 6. JADWAL MINGGUAN ────────────────────────
// Senin minggu ini
$senin = date('Y-m-d', strtotime('monday this week'));
$sabtu = date('Y-m-d', strtotime('saturday this week'));
$qMinggu = mysqli_query($conn, "
    SELECT j.*, p.nama AS nama_pasien
    FROM   jadwal j
    JOIN   pasien p ON p.id = j.pasien_id
    WHERE  j.dokter_id = $dokter_id
      AND  j.tanggal BETWEEN '$senin' AND '$sabtu'
    ORDER  BY j.tanggal ASC, j.jam_mulai ASC
");
$jadwalMinggu = [];
while ($row = mysqli_fetch_assoc($qMinggu)) {
    $jadwalMinggu[$row['tanggal']][] = $row;
}

// ── 7. NOTIFIKASI DOKTER ──────────────────────
$qNewAppt = mysqli_query($conn, "
    SELECT a.*, p.nama AS nama_pasien, t.nama AS nama_treatment
    FROM appointment a
    JOIN pasien p ON a.pasien_id = p.id
    LEFT JOIN treatment t ON a.treatment_id = t.id
    WHERE a.dokter_id = $dokter_id AND a.status = 'Menunggu'
    ORDER BY a.tanggal DESC, a.jam DESC
");
$newAppointments = [];
if ($qNewAppt) {
    while ($row = mysqli_fetch_assoc($qNewAppt)) {
        $newAppointments[] = $row;
    }
}

$qUnreadChat = mysqli_query($conn, "
    SELECT m.*, p.nama AS nama_pasien, c.id AS consultation_id
    FROM messages m
    JOIN consultations c ON m.consultation_id = c.id
    JOIN pasien p ON c.pasien_id = p.id
    WHERE c.dokter_id = $dokter_id 
      AND m.sender_type = 'Pasien' 
      AND m.is_read = 0
    GROUP BY c.id
    ORDER BY m.created_at DESC
");
$unreadChats = [];
if ($qUnreadChat) {
    while ($row = mysqli_fetch_assoc($qUnreadChat)) {
        $unreadChats[] = $row;
    }
}

// Cek pengumuman baru dalam 7 hari terakhir
$qRecentPengumuman = mysqli_query($conn, "SELECT COUNT(*) AS n FROM pengumuman WHERE target IN ('Semua', 'Dokter') AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$recent_pengumuman = $qRecentPengumuman ? mysqli_fetch_assoc($qRecentPengumuman)['n'] : 0;
$totalNotifCount = count($newAppointments) + count($unreadChats) + $recent_pengumuman;

// Query pengumuman untuk ditampilkan
$qPengumuman = mysqli_query($conn, "SELECT * FROM pengumuman WHERE target IN ('Semua', 'Dokter') ORDER BY created_at DESC LIMIT 20");
$pengumuman_list = [];
if ($qPengumuman) {
    while ($p = mysqli_fetch_assoc($qPengumuman)) {
        $pengumuman_list[] = $p;
    }
}

// ── 8. ULASAN PASIEN ─────────────────────────
$qUlasan = mysqli_query($conn, "
    SELECT u.*, p.nama AS nama_pasien
    FROM ulasan u
    JOIN pasien p ON p.id = u.pasien_id
    WHERE u.dokter_id = $dokter_id
    ORDER BY u.created_at DESC
    LIMIT 50
");
$daftarUlasan = [];
$totalUlasan = 0;
$sumRating = 0;
if ($qUlasan) {
    while ($row = mysqli_fetch_assoc($qUlasan)) {
        $daftarUlasan[] = $row;
        $sumRating += (int)$row['rating'];
        $totalUlasan++;
    }
}
$avgRating = $totalUlasan > 0 ? round($sumRating / $totalUlasan, 1) : 0;

// ── HELPER FUNCTIONS ──────────────────────────
function badgeClass(string $status): string {
    switch ($status) {
        case 'Selesai':
            return 'badge-green';
        case 'Berlangsung':
            return 'badge-yellow';
        case 'Menunggu':
            return 'badge-pink';
        default:
            return 'badge-gray';
    }
}

function inisial(string $nama): string {
    return strtoupper(mb_substr(trim($nama), 0, 1));
}

function formatTanggal(string $tgl): string {
    $bulan = ['','Januari','Februari','Maret','April','Mei','Juni',
              'Juli','Agustus','September','Oktober','November','Desember'];
    [$y, $m, $d] = explode('-', $tgl);
    return (int)$d . ' ' . $bulan[(int)$m] . ' ' . $y;
}

$hariIni = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'][date('w')];
$tanggalHariIni = formatTanggal($today);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GlowCare — Dashboard Dokter</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@300;400;500&display=swap">
    <link rel="stylesheet" href="../../asset/css/dokter.css?v=11">
    <style>
        body {
            background: #f7f6f3 !important;
        }
        .sidebar {
            background: #4a321f !important;
            border-right: 1px solid #3d2716 !important;
            box-shadow: 2px 0 16px rgba(0,0,0,0.06) !important;
        }
        .sidebar-logo {
            border-bottom: 1px solid rgba(255,255,255,0.08) !important;
        }
        .sidebar-logo .brand {
            color: #ffffff !important;
        }
        .sidebar-logo .role {
            color: #a89a8a !important;
        }
        .sidebar-doctor {
            border-bottom: 1px solid rgba(255,255,255,0.08) !important;
        }
        .sidebar-doc-avatar {
            border: 2px solid #e0c097 !important;
        }
        .sidebar-doc-info .doc-name {
            color: #ffffff !important;
        }
        .sidebar-doc-info .doc-spec {
            color: #e0c097 !important;
        }
        .sidebar-doc-info .doc-status {
            color: #d1c4b8 !important;
        }
        .sidebar-doc-info .doc-status::before {
            background: #e0c097 !important;
        }
        .sidebar-nav .nav-section-label {
            color: rgba(255,255,255,0.4) !important;
        }
        .sidebar-nav .nav-item {
            color: rgba(255,255,255,0.8) !important;
        }
        .sidebar-nav .nav-item:hover {
            background: rgba(255,255,255,0.06) !important;
            color: #ffffff !important;
        }
        .sidebar-nav .nav-item.active {
            background: rgba(255,255,255,0.12) !important;
            color: #ffffff !important;
            border-left-color: #e0c097 !important;
        }
        .sidebar-nav .nav-badge {
            background: #e0c097 !important;
            color: #4a321f !important;
        }
        .sidebar-footer {
            border-top: 1px solid rgba(255,255,255,0.08) !important;
        }
        .logout-btn {
            display: inline-flex !important;
            align-items: center !important;
            gap: 10px !important;
            font-size: 12px !important;
            color: #ffffff !important;
            background: #e05050 !important;
            padding: 8px 16px !important;
            border-radius: 50px !important;
            font-weight: 500 !important;
            cursor: pointer !important;
            transition: all 0.2s !important;
            text-decoration: none !important;
            justify-content: center !important;
            width: 100% !important;
            border: none !important;
        }
        .logout-btn:hover {
            background: #ba1a1a !important;
            color: #ffffff !important;
        }
        .topbar {
            background: #4a321f !important;
            border-bottom: 1px solid rgba(255,255,255,0.08) !important;
        }
        .topbar-date {
            color: #f5f3ee !important;
            background: rgba(255,255,255,0.08) !important;
            border: 1px solid rgba(255,255,255,0.15) !important;
        }
        .notif-btn-text {
            border: 1px solid rgba(255,255,255,0.25) !important;
            color: #ffffff !important;
        }
        .notif-dot {
            background: #e0c097 !important;
            border-color: #4a321f !important;
        }
        .card {
            background: #ffffff !important;
            border: 1px solid #efebe4 !important;
            box-shadow: 0 4px 16px rgba(0,0,0,0.04) !important;
        }
        .card:hover {
            box-shadow: 0 8px 24px rgba(0,0,0,0.06) !important;
        }
        /* ══ HERO BANNER ══ */
        .hero-banner {
            background: #faf6ee !important;
            padding: 36px 40px;
            border-bottom: 1px solid #efebe4 !important;
            position: relative;
            overflow: hidden;
        }
        .hero-greeting {
            font-size: 11px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #735a39 !important;
            margin-bottom: 8px;
        }
        .hero-name {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            color: #4a321f !important;
            margin-bottom: 6px;
        }
        .hero-name em {
            color: #735a39 !important;
            font-style: italic;
        }
        .hero-sub {
            font-size: 13px;
            color: #7d6756 !important;
            font-weight: 300;
            margin-bottom: 0px;
        }
    </style>
</head>
<body>

<!-- ══ SIDEBAR ══ -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="brand">GlowCare Clinic</div>
        <div class="role">Doctor Portal</div>
    </div>

    <div class="sidebar-doctor">
        <div class="sidebar-doc-avatar">
            <img src="<?= $fotoUrl ?>" alt="<?= $namaDisplay ?>" onclick="showPanel('profil', document.querySelector('[onclick*=profil]'))" style="cursor:pointer">
        </div>
        <div class="sidebar-doc-info">
            <div class="doc-name"><?= $namaDisplay ?></div>
            <div class="doc-spec"><?= $spesialisasi ?></div>
            <div class="doc-status">Sedang Bertugas</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Menu Utama</div>
        <a class="nav-item active" onclick="showPanel('overview', this)">
            Overview
        </a>
        <a class="nav-item" onclick="showPanel('jadwal', this)">
            Jadwal Praktik
        </a>

        <div class="nav-section-label" style="margin-top:8px">Pasien</div>
        <a class="nav-item" onclick="showPanel('daftar-pasien', this)">
            Daftar Pasien
        </a>
        <a class="nav-item" onclick="showPanel('rekam-medis', this)">
            Rekam Medis
        </a>
        <a class="nav-item" onclick="showPanel('ulasan', this)">
            Ulasan Pasien
        </a>
        <a class="nav-item" href="chat.php">
            Konsultasi Chat
        </a>

        <div class="nav-section-label" style="margin-top:8px">Akun</div>
        <a class="nav-item" onclick="showPanel('profil', this)">
            Profil Saya
        </a>
    </nav>

</aside>

<!-- ══ MAIN ══ -->
<div class="main">

    <!-- TOPBAR -->
    <div class="topbar" style="justify-content: flex-end;">
        <div class="topbar-right">
            <button class="notif-btn-text btn-outline" style="font-size: 11px; padding: 6px 16px; position: relative; display: flex; align-items: center; gap: 8px; cursor: pointer;" onclick="showPanel('notifikasi', this)">
                Notifikasi
                <?php if ($totalNotifCount > 0): ?>
                    <span class="notif-dot" style="display: inline-block; width: 6px; height: 6px; background: #e0c097; border-radius: 50%;"></span>
                <?php endif; ?>
            </button>
            <a class="logout-btn" href="#" onclick="showLogoutModal(); return false;" style="width: auto !important; padding: 6px 16px;">
                Keluar
            </a>
        </div>
    </div>

    <!-- ALERT SUCCESS/ERROR GLOBAL -->
    <?php if (!in_array($_GET['page'] ?? '', ['profil', 'rekam-medis'])): ?>
        <?php if (!empty($_GET['success'])): ?>
            <div class="alert alert-success alert-notification" style="margin:16px 28px 0; padding:12px 18px; background:#d4edda; border:1px solid #c3e6cb; border-radius:8px; color:#155724; font-size:13px; transition: opacity 0.5s ease;">
                <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php elseif (!empty($_GET['error'])): ?>
            <div class="alert alert-error alert-notification" style="margin:16px 28px 0; padding:12px 18px; background:#f8d7da; border:1px solid #f5c6cb; border-radius:8px; color:#721c24; font-size:13px; transition: opacity 0.5s ease;">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- CONTENT -->
    <div class="content">

        <!-- ══ PANEL: OVERVIEW ══ -->
        <div class="panel active" id="panel-overview">
            <!-- Hero banner -->
            <div class="hero-banner" style="margin: -36px -40px 36px -40px;">
                <div class="hero-greeting"><?= ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'][date('w')] ?>, <?= date('d M Y') ?></div>
                <div class="hero-name">Halo, <em><?= htmlspecialchars($namaDisplay) ?></em></div>
                <div class="hero-sub">Selamat datang kembali di GlowCare Clinic. Yuk, layani pasien dengan sepenuh hati.</div>
            </div>

            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-icon"></div>
                    <div class="stat-value"><?= (int)$statHari['total'] ?></div>
                    <div class="stat-label">Jadwal Hari Ini</div>
                </div>
                <div class="stat-card teal">
                    <div class="stat-icon"></div>
                    <div class="stat-value"><?= (int)$statHari['selesai'] ?></div>
                    <div class="stat-label">Sudah Ditangani</div>
                </div>
                <div class="stat-card purple">
                    <div class="stat-icon"></div>
                    <div class="stat-value"><?= (int)$statHari['menunggu'] ?></div>
                    <div class="stat-label">Menunggu</div>
                </div>
                <div class="stat-card orange">
                    <div class="stat-icon"></div>
                    <div class="stat-value"><?= $totalPasien ?></div>
                    <div class="stat-label">Total Pasien</div>
                </div>
            </div>

            <div class="two-col">
                <!-- Jadwal hari ini -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Jadwal Hari Ini</div>
                        <a class="card-action" onclick="showPanel('jadwal', document.querySelector('[onclick*=jadwal]'))">Lihat semua →</a>
                    </div>
                    <div class="schedule-list">
                        <?php if (empty($jadwalHariIni)): ?>
                            <div style="padding:24px; text-align:center; color:#7a7571; font-size:13px;">
                                Tidak ada jadwal hari ini.
                            </div>
                        <?php else: ?>
                            <?php foreach ($jadwalHariIni as $j): ?>
                            <div class="schedule-item">
                                <div class="sch-time-col"><?= substr($j['jam_mulai'], 0, 5) ?></div>
                                <div class="sch-bar"></div>
                                <div>
                                    <div class="sch-info-title"><?= htmlspecialchars($j['nama_pasien']) ?></div>
                                    <div class="sch-info-sub"><?= htmlspecialchars($j['treatment']) ?></div>
                                </div>
                                <span class="badge <?= badgeClass($j['status']) ?>" style="margin-left:auto; align-self:center">
                                    <?= htmlspecialchars($j['status']) ?>
                                </span>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Kanan: Pasien berikutnya + Statistik -->
                <div style="display:flex; flex-direction:column; gap:22px">

                    <!-- Pasien berikutnya -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Pasien Berikutnya</div>
                        </div>
                        <div style="padding:0 24px 24px">
                            <?php if ($pasienBerikutnya): ?>
                            <div style="display:flex; align-items:center; gap:14px; margin-bottom:16px">
                                <div class="pasien-detail-avatar" style="width:52px;height:52px;font-size:18px">
                                    <?= inisial($pasienBerikutnya['nama_pasien']) ?>
                                </div>
                                <div>
                                    <div style="font-family:'Playfair Display',serif; font-size:16px; color:#2D3436">
                                        <?= htmlspecialchars($pasienBerikutnya['nama_pasien']) ?>
                                    </div>
                                    <div style="font-size:11px; color:#7a7571">
                                        <?= htmlspecialchars($pasienBerikutnya['usia'] ?? '-') ?> Tahun
                                        <?= !empty($pasienBerikutnya['jenis_kelamin']) ? '· ' . htmlspecialchars($pasienBerikutnya['jenis_kelamin']) : '' ?>
                                    </div>
                                </div>
                                <span class="badge badge-yellow" style="margin-left:auto">
                                    <?= substr($pasienBerikutnya['jam_mulai'], 0, 5) ?>
                                </span>
                            </div>
                            <?php if (!empty($pasienBerikutnya['keluhan'])): ?>
                            <div style="background:#F9F7F2; border-radius:8px; padding:14px; font-size:12px; color:#585552; line-height:1.7; font-weight:300">
                                <strong style="color:#2D3436; font-size:11px; letter-spacing:1px; text-transform:uppercase">Keluhan:</strong><br>
                                <?= htmlspecialchars($pasienBerikutnya['keluhan']) ?>
                            </div>
                            <?php endif; ?>
                            <div style="display:flex; gap:8px; margin-top:14px">
                                <button class="btn-primary" style="font-size:10px; padding:8px 18px"
                                    onclick="openModal('modal-rm-baru', <?= $pasienBerikutnya['pasien_id'] ?>)">
                                    + Rekam Medis
                                </button>
                                <button class="btn-outline" style="font-size:10px; padding:8px 18px"
                                    onclick="showPanel('rekam-medis', document.querySelector('[onclick*=rekam-medis]'))">
                                    Lihat Riwayat
                                </button>
                            </div>
                            <?php else: ?>
                                <div style="padding:8px 0; color:#7a7571; font-size:13px;">Tidak ada pasien berikutnya.</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Statistik bulan ini -->
                    <div class="card">
                        <div class="card-header"><div class="card-title">Statistik Bulan Ini</div></div>
                        <div style="padding:0 24px 20px; display:flex; flex-direction:column; gap:14px">
                            <div style="display:flex; justify-content:space-between; align-items:center">
                                <span style="font-size:12px; color:#585552">Total Pasien Ditangani</span>
                                <span style="font-family:'Playfair Display',serif; font-size:18px; color:#735a39">
                                    <?= (int)$statBulan['total_rm'] ?>
                                </span>
                            </div>
                            <div style="height:1px; background:#F9F7F2"></div>
                            <div style="display:flex; justify-content:space-between; align-items:center">
                                <span style="font-size:12px; color:#585552">Treatment Facelift</span>
                                <span style="font-size:14px; color:#2D3436; font-weight:500"><?= (int)$statBulan['facelift'] ?></span>
                            </div>
                            <div style="display:flex; justify-content:space-between; align-items:center">
                                <span style="font-size:12px; color:#585552">Rhinoplasty</span>
                                <span style="font-size:14px; color:#2D3436; font-weight:500"><?= (int)$statBulan['rhinoplasty'] ?></span>
                            </div>
                            <div style="display:flex; justify-content:space-between; align-items:center">
                                <span style="font-size:12px; color:#585552">Blepharoplasty</span>
                                <span style="font-size:14px; color:#2D3436; font-weight:500"><?= (int)$statBulan['blepharoplasty'] ?></span>
                            </div>
                            <div style="height:1px; background:#F9F7F2"></div>
                            <div style="display:flex; justify-content:space-between; align-items:center">
                                <span style="font-size:12px; color:#585552">Rating Rata-rata</span>
                                <span style="font-size:14px; color:#2D3436; font-weight:500"> <?= $rating ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /panel-overview -->


        <!-- ══ PANEL: JADWAL ══ -->
        <div class="panel" id="panel-jadwal">
            <div class="page-header">
                <div>
                    <h2 class="section-title">Jadwal <em>Praktik</em></h2>
                    <p class="section-sub">Jadwal yang telah ditentukan oleh admin klinik</p>
                </div>
                <div style="font-size:11px; color:#7a7571; padding:8px 14px; background:#fff; border:1px solid #d1c4b8; border-radius:50px">
                    <?= formatTanggal($senin) ?> - <?= formatTanggal($sabtu) ?>
                </div>
            </div>

            <!-- Week grid -->
            <?php
            $hariList = [];
            for ($i = 0; $i < 6; $i++) {
                $hariList[] = date('Y-m-d', strtotime("$senin +$i days"));
            }
            $namaHari = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
            $slotJam  = ['09:00','10:30','13:00','15:00'];
            ?>
            <div class="week-grid">
                <div class="wg-head">Jam</div>
                <?php foreach ($hariList as $idx => $tgl): ?>
                    <div class="wg-head">
                        <?= $namaHari[$idx] ?><br>
                        <span style="font-size:10px; <?= $tgl === $today ? 'color:#735a39' : '' ?>">
                            <?= date('d M', strtotime($tgl)) ?><?= $tgl === $today ? ' ●' : '' ?>
                        </span>
                    </div>
                <?php endforeach; ?>

                <?php foreach ($slotJam as $jam): ?>
                    <div class="wg-time"><?= $jam ?></div>
                    <?php foreach ($hariList as $tgl): ?>
                        <div class="wg-cell">
                            <?php
                            // Tampilkan event yang jam_mulai-nya di slot ini (H:i cocok)
                            foreach ($jadwalMinggu[$tgl] ?? [] as $ev) {
                                $evJam = substr($ev['jam_mulai'], 0, 5);
                                if ($evJam === $jam):
                            ?>
                                <div class="wg-event">
                                    <?= htmlspecialchars(explode(' ', $ev['nama_pasien'])[0]) ?><br>
                                    <?= htmlspecialchars($ev['treatment']) ?>
                                </div>
                            <?php
                                endif;
                            }
                            ?>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>

            <!-- Detail tabel hari ini -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Detail Jadwal Hari Ini — <?= $hariIni ?>, <?= $tanggalHariIni ?></div>
                </div>
                <table class="data-table">
                    <thead>
                        <tr><th>Jam</th><th>Pasien</th><th>Treatment</th><th>Ruangan</th><th>Durasi</th><th>Status</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($jadwalHariIni)): ?>
                        <tr><td colspan="7" style="text-align:center; color:#7a7571; padding:24px">Tidak ada jadwal hari ini.</td></tr>
                        <?php else: ?>
                        <?php foreach ($jadwalHariIni as $j): ?>
                        <tr>
                            <td class="td-jam" style="<?= $j['status'] === 'Berlangsung' ? 'color:#735a39; font-weight:500' : '' ?>">
                                <?= substr($j['jam_mulai'], 0, 5) ?>
                            </td>
                            <td>
                                <span class="avatar"><?= inisial($j['nama_pasien']) ?></span>
                                <span class="td-name"><?= htmlspecialchars($j['nama_pasien']) ?></span>
                            </td>
                            <td><?= htmlspecialchars($j['treatment']) ?></td>
                            <td><?= htmlspecialchars($j['ruangan'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($j['durasi'] ?? '-') ?></td>
                            <td><span class="badge <?= badgeClass($j['status']) ?>"><?= htmlspecialchars($j['status']) ?></span></td>
                            <td>
                                <button class="act-btn" style="border: 1px solid #d1c4b8; padding: 4px 10px;"
                                    onclick="openModal('modal-rm-baru', <?= (int)$j['pasien_id'] ?>)"
                                    title="Tambah rekam medis">Rekam Medis</button>
                                <?php if (!empty($j['appt_id'])): ?>
                                <button class="act-btn" style="border: 1px solid #735a39; padding: 4px 10px; color: #735a39; background: #fff;"
                                    onclick="window.location.href='chat.php?appt_id=<?= (int)$j['appt_id'] ?>'"
                                    title="Chat dengan pasien">Chat</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div><!-- /panel-jadwal -->


        <!-- ══ PANEL: DAFTAR PASIEN ══ -->
        <div class="panel" id="panel-daftar-pasien">
            <div class="page-header">
                <div>
                    <h2 class="section-title">Daftar <em>Pasien</em></h2>
                    <p class="section-sub">Pasien yang akan dan pernah berkonsultasi dengan Anda</p>
                </div>
            </div>

            <div class="filter-bar">
                <input class="filter-input" type="text" id="cari-pasien" placeholder="Cari nama pasien..."
                    oninput="filterPasien()">
                <select class="filter-select" id="filter-status" onchange="filterPasien()">
                    <option value="">Semua Status</option>
                    <option value="Selesai">Selesai</option>
                    <option value="Berlangsung">Berlangsung</option>
                    <option value="Menunggu">Menunggu</option>
                    <option value="Terjadwal">Terjadwal</option>
                </select>
                <select class="filter-select" id="filter-treatment" onchange="filterPasien()">
                    <option value="">Semua Treatment</option>
                    <option value="Facelift">Facelift</option>
                    <option value="Rhinoplasty">Rhinoplasty</option>
                    <option value="Blepharoplasty">Blepharoplasty</option>
                    <option value="Body Contouring">Body Contouring</option>
                </select>
            </div>

            <div class="card">
                <table class="data-table" id="tabel-pasien">
                    <thead>
                        <tr><th>Pasien</th><th>Usia</th><th>Kontak</th><th>Treatment</th><th>Jadwal</th><th>Kunjungan</th><th>Status</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($daftarPasien)): ?>
                        <tr><td colspan="8" style="text-align:center; color:#7a7571; padding:24px">Belum ada data pasien.</td></tr>
                        <?php else: ?>
                        <?php foreach ($daftarPasien as $p): ?>
                        <tr data-nama="<?= htmlspecialchars(strtolower($p['nama'])) ?>"
                            data-status="<?= htmlspecialchars($p['status_jadwal'] ?? '') ?>"
                            data-treatment="<?= htmlspecialchars($p['treatment'] ?? '') ?>">
                            <td>
                                <span class="avatar"><?= inisial($p['nama']) ?></span>
                                <div style="display:inline-block; vertical-align:middle">
                                    <span class="td-name"><?= htmlspecialchars($p['nama']) ?></span>
                                    <span class="td-sub"><?= htmlspecialchars($p['no_rekam'] ?? '') ?></span>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($p['usia'] ?? '-') ?> Thn</td>
                            <td><div style="font-size:12px"><?= htmlspecialchars($p['no_telp'] ?? '-') ?></div></td>
                            <td><?= htmlspecialchars($p['treatment'] ?? '-') ?></td>
                            <td style="font-size:12px; <?= $p['tanggal'] === $today ? 'color:#735a39' : '' ?>">
                                <?= $p['tanggal'] === $today ? 'Hari ini ' : (htmlspecialchars($p['tanggal'] ?? '')) ?>
                                <?= !empty($p['jam_mulai']) ? substr($p['jam_mulai'], 0, 5) : '' ?>
                            </td>
                            <td style="text-align:center"><?= (int)$p['total_kunjungan'] ?></td>
                            <td>
                                <span class="badge <?= badgeClass($p['status_jadwal'] ?? 'Terjadwal') ?>">
                                    <?= htmlspecialchars($p['status_jadwal'] ?? 'Terjadwal') ?>
                                </span>
                            </td>
                            <td>
                                <button class="act-btn" style="border: 1px solid #d1c4b8; padding: 4px 10px; margin-right: 4px;" title="Lihat detail"
                                    onclick="showPasienDetail(<?= (int)$p['id'] ?>)">Detail</button>
                                <button class="act-btn" style="border: 1px solid #735a39; padding: 4px 10px; color: #735a39;" title="Rekam medis"
                                    onclick="openModal('modal-rm-baru', <?= (int)$p['id'] ?>)">Rekam Medis</button>
                                <?php if (!empty($p['appt_id'])): ?>
                                <button class="act-btn" style="border: 1px solid #735a39; padding: 4px 10px; color: #735a39; background: #fff;" title="Chat"
                                    onclick="window.location.href='chat.php?appt_id=<?= (int)$p['appt_id'] ?>'">Chat</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Detail pasien (muncul saat klik 👁️ via AJAX) -->
            <div id="pasien-detail-box" style="display:none; margin-top:22px">
                <div class="card">
                    <div class="pasien-detail-header">
                        <div class="pasien-detail-avatar" id="detail-avatar">?</div>
                        <div>
                            <div class="pasien-detail-name" id="detail-name">-</div>
                            <div class="pasien-detail-meta" id="detail-meta">-</div>
                        </div>
                        <div style="margin-left:auto; display:flex; gap:10px; align-items:center">
                            <span class="badge badge-green">Pasien Aktif</span>
                            <button class="btn-outline" style="font-size:10px; padding:7px 16px"
                                id="btn-rm-dari-detail"
                                onclick="openModal('modal-rm-baru')">+ Rekam Medis</button>
                        </div>
                    </div>
                    <div class="info-grid" id="detail-info-grid">
                        <!-- Diisi oleh JS via AJAX -->
                    </div>
                </div>
            </div>
        </div><!-- /panel-daftar-pasien -->


        <!-- ══ PANEL: REKAM MEDIS ══ -->
        <div class="panel" id="panel-rekam-medis">
            <div class="page-header">
                <div>
                    <h2 class="section-title">Rekam <em>Medis</em></h2>
                    <p class="section-sub">Kelola dan perbarui rekam medis pasien</p>
                </div>
                <button class="btn-primary" onclick="openModal('modal-rm-baru')">+ Tambah Rekam Medis</button>
            </div>

            <!-- ALERT SUCCESS/ERROR LOCAL TO REKAM MEDIS -->
            <?php if (($_GET['page'] ?? '') === 'rekam-medis'): ?>
                <?php if (!empty($_GET['success'])): ?>
                    <div class="alert alert-success alert-notification" style="margin-bottom:20px; padding:12px 18px; background:#d4edda; border:1px solid #c3e6cb; border-radius:8px; color:#155724; font-size:13px; transition: opacity 0.5s ease;">
                        <?= htmlspecialchars($_GET['success']) ?>
                    </div>
                <?php elseif (!empty($_GET['error'])): ?>
                    <div class="alert alert-error alert-notification" style="margin-bottom:20px; padding:12px 18px; background:#f8d7da; border:1px solid #f5c6cb; border-radius:8px; color:#721c24; font-size:13px; transition: opacity 0.5s ease;">
                        <?= htmlspecialchars($_GET['error']) ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="filter-bar">
                <input class="filter-input" type="text" id="cari-rm" placeholder="Cari nama pasien..." oninput="filterRM()">
                <select class="filter-select" id="filter-rm-treatment" onchange="filterRM()">
                    <option value="">Semua Treatment</option>
                    <option value="Facelift">Facelift</option>
                    <option value="Rhinoplasty">Rhinoplasty</option>
                    <option value="Blepharoplasty">Blepharoplasty</option>
                    <option value="Body Contouring">Body Contouring</option>
                </select>
                <select class="filter-select" id="filter-rm-bulan" onchange="filterRM()">
                    <option value="">Semua Bulan</option>
                    <?php
                    for ($i = 0; $i < 6; $i++) {
                        $bln = date('Y-m', strtotime("-$i months"));
                        $label = date('M Y', strtotime("-$i months"));
                        echo "<option value=\"$bln\">$label</option>";
                    }
                    ?>
                </select>
            </div>

            <!-- Tabs -->
            <div class="rm-tabs">
                <div class="rm-tab active" onclick="switchTab('rm-list', this)">Daftar Rekam Medis</div>
                <div class="rm-tab" onclick="switchTab('rm-timeline', this)">Timeline Pasien</div>
            </div>

            <!-- Tab: List -->
            <div class="rm-content active" id="tab-rm-list">
                <?php if (empty($daftarRM)): ?>
                    <div style="padding:32px; text-align:center; color:#7a7571">Belum ada rekam medis.</div>
                <?php else: ?>
                <?php foreach ($daftarRM as $rm): ?>
                <div class="rm-card"
                     data-nama="<?= htmlspecialchars(strtolower($rm['nama_pasien'])) ?>"
                     data-treatment="<?= htmlspecialchars($rm['treatment'] ?? '') ?>"
                     data-bulan="<?= date('Y-m', strtotime($rm['tanggal'])) ?>">
                    <div class="rm-card-header">
                        <div>
                            <div class="rm-card-title">
                                <?= htmlspecialchars($rm['nama_pasien']) ?> · <?= htmlspecialchars($rm['treatment'] ?? '-') ?>
                            </div>
                            <div style="font-size:11px; color:#7a7571; margin-top:3px">
                                <?= htmlspecialchars($rm['no_pasien'] ?? '') ?>
                                <?= !empty($rm['ruangan']) ? ' · ' . htmlspecialchars($rm['ruangan']) : '' ?>
                            </div>
                        </div>
                        <div style="text-align:right">
                            <div class="rm-date"><?= formatTanggal($rm['tanggal']) ?></div>
                            <span class="badge <?= badgeClass($rm['status']) ?>" style="margin-top:6px; display:inline-block">
                                <?= htmlspecialchars($rm['status']) ?>
                            </span>
                        </div>
                    </div>
                    <div class="rm-body">
                        <?php if (!empty($rm['anamnesis'])): ?>
                            <strong style="font-size:11px; letter-spacing:1px; text-transform:uppercase; color:#2D3436">Anamnesis:</strong><br>
                            <?= nl2br(htmlspecialchars($rm['anamnesis'])) ?>
                            <br><br>
                        <?php endif; ?>
                        <?php if (!empty($rm['pemeriksaan'])): ?>
                            <strong style="font-size:11px; letter-spacing:1px; text-transform:uppercase; color:#2D3436">Hasil Pemeriksaan & Tindakan:</strong><br>
                            <?= nl2br(htmlspecialchars($rm['pemeriksaan'])) ?>
                        <?php endif; ?>
                    </div>
                    <div class="rm-tags">
                        <?php if (!empty($rm['treatment'])): ?>
                            <span class="rm-tag"><?= htmlspecialchars($rm['treatment']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($rm['jadwal_followup'])): ?>
                            <span class="rm-tag">Follow-up: <?= formatTanggal($rm['jadwal_followup']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div style="display:flex; gap:8px; margin-top:16px">
                        <button class="btn-outline" style="font-size:10px; padding:7px 16px"
                            onclick="openModalEdit(<?= (int)$rm['id'] ?>, '<?= htmlspecialchars(addslashes($rm['anamnesis'] ?? ''), ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($rm['pemeriksaan'] ?? ''), ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($rm['tindak_lanjut'] ?? ''), ENT_QUOTES) ?>', '<?= htmlspecialchars($rm['status']) ?>', '<?= $rm['jadwal_followup'] ?? '' ?>')">
                            Edit
                        </button>
                        <button class="btn-outline" style="font-size:10px; padding:7px 16px; color:#c0392b; border-color:#e8b5ac;"
                            onclick="konfirmasiHapusRM(<?= (int)$rm['id'] ?>, '<?= htmlspecialchars(addslashes($rm['nama_pasien']), ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($rm['treatment'] ?? ''), ENT_QUOTES) ?>')">
                            Hapus
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Tab: Timeline -->
            <div class="rm-content" id="tab-rm-timeline">
                <div style="margin-bottom:16px; display:flex; gap:10px; align-items:center">
                    <span style="font-size:12px; color:#585552">Pasien:</span>
                    <select class="filter-select" id="timeline-pasien" onchange="loadTimeline(this.value)">
                        <option value="">-- Pilih Pasien --</option>
                        <?php foreach ($pasienDropdown as $pd): ?>
                            <option value="<?= (int)$pd['id'] ?>">
                                <?= htmlspecialchars($pd['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="timeline" id="timeline-container">
                    <div style="color:#7a7571; font-size:13px;">Pilih pasien untuk melihat riwayat.</div>
                </div>
            </div>
        </div><!-- /panel-rekam-medis -->


        <!-- ══ PANEL: NOTIFIKASI ══ -->
        <div class="panel" id="panel-notifikasi">
            <div class="page-header">
                <div>
                    <h2 class="section-title">Notifikasi</h2>
                    <p class="section-sub">Semua janji temu baru masuk dan pesan chat belum dibaca.</p>
                </div>
            </div>

            <div class="card" style="padding: 24px;">
                <?php if (empty($newAppointments) && empty($unreadChats) && empty($pengumuman_list)): ?>
                    <div style="padding: 32px; text-align: center; color: #7a7571; font-size: 13px;">
                         Tidak ada notifikasi baru saat ini.
                    </div>
                <?php else: ?>
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

                    <!-- Pending Appointments -->
                    <?php foreach ($newAppointments as $appt): ?>
                        <div style="padding: 16px; background:#e8f9f1; border:1px solid #a7f3d0; border-radius:10px; margin-bottom:12px; display:flex; gap:15px; align-items:center;">
                            <span style="font-size:24px; font-weight:bold; color:#735a39;">!</span>
                            <div style="flex: 1;">
                                <div style="font-weight:500; color:#594323;">Permintaan Konsultasi Baru</div>
                                <div style="font-size:12px; color:#735a39; margin-top:3px;">Pasien <strong><?= htmlspecialchars($appt['nama_pasien']) ?></strong> mengajukan janji temu untuk <?= htmlspecialchars($appt['nama_treatment'] ?: 'Konsultasi Umum') ?> pada <?= date('d M Y', strtotime($appt['tanggal'])) ?> pukul <?= substr($appt['jam'], 0, 5) ?> WIB.</div>
                            </div>
                            <button class="btn-primary btn-sm" onclick="showPanel('jadwal', document.querySelector('[onclick*=jadwal]'))">Lihat Jadwal</button>
                        </div>
                    <?php endforeach; ?>

                    <!-- Unread Chats -->
                    <?php foreach ($unreadChats as $chat): ?>
                        <div style="padding: 16px; background:#f8efe8; border:1px solid #f1dec9; border-radius:10px; margin-bottom:12px; display:flex; gap:15px; align-items:center;">
                            <span style="font-size:24px; font-weight:bold; color:#735a39;">💬</span>
                            <div style="flex: 1;">
                                <div style="font-weight:500; color:#4a321f;">Pesan Chat Baru</div>
                                <div style="font-size:12px; color:#7d6756; margin-top:3px;">Ada pesan baru belum dibaca dari pasien <strong><?= htmlspecialchars($chat['nama_pasien']) ?></strong>.</div>
                            </div>
                            <a class="btn-primary btn-sm" href="chat.php?appt_id=<?= (int)$chat['consultation_id'] ?>" style="text-decoration:none;">Balas Chat</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div><!-- /panel-notifikasi -->


        <!-- ══ PANEL: PROFIL ══ -->
        <div class="panel" id="panel-profil">
            <div class="page-header">
                <div>
                    <h2 class="section-title">Profil <em>Saya</em></h2>
                    <p class="section-sub">Kelola data profil dan informasi profesional Anda</p>
                </div>
            </div>

            <!-- ALERT SUCCESS/ERROR LOCAL TO PROFIL -->
            <?php if (($_GET['page'] ?? '') === 'profil'): ?>
                <?php if (!empty($_GET['success'])): ?>
                    <div class="alert alert-success alert-notification" style="margin-bottom:20px; padding:12px 18px; background:#d4edda; border:1px solid #c3e6cb; border-radius:8px; color:#155724; font-size:13px; transition: opacity 0.5s ease;">
                        <?= htmlspecialchars($_GET['success']) ?>
                    </div>
                <?php elseif (!empty($_GET['error'])): ?>
                    <div class="alert alert-error alert-notification" style="margin-bottom:20px; padding:12px 18px; background:#f8d7da; border:1px solid #f5c6cb; border-radius:8px; color:#721c24; font-size:13px; transition: opacity 0.5s ease;">
                        <?= htmlspecialchars($_GET['error']) ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Hero profil -->
            <div class="profil-hero">
                <div class="profil-avatar-wrap">
                    <div class="profil-avatar">
                            <img src="<?= $fotoUrl ?>" alt="<?= $namaDisplay ?>" onclick="showPanel('profil', document.querySelector('[onclick*=profil]'))" style="cursor:pointer">
                    </div>
                    <div class="profil-edit-avatar"></div>
                </div>
                <div class="profil-info">
                    <div class="profil-name"><?= $namaDisplay ?></div>
                    <div class="profil-spec"><?= $spesialisasi ?> · GlowCare Clinic</div>
                    <div class="profil-meta">
                        <div class="profil-meta-item">
                            <span class="profil-meta-label">No. STR</span>
                            <span class="profil-meta-value"><?= $noStr ?></span>
                        </div>
                        <div class="profil-meta-item">
                            <span class="profil-meta-label">Pengalaman</span>
                            <span class="profil-meta-value"><?= $tahunPengalaman ?>+ Tahun</span>
                        </div>
                        <div class="profil-meta-item">
                            <span class="profil-meta-label">Rating</span>
                            <span class="profil-meta-value"><?= $rating ?></span>
                        </div>
                    </div>
                    <div class="profil-tags">
                        <?php
                        $treatments = ['Facelift','Rhinoplasty','Blepharoplasty','Body Contouring'];
                        foreach ($treatments as $t): ?>
                            <span class="profil-tag"><?= $t ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="profil-stats">
                    <div class="profil-stat">
                        <div class="profil-stat-val"><?= $totalPasien ?></div>
                        <div class="profil-stat-lbl">Total Pasien</div>
                    </div>
                </div>
            </div>

            <!-- Form profil -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:22px">
                <!-- Data pribadi -->
                <div class="card">
                    <div class="card-header"><div class="card-title">Data Pribadi</div></div>
                    <div style="padding:0 24px 24px">
                        <form method="POST" action="../../backend/dokter/update_profil.php">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap</label>
                                <input class="form-input" name="nama_lengkap" value="<?= $namaLengkap ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Gelar</label>
                                <input class="form-input" name="gelar" value="<?= $gelar ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">No. Telepon</label>
                                <input class="form-input" name="no_telp" value="<?= $noTelp ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input class="form-input" value="<?= $emailDokter ?>" disabled>
                            </div>
                            <div class="form-group full">
                                <label class="form-label">Alamat</label>
                                <input class="form-input" name="alamat" value="<?= $alamat ?>">
                            </div>
                        </div>
                        <button type="submit" class="btn-primary" style="margin-top:8px">Simpan Data Pribadi</button>
                        </form>
                    </div>
                </div>

                <!-- Data profesional -->
                <div class="card">
                    <div class="card-header"><div class="card-title">Informasi Profesional</div></div>
                    <div style="padding:0 24px 24px">
                        <form method="POST" action="../../backend/dokter/update_profil.php">
                        <div class="form-group">
                            <label class="form-label">Spesialisasi</label>
                            <select class="form-select" name="spesialisasi">
                                <?php foreach (['Plastic Surgeon','Aesthetic Physician','Dermatologist'] as $sp): ?>
                                    <option <?= $spesialisasi === $sp ? 'selected' : '' ?>><?= $sp ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">No. STR</label>
                            <input class="form-input" name="no_str" value="<?= $noStr ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">No. SIP</label>
                            <input class="form-input" name="no_sip" value="<?= $noSip ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Tahun Pengalaman</label>
                            <input class="form-input" type="number" name="tahun_pengalaman" value="<?= $tahunPengalaman ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Bio Singkat</label>
                            <textarea class="form-textarea" name="bio"><?= $bio ?></textarea>
                        </div>
                        <button type="submit" class="btn-primary" style="margin-top:8px">Simpan Info Profesional</button>
                        </form>
                    </div>
                </div>

                <!-- Ganti password -->
                <div class="card" style="grid-column:1/-1">
                    <div class="card-header"><div class="card-title">Keamanan Akun</div></div>
                    <div style="padding:0 24px 24px">
                        <form method="POST" action="../../backend/dokter/ganti_password.php">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Password Lama</label>
                                <input class="form-input" type="password" name="password_lama" placeholder="••••••••">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Password Baru</label>
                                <input class="form-input" type="password" name="password_baru" placeholder="••••••••">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Konfirmasi Password</label>
                                <input class="form-input" type="password" name="konfirmasi" placeholder="••••••••">
                            </div>
                        </div>
                        <button type="submit" class="btn-outline">Ubah Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div><!-- /panel-profil -->

        <!-- ══ PANEL: ULASAN PASIEN ══ -->
        <div class="panel" id="panel-ulasan">
            <div class="hero-banner" style="margin: -36px -40px 36px -40px;">
                <div class="hero-greeting">Ulasan &amp; Rating</div>
                <div class="hero-name">Ulasan <em>Pasien</em></div>
                <div class="hero-sub">Lihat tanggapan dan rating dari pasien yang pernah Anda tangani.</div>
            </div>

            <!-- Rating Summary -->
            <div class="card" style="text-align:center; padding:32px 24px; margin-bottom:28px;">
                <div style="font-size:48px; font-family:'Playfair Display',serif; color:#4a321f; font-weight:600;">
                    <?= $avgRating > 0 ? $avgRating : '—' ?>
                </div>
                <div style="font-size:24px; color:#c4a882; margin:4px 0 8px; letter-spacing:2px;">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <?= $i <= round($avgRating) ? '★' : '☆' ?>
                    <?php endfor; ?>
                </div>
                <div style="font-size:13px; color:#7d6756; font-weight:300;">
                    Rata-rata dari <strong><?= $totalUlasan ?></strong> ulasan pasien
                </div>
            </div>

            <!-- Review List -->
            <?php if (empty($daftarUlasan)): ?>
                <div class="card" style="text-align:center; padding:48px 24px;">
                    <div style="font-size:36px; margin-bottom:12px;">📝</div>
                    <div style="font-family:'Playfair Display',serif; font-size:18px; color:#4a321f; margin-bottom:6px;">Belum Ada Ulasan</div>
                    <div style="font-size:13px; color:#7d6756; font-weight:300;">Ulasan akan muncul setelah pasien memberikan rating setelah kunjungan selesai.</div>
                </div>
            <?php else: ?>
                <?php foreach ($daftarUlasan as $ul): ?>
                    <div class="card" style="padding:20px 24px; margin-bottom:12px;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px;">
                            <div style="display:flex; align-items:center; gap:12px;">
                                <div style="width:40px; height:40px; background:#f5e7dc; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:600; color:#735a39; font-size:15px;">
                                    <?= strtoupper(mb_substr($ul['nama_pasien'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div style="font-size:14px; font-weight:500; color:#4a321f;"><?= htmlspecialchars($ul['nama_pasien']) ?></div>
                                    <div style="font-size:11px; color:#a89a8a;"><?= date('d M Y, H:i', strtotime($ul['created_at'])) ?> WIB</div>
                                </div>
                            </div>
                            <div style="font-size:14px; color:#c4a882; letter-spacing:1px;">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?= $i <= (int)$ul['rating'] ? '★' : '☆' ?>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <?php if (!empty($ul['komentar'])): ?>
                            <div style="font-size:13px; color:#5a4a3a; line-height:1.7; font-weight:300; padding-left:52px;">
                                "<?= htmlspecialchars($ul['komentar']) ?>"
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div><!-- /panel-ulasan -->

    </div><!-- /content -->
</div><!-- /main -->


<!-- ══ MODAL: REKAM MEDIS BARU ══ -->
<div class="modal-overlay" id="modal-rm-baru" onclick="closeModalOutside(event,'modal-rm-baru')">
    <div class="modal">
        <h3 class="modal-title">Tambah <em>Rekam Medis</em></h3>
        <p class="modal-sub">Isi data rekam medis pasien dengan lengkap</p>
        <form method="POST" action="../../backend/dokter/simpan_rekam_medis.php">
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Nama Pasien</label>
                <select class="form-select" name="pasien_id" id="modal-pasien-select">
                    <?php foreach ($pasienDropdown as $pd): ?>
                        <option value="<?= (int)$pd['id'] ?>">
                            <?= htmlspecialchars($pd['nama']) ?>
                            <?= !empty($pd['no_rekam']) ? '(' . htmlspecialchars($pd['no_rekam']) . ')' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal Kunjungan</label>
                <input class="form-input" type="date" name="tanggal" value="<?= $today ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Treatment</label>
                <select class="form-select" name="treatment">
                    <option>Facelift</option>
                    <option>Rhinoplasty</option>
                    <option>Blepharoplasty</option>
                    <option>Body Contouring</option>
                    <option>Konsultasi</option>
                    <option>Follow-up</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Ruangan</label>
                <select class="form-select" name="ruangan">
                    <option>Ruang A-1</option>
                    <option>Ruang A-2</option>
                    <option>Ruang Operasi B</option>
                </select>
            </div>
            <div class="form-group full">
                <label class="form-label">Anamnesis / Keluhan Pasien</label>
                <textarea class="form-textarea" name="anamnesis" placeholder="Tulis keluhan dan riwayat penyakit pasien..."></textarea>
            </div>
            <div class="form-group full">
                <label class="form-label">Hasil Pemeriksaan &amp; Tindakan</label>
                <textarea class="form-textarea" name="pemeriksaan" placeholder="Hasil pemeriksaan fisik, tindakan yang dilakukan, dosis obat..."></textarea>
            </div>
            <div class="form-group full">
                <label class="form-label">Rencana Tindak Lanjut</label>
                <textarea class="form-textarea" name="tindak_lanjut" placeholder="Follow-up, obat yang diberikan, jadwal kunjungan berikutnya..."></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option>Selesai</option>
                    <option>Dalam Perawatan</option>
                    <option>Follow-up Diperlukan</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Jadwal Follow-up</label>
                <input class="form-input" type="date" name="jadwal_followup">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-outline" onclick="closeModal('modal-rm-baru')">Batal</button>
            <button type="submit" class="btn-primary">Simpan Rekam Medis</button>
        </div>
        </form>
    </div>
</div>

<!-- ══ MODAL: EDIT REKAM MEDIS ══ -->
<div class="modal-overlay" id="modal-rm-edit" onclick="closeModalOutside(event,'modal-rm-edit')">
    <div class="modal">
        <h3 class="modal-title">Edit <em>Rekam Medis</em></h3>
        <p class="modal-sub" id="modal-edit-sub">Perbarui data rekam medis</p>
        <form method="POST" action="../../backend/dokter/update_rekam_medis.php">
        <input type="hidden" name="rm_id" id="edit-rm-id">
        <div class="form-group">
            <label class="form-label">Anamnesis / Keluhan Pasien</label>
            <textarea class="form-textarea" name="anamnesis" id="edit-anamnesis"></textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Hasil Pemeriksaan &amp; Tindakan</label>
            <textarea class="form-textarea" name="pemeriksaan" id="edit-pemeriksaan"></textarea>
        </div>
        <div class="form-group">
            <label class="form-label">Rencana Tindak Lanjut</label>
            <textarea class="form-textarea" name="tindak_lanjut" id="edit-tindak-lanjut"></textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Status</label>
                <select class="form-select" name="status" id="edit-status">
                    <option>Selesai</option>
                    <option>Dalam Perawatan</option>
                    <option>Follow-up Diperlukan</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Jadwal Follow-up</label>
                <input class="form-input" type="date" name="jadwal_followup" id="edit-followup">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-outline" onclick="closeModal('modal-rm-edit')">Batal</button>
            <button type="submit" class="btn-primary">Perbarui</button>
        </div>
        </form>
    </div>
</div>

<!-- ══ MODAL: HAPUS REKAM MEDIS ══ -->
<div class="modal-overlay" id="modal-hapus-rm" onclick="closeModalOutside(event,'modal-hapus-rm')">
    <div class="modal" style="max-width:420px; text-align:center;">
        <div style="width:56px;height:56px;background:#fce4df;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 18px;font-size:24px;">🗑️</div>
        <h3 class="modal-title">Hapus <em>Rekam Medis</em>?</h3>
        <p class="modal-sub" id="hapus-rm-desc">Data ini akan dihapus permanen dan tidak dapat dikembalikan.</p>
        <form method="POST" action="../../backend/dokter/hapus_rekam_medis.php">
            <input type="hidden" name="rm_id" id="hapus-rm-id">
            <div class="modal-footer" style="justify-content:center; gap:12px; margin-top:24px;">
                <button type="button" class="btn-outline" onclick="closeModal('modal-hapus-rm')">Batal</button>
                <button type="submit" style="background:#e05050; color:#fff; border:none; padding:10px 24px; border-radius:50px; font-size:11px; letter-spacing:1.5px; text-transform:uppercase; font-family:'DM Sans',sans-serif; cursor:pointer;">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"><span id="toast-msg">Berhasil disimpan</span></div>

    <script src="../../asset/js/dokter.js?v=7"></script>
    <!-- LOGOUT CONFIRMATION MODAL -->
    <div id="logout-modal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.45);backdrop-filter:blur(4px);align-items:center;justify-content:center">
        <div style="background:#fff;border-radius:20px;padding:40px 36px;width:360px;text-align:center;box-shadow:0 24px 64px rgba(0,0,0,0.18);animation:logoutFadeIn .25s ease">
            <div style="width:56px;height:56px;background:#fef0f0;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:22px">&#x23FB;</div>
            <div style="font-family:'Playfair Display',serif;font-size:20px;color:#2D3436;margin-bottom:10px">Yakin ingin <em>keluar</em>?</div>
            <p style="font-size:13px;color:#64748b;margin-bottom:28px;line-height:1.6">Sesi Anda sebagai <strong>Dokter</strong> akan diakhiri. Anda perlu login kembali untuk mengakses portal.</p>
            <div style="display:flex;gap:12px;justify-content:center">
                <button onclick="hideLogoutModal()" style="flex:1;padding:11px;border:1.5px solid #d1c4b8;border-radius:50px;background:#fff;color:#64748b;font-size:12px;font-weight:500;letter-spacing:1px;text-transform:uppercase;cursor:pointer;font-family:'DM Sans',sans-serif">Batal</button>
                <a href="../../backend/auth/logout.php" style="flex:1;padding:11px;border-radius:50px;background:#e05050;color:#fff;font-size:12px;font-weight:500;letter-spacing:1px;text-transform:uppercase;cursor:pointer;font-family:'DM Sans',sans-serif;text-decoration:none;display:flex;align-items:center;justify-content:center">Ya, Keluar</a>
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
        fetch('../../backend/notifikasi/notif_count.php')
            .then(r => r.json())
            .then(data => {
                const btn = document.querySelector('.notif-btn-text');
                if (!btn) return;
                // Hapus dot lama
                const oldDot = btn.querySelector('.notif-dot');
                if (oldDot) oldDot.remove();

                if (data.count > 0) {
                    const dot = document.createElement('span');
                    dot.className = 'notif-dot';
                    dot.style.cssText = 'display: inline-block; width: 6px; height: 6px; background: #735a39; border-radius: 50%;';
                    btn.appendChild(dot);
                }

                const sidebarBadge = document.getElementById('notif-badge-sidebar');
                if (sidebarBadge) {
                    if (data.count > 0) {
                        sidebarBadge.textContent = data.count;
                        sidebarBadge.style.display = 'inline-block';
                    } else {
                        sidebarBadge.style.display = 'none';
                    }
                }
            })
            .catch(() => {});
    }
    // Poll setiap 10 detik
    pollNotifications();
    setInterval(pollNotifications, 10000);
    </script>

    <!-- Page Routing, Notification Fadeout and URL parameter cleanup -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Parse URL params for routing
        const params = new URLSearchParams(window.location.search);
        const page = params.get('page');
        if (page) {
            const btn = document.querySelector(`.sidebar-nav .nav-item[onclick*="${page}"]`);
            if (btn) {
                showPanel(page, btn);
            }
        }

        // Auto fadeout alert notifications and clear success/error params from URL after 10 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert-notification').forEach(el => {
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            });
        }, 10000);

        const url = new URL(window.location.href);
        if (url.searchParams.has('success') || url.searchParams.has('error')) {
            url.searchParams.delete('success');
            url.searchParams.delete('error');
            window.history.replaceState({}, document.title, url.pathname + url.search);
        }
    });
    </script>
</body>
</html>