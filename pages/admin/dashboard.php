<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/Signin.php');
    exit;
}
$conn = require_once '../../backend/koneksi.php';

// Auto-migrasi tabel pengumuman
try {
    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `pengumuman` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `judul` VARCHAR(255) NOT NULL,
        `konten` TEXT NOT NULL,
        `target` ENUM('Semua', 'Pasien', 'Dokter') NOT NULL DEFAULT 'Semua',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
} catch (Exception $e) {
    // Abaikan error pembuatan tabel
}

// ── Statistik ──
$total_pasien     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM pasien"))['n'];
$dokter_aktif     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM dokter WHERE status='Aktif'"))['n'];
$appt_hari_ini    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM appointment WHERE tanggal=CURDATE()"))['n'];

$pendapatan_bulan = 0;
try {
    $r_pendapatan = mysqli_query($conn, "SELECT COALESCE(SUM(jumlah),0) AS n FROM keuangan WHERE jenis='Pemasukan' AND MONTH(tanggal)=MONTH(NOW()) AND YEAR(tanggal)=YEAR(NOW())");
    if ($r_pendapatan) {
        $pendapatan_bulan = mysqli_fetch_assoc($r_pendapatan)['n'];
    }
} catch (Exception $e) {
    try {
        $r_pembayaran = mysqli_query($conn, "SELECT COALESCE(SUM(jumlah),0) AS n FROM pembayaran WHERE status='Lunas' AND MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW())");
        if ($r_pembayaran) {
            $pendapatan_bulan = mysqli_fetch_assoc($r_pembayaran)['n'];
        }
    } catch (Exception $ex) {}
}

