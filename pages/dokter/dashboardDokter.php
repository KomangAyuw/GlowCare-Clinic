<?php
// ══════════════════════════════════════════════
//  GlowCare — Dashboard Dokter (Dinamis)
// ══════════════════════════════════════════════
require '../../backend/guard_dokter.php';
require '../../backend/koneksi.php';

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
                ? htmlspecialchars($profil['foto'])
                : 'https://images.unsplash.com/photo-1651008376811-b90baee60c1f?auto=format&fit=crop&w=400&q=80';
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

// ── 3. JADWAL HARI INI ────────────────────────
$qJadwal = mysqli_query($conn, "
    SELECT j.*, p.nama AS nama_pasien, p.usia, p.jenis_kelamin,
           p.keluhan, p.id AS pasien_id
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

// ── 4. DAFTAR PASIEN ──────────────────────────
$qPasien = mysqli_query($conn, "
    SELECT
        p.*,
        j.treatment,
        j.jam_mulai,
        j.status AS status_jadwal,
        COUNT(rm.id) AS total_kunjungan
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

// ── HELPER FUNCTIONS ──────────────────────────
function badgeClass(string $status): string {
    return match($status) {
        'Selesai'    => 'badge-green',
        'Berlangsung'=> 'badge-yellow',
        'Menunggu'   => 'badge-pink',
        default      => 'badge-gray',
    };
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
    <link rel="stylesheet" href="../../asset/css/dokter.css?v=4">
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
            <img src="<?= $fotoUrl ?>" alt="<?= $namaDisplay ?>">
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
            <?php if (count($daftarPasien) > 0): ?>
                <span class="nav-badge"><?= count($daftarPasien) ?></span>
            <?php endif; ?>
        </a>
        <a class="nav-item" onclick="showPanel('rekam-medis', this)">
            Rekam Medis
        </a>

        <div class="nav-section-label" style="margin-top:8px">Akun</div>
        <a class="nav-item" onclick="showPanel('profil', this)">
            Profil Saya
        </a>
    </nav>

    <div class="sidebar-footer">
        <a class="logout-btn" href="#" onclick="showLogoutModal(); return false;">
            Keluar
        </a>
    </div>
</aside>

<!-- ══ MAIN ══ -->
<div class="main">

    <!-- TOPBAR -->
    <div class="topbar">
        <div>
            <div class="topbar-title" id="topbar-title">Overview</div>
            <div class="topbar-bc" id="topbar-bc">GlowCare Dokter → Overview</div>
        </div>
        <div class="topbar-right">
            <div class="topbar-date"><?= $hariIni ?>, <?= $tanggalHariIni ?></div>
            <button style="border: 1px solid #d1c4b8; padding: 4px 10px; background: transparent; color: #7a7571; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; cursor: pointer; position: relative;">
                Notifikasi
                <div class="notif-dot" style="top: 0; right: 0; position: absolute; width: 6px; height: 6px; background: #735a39; border-radius: 50%;"></div>
            </button>
        </div>
    </div>

    <!-- ALERT SUCCESS/ERROR -->
    <?php if (!empty($_GET['success'])): ?>
        <div class="alert alert-success" style="margin:16px 28px 0; padding:12px 18px; background:#d4edda; border:1px solid #c3e6cb; border-radius:8px; color:#155724; font-size:13px;">
            <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php elseif (!empty($_GET['error'])): ?>
        <div class="alert alert-error" style="margin:16px 28px 0; padding:12px 18px; background:#f8d7da; border:1px solid #f5c6cb; border-radius:8px; color:#721c24; font-size:13px;">
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <!-- CONTENT -->
    <div class="content">

        <!-- ══ PANEL: OVERVIEW ══ -->
        <div class="panel active" id="panel-overview">
            <p class="section-sub">
                Selamat <?= (date('H') < 12 ? 'pagi' : (date('H') < 17 ? 'siang' : 'malam')) ?>,
                <strong><?= $namaDisplay ?></strong> — berikut ringkasan hari ini.
            </p>

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
                            ✏️ Edit
                        </button>
                        <button class="btn-outline" style="font-size:10px; padding:7px 16px"
                            onclick="window.print()">🖨️ Cetak</button>
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


        <!-- ══ PANEL: PROFIL ══ -->
        <div class="panel" id="panel-profil">
            <div class="page-header">
                <div>
                    <h2 class="section-title">Profil <em>Saya</em></h2>
                    <p class="section-sub">Kelola data profil dan informasi profesional Anda</p>
                </div>
            </div>

            <!-- Hero profil -->
            <div class="profil-hero">
                <div class="profil-avatar-wrap">
                    <div class="profil-avatar">
                        <img src="<?= $fotoUrl ?>" alt="<?= $namaDisplay ?>">
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
                    <div class="profil-stat" style="margin-top:8px">
                        <div class="profil-stat-val"><?= (int)$statBulan['total_rm'] ?></div>
                        <div class="profil-stat-lbl">Bulan Ini</div>
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

<!-- Toast -->
<div class="toast" id="toast"><span id="toast-msg">Berhasil disimpan</span></div>

    <script src="../../asset/js/dokter.js?v=2"></script>
    <!-- LOGOUT CONFIRMATION MODAL -->
    <div id="logout-modal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.45);backdrop-filter:blur(4px);align-items:center;justify-content:center">
        <div style="background:#fff;border-radius:20px;padding:40px 36px;width:360px;text-align:center;box-shadow:0 24px 64px rgba(0,0,0,0.18);animation:logoutFadeIn .25s ease">
            <div style="width:56px;height:56px;background:#fef0f0;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:22px">&#x23FB;</div>
            <div style="font-family:'Playfair Display',serif;font-size:20px;color:#2D3436;margin-bottom:10px">Yakin ingin <em>keluar</em>?</div>
            <p style="font-size:13px;color:#64748b;margin-bottom:28px;line-height:1.6">Sesi Anda sebagai <strong>Dokter</strong> akan diakhiri. Anda perlu login kembali untuk mengakses portal.</p>
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