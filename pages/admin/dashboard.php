<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/Signin.php');
    exit;
}
$conn = require_once '../../backend/koneksi.php';

// ── Statistik ──
$total_pasien     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM pasien"))['n'];
$dokter_aktif     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM dokter WHERE status='Aktif'"))['n'];
$appt_hari_ini    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM appointment WHERE tanggal=CURDATE()"))['n'];
$pendapatan_bulan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(jumlah),0) AS n FROM pembayaran WHERE status='Lunas' AND MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW())"))['n'];

// ── Data per panel ──
$appt_today   = mysqli_query($conn, "SELECT a.id,a.jam,a.status,p.nama AS nama_pasien,d.nama AS nama_dokter,t.nama AS nama_treatment FROM appointment a JOIN pasien p ON a.pasien_id=p.id JOIN dokter d ON a.dokter_id=d.id LEFT JOIN treatment t ON a.treatment_id=t.id WHERE a.tanggal=CURDATE() ORDER BY a.jam ASC LIMIT 10");
$aktivitas    = mysqli_query($conn, "SELECT * FROM log_aktivitas ORDER BY created_at DESC LIMIT 5");
$pasien_list  = mysqli_query($conn, "SELECT p.*, (SELECT t.nama FROM appointment a2 LEFT JOIN treatment t ON t.id=a2.treatment_id WHERE a2.pasien_id=p.id ORDER BY a2.id DESC LIMIT 1) AS treatment_terakhir FROM pasien p ORDER BY p.id DESC LIMIT 100");
$dokter_list  = mysqli_query($conn, "SELECT * FROM dokter ORDER BY id ASC");
$jadwal_list  = mysqli_query($conn, "SELECT j.*,d.nama AS nama_dokter,t.nama AS nama_treatment FROM jadwal_dokter j JOIN dokter d ON j.dokter_id=d.id LEFT JOIN treatment t ON j.treatment_id=t.id ORDER BY d.nama ASC");
$log_list     = mysqli_query($conn, "SELECT * FROM log_aktivitas ORDER BY created_at DESC LIMIT 50");
$treatment_list = mysqli_query($conn, "SELECT * FROM treatment ORDER BY urutan ASC");
$laporan      = mysqli_query($conn, "SELECT d.nama AS nama_dokter,COUNT(a.id) AS total_appt,SUM(a.status='Selesai') AS selesai,SUM(a.status='Dibatalkan') AS batal,COALESCE(SUM(py.jumlah),0) AS pendapatan,d.rating FROM dokter d LEFT JOIN appointment a ON a.dokter_id=d.id AND MONTH(a.tanggal)=MONTH(NOW()) AND YEAR(a.tanggal)=YEAR(NOW()) LEFT JOIN pembayaran py ON py.appointment_id=a.id AND py.status='Lunas' GROUP BY d.id,d.nama,d.rating ORDER BY pendapatan DESC");
$pesan_list  = mysqli_query($conn, "SELECT * FROM pesan_kontak ORDER BY created_at DESC LIMIT 50");
$pesan_belum = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM pesan_kontak WHERE sudah_baca=0"))['n'];
$admin        = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=".(int)$_SESSION['user_id']));

// Daftar dokter & treatment untuk select di modal
$dlist_modal  = mysqli_query($conn, "SELECT id,nama FROM dokter WHERE status='Aktif' ORDER BY nama");
$tlist_modal  = mysqli_query($conn, "SELECT id,nama FROM treatment WHERE status='Aktif' ORDER BY urutan");