// ── Data per panel ──
$appt_today   = mysqli_query($conn, "SELECT a.id,a.jam,a.status,p.nama AS nama_pasien,d.nama AS nama_dokter,t.nama AS nama_treatment FROM appointment a JOIN pasien p ON a.pasien_id=p.id JOIN dokter d ON a.dokter_id=d.id LEFT JOIN treatment t ON a.treatment_id=t.id WHERE a.tanggal=CURDATE() ORDER BY a.jam ASC LIMIT 10");
$aktivitas    = mysqli_query($conn, "SELECT * FROM log_aktivitas ORDER BY created_at DESC LIMIT 5");
$pasien_list  = mysqli_query($conn, "SELECT p.*, (SELECT t.nama FROM appointment a2 LEFT JOIN treatment t ON t.id=a2.treatment_id WHERE a2.pasien_id=p.id ORDER BY a2.id DESC LIMIT 1) AS treatment_terakhir FROM pasien p ORDER BY p.id DESC LIMIT 100");
$dokter_list  = mysqli_query($conn, "SELECT * FROM dokter ORDER BY id ASC");
$jadwal_list  = mysqli_query($conn, "SELECT j.*,d.nama AS nama_dokter,d.foto AS foto_dokter,t.nama AS nama_treatment FROM jadwal_dokter j JOIN dokter d ON j.dokter_id=d.id LEFT JOIN treatment t ON j.treatment_id=t.id ORDER BY d.nama ASC");
$log_list     = mysqli_query($conn, "SELECT * FROM log_aktivitas ORDER BY created_at DESC LIMIT 50");
$treatment_list = mysqli_query($conn, "SELECT * FROM treatment ORDER BY urutan ASC");
// Laporan filter
$lap_bulan = (int)($_GET['lap_bulan'] ?? date('n'));
$lap_tahun = (int)($_GET['lap_tahun'] ?? date('Y'));
$laporan   = mysqli_query($conn, "SELECT d.nama AS nama_dokter,
    COUNT(a.id) AS total_appt,
    COALESCE(SUM(a.status='Selesai'),0) AS selesai,
    COALESCE(SUM(a.status='Dibatalkan'),0) AS batal,
    COALESCE(SUM(a.status='Berlangsung'),0) AS berlangsung,
    COALESCE(SUM(py.jumlah),0) AS pendapatan,
    d.spesialisasi, d.rating
    FROM dokter d
    LEFT JOIN appointment a ON a.dokter_id=d.id
        AND MONTH(a.tanggal)=$lap_bulan AND YEAR(a.tanggal)=$lap_tahun
    LEFT JOIN pembayaran py ON py.appointment_id=a.id AND py.status='Lunas'
    GROUP BY d.id,d.nama,d.spesialisasi,d.rating
    ORDER BY pendapatan DESC");

// Auto-migrasi kolom balasan: hanya ALTER jika kolom belum ada
try {
    $_db = mysqli_fetch_assoc(mysqli_query($conn, "SELECT DATABASE() AS db"))['db'];
    $_cols_exist = [];
    $_chk = mysqli_query($conn, "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA='$_db' AND TABLE_NAME='pesan_kontak'
        AND COLUMN_NAME IN ('balasan','dibalas_at','dibalas_oleh')");
    while ($_r = mysqli_fetch_assoc($_chk)) $_cols_exist[] = $_r['COLUMN_NAME'];
    if (!in_array('balasan',    $_cols_exist)) mysqli_query($conn, "ALTER TABLE `pesan_kontak` ADD COLUMN `balasan` TEXT NULL AFTER `sudah_baca`");
    if (!in_array('dibalas_at', $_cols_exist)) mysqli_query($conn, "ALTER TABLE `pesan_kontak` ADD COLUMN `dibalas_at` TIMESTAMP NULL AFTER `balasan`");
    if (!in_array('dibalas_oleh',$_cols_exist)) mysqli_query($conn, "ALTER TABLE `pesan_kontak` ADD COLUMN `dibalas_oleh` VARCHAR(100) NULL AFTER `dibalas_at`");
    unset($_db, $_cols_exist, $_chk, $_r);
} catch (Exception $e) {
    // Abaikan error migrasi jika kolom/tabel belum siap
}

$pesan_list  = mysqli_query($conn, "SELECT * FROM pesan_kontak ORDER BY created_at DESC LIMIT 50");
$pesan_belum = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM pesan_kontak WHERE sudah_baca=0"))['n'];
$admin        = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=".(int)$_SESSION['user_id']));
$pengumuman_list = mysqli_query($conn, "SELECT * FROM pengumuman ORDER BY created_at DESC LIMIT 50");

// ── Keuangan ──
$keu_bulan = (int)($_GET['keu_bulan'] ?? date('n'));
$keu_tahun = (int)($_GET['keu_tahun'] ?? date('Y'));
$keu_filter_jenis = $_GET['keu_jenis'] ?? '';
// Pastikan tabel ada (aman jika belum di-setup)
try {
    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `keuangan` (
      `id` INT AUTO_INCREMENT PRIMARY KEY, `tanggal` DATE NOT NULL,
      `jenis` ENUM('Pemasukan','Pengeluaran') NOT NULL DEFAULT 'Pemasukan',
      `kategori` VARCHAR(100) NOT NULL, `keterangan` VARCHAR(255) NOT NULL,
      `jumlah` DECIMAL(15,2) NOT NULL DEFAULT 0, `metode` VARCHAR(50) DEFAULT 'Tunai',
      `referensi` VARCHAR(100) NULL, `catatan` TEXT NULL,
      `dibuat_oleh` INT NULL, `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (`dibuat_oleh`) REFERENCES `users`(`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
} catch (Exception $e) {
    // Abaikan error jika pembuatan tabel gagal
}

$keu_where = "MONTH(tanggal)=$keu_bulan AND YEAR(tanggal)=$keu_tahun";
if ($keu_filter_jenis === 'Pemasukan' || $keu_filter_jenis === 'Pengeluaran') {
    $keu_where .= " AND jenis='" . mysqli_real_escape_string($conn, $keu_filter_jenis) . "'";
}

try {
    $keu_list = mysqli_query($conn, "SELECT * FROM keuangan WHERE $keu_where ORDER BY tanggal DESC, id DESC");
    $keu_stats = mysqli_fetch_assoc(mysqli_query($conn, "SELECT
        COALESCE(SUM(CASE WHEN jenis='Pemasukan' THEN jumlah ELSE 0 END),0) AS total_masuk,
        COALESCE(SUM(CASE WHEN jenis='Pengeluaran' THEN jumlah ELSE 0 END),0) AS total_keluar,
        COUNT(*) AS total_trx
        FROM keuangan WHERE MONTH(tanggal)=$keu_bulan AND YEAR(tanggal)=$keu_tahun"));
    $keu_saldo = $keu_stats['total_masuk'] - $keu_stats['total_keluar'];
} catch (Exception $e) {
    $keu_list = false;
    $keu_stats = ['total_masuk' => 0, 'total_keluar' => 0, 'total_trx' => 0];
    $keu_saldo = 0;
}

// Daftar dokter & treatment untuk select di modal
$dlist_modal  = mysqli_query($conn, "SELECT id,nama FROM dokter WHERE status='Aktif' ORDER BY nama");
$tlist_modal  = mysqli_query($conn, "SELECT id,nama FROM treatment WHERE status='Aktif' ORDER BY urutan");

function rupiah(float $n): string {
    if ($n >= 1_000_000) return 'Rp '.number_format($n/1_000_000,1,',','.').' Jt';
    return 'Rp '.number_format($n,0,',','.');
}
function badge_appt(string $s): string {
    switch ($s) {
        case 'Selesai':
            return '<span class="badge badge-green">Selesai</span>';
        case 'Berlangsung':
            return '<span class="badge badge-yellow">Berlangsung</span>';
        case 'Terjadwal':
            return '<span class="badge badge-gray">Terjadwal</span>';
        case 'Dibatalkan':
            return '<span class="badge badge-pink">Dibatalkan</span>';
        default:
            return '<span class="badge badge-gray">'.$s.'</span>';
    }
}
$bln_ind = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$hari_ind = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
$tgl_now  = $hari_ind[date('w')].', '.date('d').' '.$bln_ind[(int)date('n')].' '.date('Y');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>GlowCare Admin Dashboard</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@300;400;500&display=swap">
    <link rel="stylesheet" href="../../asset/css/admin.css?v=11">
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
        .sidebar-user .user-avatar {
            background: #e0c097 !important;
            color: #4a321f !important;
        }
        .sidebar-user .user-info .name {
            color: #ffffff !important;
        }
        .sidebar-user .user-info .label {
            color: #a89a8a !important;
        }
        .topbar {
            background: #4a321f !important;
            border-bottom: 1px solid rgba(255,255,255,0.08) !important;
        }
        .topbar-left {
            display: none !important;
        }
        .topbar-left .page-title {
            color: #ffffff !important;
        }
        .topbar-left .breadcrumb {
            color: rgba(255,255,255,0.6) !important;
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
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="brand">GlowCare Clinic</div>
            <div class="role">Admin Panel</div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section-label">Utama</div>
            <a class="nav-item active" onclick="showPanel('dashboard',this)">Dashboard</a>
            <a class="nav-item" onclick="showPanel('aktivitas',this)">
                Aktivitas
                <?php
                $time_cond = isset($_SESSION['last_view_log_time']) 
                    ? "'" . mysqli_real_escape_string($conn, $_SESSION['last_view_log_time']) . "'" 
                    : "DATE(NOW())";
                $unread = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM log_aktivitas WHERE created_at > $time_cond"))['n'];
                if ($unread > 0) {
                    echo "<span class='nav-badge' id='aktivitas-badge'>$unread</span>";
                }
                ?>
            </a>
            <div class="nav-section-label">Manajemen</div>
            <a class="nav-item" onclick="showPanel('pasien',this)">Data Pasien</a>
            <a class="nav-item" onclick="showPanel('dokter',this)">Data Dokter</a>
            <a class="nav-item" onclick="showPanel('jadwal',this)">Jadwal Dokter</a>
            <div class="nav-section-label">Konten</div>
            <a class="nav-item" onclick="showPanel('treatment',this)">Treatment</a>
            <div class="nav-section-label">Pesan</div>
            <a class="nav-item" onclick="showPanel('pesan',this)">
                Pesan Kontak
                <?php if ($pesan_belum > 0): ?>
                    <span class="nav-badge"><?= $pesan_belum ?></span>
                <?php endif; ?>
            </a>
            <a class="nav-item" onclick="showPanel('pengumuman',this)">Pengumuman</a>
            <div class="nav-section-label">Laporan</div>
            <a class="nav-item" onclick="showPanel('laporan',this)">Laporan</a>
            <a class="nav-item" onclick="showPanel('keuangan',this)">Keuangan</a>
            <div class="nav-section-label">Akun</div>
            <a class="nav-item" onclick="showPanel('profil',this)">Profil</a>
        </nav>
        <div class="sidebar-footer">
            <div class="sidebar-user" onclick="showPanel('profil',document.querySelector('[onclick*=profil]'))">
                <div class="user-avatar"><?= strtoupper(substr($admin['username'],0,1)) ?></div>
                <div class="user-info">
                    <div class="name"><?= htmlspecialchars($admin['username']) ?></div>
                    <div class="label">Administrator</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- MAIN -->
    <div class="main">
        <div class="topbar" style="justify-content: flex-end;">
            <div class="topbar-left">
                <div class="page-title" id="topbar-title">Dashboard</div>
                <div class="breadcrumb" id="topbar-bc">GlowCare Admin → Dashboard</div>
            </div>
            <div class="topbar-right">
                <a href="#" onclick="showLogoutModal(); return false;" class="logout-btn" style="width: auto !important; padding: 6px 16px;">Logout</a>
            </div>
        </div>

        <div class="content">

        <!-- ══ PANEL DASHBOARD ══ -->
        <div class="panel active" id="panel-dashboard">
            <!-- Hero banner -->
            <div class="hero-banner" style="margin: -36px -40px 36px -40px;">
                <div class="hero-greeting"><?= ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'][date('w')] ?>, <?= date('d M Y') ?></div>
                <div class="hero-name">Halo, <em><?= htmlspecialchars($admin['username']) ?></em></div>
                <div class="hero-sub">Selamat datang kembali di GlowCare Clinic. Yuk, kelola operasional klinik dengan baik.</div>
            </div>
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-icon"></div>
                    <div class="stat-value"><?= number_format($total_pasien) ?></div>
                    <div class="stat-label">Total Pasien</div>
                </div>
                <div class="stat-card green">
                    <div class="stat-icon"></div>
                    <div class="stat-value"><?= $appt_hari_ini ?></div>
                    <div class="stat-label">Janji Hari Ini</div>
                </div>
                <div class="stat-card purple">
                    <div class="stat-icon"></div>
                    <div class="stat-value"><?= $dokter_aktif ?></div>
                    <div class="stat-label">Dokter Aktif</div>
                </div>
                <div class="stat-card orange">
                    <div class="stat-icon"></div>
                    <div class="stat-value"><?= rupiah((float)$pendapatan_bulan) ?></div>
                    <div class="stat-label">Pendapatan Bulan Ini</div>
                </div>
            </div>
            <div class="two-col">
                <div class="card">
                    <div class="card-header"><div class="card-title">Kunjungan Pasien — <?= date('Y') ?></div></div>
                    <div class="chart-area">
                        <?php
                        $bulan_lbl=['Jan','Feb','Mar','Apr','Mei','Jun'];
                        $chart_data=[];
                        for($m=1;$m<=6;$m++){$r=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS n FROM appointment WHERE MONTH(tanggal)=$m AND YEAR(tanggal)=".date('Y')));$chart_data[$m]=(int)$r['n'];}
                        $max_val=max(array_values($chart_data))?:1;
                        ?>
                        <div class="chart-bars">
                        <?php for($m=1;$m<=6;$m++): $h=max(round(($chart_data[$m]/$max_val)*100),5); ?>
                            <div class="chart-bar-wrap">
                                <div class="chart-bar" style="height:<?=$h?>%" title="<?=$chart_data[$m]?> kunjungan"></div>
                                <div class="chart-label"><?=$bulan_lbl[$m-1]?></div>
                            </div>
                        <?php endfor; ?>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Aktivitas Terkini</div>
                        <a class="card-action" onclick="showPanel('aktivitas',document.querySelector('[onclick*=aktivitas]'))">Semua →</a>
                    </div>
                    <div class="activity-list">
                    <?php mysqli_data_seek($aktivitas,0); while($ak=mysqli_fetch_assoc($aktivitas)): ?>
                        <div class="activity-item">
                            <div class="activity-dot"></div>
                            <div>
                                <div class="activity-text"><strong><?= htmlspecialchars($ak['judul']) ?></strong> — <?= htmlspecialchars($ak['deskripsi']) ?></div>
                                <div class="activity-time"><?= date('d M Y, H:i',strtotime($ak['created_at'])) ?></div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><div class="card-title">Appointment Hari Ini</div></div>
                <table class="data-table">
                    <thead><tr><th>Pasien</th><th>Dokter</th><th>Treatment</th><th>Jam</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php if(mysqli_num_rows($appt_today)===0): ?>
                        <tr><td colspan="5" style="text-align:center;color:#7a7571;padding:24px">Tidak ada appointment hari ini.</td></tr>
                    <?php else: while($ap=mysqli_fetch_assoc($appt_today)): ?>
                        <tr>
                            <td><span class="avatar"><?= strtoupper(substr($ap['nama_pasien'],0,1)) ?></span><?= htmlspecialchars($ap['nama_pasien']) ?></td>
                            <td><?= htmlspecialchars($ap['nama_dokter']) ?></td>
                            <td><?= htmlspecialchars($ap['nama_treatment']??'-') ?></td>
                            <td><?= date('H:i',strtotime($ap['jam'])) ?></td>
                            <td><?= badge_appt($ap['status']) ?></td>
                        </tr>
                    <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ══ PANEL PASIEN ══ -->
        <div class="panel" id="panel-pasien">
            <div class="page-header">
                <div><h2 class="section-title">Data <em>Pasien</em></h2><p class="section-sub">Kelola seluruh data pasien terdaftar</p></div>
                <button class="btn-add" onclick="openModal('modal-pasien')">+ Tambah Pasien</button>
            </div>
            <div class="card">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No. Pasien</th>
                            <th>Nama</th>
                            <th>Kontak</th>
                            <th>Treatment Terakhir</th>
                            <th>Kunjungan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php mysqli_data_seek($pasien_list,0); while($p=mysqli_fetch_assoc($pasien_list)):
                        $usia=$p['tanggal_lahir']?(date('Y')-date('Y',strtotime($p['tanggal_lahir']))).''  :'-'; ?>
                        <tr>
                            <td style="color:#7a7571;font-size:11px">#<?= htmlspecialchars($p['no_pasien']) ?></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px">
                                    <span class="avatar"><?= strtoupper(substr($p['nama'],0,1)) ?></span>
                                    <div>
                                        <div class="td-name"><?= htmlspecialchars($p['nama']) ?></div>
                                        <span class="td-sub"><?= $usia ?> Thn · <?= $p['jenis_kelamin'] ?></span>
                                    </div>
                                </div>
                            </td>
                            <td><div><?= htmlspecialchars($p['telepon']??'-') ?></div><div style="font-size:11px;color:#7a7571"><?= htmlspecialchars($p['email']??'-') ?></div></td>
                            <td><?= htmlspecialchars($p['treatment_terakhir']??'-') ?></td>
                            <td style="text-align:center"><?= $p['total_kunjungan'] ?></td>
                            <td><?= $p['status']==='Aktif'?'<span class="badge badge-green">Aktif</span>':'<span class="badge badge-gray">Tidak Aktif</span>' ?></td>
                            <td>
                                <button class="act-btn" style="border: 1px solid #d1c4b8; padding: 4px 10px; margin-right: 4px;" onclick="editPasien(<?= htmlspecialchars(json_encode($p)) ?>)" title="Edit">Edit</button>
                                <button class="act-btn" style="border: 1px solid #e05050; color: #e05050; padding: 4px 10px;" onclick="confirmDelete('hapus_pasien.php','<?= $p['id'] ?>','pasien <?= htmlspecialchars(addslashes($p['nama'])) ?>')" title="Hapus">Hapus</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
                <div style="padding:14px 18px;border-top:1px solid #F9F7F2;font-size:12px;color:#7a7571">Total <?= number_format($total_pasien) ?> pasien terdaftar</div>
            </div>
        </div>

        <!-- ══ PANEL DOKTER ══ -->
        <div class="panel" id="panel-dokter">
            <div class="page-header">
                <div><h2 class="section-title">Data <em>Dokter</em></h2><p class="section-sub">Kelola profil dan spesialisasi dokter</p></div>
                <button class="btn-add" onclick="document.getElementById('form-dokter').reset(); document.getElementById('md-id').value=''; document.getElementById('md-current-foto').value=''; document.getElementById('md-foto').required=true; openModal('modal-dokter')">+ Tambah Dokter</button>
            </div>
            <div class="card">
                <table class="data-table">
                    <thead><tr><th>ID</th><th>Nama</th><th>Spesialisasi</th><th>Pengalaman</th><th>Total Pasien</th><th>Rating</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                    <?php mysqli_data_seek($dokter_list,0); while($d=mysqli_fetch_assoc($dokter_list)):
                        switch($d['status']){
                            case 'Aktif': $badge_d='badge-green'; break;
                            case 'Cuti': $badge_d='badge-yellow'; break;
                            default: $badge_d='badge-gray'; break;
                        } ?>
                        <tr>
                            <td style="color:#7a7571;font-size:11px">#D-00<?= $d['id'] ?></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px">
                                    <?php
                                    $foto = !empty($d['foto']) 
                                        ? (strpos($d['foto'], 'http') === 0 || strpos($d['foto'], 'asset/') === 0 ? $d['foto'] : '../../backend/uploads/' . $d['foto']) 
                                        : '';
                                    if ($foto): ?>
                                        <img class="avatar" src="<?= htmlspecialchars($foto) ?>" style="object-fit:cover; width:30px; height:30px; border-radius:50%; margin-right:8px;" alt="<?= htmlspecialchars($d['nama']) ?>">
                                    <?php else: ?>
                                        <span class="avatar"><?= strtoupper(substr($d['nama'],0,1)) ?></span>
                                    <?php endif; ?>
                                    <span class="td-name"><?= htmlspecialchars($d['nama']) ?></span>
                                </div>
                            </td>
                            <td><span class="badge badge-pink"><?= htmlspecialchars($d['spesialisasi']) ?></span></td>
                            <td><?= $d['pengalaman'] ?>+ Thn</td>
                            <td style="text-align:center"><?= number_format($d['total_pasien']) ?></td>
                            <td>⭐ <?= number_format($d['rating'],1) ?></td>
                            <td><span class="badge <?= $badge_d ?>"><?= $d['status'] ?></span></td>
                            <td>
                                <button class="act-btn" style="border: 1px solid #d1c4b8; padding: 4px 10px; margin-right: 4px;" onclick="editDokter(<?= htmlspecialchars(json_encode($d)) ?>)" title="Edit">Edit</button>
                                <button class="act-btn" style="border: 1px solid #e05050; color: #e05050; padding: 4px 10px;" onclick="confirmDelete('hapus_dokter.php','<?= $d['id'] ?>','dokter <?= htmlspecialchars(addslashes($d['nama'])) ?>')" title="Hapus">Hapus</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ══ PANEL JADWAL ══ -->
        <div class="panel" id="panel-jadwal">
            <div class="page-header">
                <div>
                    <h2 class="section-title">Jadwal <em>Dokter</em></h2>
                    <p class="section-sub">Atur jadwal praktik seluruh dokter</p>
                </div>
                <button class="btn-add" onclick="openModal('modal-jadwal')">+ Tambah Jadwal</button>
            </div>

            <?php
            // Group jadwal by doctor
            $grouped = [];
            mysqli_data_seek($jadwal_list, 0);
            while ($j = mysqli_fetch_assoc($jadwal_list)) {
                $did = $j['dokter_id'];
                if (!isset($grouped[$did])) {
                    $grouped[$did] = ['nama' => $j['nama_dokter'], 'foto' => $j['foto_dokter'] ?? '', 'jadwal' => []];
                }
                $grouped[$did]['jadwal'][] = $j;
            }
            $day_order = ['Senin'=>0,'Selasa'=>1,'Rabu'=>2,'Kamis'=>3,'Jumat'=>4,'Sabtu'=>5,'Minggu'=>6];
            ?>

            <?php if (empty($grouped)): ?>
                <div class="card" style="text-align:center;padding:48px;color:#64748b;font-size:14px;">
                    Belum ada jadwal. Klik <strong>+ Tambah Jadwal</strong> untuk menambahkan.
                </div>
            <?php else: ?>
            <div class="jadwal-grid">
                <?php foreach ($grouped as $did => $grp):
                    usort($grp['jadwal'], fn($a,$b) => ($day_order[$a['hari']]??9) - ($day_order[$b['hari']]??9));
                    $initials = strtoupper(substr($grp['nama'], 0, 1));
                    $total_days = count($grp['jadwal']);
                ?>
                <div class="jadwal-doctor-card">
                    <!-- Doctor Header -->
                    <div class="jdc-header">
                        <?php
                        $j_foto = !empty($grp['foto']) 
                            ? (strpos($grp['foto'], 'http') === 0 || strpos($grp['foto'], 'asset/') === 0 ? $grp['foto'] : '../../backend/uploads/' . $grp['foto']) 
                            : '';
                        if ($j_foto): ?>
                            <img class="jdc-avatar" src="<?= htmlspecialchars($j_foto) ?>" style="object-fit:cover; width:44px; height:44px; border-radius:50%; margin-right:16px;" alt="<?= htmlspecialchars($grp['nama']) ?>">
                        <?php else: ?>
                            <div class="jdc-avatar"><?= $initials ?></div>
                        <?php endif; ?>
                        <div class="jdc-info">
                            <div class="jdc-name"><?= htmlspecialchars($grp['nama']) ?></div>
                            <div class="jdc-meta"><?= $total_days ?> hari praktik / minggu</div>
                        </div>
                        <div class="jdc-days-chips">
                            <?php foreach ($grp['jadwal'] as $jj): ?>
                                <span class="day-chip"><?= htmlspecialchars(substr($jj['hari'], 0, 3)) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Schedule Rows -->
                    <div class="jdc-body">
                        <div class="jdc-table-header">
                            <span>Hari</span>
                            <span>Jam Praktik</span>
                            <span>Treatment</span>
                            <span style="text-align:center">Max Pasien</span>
                            <span style="text-align:center">Status</span>
                            <span style="text-align:right">Aksi</span>
                        </div>
                        <?php foreach ($grp['jadwal'] as $jj): ?>
                        <div class="jdc-row">
                            <span class="jdc-hari"><?= htmlspecialchars($jj['hari']) ?></span>
                            <span class="jdc-jam">
                                <span class="jam-badge"><?= date('H:i', strtotime($jj['jam_mulai'])) ?></span>
                                <span class="jam-sep">→</span>
                                <span class="jam-badge"><?= date('H:i', strtotime($jj['jam_selesai'])) ?></span>
                            </span>
                            <span class="jdc-treatment"><?= htmlspecialchars($jj['nama_treatment'] ?? 'Semua Treatment') ?></span>
                            <span style="text-align:center;font-size:13px;color:#2D3436;font-weight:500"><?= $jj['max_pasien'] ?></span>
                            <span style="text-align:center">
                                <?php $st = $jj['status'] ?? 'Aktif'; ?>
                                <span class="badge <?= $st === 'Aktif' ? 'badge-green' : 'badge-gray' ?>"><?= htmlspecialchars($st) ?></span>
                            </span>
                            <span class="jdc-actions">
                                <button class="act-btn jdc-btn-edit" onclick="editJadwal(<?= htmlspecialchars(json_encode($jj)) ?>)" title="Edit">Edit</button>
                                <button class="act-btn jdc-btn-hapus" onclick="confirmDelete('hapus_jadwal.php','<?= $jj['id'] ?>','jadwal ini')" title="Hapus">Hapus</button>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- ══ PANEL TREATMENT ══ -->
        <div class="panel" id="panel-treatment">
            <div class="page-header">
                <div><h2 class="section-title">Kelola <em>Treatment</em></h2><p class="section-sub">Data treatment yang tampil di halaman utama</p></div>
                <button class="btn-add" onclick="openModal('modal-treatment')">+ Tambah Treatment</button>
            </div>
            <div class="card">
                <table class="data-table">
                    <thead><tr><th>No</th><th>Gambar</th><th>Nama</th><th>Kategori</th><th>Durasi</th><th>Deskripsi</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                    <?php mysqli_data_seek($treatment_list,0); while($tr=mysqli_fetch_assoc($treatment_list)):
                        $img=$tr['gambar_url']?:($tr['gambar_file']?'../../uploads/treatment/'.$tr['gambar_file']:''); ?>
                        <tr>
                            <td style="color:#7a7571;font-size:11px"><?= $tr['urutan'] ?></td>
                            <td>
                                <?php if($img): ?>
                                    <img src="<?= htmlspecialchars($img) ?>" class="treatment-thumb">
                                <?php else: ?>
                                    <div style="width:50px;height:38px;background:#d1c4b8;border-radius:6px;display:flex;align-items:center;justify-content:center">💉</div>
                                <?php endif; ?>
                            </td>
                            <td><div class="td-name"><?= htmlspecialchars($tr['nama']) ?></div></td>
                            <td><span class="badge badge-pink"><?= htmlspecialchars($tr['kategori']) ?></span></td>
                            <td style="font-size:13px;font-weight:500"><?= htmlspecialchars($tr['durasi']??'') ?></td>
                            <td style="max-width:200px;font-size:12px;color:#585552"><?= htmlspecialchars(mb_substr($tr['deskripsi_panjang']??$tr['deskripsi']??'',0,70)) ?>...</td>
                            <td><?= $tr['status']==='Aktif'?'<span class="badge badge-green">Aktif</span>':'<span class="badge badge-gray">Nonaktif</span>' ?></td>
                            <td style="white-space:nowrap">
                                <a class="act-btn" style="border: 1px solid #386663; color: #386663; padding: 4px 10px; margin-right: 4px; text-decoration:none; display:inline-block;" href="../../detail_treatment.php?id=<?= $tr['id'] ?>" target="_blank" title="Preview">Preview</a>
                                <button class="act-btn" style="border: 1px solid #d1c4b8; padding: 4px 10px; margin-right: 4px;" onclick="editTreatment(<?= htmlspecialchars(json_encode($tr)) ?>)" title="Edit">Edit</button>
                                <button class="act-btn" style="border: 1px solid #e05050; color: #e05050; padding: 4px 10px;" onclick="confirmDelete('hapus_treatment.php','<?= $tr['id'] ?>','treatment <?= htmlspecialchars(addslashes($tr['nama'])) ?>')" title="Hapus">Hapus</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ══ PANEL AKTIVITAS ══ -->
        <div class="panel" id="panel-aktivitas">
            <h2 class="section-title">Log <em>Aktivitas</em></h2>
            <p class="section-sub">Seluruh aktivitas sistem tercatat di sini</p>
            <div class="card">
                <div class="card-header"><div class="card-title">Riwayat Aktivitas</div></div>
                <div style="padding:0 22px 22px">
                <?php mysqli_data_seek($log_list,0); while($lg=mysqli_fetch_assoc($log_list)): ?>
                    <div class="activity-item">
                        <div class="activity-dot"></div>
                        <div style="flex:1">
                            <div class="activity-text"><strong><?= htmlspecialchars($lg['judul']) ?></strong><?= $lg['deskripsi']?' — '.htmlspecialchars($lg['deskripsi']):'' ?></div>
                            <div class="activity-time"><?= date('d M Y, H:i',strtotime($lg['created_at'])) ?></div>
                        </div>
                    </div>
                <?php endwhile; ?>
                </div>
            </div>
        </div>
        <!-- ══ PANEL PESAN KONTAK ══ -->
        <div class="panel" id="panel-pesan">
            <div class="page-header">
                <div>
                    <h2 class="section-title">Pesan <em>Kontak</em></h2>
                    <p class="section-sub">Pesan yang masuk dari form kontak di halaman utama</p>
                </div>
            </div>
            <div class="card">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Pengirim</th>
                            <th>Kontak</th>
                            <th>Pesan</th>
                            <th>Waktu</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (mysqli_num_rows($pesan_list) === 0): ?>
                        <tr>
                            <td colspan="6" style="text-align:center;color:#7a7571;padding:32px">
                                Belum ada pesan masuk.
                            </td>
                        </tr>
                    <?php else:
                        mysqli_data_seek($pesan_list, 0);
                        while ($pk = mysqli_fetch_assoc($pesan_list)):
                            $belum = !$pk['sudah_baca'];
                    ?>
                        <tr style="<?= $belum ? 'background:#fbf9f4;font-weight:500' : '' ?>">
        
                            <!-- Pengirim -->
                            <td>
                                <div style="display:flex;align-items:center;gap:8px">
                                    <span class="avatar"><?= strtoupper(substr($pk['nama'], 0, 1)) ?></span>
                                    <div>
                                        <div class="td-name"><?= htmlspecialchars($pk['nama']) ?></div>
                                        <?php if ($belum): ?>
                                            <span style="font-size:9px;letter-spacing:1px;text-transform:uppercase;color:#735a39">● Baru</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
        
                            <!-- Kontak -->
                            <td>
                                <div><?= htmlspecialchars($pk['telepon'] ?: '-') ?></div>
                                <div style="font-size:11px;color:#7a7571"><?= htmlspecialchars($pk['email'] ?: '-') ?></div>
                            </td>
        
                            <!-- Pesan (preview) -->
                            <td style="max-width:260px">
                                <div style="font-size:12px;color:#2D3436;line-height:1.5;cursor:pointer"
                                    onclick="lihatPesan(<?= htmlspecialchars(json_encode($pk)) ?>)">
                                    <?= htmlspecialchars(mb_substr($pk['pesan'], 0, 80)) ?>
                                    <?= mb_strlen($pk['pesan']) > 80 ? '…' : '' ?>
                                </div>
                            </td>
        
                            <!-- Waktu -->
                            <td style="white-space:nowrap;font-size:12px;color:#7a7571">
                                <?= date('d M Y', strtotime($pk['created_at'])) ?><br>
                                <?= date('H:i', strtotime($pk['created_at'])) ?>
                            </td>
        
                            <!-- Status -->
                            <td>
                                <?php
                                $sudah_dibalas = !empty($pk['balasan']);
                                if ($sudah_dibalas): ?>
                                    <span class="badge badge-green">Dibalas</span>
                                <?php elseif ($belum): ?>
                                    <span class="badge badge-pink">Belum Dibaca</span>
                                <?php else: ?>
                                    <span class="badge" style="background:#e8f9f1;color:#3dab74">Sudah Dibaca</span>
                                <?php endif; ?>
                            </td>
        
                            <!-- Aksi -->
                            <td style="white-space:nowrap">
                                <!-- Tombol Lihat / tandai baca -->
                                <button class="act-btn" style="border: 1px solid #d1c4b8; padding: 4px 10px; margin-right: 4px;"
                                    onclick="lihatPesan(<?= htmlspecialchars(json_encode($pk)) ?>)"
                                    title="Lihat pesan">Lihat</button>
        
                                <?php if ($belum): ?>
                                <!-- Tandai sudah dibaca -->
                                <form method="POST" action="../../backend/admin/kelola_pesan.php"
                                    style="display:inline">
                                    <input type="hidden" name="aksi" value="baca">
                                    <input type="hidden" name="id" value="<?= $pk['id'] ?>">
                                    <button type="submit" class="act-btn" style="border: 1px solid #3dab74; color: #3dab74; padding: 4px 10px;" title="Tandai sudah dibaca">Sudah Dibaca</button>
                                </form>
                                <?php endif; ?>
        
                                <!-- Hapus -->
                                <button class="act-btn" style="border: 1px solid #e05050; color: #e05050; padding: 4px 10px;"
                                        onclick="confirmDelete('kelola_pesan.php','<?= $pk['id'] ?>','pesan dari <?= htmlspecialchars(addslashes($pk['nama'])) ?>')"
                                        title="Hapus">Hapus</button>
                            </td>
                        </tr>
                    <?php endwhile; endif; ?>
                    </tbody>
                </table>
        
                <div style="padding:14px 18px;border-top:1px solid #F9F7F2;font-size:12px;color:#7a7571">
                    Total <?= mysqli_num_rows($pesan_list) ?> pesan
                    <?php if ($pesan_belum > 0): ?>
                        · <span style="color:#735a39;font-weight:600"><?= $pesan_belum ?> belum dibaca</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ══ PANEL PENGUMUMAN ══ -->
        <div class="panel" id="panel-pengumuman">
            <div class="page-header">
                <div>
                    <h2 class="section-title">Kelola <em>Pengumuman</em></h2>
                    <p class="section-sub">Buat pengumuman baru yang akan dikirim sebagai notifikasi ke pasien &amp; dokter</p>
                </div>
                <button class="btn-add" onclick="openModal('modal-pengumuman')">+ Tambah Pengumuman</button>
            </div>
            <div class="card">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Target</th>
                            <th>Konten</th>
                            <th>Tanggal Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!$pengumuman_list || mysqli_num_rows($pengumuman_list) === 0): ?>
                        <tr><td colspan="5" style="text-align:center; color:#7a7571; padding:24px">Belum ada pengumuman.</td></tr>
                    <?php else: mysqli_data_seek($pengumuman_list, 0); while($p = mysqli_fetch_assoc($pengumuman_list)): ?>
                        <tr>
                            <td><strong style="color:#2D3436; font-size:13px;"><?= htmlspecialchars($p['judul']) ?></strong></td>
                            <td>
                                <?php if ($p['target'] === 'Semua'): ?>
                                    <span class="badge badge-green">Semua</span>
                                <?php elseif ($p['target'] === 'Pasien'): ?>
                                    <span class="badge badge-pink">Hanya Pasien</span>
                                <?php else: ?>
                                    <span class="badge badge-yellow">Hanya Dokter</span>
                                <?php endif; ?>
                            </td>
                            <td style="max-width:300px; font-size:12px; color:#585552; line-height:1.5;"><?= nl2br(htmlspecialchars($p['konten'])) ?></td>
                            <td style="font-size:12px; color:#7a7571; white-space:nowrap;"><?= date('d M Y, H:i', strtotime($p['created_at'])) ?> WIB</td>
                            <td>
                                <button class="act-btn" style="border: 1px solid #e05050; color: #e05050; padding: 4px 10px;" onclick="confirmDelete('hapus_pengumuman.php', '<?= $p['id'] ?>', 'pengumuman ini')" title="Hapus">Hapus</button>
                            </td>
                        </tr>
                    <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ══ PANEL LAPORAN ══ -->
        <div class="panel" id="panel-laporan">
            <div class="page-header">
                <div>
                    <h2 class="section-title">Laporan <em>Bulanan</em></h2>
                    <p class="section-sub">Ringkasan kinerja dan pendapatan klinik per bulan</p>
                </div>
                <div style="display:flex;gap:10px">
                    <a href="../../backend/admin/laporan_download.php?format=csv&lap_bulan=<?= $lap_bulan ?>&lap_tahun=<?= $lap_tahun ?>" class="btn-lap-download btn-lap-csv">Download CSV</a>
                    <a href="../../backend/admin/laporan_download.php?format=print&lap_bulan=<?= $lap_bulan ?>&lap_tahun=<?= $lap_tahun ?>" target="_blank" class="btn-lap-download btn-lap-print">Cetak / PDF</a>
                </div>
            </div>

            <?php
            // Hitung total & stats untuk kartu ringkasan
            $lap_rows = [];
            $tot_a=$tot_s=$tot_b=$tot_bl=$tot_p=0;
            mysqli_data_seek($laporan,0);
            while($lp=mysqli_fetch_assoc($laporan)) {
                $lap_rows[] = $lp;
                $tot_a += $lp['total_appt'];
                $tot_s += $lp['selesai'];
                $tot_b += $lp['batal'];
                $tot_bl += $lp['berlangsung'];
                $tot_p += $lp['pendapatan'];
            }
            $pct_selesai = $tot_a > 0 ? round($tot_s/$tot_a*100) : 0;
            ?>

            <!-- Filter Bar -->
            <div class="keu-filter-bar" style="margin-bottom:22px">
                <form method="GET" class="keu-filter-form">
                    <input type="hidden" name="panel" value="laporan">
                    <div class="keu-filter-group">
                        <label class="form-label">Bulan</label>
                        <select class="form-select" name="lap_bulan" onchange="this.form.submit()">
                            <?php for($m=1;$m<=12;$m++): ?>
                                <option value="<?=$m?>" <?=$m==$lap_bulan?'selected':''?>><?=$bln_ind[$m]?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="keu-filter-group">
                        <label class="form-label">Tahun</label>
                        <select class="form-select" name="lap_tahun" onchange="this.form.submit()">
                            <?php for($y=date('Y');$y>=date('Y')-4;$y--): ?>
                                <option value="<?=$y?>" <?=$y==$lap_tahun?'selected':''?>><?=$y?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </form>
                <div class="keu-period-label"><?= $bln_ind[$lap_bulan].' '.$lap_tahun ?></div>
            </div>

            <!-- Stat Cards Ringkasan -->
            <div class="lap-stats-row">
                <div class="lap-stat-card">
                    <div class="lap-stat-label">Total Appointment</div>
                    <div class="lap-stat-val"><?= $tot_a ?></div>
                    <div class="lap-stat-sub"><?= count($lap_rows) ?> dokter aktif</div>
                </div>
                <div class="lap-stat-card lap-stat-green">
                    <div class="lap-stat-label">Selesai</div>
                    <div class="lap-stat-val"><?= $tot_s ?></div>
                    <div class="lap-stat-sub"><?= $pct_selesai ?>% dari total</div>
                </div>
                <div class="lap-stat-card lap-stat-red">
                    <div class="lap-stat-label">Dibatalkan</div>
                    <div class="lap-stat-val"><?= $tot_b ?></div>
                    <div class="lap-stat-sub">&nbsp;</div>
                </div>
                <div class="lap-stat-card lap-stat-gold">
                    <div class="lap-stat-label">Total Pendapatan</div>
                    <div class="lap-stat-val" style="font-size:18px"><?= rupiah((float)$tot_p) ?></div>
                    <div class="lap-stat-sub">dari pembayaran lunas</div>
                </div>
            </div>

            <!-- Tabel Detail -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Detail Per Dokter — <?= $bln_ind[$lap_bulan].' '.$lap_tahun ?></div>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Dokter</th>
                            <th>Spesialisasi</th>
                            <th style="text-align:center">Total Appt</th>
                            <th style="text-align:center">Selesai</th>
                            <th style="text-align:center">Batal</th>
                            <th style="text-align:right">Pendapatan</th>
                            <th style="text-align:center">Rating</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($lap_rows)): ?>
                        <tr><td colspan="7" style="text-align:center;color:#64748b;padding:40px">Tidak ada data untuk periode ini.</td></tr>
                    <?php else: foreach ($lap_rows as $lp): ?>
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px">
                                    <span class="avatar"><?= strtoupper(substr($lp['nama_dokter'],0,1)) ?></span>
                                    <span class="td-name"><?= htmlspecialchars($lp['nama_dokter']) ?></span>
                                </div>
                            </td>
                            <td><span class="badge badge-pink"><?= htmlspecialchars($lp['spesialisasi'] ?? '-') ?></span></td>
                            <td style="text-align:center;font-weight:600"><?= $lp['total_appt'] ?></td>
                            <td style="text-align:center">
                                <span class="badge badge-green"><?= $lp['selesai'] ?></span>
                            </td>
                            <td style="text-align:center">
                                <?php if ($lp['batal'] > 0): ?>
                                    <span class="badge" style="background:#fef0f0;color:#e05050"><?= $lp['batal'] ?></span>
                                <?php else: ?>
                                    <span style="color:#b0bec5">—</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:right;font-weight:600;color:#3dab74"><?= rupiah((float)$lp['pendapatan']) ?></td>
                            <td style="text-align:center"><?= number_format($lp['rating'],1) ?> / 5.0</td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                    <?php if (!empty($lap_rows)): ?>
                    <tfoot>
                        <tr class="lap-tfoot-row">
                            <td><strong>Total</strong></td>
                            <td></td>
                            <td style="text-align:center"><strong><?= $tot_a ?></strong></td>
                            <td style="text-align:center"><strong><?= $tot_s ?></strong></td>
                            <td style="text-align:center;color:#e05050"><strong><?= $tot_b ?: '—' ?></strong></td>
                            <td style="text-align:right;color:#3dab74"><strong><?= rupiah((float)$tot_p) ?></strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <!-- ══ PANEL KEUANGAN ══ -->
        <div class="panel" id="panel-keuangan">
            <div class="page-header">
                <div>
                    <h2 class="section-title">Keuangan <em>Klinik</em></h2>
                    <p class="section-sub">Catat pemasukan dan pengeluaran operasional klinik</p>
                </div>
                <button class="btn-add" onclick="openKeuanganModal()">+ Tambah Transaksi</button>
            </div>

            <?php
            // Ringkasan Statistik Keuangan
            $saldo_positif = $keu_saldo >= 0;
            ?>

            <!-- Stats Keuangan -->
            <div class="keu-stats-row">
                <div class="keu-stat-card keu-masuk">
                    <div class="keu-stat-label">Total Pemasukan</div>
                    <div class="keu-stat-value"><?= rupiah((float)$keu_stats['total_masuk']) ?></div>
                </div>
                <div class="keu-stat-card keu-keluar">
                    <div class="keu-stat-label">Total Pengeluaran</div>
                    <div class="keu-stat-value"><?= rupiah((float)$keu_stats['total_keluar']) ?></div>
                </div>
                <div class="keu-stat-card <?= $saldo_positif ? 'keu-saldo-plus' : 'keu-saldo-minus' ?>">
                    <div class="keu-stat-label">Saldo Bersih</div>
                    <div class="keu-stat-value"><?= rupiah(abs((float)$keu_saldo)) ?></div>
                </div>
                <div class="keu-stat-card keu-trx">
                    <div class="keu-stat-label">Jumlah Transaksi</div>
                    <div class="keu-stat-value"><?= (int)$keu_stats['total_trx'] ?></div>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="keu-filter-bar">
                <form method="GET" class="keu-filter-form">
                    <input type="hidden" name="panel" value="keuangan">
                    <div class="keu-filter-group">
                        <label class="form-label">Bulan</label>
                        <select class="form-select" name="keu_bulan" onchange="this.form.submit()">
                            <?php
                            $bln_names=['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                            for($m=1;$m<=12;$m++): ?>
                                <option value="<?=$m?>" <?=$m==$keu_bulan?'selected':''?>><?=$bln_names[$m]?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="keu-filter-group">
                        <label class="form-label">Tahun</label>
                        <select class="form-select" name="keu_tahun" onchange="this.form.submit()">
                            <?php for($y=date('Y');$y>=date('Y')-4;$y--): ?>
                                <option value="<?=$y?>" <?=$y==$keu_tahun?'selected':''?>><?=$y?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="keu-filter-group">
                        <label class="form-label">Jenis</label>
                        <select class="form-select" name="keu_jenis" onchange="this.form.submit()">
                            <option value="" <?=$keu_filter_jenis===''?'selected':''?>>Semua</option>
                            <option value="Pemasukan" <?=$keu_filter_jenis==='Pemasukan'?'selected':''?>>Pemasukan</option>
                            <option value="Pengeluaran" <?=$keu_filter_jenis==='Pengeluaran'?'selected':''?>>Pengeluaran</option>
                        </select>
                    </div>
                </form>
                <div class="keu-period-label">
                    <?= $bln_names[$keu_bulan] . ' ' . $keu_tahun ?>
                </div>
            </div>

            <!-- Tabel Transaksi -->
            <div class="card">
                <table class="data-table" id="keu-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Kategori</th>
                            <th>Keterangan</th>
                            <th>Metode</th>
                            <th>Referensi</th>
                            <th style="text-align:right">Jumlah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $no_keu = 0;
                    if (mysqli_num_rows($keu_list) === 0): ?>
                        <tr>
                            <td colspan="8" style="text-align:center;color:#64748b;padding:48px">
                                Belum ada transaksi untuk periode ini.
                            </td>
                        </tr>
                    <?php else:
                        while ($keu = mysqli_fetch_assoc($keu_list)):
                            $no_keu++;
                            $is_masuk = $keu['jenis'] === 'Pemasukan';
                    ?>
                        <tr>
                            <td style="white-space:nowrap;font-size:12px;color:#64748b">
                                <?= date('d M Y', strtotime($keu['tanggal'])) ?>
                            </td>
                            <td>
                                <span class="keu-jenis-badge <?= $is_masuk ? 'keu-badge-masuk' : 'keu-badge-keluar' ?>">
                                    <?= $is_masuk ? '↑ Pemasukan' : '↓ Pengeluaran' ?>
                                </span>
                            </td>
                            <td><span class="badge badge-pink"><?= htmlspecialchars($keu['kategori']) ?></span></td>
                            <td>
                                <div class="td-name" style="font-size:13px"><?= htmlspecialchars($keu['keterangan']) ?></div>
                                <?php if ($keu['catatan']): ?>
                                    <div style="font-size:11px;color:#64748b;margin-top:2px"><?= htmlspecialchars(mb_substr($keu['catatan'],0,60)) ?><?= mb_strlen($keu['catatan'])>60?'…':'' ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="font-size:12px"><?= htmlspecialchars($keu['metode']) ?></td>
                            <td style="font-size:11px;color:#64748b"><?= htmlspecialchars($keu['referensi'] ?: '-') ?></td>
                            <td style="text-align:right;font-weight:600;white-space:nowrap;font-size:14px;color:<?= $is_masuk ? '#3dab74' : '#e05050' ?>">
                                <?= $is_masuk ? '+' : '-' ?><?= rupiah((float)$keu['jumlah']) ?>
                            </td>
                            <td style="white-space:nowrap">
                                <button class="act-btn jdc-btn-edit" onclick="editKeuangan(<?= htmlspecialchars(json_encode($keu)) ?>)" title="Edit">Edit</button>
                                <button class="act-btn jdc-btn-hapus" onclick="confirmDelete('hapus_keuangan.php','<?= $keu['id'] ?>','transaksi ini')" title="Hapus">Hapus</button>
                            </td>
                        </tr>
                    <?php endwhile; endif; ?>
                    </tbody>
                </table>
                <?php if (mysqli_num_rows($keu_list) > 0): ?>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 18px;border-top:1px solid #F9F7F2;font-size:12px;color:#64748b">
                    <span><?= $no_keu ?> transaksi ditemukan</span>
                    <span style="font-weight:600;color:<?= $saldo_positif?'#3dab74':'#e05050'?>">
                        Saldo: <?= $saldo_positif?'+':'-' ?><?= rupiah(abs((float)$keu_saldo)) ?>
                    </span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ══ PANEL PROFIL ══ -->
        <div class="panel" id="panel-profil">
            <div class="page-header">
                <div><h2 class="section-title">Profil & <em>Keamanan</em></h2><p class="section-sub">Kelola informasi akun admin</p></div>
            </div>
            <div class="profil-hero">
                <div class="profil-avatar"><?= strtoupper(substr($admin['username'],0,1)) ?></div>
                <div class="profil-info">
                    <div class="profil-name"><?= htmlspecialchars($admin['username']) ?></div>
                    <div class="profil-role">Super Admin · GlowCare Clinic</div>
                    <div class="profil-meta">
                        <div><div class="profil-meta-label">Email</div><div class="profil-meta-value"><?= htmlspecialchars($admin['email']) ?></div></div>
                        <div><div class="profil-meta-label">Bergabung</div><div class="profil-meta-value"><?= date('d M Y',strtotime($admin['created_at'])) ?></div></div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><div class="card-title">Ganti <em>Password</em></div></div>
                <div style="padding:22px">
                    <form method="POST" action="../../backend/admin/ganti_password.php">
                        <div class="form-row">
                            <div class="form-group full"><label class="form-label">Password Saat Ini</label><input class="form-input" type="password" name="password_lama" placeholder="••••••••" required></div>
                            <div class="form-group"><label class="form-label">Password Baru</label><input class="form-input" type="password" name="password_baru" placeholder="Min. 8 karakter" required></div>
                            <div class="form-group"><label class="form-label">Konfirmasi</label><input class="form-input" type="password" name="konfirmasi" placeholder="Ulangi password" required></div>
                        </div>
                        <button type="submit" class="btn-save">Ubah Password</button>
                    </form>
                </div>
            </div>
        </div>

        </div><!-- /content -->
    </div><!-- /main -->

    <!-- ═══ MODAL PASIEN ═══ -->
    <div class="modal-overlay" id="modal-pasien" onclick="closeModalOutside(event,'modal-pasien')">
        <div class="modal">
            <h3 class="modal-title" id="mp-title">Tambah <em>Pasien</em></h3>
            <p class="modal-sub">Isi data pasien dengan lengkap dan benar</p>
            <form method="POST" id="form-pasien">
                <input type="hidden" name="id" id="mp-id">
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Nama Lengkap</label><input class="form-input" name="nama" id="mp-nama" placeholder="Nama lengkap" required></div>
                    <div class="form-group"><label class="form-label">No. Pasien</label><input class="form-input" name="no_pasien" id="mp-no" placeholder="P-XXXX" required></div>
                    <div class="form-group"><label class="form-label">Tanggal Lahir</label><input class="form-input" type="date" name="tanggal_lahir" id="mp-tgl"></div>
                    <div class="form-group"><label class="form-label">Jenis Kelamin</label>
                        <select class="form-select" name="jenis_kelamin" id="mp-jk"><option>Perempuan</option><option>Laki-laki</option></select>
                    </div>
                    <div class="form-group"><label class="form-label">No. Telepon</label><input class="form-input" name="telepon" id="mp-telp" placeholder="+62"></div>
                    <div class="form-group"><label class="form-label">Email</label><input class="form-input" type="email" name="email" id="mp-email" placeholder="email@contoh.com"></div>
                    <div class="form-group full"><label class="form-label">Alamat</label><input class="form-input" name="alamat" id="mp-alamat" placeholder="Alamat lengkap"></div>
                    <div class="form-group"><label class="form-label">Status</label>
                        <select class="form-select" name="status" id="mp-status"><option>Aktif</option><option>Tidak Aktif</option></select>
                    </div>
                    <div class="form-group full"><label class="form-label">Catatan Medis</label><textarea class="form-textarea" name="catatan_medis" id="mp-catatan" placeholder="Alergi, kondisi khusus..."></textarea></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('modal-pasien')">Batal</button>
                    <button type="submit" class="btn-save">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ═══ MODAL DOKTER ═══ -->
    <div class="modal-overlay" id="modal-dokter" onclick="closeModalOutside(event,'modal-dokter')">
        <div class="modal">
            <h3 class="modal-title" id="md-title">Tambah <em>Dokter</em></h3>
            <p class="modal-sub">Lengkapi profil dan spesialisasi dokter</p>
            <form method="POST" id="form-dokter" enctype="multipart/form-data">
                <input type="hidden" name="id" id="md-id">
                <input type="hidden" name="current_foto" id="md-current-foto">
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Nama Lengkap</label><input class="form-input" name="nama" id="md-nama" placeholder="Dr. Nama Lengkap" required></div>
                    <div class="form-group"><label class="form-label">No. STR / SIP</label><input class="form-input" name="no_str" id="md-str" placeholder="STR-XX-000"></div>
                    <div class="form-group full"><label class="form-label">Spesialisasi</label>
                        <select class="form-select" name="spesialisasi" id="md-spesialis">
                            <option>Plastic Surgeon</option><option>Aesthetic Physician</option><option>Dermatologist</option><option>Other</option>
                        </select>
                    </div>
                    <div class="form-group"><label class="form-label">No. Telepon</label><input class="form-input" name="telepon" id="md-telp" placeholder="+62"></div>
                    <div class="form-group"><label class="form-label">Email</label><input class="form-input" type="email" name="email" id="md-email" placeholder="dokter@glowcare.com"></div>
                    <div class="form-group"><label class="form-label">Pengalaman (Tahun)</label><input class="form-input" type="number" name="pengalaman" id="md-exp" placeholder="0" min="0"></div>
                    <div class="form-group"><label class="form-label">Rating</label><input class="form-input" type="number" name="rating" id="md-rating" placeholder="5.0" min="0" max="5" step="0.1"></div>
                    <div class="form-group full"><label class="form-label">Status</label>
                        <select class="form-select" name="status" id="md-status"><option>Aktif</option><option>Cuti</option><option>Tidak Aktif</option></select>
                    </div>
                    <div class="form-group full"><label class="form-label">Foto Dokter</label><input type="file" class="form-input" name="foto" id="md-foto" accept="image/*"></div>
                    <div class="form-group full"><label class="form-label">Bio Singkat</label><textarea class="form-textarea" name="bio" id="md-bio" placeholder="Deskripsi singkat dokter..."></textarea></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('modal-dokter')">Batal</button>
                    <button type="submit" class="btn-save">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ═══ MODAL JADWAL ═══ -->
    <div class="modal-overlay" id="modal-jadwal" onclick="closeModalOutside(event,'modal-jadwal')">
        <div class="modal">
            <h3 class="modal-title" id="mj-title">Tambah <em>Jadwal</em></h3>
            <p class="modal-sub">Atur jadwal praktik dokter</p>
            <form method="POST" id="form-jadwal">
                <input type="hidden" name="id" id="mj-id">
                <div class="form-row">
                    <div class="form-group full"><label class="form-label">Dokter</label>
                        <select class="form-select" name="dokter_id" id="mj-dokter">
                            <?php mysqli_data_seek($dlist_modal,0); while($dl=mysqli_fetch_assoc($dlist_modal)): ?>
                                <option value="<?= $dl['id'] ?>"><?= htmlspecialchars($dl['nama']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group full"><label class="form-label">Hari</label>
                        <select class="form-select" name="hari" id="mj-hari">
                            <?php foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $hr): ?>
                                <option><?= $hr ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group"><label class="form-label">Jam Mulai</label><input class="form-input" type="time" name="jam_mulai" id="mj-mulai" value="09:00"></div>
                    <div class="form-group"><label class="form-label">Jam Selesai</label><input class="form-input" type="time" name="jam_selesai" id="mj-selesai" value="17:00"></div>
                    <div class="form-group"><label class="form-label">Max Pasien/Hari</label><input class="form-input" type="number" name="max_pasien" id="mj-max" value="8" min="1"></div>
                    <div class="form-group"><label class="form-label">Treatment</label>
                        <select class="form-select" name="treatment_id" id="mj-treatment">
                            <option value="">Semua Treatment</option>
                            <?php mysqli_data_seek($tlist_modal,0); while($tl=mysqli_fetch_assoc($tlist_modal)): ?>
                                <option value="<?= $tl['id'] ?>"><?= htmlspecialchars($tl['nama']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group full"><label class="form-label">Status</label>
                        <select class="form-select" name="status" id="mj-status"><option>Aktif</option><option>Nonaktif</option></select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('modal-jadwal')">Batal</button>
                    <button type="submit" class="btn-save">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ═══ MODAL TREATMENT ═══ -->
    <div class="modal-overlay" id="modal-treatment" onclick="closeModalOutside(event,'modal-treatment')">
        <div class="modal" style="width:600px">
            <h3 class="modal-title" id="mt-title">Tambah <em>Treatment</em></h3>
            <p class="modal-sub">Data ini akan tampil di halaman index klinik</p>
            <form method="POST" id="form-treatment">
                <input type="hidden" name="id" id="mt-id">
                <div class="form-row">
                    <div class="form-group full"><label class="form-label">Nama Treatment</label><input class="form-input" name="nama" id="mt-nama" placeholder="Contoh: Facelift Procedures" required></div>
                    <div class="form-group"><label class="form-label">Kategori</label>
                        <select class="form-select" name="kategori" id="mt-kategori">
                            <option>Acne & Clear Skin</option><option>Anti-Aging</option><option>Brightening</option><option>Hair & Body Care</option><option>Surgical</option><option>Injectable</option><option>Technology</option><option>Contouring</option><option>Skincare</option><option>Other</option>
                        </select>
                    </div>
                    <div class="form-group"><label class="form-label">Estimasi Durasi</label><input class="form-input" name="durasi" id="mt-durasi" placeholder="Contoh: 60 Menit"></div>
                    <div class="form-group"><label class="form-label">Urutan Tampil</label><input class="form-input" type="number" name="urutan" id="mt-urutan" value="1" min="1"></div>
                    <div class="form-group full"><label class="form-label">URL Gambar</label><input class="form-input" name="gambar_url" id="mt-gambar" placeholder="https://images.unsplash.com/..."></div>
                    <div class="form-group full" style="background:#f5f3ee;padding:12px;border-radius:8px;border:1px dashed #d1c4b8"><p style="font-size:12px;color:#7a7571;margin:0">💡 Halaman detail treatment akan otomatis dibuat berdasarkan data di atas. Preview tersedia di tabel setelah disimpan.</p></div>
                    <div class="form-group full"><label class="form-label">Deskripsi Singkat (tampil di index)</label><textarea class="form-textarea" name="deskripsi" id="mt-desc" placeholder="Deskripsi singkat..."></textarea></div>
                    <div class="form-group full"><label class="form-label">Deskripsi Lengkap</label><textarea class="form-textarea" style="min-height:90px" name="deskripsi_panjang" id="mt-desc-panjang" placeholder="Deskripsi lengkap treatment..."></textarea></div>
                    <div class="form-group full"><label class="form-label">Status</label>
                        <select class="form-select" name="status" id="mt-status"><option>Aktif</option><option>Nonaktif</option></select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('modal-treatment')">Batal</button>
                    <button type="submit" class="btn-save">Simpan & Publikasikan</button>
                </div>
            </form>
        </div>
    </div>
    <!-- ═══ MODAL KEUANGAN ═══ -->
    <div class="modal-overlay" id="modal-keuangan" onclick="closeModalOutside(event,'modal-keuangan')">
        <div class="modal" style="width:580px">
            <h3 class="modal-title" id="mkeu-title">Tambah <em>Transaksi</em></h3>
            <p class="modal-sub">Catat pemasukan atau pengeluaran klinik</p>
            <form method="POST" action="../../backend/admin/simpan_keuangan.php" id="form-keuangan">
                <input type="hidden" name="id" id="mkeu-id">
                <div class="form-row">
                    <!-- Jenis (toggle besar) -->
                    <div class="form-group full">
                        <label class="form-label">Jenis Transaksi</label>
                        <div class="keu-toggle-wrap">
                            <label class="keu-toggle-opt keu-toggle-masuk">
                                <input type="radio" name="jenis" value="Pemasukan" id="keu-r-masuk" required onchange="updateKeuKategori('Pemasukan')">
                                <span>↑ Pemasukan</span>
                            </label>
                            <label class="keu-toggle-opt keu-toggle-keluar">
                                <input type="radio" name="jenis" value="Pengeluaran" id="keu-r-keluar" onchange="updateKeuKategori('Pengeluaran')">
                                <span>↓ Pengeluaran</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal</label>
                        <input class="form-input" type="date" name="tanggal" id="mkeu-tgl" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jumlah (Rp)</label>
                        <input class="form-input" type="number" name="jumlah" id="mkeu-jumlah" placeholder="0" min="1" step="1" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <select class="form-select" name="kategori" id="mkeu-kategori">
                            <optgroup label="Pemasukan" id="keu-opt-masuk">
                                <option>Pendapatan Konsultasi</option>
                                <option>Pendapatan Treatment</option>
                                <option>Pendapatan Produk</option>
                                <option>Pendapatan Lainnya</option>
                            </optgroup>
                            <optgroup label="Pengeluaran" id="keu-opt-keluar" style="display:none">
                                <option>Gaji Karyawan</option>
                                <option>Bahan &amp; Alat Medis</option>
                                <option>Sewa &amp; Utilitas</option>
                                <option>Pemasaran &amp; Promosi</option>
                                <option>Pemeliharaan Alat</option>
                                <option>Transportasi</option>
                                <option>Pengeluaran Lainnya</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Metode Pembayaran</label>
                        <select class="form-select" name="metode" id="mkeu-metode">
                            <option>Tunai</option>
                            <option>Transfer Bank</option>
                            <option>QRIS / E-Wallet</option>
                            <option>Kartu Debit</option>
                            <option>Kartu Kredit</option>
                        </select>
                    </div>

                    <div class="form-group full">
                        <label class="form-label">Keterangan</label>
                        <input class="form-input" name="keterangan" id="mkeu-ket" placeholder="Contoh: Pembayaran treatment Botox pasien Siti" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. Referensi / Invoice</label>
                        <input class="form-input" name="referensi" id="mkeu-ref" placeholder="INV-2025-001 (opsional)">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Catatan Tambahan</label>
                        <input class="form-input" name="catatan" id="mkeu-cat" placeholder="Opsional">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('modal-keuangan')">Batal</button>
                    <button type="submit" class="btn-save" id="mkeu-submit">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ═══ MODAL DETAIL PESAN ═══ -->
    <div class="modal-overlay" id="modal-pesan" onclick="closeModalOutside(event,'modal-pesan')">
        <div class="modal" style="width:580px;max-height:90vh;overflow-y:auto">
            <h3 class="modal-title">Detail &amp; Balas <em>Pesan</em></h3>
            <p class="modal-sub" id="mp-pengirim-sub">Dari pengunjung</p>

            <!-- Info pengirim -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:18px">
                <div class="form-group">
                    <label class="form-label">Nama</label>
                    <div class="form-input" id="mp-detail-nama" style="background:#F9F7F2;cursor:default"></div>
                </div>
                <div class="form-group">
                    <label class="form-label">Telepon</label>
                    <div class="form-input" id="mp-detail-telp" style="background:#F9F7F2;cursor:default"></div>
                </div>
                <div class="form-group" style="grid-column:1/-1">
                    <label class="form-label">Email</label>
                    <div class="form-input" id="mp-detail-email" style="background:#F9F7F2;cursor:default"></div>
                </div>
                <div class="form-group" style="grid-column:1/-1">
                    <label class="form-label">Pesan dari Pengunjung</label>
                    <div class="form-textarea" id="mp-detail-pesan"
                        style="background:#F9F7F2;cursor:default;min-height:90px;line-height:1.6;white-space:pre-wrap"></div>
                </div>
            </div>

            <!-- Balasan sebelumnya (jika ada) -->
            <div id="mp-balasan-box" style="display:none;margin-bottom:18px">
                <div style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:#735a39;font-weight:600;margin-bottom:8px">Balasan Admin</div>
                <div style="background:linear-gradient(135deg,#f5f3ee,#F9F7F2);border-left:3px solid #735a39;border-radius:8px;padding:14px 16px;font-size:13px;color:#2D3436;line-height:1.6;white-space:pre-wrap" id="mp-balasan-isi"></div>
                <div style="font-size:11px;color:#94a3b8;margin-top:6px" id="mp-balasan-info"></div>
            </div>

            <!-- Form balas -->
            <div id="mp-form-balas">
                <div style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:#2D3436;font-weight:600;margin-bottom:8px">Tulis Balasan</div>
                <form method="POST" action="../../backend/admin/kelola_pesan.php" id="form-balas-pesan">
                    <input type="hidden" name="aksi" value="balas">
                    <input type="hidden" name="id" id="mp-balas-id" value="">
                    <textarea name="balasan" id="mp-balas-teks" class="form-textarea"
                        style="min-height:100px;margin-bottom:12px"
                        placeholder="Tulis balasan untuk dikirimkan ke email pengunjung..."></textarea>
                    <div style="font-size:11px;color:#94a3b8;margin-bottom:14px">Balasan akan dikirim ke email pengunjung secara otomatis.</div>
                    <div style="display:flex;gap:10px;justify-content:flex-end">
                        <button type="button" class="btn-cancel" onclick="closeModal('modal-pesan')">Tutup</button>
                        <button type="submit" class="btn-save" id="mp-btn-kirim">Kirim Balasan</button>
                    </div>
                </form>
            </div>

            <!-- Footer aksi tambahan -->
            <div class="modal-footer" id="mp-detail-footer" style="border-top:1px solid #F9F7F2;margin-top:4px;padding-top:12px">
                <!-- JS inject tombol Tandai Baca jika belum dibaca -->
            </div>
        </div>
    </div>

    <!-- ═══ MODAL TAMBAH PENGUMUMAN ═══ -->
    <div class="modal-overlay" id="modal-pengumuman" onclick="closeModalOutside(event,'modal-pengumuman')">
        <div class="modal" style="width:500px;">
            <h3 class="modal-title">Tambah <em>Pengumuman</em></h3>
            <p class="modal-sub">Kirim pengumuman/notifikasi baru ke sistem</p>
            <form method="POST" action="../../backend/admin/simpan_pengumuman.php">
                <div class="form-row">
                    <div class="form-group full">
                        <label class="form-label">Judul Pengumuman</label>
                        <input class="form-input" name="judul" placeholder="Contoh: Jadwal Libur Lebaran Klinik" required>
                    </div>
                    <div class="form-group full">
                        <label class="form-label">Target Penerima</label>
                        <select class="form-select" name="target" required>
                            <option value="Semua">Semua (Pasien &amp; Dokter)</option>
                            <option value="Pasien">Hanya Pasien</option>
                            <option value="Dokter">Hanya Dokter</option>
                        </select>
                    </div>
                    <div class="form-group full">
                        <label class="form-label">Konten Pengumuman</label>
                        <textarea class="form-textarea" name="konten" style="min-height:120px;" placeholder="Tulis isi pengumuman secara lengkap..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('modal-pengumuman')">Batal</button>
                    <button type="submit" class="btn-save">Simpan &amp; Kirim</button>
                </div>
            </form>
        </div>
    </div>

    <!-- CONFIRM DELETE -->
    <div class="confirm-overlay" id="confirm-overlay">
        <div class="confirm-box">
            <div class="confirm-icon">🗑️</div>
            <div class="confirm-title">Yakin ingin menghapus?</div>
            <div class="confirm-desc" id="confirm-desc">Data ini akan dihapus permanen.</div>
            <div class="confirm-btns">
                <button class="btn-cancel" onclick="closeConfirm()">Batal</button>
                <form method="POST" id="confirm-form">
                    <input type="hidden" name="id" id="confirm-id">
                    <button type="submit" class="btn-save" style="background:#e05050">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>

    <div class="toast" id="toast">✅ <span id="toast-msg">Berhasil</span></div>

    <script src="../../asset/js/admin.js?v=3"></script>
    <script>
    // ── Keuangan JS ─────────────────────────────────────────
    function openKeuanganModal() {
        document.getElementById('mkeu-title').innerHTML = 'Tambah <em>Transaksi</em>';
        document.getElementById('mkeu-id').value = '';
        document.getElementById('form-keuangan').reset();
        document.getElementById('mkeu-tgl').value = new Date().toISOString().split('T')[0];
        document.getElementById('keu-r-masuk').checked = true;
        updateKeuKategori('Pemasukan');
        openModal('modal-keuangan');
    }
    function editKeuangan(data) {
        document.getElementById('mkeu-title').innerHTML = 'Edit <em>Transaksi</em>';
        document.getElementById('mkeu-id').value      = data.id;
        document.getElementById('mkeu-tgl').value     = data.tanggal;
        document.getElementById('mkeu-jumlah').value  = data.jumlah;
        document.getElementById('mkeu-ket').value     = data.keterangan;
        document.getElementById('mkeu-ref').value     = data.referensi || '';
        document.getElementById('mkeu-cat').value     = data.catatan   || '';
        // Set jenis radio
        if (data.jenis === 'Pengeluaran') {
            document.getElementById('keu-r-keluar').checked = true;
            updateKeuKategori('Pengeluaran');
        } else {
            document.getElementById('keu-r-masuk').checked = true;
            updateKeuKategori('Pemasukan');
        }
        // Set kategori & metode
        ['mkeu-kategori','mkeu-metode'].forEach(id => {
            const el = document.getElementById(id);
            const key = id === 'mkeu-kategori' ? 'kategori' : 'metode';
            if (el) { [...el.options].forEach(o => { if(o.text===data[key]||o.value===data[key]) el.value=o.value; }); }
        });
        openModal('modal-keuangan');
    }
    function updateKeuKategori(jenis) {
        const optMasuk  = document.getElementById('keu-opt-masuk');
        const optKeluar = document.getElementById('keu-opt-keluar');
        const sel       = document.getElementById('mkeu-kategori');
        if (jenis === 'Pemasukan') {
            optMasuk.style.display  = '';
            optKeluar.style.display = 'none';
            sel.value = optMasuk.querySelector('option').value;
        } else {
            optMasuk.style.display  = 'none';
            optKeluar.style.display = '';
            sel.value = optKeluar.querySelector('option').value;
        }
    }
    </script>
    <?php
    // PHP auto-open panel berdasarkan GET param (lebih handal)
    $auto_panel = '';
    if (isset($_GET['panel']) && in_array($_GET['panel'], ['laporan','keuangan','pasien','dokter','jadwal','aktivitas','pesan','treatment','profil'])) {
        $auto_panel = $_GET['panel'];
    } elseif (isset($_GET['lap_bulan'])) {
        $auto_panel = 'laporan';
    } elseif (isset($_GET['keu_bulan'])) {
        $auto_panel = 'keuangan';
    }
    if ($auto_panel): ?>
    <script>
    // PHP-injected: langsung aktifkan panel tanpa tunggu event
    (function() {
        var p = <?= json_encode($auto_panel) ?>;
        var el = document.querySelector('[onclick*="showPanel(\'' + p + '\'"]');
        if (el) showPanel(p, el);
    })();
    </script>
    <?php endif; ?>
    <!-- LOGOUT CONFIRMATION MODAL -->
    <div id="logout-modal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.45);backdrop-filter:blur(4px);align-items:center;justify-content:center">
        <div style="background:#fff;border-radius:20px;padding:40px 36px;width:360px;text-align:center;box-shadow:0 24px 64px rgba(0,0,0,0.18);animation:logoutFadeIn .25s ease">
            <div style="width:56px;height:56px;background:#fef0f0;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:22px">⏻</div>
            <div style="font-family:'Playfair Display',serif;font-size:20px;color:#2D3436;margin-bottom:10px">Yakin ingin <em>keluar</em>?</div>
            <p style="font-size:13px;color:#64748b;margin-bottom:28px;line-height:1.6">Sesi Anda sebagai <strong>Admin</strong> akan diakhiri. Anda perlu login kembali untuk mengakses dashboard.</p>
            <div style="display:flex;gap:12px;justify-content:center">
                <button onclick="hideLogoutModal()" style="flex:1;padding:11px;border:1.5px solid #d1c4b8;border-radius:50px;background:#fff;color:#64748b;font-size:12px;font-weight:500;letter-spacing:1px;text-transform:uppercase;cursor:pointer;font-family:'DM Sans',sans-serif">Batal</button>
                <a href="../../backend/logout.php" style="flex:1;padding:11px;border-radius:50px;background:#e05050;color:#fff;font-size:12px;font-weight:500;letter-spacing:1px;text-transform:uppercase;cursor:pointer;font-family:'DM Sans',sans-serif;text-decoration:none;display:flex;align-items:center;justify-content:center">Ya, Keluar</a>
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
</body>
</html>
<?php mysqli_close($conn); ?>