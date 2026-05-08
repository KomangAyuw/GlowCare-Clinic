<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GlowCare Admin Dashboard</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@300;400;500&display=swap">
    <link rel="stylesheet" href="../../asset/css/admin.css">
</head>
<body>
    <!-- ══ SIDEBAR ══ -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="brand">GlowCare Clinic</div>
            <div class="role">Admin Panel</div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-label">Utama</div>
                <a class="nav-item active" onclick="showPanel('dashboard', this)">
                    <span class="nav-icon"></span> Dashboard
                </a>
                <a class="nav-item" onclick="showPanel('aktivitas', this)">
                    <span class="nav-icon"></span> Aktivitas
                    <span class="nav-badge">5</span>
                </a>
            </div>
            <div class="nav-section">
                <div class="nav-section-label">Manajemen</div>
                <a class="nav-item" onclick="showPanel('pasien', this)">
                    <span class="nav-icon"></span> Data Pasien
                </a>
                <a class="nav-item" onclick="showPanel('dokter', this)">
                    <span class="nav-icon"></span> Data Dokter
                </a>
                <a class="nav-item" onclick="showPanel('jadwal', this)">
                    <span class="nav-icon"></span> Jadwal Dokter
                </a>
            </div>
            <div class="nav-section">
                <div class="nav-section-label">Laporan</div>
                <a class="nav-item" onclick="showPanel('laporan', this)">
                    <span class="nav-icon"></span> Laporan
                </a>
            </div>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="user-avatar">A</div>
                <div class="user-info">
                    <div class="name">Admin</div>
                    <div class="label">Administrator</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- ══ MAIN ══ -->
    <div class="main">

        <!-- TOPBAR -->
        <div class="topbar">
            <div class="topbar-left">
                <div class="page-title" id="topbar-title">Dashboard</div>
                <div class="breadcrumb" id="topbar-bc">GlowCare Admin → Dashboard</div>
            </div>
            <div class="topbar-right">
                <input class="topbar-search" type="text" placeholder="Cari pasien, dokter...">
                <div class="notif-btn"><div class="notif-dot"></div></div>
            </div>
        </div>

        <!-- CONTENT -->
        <div class="content">

            <!-- ══ PANEL: DASHBOARD ══ -->
            <div class="panel active" id="panel-dashboard">
                <p class="section-sub">Selamat datang kembali — Sabtu, 02 Mei 2026</p>

                <div class="stats-row">
                    <div class="stat-card">
                        <div class="stat-icon"></div>
                        <div class="stat-value">1.248</div>
                        <div class="stat-label">Total Pasien</div>
                        <div class="stat-change">↑ 12%</div>
                    </div>
                    <div class="stat-card green">
                        <div class="stat-icon"></div>
                        <div class="stat-value">34</div>
                        <div class="stat-label">Janji Hari Ini</div>
                        <div class="stat-change">↑ 8%</div>
                    </div>
                    <div class="stat-card purple">
                        <div class="stat-icon"></div>
                        <div class="stat-value">8</div>
                        <div class="stat-label">Dokter Aktif</div>
                        <div class="stat-change">→ 0%</div>
                    </div>
                    <div class="stat-card orange">
                        <div class="stat-icon"></div>
                        <div class="stat-value">68Jt</div>
                        <div class="stat-label">Pendapatan Bulan Ini</div>
                        <div class="stat-change down">↓ 3%</div>
                    </div>
                </div>

                <div class="two-col">
                    <!-- Chart -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Kunjungan Pasien — 2026</div>
                            <a class="card-action">Lihat detail →</a>
                        </div>
                        <div class="chart-area">
                            <div class="chart-bars">
                                <div class="chart-bar-wrap"><div class="chart-bar" style="height:55%"></div><div class="chart-label">Jan</div></div>
                                <div class="chart-bar-wrap"><div class="chart-bar" style="height:70%"></div><div class="chart-label">Feb</div></div>
                                <div class="chart-bar-wrap"><div class="chart-bar" style="height:62%"></div><div class="chart-label">Mar</div></div>
                                <div class="chart-bar-wrap"><div class="chart-bar" style="height:88%"></div><div class="chart-label">Apr</div></div>
                                <div class="chart-bar-wrap"><div class="chart-bar" style="height:95%"></div><div class="chart-label">Mei</div></div>
                                <div class="chart-bar-wrap"><div class="chart-bar" style="height:40%"></div><div class="chart-label">Jun</div></div>
                            </div>
                            <div class="chart-legend">
                                <div class="legend-item"><div class="legend-dot"></div> Kunjungan</div>
                                <div class="legend-item"><div class="legend-dot green"></div> Target</div>
                            </div>
                        </div>
                    </div>

                    <!-- Aktivitas terkini -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Aktivitas Terkini</div>
                            <a class="card-action" onclick="showPanel('aktivitas', document.querySelector('[onclick*=aktivitas]'))">Semua →</a>
                        </div>
                        <div class="activity-list">
                            <div class="activity-item">
                                <div class="activity-dot"></div>
                                <div>
                                    <div class="activity-text"><strong>Siti Rahayu</strong> mendaftar pasien baru</div>
                                    <div class="activity-time">5 menit lalu</div>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-dot green"></div>
                                <div>
                                    <div class="activity-text"><strong>Dr. Michael Chen</strong> konfirmasi jadwal Senin</div>
                                    <div class="activity-time">18 menit lalu</div>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-dot orange"></div>
                                <div>
                                    <div class="activity-text"><strong>Budi Santoso</strong> membatalkan appointment</div>
                                    <div class="activity-time">42 menit lalu</div>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-dot"></div>
                                <div>
                                    <div class="activity-text"><strong>Dr. Anisa Putri</strong> menyelesaikan treatment Facelift</div>
                                    <div class="activity-time">1 jam lalu</div>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-dot green"></div>
                                <div>
                                    <div class="activity-text"><strong>3 pasien baru</strong> menyelesaikan pembayaran</div>
                                    <div class="activity-time">2 jam lalu</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Appointment hari ini -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Appointment Hari Ini</div>
                        <a class="card-action" onclick="showPanel('jadwal', document.querySelector('[onclick*=jadwal]'))">Kelola Jadwal →</a>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Pasien</th>
                                <th>Dokter</th>
                                <th>Treatment</th>
                                <th>Jam</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="avatar">S</span><span class="td-name">Siti Rahayu</span></td>
                                <td>Dr. Anisa Putri</td>
                                <td>Facelift Consultation</td>
                                <td class="td-jam">09.00</td>
                                <td><span class="badge badge-green">Selesai</span></td>
                            </tr>
                            <tr>
                                <td><span class="avatar">A</span><span class="td-name">Andini Kusuma</span></td>
                                <td>Dr. Michael Chen</td>
                                <td>Laser Treatment</td>
                                <td class="td-jam">10.30</td>
                                <td><span class="badge badge-yellow">Berlangsung</span></td>
                            </tr>
                            <tr>
                                <td><span class="avatar">R</span><span class="td-name">Rina Wulandari</span></td>
                                <td>Dr. Marina Crystine</td>
                                <td>Botox & Fillers</td>
                                <td class="td-jam">13.00</td>
                                <td><span class="badge badge-pink">Menunggu</span></td>
                            </tr>
                            <tr>
                                <td><span class="avatar">D</span><span class="td-name">Dewi Anggraini</span></td>
                                <td>Dr. Anisa Putri</td>
                                <td>Body Contouring</td>
                                <td class="td-jam">15.00</td>
                                <td><span class="badge badge-pink">Menunggu</span></td>
                            </tr>
                            <tr>
                                <td><span class="avatar">M</span><span class="td-name">Maya Sari</span></td>
                                <td>Dr. Michael Chen</td>
                                <td>Botox</td>
                                <td class="td-jam">16.30</td>
                                <td><span class="badge badge-gray">Terjadwal</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ══ PANEL: PASIEN ══ -->
            <div class="panel" id="panel-pasien">
                <div class="page-header">
                    <div>
                        <h2 class="section-title">Data <em>Pasien</em></h2>
                        <p class="section-sub">Kelola seluruh data pasien terdaftar di GlowCare Clinic</p>
                    </div>
                    <button class="btn-add" onclick="openModal('modal-pasien')">+ Tambah Pasien</button>
                </div>

                <div class="filter-bar">
                    <input class="filter-input" type="text" placeholder="Cari nama, nomor...">
                    <select class="filter-select">
                        <option>Semua Treatment</option>
                        <option>Facelift</option>
                        <option>Botox & Fillers</option>
                        <option>Laser Treatment</option>
                        <option>Body Contouring</option>
                    </select>
                    <select class="filter-select">
                        <option>Semua Status</option>
                        <option>Aktif</option>
                        <option>Tidak Aktif</option>
                    </select>
                </div>

                <div class="card">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Pasien</th>
                                <th>Kontak</th>
                                <th>Treatment Terakhir</th>
                                <th>Kunjungan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="color:#b89098; font-size:11px">#P-0041</td>
                                <td><span class="avatar">S</span><div style="display:inline-block"><span class="td-name">Siti Rahayu</span><span class="td-sub">28 Tahun · Perempuan</span></div></td>
                                <td><div style="font-size:13px">+62 812 1234 5678</div><div style="font-size:11px;color:#b89098">siti@email.com</div></td>
                                <td>Facelift Consultation</td>
                                <td style="text-align:center">12</td>
                                <td><span class="badge badge-green">Aktif</span></td>
                                <td>
                                    <button class="act-btn" title="Edit" onclick="openModal('modal-pasien')"></button>
                                    <button class="act-btn" title="Detail"></button>
                                    <button class="act-btn" title="Hapus"></button>
                                </td>
                            </tr>
                            <tr>
                                <td style="color:#b89098; font-size:11px">#P-0042</td>
                                <td><span class="avatar">A</span><div style="display:inline-block"><span class="td-name">Andini Kusuma</span><span class="td-sub">32 Tahun · Perempuan</span></div></td>
                                <td><div style="font-size:13px">+62 813 9876 5432</div><div style="font-size:11px;color:#b89098">andini@email.com</div></td>
                                <td>Laser Treatment</td>
                                <td style="text-align:center">7</td>
                                <td><span class="badge badge-green">Aktif</span></td>
                                <td>
                                    <button class="act-btn" onclick="openModal('modal-pasien')"></button>
                                    <button class="act-btn"></button>
                                    <button class="act-btn"></button>
                                </td>
                            </tr>
                            <tr>
                                <td style="color:#b89098; font-size:11px">#P-0043</td>
                                <td><span class="avatar">B</span><div style="display:inline-block"><span class="td-name">Budi Santoso</span><span class="td-sub">45 Tahun · Laki-laki</span></div></td>
                                <td><div style="font-size:13px">+62 819 1111 2222</div><div style="font-size:11px;color:#b89098">budi@email.com</div></td>
                                <td>Body Contouring</td>
                                <td style="text-align:center">3</td>
                                <td><span class="badge badge-gray">Tidak Aktif</span></td>
                                <td>
                                    <button class="act-btn" onclick="openModal('modal-pasien')"></button>
                                    <button class="act-btn"></button>
                                    <button class="act-btn"></button>
                                </td>
                            </tr>
                            <tr>
                                <td style="color:#b89098; font-size:11px">#P-0044</td>
                                <td><span class="avatar">R</span><div style="display:inline-block"><span class="td-name">Rina Wulandari</span><span class="td-sub">25 Tahun · Perempuan</span></div></td>
                                <td><div style="font-size:13px">+62 857 3333 4444</div><div style="font-size:11px;color:#b89098">rina@email.com</div></td>
                                <td>Botox & Fillers</td>
                                <td style="text-align:center">5</td>
                                <td><span class="badge badge-green">Aktif</span></td>
                                <td>
                                    <button class="act-btn" onclick="openModal('modal-pasien')"></button>
                                    <button class="act-btn"></button>
                                    <button class="act-btn"></button>
                                </td>
                            </tr>
                            <tr>
                                <td style="color:#b89098; font-size:11px">#P-0045</td>
                                <td><span class="avatar">D</span><div style="display:inline-block"><span class="td-name">Dewi Anggraini</span><span class="td-sub">38 Tahun · Perempuan</span></div></td>
                                <td><div style="font-size:13px">+62 878 5555 6666</div><div style="font-size:11px;color:#b89098">dewi@email.com</div></td>
                                <td>Body Contouring</td>
                                <td style="text-align:center">9</td>
                                <td><span class="badge badge-green">Aktif</span></td>
                                <td>
                                    <button class="act-btn" onclick="openModal('modal-pasien')"></button>
                                    <button class="act-btn"></button>
                                    <button class="act-btn"></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div style="padding:16px 20px; border-top:1px solid #fdf0f5; display:flex; justify-content:space-between; align-items:center; font-size:12px; color:#b89098">
                        <span>Menampilkan 1-5 dari 1.248 pasien</span>
                        <div style="display:flex; gap:8px">
                            <button class="act-btn">← Prev</button>
                            <button class="act-btn" style="background:#c55085;color:#fff;border-radius:4px;padding:4px 10px">1</button>
                            <button class="act-btn">2</button>
                            <button class="act-btn">3</button>
                            <button class="act-btn">Next →</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══ PANEL: DOKTER ══ -->
            <div class="panel" id="panel-dokter">
                <div class="page-header">
                    <div>
                        <h2 class="section-title">Data <em>Dokter</em></h2>
                        <p class="section-sub">Kelola profil dan spesialisasi seluruh dokter klinik</p>
                    </div>
                    <button class="btn-add" onclick="openModal('modal-dokter')">+ Tambah Dokter</button>
                </div>

                <div class="card">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Dokter</th>
                                <th>Spesialisasi</th>
                                <th>Pengalaman</th>
                                <th>Total Pasien</th>
                                <th>Rating</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="color:#b89098; font-size:11px">#D-001</td>
                                <td><span class="avatar">A</span><span class="td-name">Dr. Anisa Putri</span></td>
                                <td><span class="badge badge-pink">Plastic Surgeon</span></td>
                                <td>10+ Tahun</td>
                                <td style="text-align:center">412</td>
                                <td>5.0</td>
                                <td><span class="badge badge-green">Aktif</span></td>
                                <td>
                                    <button class="act-btn" onclick="openModal('modal-dokter')"></button>
                                    <button class="act-btn"></button>
                                    <button class="act-btn"></button>
                                </td>
                            </tr>
                            <tr>
                                <td style="color:#b89098; font-size:11px">#D-002</td>
                                <td><span class="avatar">M</span><span class="td-name">Dr. Marina Crystine</span></td>
                                <td><span class="badge badge-pink">Aesthetic Physician</span></td>
                                <td>8 Tahun</td>
                                <td style="text-align:center">387</td>
                                <td>5.0</td>
                                <td><span class="badge badge-green">Aktif</span></td>
                                <td>
                                    <button class="act-btn" onclick="openModal('modal-dokter')"></button>
                                    <button class="act-btn"></button>
                                    <button class="act-btn"></button>
                                </td>
                            </tr>
                            <tr>
                                <td style="color:#b89098; font-size:11px">#D-003</td>
                                <td><span class="avatar">M</span><span class="td-name">Dr. Michael Chen</span></td>
                                <td><span class="badge badge-pink">Dermatologist</span></td>
                                <td>12 Tahun</td>
                                <td style="text-align:center">449</td>
                                <td>5.0</td>
                                <td><span class="badge badge-green">Aktif</span></td>
                                <td>
                                    <button class="act-btn" onclick="openModal('modal-dokter')"></button>
                                    <button class="act-btn"></button>
                                    <button class="act-btn"></button>
                                </td>
                            </tr>
                            <tr>
                                <td style="color:#b89098; font-size:11px">#D-004</td>
                                <td><span class="avatar">S</span><span class="td-name">Dr. Sarah Dewi</span></td>
                                <td><span class="badge badge-pink">Aesthetic Physician</span></td>
                                <td>5 Tahun</td>
                                <td style="text-align:center">198</td>
                                <td>4.8</td>
                                <td><span class="badge badge-yellow">Cuti</span></td>
                                <td>
                                    <button class="act-btn" onclick="openModal('modal-dokter')"></button>
                                    <button class="act-btn"></button>
                                    <button class="act-btn"></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ══ PANEL: JADWAL ══ -->
            <div class="panel" id="panel-jadwal">
                <div class="page-header">
                    <div>
                        <h2 class="section-title">Jadwal <em>Dokter</em></h2>
                        <p class="section-sub">Atur dan pantau jadwal praktik seluruh dokter klinik</p>
                    </div>
                    <button class="btn-add" onclick="openModal('modal-jadwal')">+ Tambah Jadwal</button>
                </div>

                <!-- Week nav -->
                <div style="display:flex; align-items:center; gap:16px; margin-bottom:20px">
                    <button class="act-btn">← Minggu Lalu</button>
                    <span style="font-family:'Playfair Display',serif; font-size:16px; color:#3d1a22">28 Apr - 03 Mei 2026</span>
                    <button class="act-btn">Minggu Depan →</button>
                    <button class="btn-add" style="margin-left:auto; background:transparent; color:#c55085; border:1px solid #f2c4ce; padding:8px 18px; font-size:11px">Tampilan List</button>
                </div>

                <div class="schedule-week">
                    <!-- Header -->
                    <div class="sch-head">Jam</div>
                    <div class="sch-head">Senin<br><span style="font-size:11px; color:#c55085">28 Apr</span></div>
                    <div class="sch-head">Selasa<br><span style="font-size:11px">29 Apr</span></div>
                    <div class="sch-head">Rabu<br><span style="font-size:11px">30 Apr</span></div>
                    <div class="sch-head">Kamis<br><span style="font-size:11px">01 Mei</span></div>
                    <div class="sch-head">Jumat<br><span style="font-size:11px">02 Mei</span></div>
                    <div class="sch-head">Sabtu<br><span style="font-size:11px">03 Mei</span></div>

                    <!-- 09:00 -->
                    <div class="sch-time">09:00</div>
                    <div class="sch-cell"><div class="sch-event">Dr. Anisa<br>Facelift</div></div>
                    <div class="sch-cell"></div>
                    <div class="sch-cell"><div class="sch-event teal">Dr. Marina<br>Botox</div></div>
                    <div class="sch-cell"></div>
                    <div class="sch-cell"><div class="sch-event">Dr. Michael<br>Laser</div></div>
                    <div class="sch-cell"></div>

                    <!-- 10:30 -->
                    <div class="sch-time">10:30</div>
                    <div class="sch-cell"></div>
                    <div class="sch-cell"><div class="sch-event purple">Dr. Michael<br>Dermatologi</div></div>
                    <div class="sch-cell"></div>
                    <div class="sch-cell"><div class="sch-event">Dr. Anisa<br>Rhinoplasty</div></div>
                    <div class="sch-cell"></div>
                    <div class="sch-cell"><div class="sch-event teal">Dr. Marina<br>Thread</div></div>

                    <!-- 13:00 -->
                    <div class="sch-time">13:00</div>
                    <div class="sch-cell"><div class="sch-event orange">Dr. Marina<br>CoolSculpting</div></div>
                    <div class="sch-cell"></div>
                    <div class="sch-cell"><div class="sch-event purple">Dr. Michael<br>Botox</div></div>
                    <div class="sch-cell"><div class="sch-event">Dr. Anisa<br>Konsul</div></div>
                    <div class="sch-cell"></div>
                    <div class="sch-cell"></div>

                    <!-- 15:00 -->
                    <div class="sch-time">15:00</div>
                    <div class="sch-cell"></div>
                    <div class="sch-cell"><div class="sch-event">Dr. Anisa<br>Body Cont.</div></div>
                    <div class="sch-cell"></div>
                    <div class="sch-cell"></div>
                    <div class="sch-cell"><div class="sch-event teal">Dr. Marina<br>Ultherapy</div></div>
                    <div class="sch-cell"><div class="sch-event purple">Dr. Michael<br>Laser</div></div>
                </div>

                <!-- Jadwal list -->
                <div class="card">
                    <div class="card-header"><div class="card-title">Semua Jadwal Aktif</div></div>
                    <table class="data-table">
                        <thead>
                            <tr><th>Dokter</th><th>Hari</th><th>Jam Mulai</th><th>Jam Selesai</th><th>Treatment</th><th>Slot Tersisa</th><th>Status</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="td-name">Dr. Anisa Putri</span></td>
                                <td>Senin, Rabu, Jumat</td>
                                <td class="td-jam">09:00</td>
                                <td class="td-jam">17:00</td>
                                <td>Facelift / Rhinoplasty</td>
                                <td style="text-align:center; color:#c55085">3 / 6</td>
                                <td><span class="badge badge-green">Aktif</span></td>
                                <td><button class="act-btn" onclick="openModal('modal-jadwal')"></button><button class="act-btn"></button></td>
                            </tr>
                            <tr>
                                <td><span class="td-name">Dr. Marina Crystine</span></td>
                                <td>Selasa, Kamis, Sabtu</td>
                                <td class="td-jam">10:00</td>
                                <td class="td-jam">18:00</td>
                                <td>Botox / CoolSculpting</td>
                                <td style="text-align:center; color:#c55085">5 / 8</td>
                                <td><span class="badge badge-green">Aktif</span></td>
                                <td><button class="act-btn" onclick="openModal('modal-jadwal')"></button><button class="act-btn"></button></td>
                            </tr>
                            <tr>
                                <td><span class="td-name">Dr. Michael Chen</span></td>
                                <td>Senin - Jumat</td>
                                <td class="td-jam">09:00</td>
                                <td class="td-jam">16:00</td>
                                <td>Laser / Dermatologi</td>
                                <td style="text-align:center; color:#6dbf9e">7 / 10</td>
                                <td><span class="badge badge-green">Aktif</span></td>
                                <td><button class="act-btn" onclick="openModal('modal-jadwal')"></button><button class="act-btn"></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ══ PANEL: AKTIVITAS ══ -->
            <div class="panel" id="panel-aktivitas">
                <h2 class="section-title">Log <em>Aktivitas</em></h2>
                <p class="section-sub">Pantau seluruh aktivitas dan kejadian dalam sistem</p>

                <div class="stats-row" style="grid-template-columns: repeat(3,1fr)">
                    <div class="stat-card">
                        <div class="stat-icon"></div>
                        <div class="stat-value">128</div>
                        <div class="stat-label">Aktivitas Hari Ini</div>
                    </div>
                    <div class="stat-card green">
                        <div class="stat-icon"></div>
                        <div class="stat-value">34</div>
                        <div class="stat-label">Appointment Selesai</div>
                    </div>
                    <div class="stat-card orange">
                        <div class="stat-icon"></div>
                        <div class="stat-value">3</div>
                        <div class="stat-label">Pembatalan</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Riwayat Aktivitas Sistem</div>
                        <div style="display:flex; gap:8px">
                            <select class="filter-select" style="font-size:11px; padding:6px 14px">
                                <option>Semua Tipe</option>
                                <option>Pasien</option>
                                <option>Dokter</option>
                                <option>Pembayaran</option>
                            </select>
                        </div>
                    </div>
                    <div style="padding: 0 24px 24px">
                        <div class="activity-item">
                            <div class="activity-dot"></div>
                            <div style="flex:1">
                                <div class="activity-text"><strong>Pasien Baru</strong> — Siti Rahayu mendaftar sebagai pasien baru (ID: #P-0041)</div>
                                <div class="activity-time">Hari ini, 08:14 · Admin</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-dot green"></div>
                            <div style="flex:1">
                                <div class="activity-text"><strong>Appointment Selesai</strong> — Dr. Anisa Putri menyelesaikan sesi Facelift untuk Siti Rahayu</div>
                                <div class="activity-time">Hari ini, 09:55 · Sistem</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-dot orange"></div>
                            <div style="flex:1">
                                <div class="activity-text"><strong>Pembatalan</strong> — Budi Santoso membatalkan appointment pukul 11:00 (Body Contouring)</div>
                                <div class="activity-time">Hari ini, 10:22 · Pasien</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-dot"></div>
                            <div style="flex:1">
                                <div class="activity-text"><strong>Pembayaran Diterima</strong> — Rp 2.500.000 dari Andini Kusuma untuk Laser Treatment</div>
                                <div class="activity-time">Hari ini, 10:48 · Kasir</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-dot"></div>
                            <div style="flex:1">
                                <div class="activity-text"><strong>Jadwal Diperbarui</strong> — Dr. Marina Crystine mengkonfirmasi jadwal Sabtu 03 Mei</div>
                                <div class="activity-time">Hari ini, 11:03 · Admin</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-dot green"></div>
                            <div style="flex:1">
                                <div class="activity-text"><strong>Appointment Selesai</strong> — Dr. Michael Chen menyelesaikan sesi Dermatologi untuk Andini Kusuma</div>
                                <div class="activity-time">Hari ini, 11:30 · Sistem</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-dot"></div>
                            <div style="flex:1">
                                <div class="activity-text"><strong>Data Dokter Diperbarui</strong> — Profil Dr. Sarah Dewi diperbarui (status: Cuti)</div>
                                <div class="activity-time">Hari ini, 12:10 · Admin</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══ PANEL: LAPORAN ══ -->
            <div class="panel" id="panel-laporan">
                <h2 class="section-title">Laporan <em>Data</em></h2>
                <p class="section-sub">Generate dan unduh laporan lengkap data klinik</p>

                <div class="report-grid">
                    <div class="report-card">
                        <div class="report-icon">👥</div>
                        <div class="report-name">Laporan Pasien</div>
                        <div class="report-desc">Data lengkap seluruh pasien terdaftar, riwayat kunjungan, dan treatment yang pernah dilakukan.</div>
                        <a class="report-link" onclick="showToast('Mengunduh laporan pasien...')">Unduh Laporan →</a>
                    </div>
                    <div class="report-card">
                        <div class="report-icon">🩺</div>
                        <div class="report-name">Laporan Dokter</div>
                        <div class="report-desc">Performa dokter, jumlah pasien ditangani, rating, dan jadwal kerja dalam periode tertentu.</div>
                        <a class="report-link" onclick="showToast('Mengunduh laporan dokter...')">Unduh Laporan →</a>
                    </div>
                    <div class="report-card">
                        <div class="report-icon"></div>
                        <div class="report-name">Laporan Keuangan</div>
                        <div class="report-desc">Pendapatan bulanan, rincian pembayaran, dan tren keuangan klinik per treatment.</div>
                        <a class="report-link" onclick="showToast('Mengunduh laporan keuangan...')">Unduh Laporan →</a>
                    </div>
                    <div class="report-card">
                        <div class="report-icon"></div>
                        <div class="report-name">Laporan Jadwal</div>
                        <div class="report-desc">Rekap appointment, pembatalan, dan tingkat kehadiran pasien per dokter per bulan.</div>
                        <a class="report-link" onclick="showToast('Mengunduh laporan jadwal...')">Unduh Laporan →</a>
                    </div>
                    <div class="report-card">
                        <div class="report-icon"></div>
                        <div class="report-name">Laporan Treatment</div>
                        <div class="report-desc">Treatment terpopuler, statistik keberhasilan, dan feedback pasien per kategori layanan.</div>
                        <a class="report-link" onclick="showToast('Mengunduh laporan treatment...')">Unduh Laporan →</a>
                    </div>
                    <div class="report-card">
                        <div class="report-icon"></div>
                        <div class="report-name">Laporan Aktivitas</div>
                        <div class="report-desc">Log lengkap aktivitas sistem, tindakan admin, dan catatan kejadian penting dalam klinik.</div>
                        <a class="report-link" onclick="showToast('Mengunduh laporan aktivitas...')">Unduh Laporan →</a>
                    </div>
                </div>

                <!-- Summary table -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Ringkasan Bulanan — Mei 2026</div>
                        <div style="display:flex; gap:8px">
                            <select class="filter-select" style="font-size:11px; padding:6px 14px">
                                <option>Mei 2026</option>
                                <option>Apr 2026</option>
                                <option>Mar 2026</option>
                            </select>
                            <button class="btn-add" style="font-size:10px; padding:8px 16px" onclick="showToast('Mengekspor ke PDF...')">Export PDF</button>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr><th>Dokter</th><th>Total Pasien</th><th>Appointment</th><th>Pembatalan</th><th>Pendapatan</th><th>Rating Avg</th></tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="td-name">Dr. Anisa Putri</span></td>
                                <td style="text-align:center">86</td>
                                <td style="text-align:center">92</td>
                                <td style="text-align:center; color:#c55085">6</td>
                                <td>Rp 28.500.000</td>
                                <td>5.0</td>
                            </tr>
                            <tr>
                                <td><span class="td-name">Dr. Marina Crystine</span></td>
                                <td style="text-align:center">74</td>
                                <td style="text-align:center">78</td>
                                <td style="text-align:center; color:#c55085">4</td>
                                <td>Rp 21.000.000</td>
                                <td>5.0</td>
                            </tr>
                            <tr>
                                <td><span class="td-name">Dr. Michael Chen</span></td>
                                <td style="text-align:center">92</td>
                                <td style="text-align:center">96</td>
                                <td style="text-align:center; color:#c55085">4</td>
                                <td>Rp 18.500.000</td>
                                <td>5.0</td>
                            </tr>
                            <tr style="background:#fdf0f5; font-weight:500">
                                <td><strong>Total</strong></td>
                                <td style="text-align:center"><strong>252</strong></td>
                                <td style="text-align:center"><strong>266</strong></td>
                                <td style="text-align:center; color:#c55085"><strong>14</strong></td>
                                <td><strong>Rp 68.000.000</strong></td>
                                <td><strong>5.0</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div><!-- /content -->
    </div><!-- /main -->

    <!-- ══ MODAL: PASIEN ══ -->
    <div class="modal-overlay" id="modal-pasien" onclick="closeModalOutside(event,'modal-pasien')">
        <div class="modal" style="position:relative">
            <h3 class="modal-title">Tambah / Edit <em>Pasien</em></h3>
            <p class="modal-sub">Isi data pasien dengan lengkap dan benar</p>
            <div class="form-row">
                <div class="form-group"><label class="form-label">Nama Lengkap</label><input class="form-input" placeholder="Nama lengkap pasien"></div>
                <div class="form-group"><label class="form-label">Tanggal Lahir</label><input class="form-input" type="date"></div>
                <div class="form-group"><label class="form-label">Jenis Kelamin</label><select class="form-select"><option>Perempuan</option><option>Laki-laki</option></select></div>
                <div class="form-group"><label class="form-label">No. Telepon</label><input class="form-input" placeholder="+62"></div>
                <div class="form-group full"><label class="form-label">Email</label><input class="form-input" placeholder="email@contoh.com"></div>
                <div class="form-group full"><label class="form-label">Alamat</label><input class="form-input" placeholder="Alamat lengkap"></div>
                <div class="form-group"><label class="form-label">Treatment Pilihan</label><select class="form-select"><option>Facelift</option><option>Botox & Fillers</option><option>Laser Treatment</option><option>Body Contouring</option></select></div>
                <div class="form-group"><label class="form-label">Status</label><select class="form-select"><option>Aktif</option><option>Tidak Aktif</option></select></div>
                <div class="form-group full"><label class="form-label">Catatan Medis</label><textarea class="form-textarea" placeholder="Alergi, kondisi khusus, catatan lainnya..."></textarea></div>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="closeModal('modal-pasien')">Batal</button>
                <button class="btn-save" onclick="closeModal('modal-pasien'); showToast('Data pasien berhasil disimpan ✓')">Simpan Data</button>
            </div>
        </div>
    </div>

    <!-- ══ MODAL: DOKTER ══ -->
    <div class="modal-overlay" id="modal-dokter" onclick="closeModalOutside(event,'modal-dokter')">
        <div class="modal" style="position:relative">
            <h3 class="modal-title">Tambah / Edit <em>Dokter</em></h3>
            <p class="modal-sub">Lengkapi profil dan spesialisasi dokter</p>
            <div class="form-row">
                <div class="form-group"><label class="form-label">Nama Lengkap</label><input class="form-input" placeholder="Dr. Nama Lengkap"></div>
                <div class="form-group"><label class="form-label">No. STR / SIP</label><input class="form-input" placeholder="Nomor lisensi"></div>
                <div class="form-group full"><label class="form-label">Spesialisasi</label><select class="form-select"><option>Plastic Surgeon</option><option>Aesthetic Physician</option><option>Dermatologist</option></select></div>
                <div class="form-group"><label class="form-label">No. Telepon</label><input class="form-input" placeholder="+62"></div>
                <div class="form-group"><label class="form-label">Email</label><input class="form-input" placeholder="dokter@glowcare.com"></div>
                <div class="form-group"><label class="form-label">Pengalaman (Tahun)</label><input class="form-input" type="number" placeholder="0"></div>
                <div class="form-group"><label class="form-label">Status</label><select class="form-select"><option>Aktif</option><option>Cuti</option><option>Tidak Aktif</option></select></div>
                <div class="form-group full"><label class="form-label">Bio Singkat</label><textarea class="form-textarea" placeholder="Deskripsi singkat tentang dokter..."></textarea></div>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="closeModal('modal-dokter')">Batal</button>
                <button class="btn-save" onclick="closeModal('modal-dokter'); showToast('Data dokter berhasil disimpan ✓')">Simpan Data</button>
            </div>
        </div>
    </div>

    <!-- ══ MODAL: JADWAL ══ -->
    <div class="modal-overlay" id="modal-jadwal" onclick="closeModalOutside(event,'modal-jadwal')">
        <div class="modal" style="position:relative">
            <h3 class="modal-title">Tambah / Edit <em>Jadwal</em></h3>
            <p class="modal-sub">Atur jadwal praktik dokter</p>
            <div class="form-row">
                <div class="form-group full"><label class="form-label">Dokter</label><select class="form-select"><option>Dr. Anisa Putri</option><option>Dr. Marina Crystine</option><option>Dr. Michael Chen</option></select></div>
                <div class="form-group full"><label class="form-label">Hari Praktik</label>
                    <div style="display:flex; flex-wrap:wrap; gap:8px; margin-top:4px">
                        <label style="font-size:12px; display:flex; gap:4px; align-items:center"><input type="checkbox"> Sen</label>
                        <label style="font-size:12px; display:flex; gap:4px; align-items:center"><input type="checkbox"> Sel</label>
                        <label style="font-size:12px; display:flex; gap:4px; align-items:center"><input type="checkbox"> Rab</label>
                        <label style="font-size:12px; display:flex; gap:4px; align-items:center"><input type="checkbox"> Kam</label>
                        <label style="font-size:12px; display:flex; gap:4px; align-items:center"><input type="checkbox"> Jum</label>
                        <label style="font-size:12px; display:flex; gap:4px; align-items:center"><input type="checkbox"> Sab</label>
                        <label style="font-size:12px; display:flex; gap:4px; align-items:center"><input type="checkbox"> Min</label>
                    </div>
                </div>
                <div class="form-group"><label class="form-label">Jam Mulai</label><input class="form-input" type="time" value="09:00"></div>
                <div class="form-group"><label class="form-label">Jam Selesai</label><input class="form-input" type="time" value="17:00"></div>
                <div class="form-group"><label class="form-label">Max Pasien/Hari</label><input class="form-input" type="number" placeholder="8"></div>
                <div class="form-group"><label class="form-label">Treatment</label><select class="form-select"><option>Semua</option><option>Facelift</option><option>Botox & Fillers</option><option>Laser Treatment</option><option>Body Contouring</option></select></div>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="closeModal('modal-jadwal')">Batal</button>
                <button class="btn-save" onclick="closeModal('modal-jadwal'); showToast('Jadwal berhasil disimpan ✓')">Simpan Jadwal</button>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div class="toast" id="toast"><span id="toast-msg">Berhasil disimpan</span></div>

    <script src="../../asset/js/admin.js"></script>
</body>
</html>