function rupiah(float $n): string {
    if ($n >= 1_000_000) return 'Rp '.number_format($n/1_000_000,1,',','.').' Jt';
    return 'Rp '.number_format($n,0,',','.');
}
function badge_appt(string $s): string {
    return match($s) {
        'Selesai'     => '<span class="badge badge-green">Selesai</span>',
        'Berlangsung' => '<span class="badge badge-yellow">Berlangsung</span>',
        'Terjadwal'   => '<span class="badge badge-gray">Terjadwal</span>',
        'Dibatalkan'  => '<span class="badge badge-pink">Dibatalkan</span>',
        default       => '<span class="badge badge-gray">'.$s.'</span>',
    };
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
    <link rel="stylesheet" href="../../asset/css/admin.css?v=4">
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
                <?php $unread=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS n FROM log_aktivitas WHERE DATE(created_at)=CURDATE()"))['n']; if($unread>0) echo "<span class='nav-badge'>$unread</span>"; ?>
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
            <div class="nav-section-label">Laporan</div>
            <a class="nav-item" onclick="showPanel('laporan',this)">Laporan</a>
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
        <div class="topbar">
            <div class="topbar-left">
                <div class="page-title" id="topbar-title">Dashboard</div>
                <div class="breadcrumb" id="topbar-bc">GlowCare Admin → Dashboard</div>
            </div>
            <div class="topbar-right">
                <a href="../../backend/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <div class="content">

        <!-- ══ PANEL DASHBOARD ══ -->
        <div class="panel active" id="panel-dashboard">
            <p class="section-sub">Selamat datang, <strong><?= htmlspecialchars($admin['username']) ?></strong> — <?= $tgl_now ?></p>
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
                                <button class="act-btn" style="border: 1px solid #a7f3d0; padding: 4px 10px; margin-right: 4px;" onclick="editPasien(<?= htmlspecialchars(json_encode($p)) ?>)" title="Edit">Edit</button>
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
                <button class="btn-add" onclick="openModal('modal-dokter')">+ Tambah Dokter</button>
            </div>
            <div class="card">
                <table class="data-table">
                    <thead><tr><th>ID</th><th>Nama</th><th>Spesialisasi</th><th>Pengalaman</th><th>Total Pasien</th><th>Rating</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                    <?php mysqli_data_seek($dokter_list,0); while($d=mysqli_fetch_assoc($dokter_list)):
                        $badge_d=match($d['status']){'Aktif'=>'badge-green','Cuti'=>'badge-yellow',default=>'badge-gray'}; ?>
                        <tr>
                            <td style="color:#7a7571;font-size:11px">#D-00<?= $d['id'] ?></td>
                            <td><div style="display:flex;align-items:center;gap:8px"><span class="avatar"><?= strtoupper(substr($d['nama'],0,1)) ?></span><span class="td-name"><?= htmlspecialchars($d['nama']) ?></span></div></td>
                            <td><span class="badge badge-pink"><?= htmlspecialchars($d['spesialisasi']) ?></span></td>
                            <td><?= $d['pengalaman'] ?>+ Thn</td>
                            <td style="text-align:center"><?= number_format($d['total_pasien']) ?></td>
                            <td>⭐ <?= number_format($d['rating'],1) ?></td>
                            <td><span class="badge <?= $badge_d ?>"><?= $d['status'] ?></span></td>
                            <td>
                                <button class="act-btn" style="border: 1px solid #a7f3d0; padding: 4px 10px; margin-right: 4px;" onclick="editDokter(<?= htmlspecialchars(json_encode($d)) ?>)" title="Edit">Edit</button>
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
                <div><h2 class="section-title">Jadwal <em>Dokter</em></h2><p class="section-sub">Atur jadwal praktik seluruh dokter</p></div>
                <button class="btn-add" onclick="openModal('modal-jadwal')">+ Tambah Jadwal</button>
            </div>
            <div class="card">
                <table class="data-table">
                    <thead><tr><th>Dokter</th><th>Hari</th><th>Jam Mulai</th><th>Jam Selesai</th><th>Treatment</th><th>Max Pasien</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                    <?php mysqli_data_seek($jadwal_list,0); while($j=mysqli_fetch_assoc($jadwal_list)): ?>
                        <tr>
                            <td><span class="td-name"><?= htmlspecialchars($j['nama_dokter']) ?></span></td>
                            <td><?= htmlspecialchars($j['hari']) ?></td>
                            <td><?= date('H:i',strtotime($j['jam_mulai'])) ?></td>
                            <td><?= date('H:i',strtotime($j['jam_selesai'])) ?></td>
                            <td><?= htmlspecialchars($j['nama_treatment']??'Semua Treatment') ?></td>
                            <td style="text-align:center"><?= $j['max_pasien'] ?></td>
                            <td><span class="badge badge-green">Aktif</span></td>
                            <td>
                                <button class="act-btn" style="border: 1px solid #a7f3d0; padding: 4px 10px; margin-right: 4px;" onclick="editJadwal(<?= htmlspecialchars(json_encode($j)) ?>)" title="Edit">Edit</button>
                                <button class="act-btn" style="border: 1px solid #e05050; color: #e05050; padding: 4px 10px;" onclick="confirmDelete('hapus_jadwal.php','<?= $j['id'] ?>','jadwal ini')" title="Hapus">Hapus</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ══ PANEL TREATMENT ══ -->
        <div class="panel" id="panel-treatment">
            <div class="page-header">
                <div><h2 class="section-title">Kelola <em>Treatment</em></h2><p class="section-sub">Data treatment yang tampil di halaman utama</p></div>
                <button class="btn-add" onclick="openModal('modal-treatment')">+ Tambah Treatment</button>
            </div>
            <div class="card">
                <table class="data-table">
                    <thead><tr><th>No</th><th>Gambar</th><th>Nama</th><th>Kategori</th><th>Deskripsi</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                    <?php mysqli_data_seek($treatment_list,0); while($tr=mysqli_fetch_assoc($treatment_list)):
                        $img=$tr['gambar_url']?:($tr['gambar_file']?'../../uploads/treatment/'.$tr['gambar_file']:''); ?>
                        <tr>
                            <td style="color:#7a7571;font-size:11px"><?= $tr['urutan'] ?></td>
                            <td>
                                <?php if($img): ?>
                                    <img src="<?= htmlspecialchars($img) ?>" class="treatment-thumb">
                                <?php else: ?>
                                    <div style="width:50px;height:38px;background:#a7f3d0;border-radius:6px;display:flex;align-items:center;justify-content:center">💉</div>
                                <?php endif; ?>
                            </td>
                            <td><div class="td-name"><?= htmlspecialchars($tr['nama']) ?></div></td>
                            <td><span class="badge badge-pink"><?= htmlspecialchars($tr['kategori']) ?></span></td>
                            <td style="max-width:200px;font-size:12px;color:#585552"><?= htmlspecialchars(mb_substr($tr['deskripsi'],0,70)) ?>...</td>
                            <td><?= $tr['status']==='Aktif'?'<span class="badge badge-green">Aktif</span>':'<span class="badge badge-gray">Nonaktif</span>' ?></td>
                            <td>
                                <button class="act-btn" style="border: 1px solid #a7f3d0; padding: 4px 10px; margin-right: 4px;" onclick="editTreatment(<?= htmlspecialchars(json_encode($tr)) ?>)" title="Edit">Edit</button>
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
                                            <span style="font-size:9px;letter-spacing:1px;text-transform:uppercase;color:#064e3b">● Baru</span>
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
                                <?= $belum
                                    ? '<span class="badge badge-pink">Belum Dibaca</span>'
                                    : '<span class="badge badge-green">Sudah Dibaca</span>' ?>
                            </td>
        
                            <!-- Aksi -->
                            <td style="white-space:nowrap">
                                <!-- Tombol Lihat / tandai baca -->
                                <button class="act-btn" style="border: 1px solid #a7f3d0; padding: 4px 10px; margin-right: 4px;"
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
                        · <span style="color:#064e3b;font-weight:600"><?= $pesan_belum ?> belum dibaca</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ══ PANEL LAPORAN ══ -->
        <div class="panel" id="panel-laporan">
            <h2 class="section-title">Laporan <em>Bulanan</em></h2>
            <p class="section-sub">Ringkasan data klinik bulan ini</p>
            <div class="card">
                <div class="card-header"><div class="card-title">Ringkasan — <?= $bln_ind[(int)date('n')].' '.date('Y') ?></div></div>
                <table class="data-table">
                    <thead><tr><th>Dokter</th><th>Total Appointment</th><th>Selesai</th><th>Dibatalkan</th><th>Pendapatan</th><th>Rating</th></tr></thead>
                    <tbody>
                    <?php $tot_a=$tot_s=$tot_b=$tot_p=0; mysqli_data_seek($laporan,0); while($lp=mysqli_fetch_assoc($laporan)):
                        $tot_a+=$lp['total_appt'];$tot_s+=$lp['selesai'];$tot_b+=$lp['batal'];$tot_p+=$lp['pendapatan']; ?>
                        <tr>
                            <td><span class="td-name"><?= htmlspecialchars($lp['nama_dokter']) ?></span></td>
                            <td style="text-align:center"><?= $lp['total_appt'] ?></td>
                            <td style="text-align:center"><?= $lp['selesai'] ?></td>
                            <td style="text-align:center;color:#064e3b"><?= $lp['batal'] ?></td>
                            <td><?= rupiah((float)$lp['pendapatan']) ?></td>
                            <td>⭐ <?= number_format($lp['rating'],1) ?></td>
                        </tr>
                    <?php endwhile; ?>
                        <tr style="background:#F9F7F2;font-weight:600">
                            <td><strong>Total</strong></td>
                            <td style="text-align:center"><strong><?= $tot_a ?></strong></td>
                            <td style="text-align:center"><strong><?= $tot_s ?></strong></td>
                            <td style="text-align:center;color:#064e3b"><strong><?= $tot_b ?></strong></td>
                            <td><strong><?= rupiah((float)$tot_p) ?></strong></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
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
            <form method="POST" id="form-dokter">
                <input type="hidden" name="id" id="md-id">
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
                            <option>Surgical</option><option>Injectable</option><option>Technology</option><option>Contouring</option><option>Skincare</option><option>Other</option>
                        </select>
                    </div>
                    <div class="form-group"><label class="form-label">Urutan Tampil</label><input class="form-input" type="number" name="urutan" id="mt-urutan" value="1" min="1"></div>
                    <div class="form-group full"><label class="form-label">URL Gambar</label><input class="form-input" name="gambar_url" id="mt-gambar" placeholder="https://images.unsplash.com/..."></div>
                    <div class="form-group full"><label class="form-label">Link Halaman Detail</label><input class="form-input" name="link_halaman" id="mt-link" placeholder="pages/treatment/nama.php"></div>
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
    <!-- ═══ MODAL DETAIL PESAN ═══ -->
    <div class="modal-overlay" id="modal-pesan" onclick="closeModalOutside(event,'modal-pesan')">
        <div class="modal" style="width:540px">
            <h3 class="modal-title">Detail <em>Pesan</em></h3>
            <p class="modal-sub" id="mp-pengirim-sub">Dari pengunjung</p>
    
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
                    <label class="form-label">Pesan</label>
                    <div class="form-textarea" id="mp-detail-pesan"
                        style="background:#F9F7F2;cursor:default;min-height:100px;line-height:1.6"></div>
                </div>
            </div>
    
            <!-- Tombol aksi di dalam modal -->
            <div class="modal-footer" id="mp-detail-footer">
                <!-- JS akan inject tombol "Tandai Baca" jika belum dibaca -->
                <button class="btn-cancel" onclick="closeModal('modal-pesan')">Tutup</button>
            </div>
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

    <script src="../../asset/js/admin.js?v=2"></script>
</body>
</html>
<?php mysqli_close($conn); ?>