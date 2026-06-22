<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/Signin.php'); exit;
}
$conn = require '../config/koneksi.php';

$format    = $_GET['format']    ?? 'csv';
$lap_bulan = (int)($_GET['lap_bulan'] ?? date('n'));
$lap_tahun = (int)($_GET['lap_tahun'] ?? date('Y'));

$bln_ind = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$periode = $bln_ind[$lap_bulan] . ' ' . $lap_tahun;

// ── Query ──────────────────────────────────────────────────────────────────
$sql = "SELECT
    d.nama AS nama_dokter,
    d.spesialisasi,
    COUNT(a.id)                             AS total_appt,
    COALESCE(SUM(a.status='Selesai'),0)     AS selesai,
    COALESCE(SUM(a.status='Dibatalkan'),0)  AS batal,
    COALESCE(SUM(a.status='Berlangsung'),0) AS berlangsung,
    COALESCE(SUM(py.jumlah),0)              AS pendapatan,
    d.rating
    FROM dokter d
    LEFT JOIN appointment a ON a.dokter_id=d.id
        AND MONTH(a.tanggal)=$lap_bulan AND YEAR(a.tanggal)=$lap_tahun
    LEFT JOIN pembayaran py ON py.appointment_id=a.id AND py.status='Lunas'
    GROUP BY d.id, d.nama, d.spesialisasi, d.rating
    ORDER BY pendapatan DESC";

$result = mysqli_query($conn, $sql);
$rows   = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Hitung total
$tot_a = $tot_s = $tot_b = $tot_p = 0;
foreach ($rows as $r) {
    $tot_a += $r['total_appt'];
    $tot_s += $r['selesai'];
    $tot_b += $r['batal'];
    $tot_p += $r['pendapatan'];
}

// ── Keuangan bulan ini ─────────────────────────────────────────────────────
$keu_ok = false;
$tot_masuk = $tot_keluar = 0;
$keu_rows  = [];
$keu_check = mysqli_query($conn, "SHOW TABLES LIKE 'keuangan'");
if (mysqli_num_rows($keu_check) > 0) {
    $keu_ok  = true;
    $kr = mysqli_fetch_assoc(mysqli_query($conn, "SELECT
        COALESCE(SUM(CASE WHEN jenis='Pemasukan'   THEN jumlah ELSE 0 END),0) AS masuk,
        COALESCE(SUM(CASE WHEN jenis='Pengeluaran' THEN jumlah ELSE 0 END),0) AS keluar
        FROM keuangan WHERE MONTH(tanggal)=$lap_bulan AND YEAR(tanggal)=$lap_tahun"));
    $tot_masuk  = (float)$kr['masuk'];
    $tot_keluar = (float)$kr['keluar'];

    $keu_res  = mysqli_query($conn, "SELECT tanggal,jenis,kategori,keterangan,metode,jumlah,referensi
        FROM keuangan WHERE MONTH(tanggal)=$lap_bulan AND YEAR(tanggal)=$lap_tahun
        ORDER BY tanggal ASC, id ASC");
    $keu_rows = mysqli_fetch_all($keu_res, MYSQLI_ASSOC);
}

function rupiah_fmt(float $n): string {
    return 'Rp ' . number_format($n, 0, ',', '.');
}

// ══════════════════════════════════════════════════════════════════════════
// FORMAT: CSV
// ══════════════════════════════════════════════════════════════════════════
if ($format === 'csv') {
    $filename = 'Laporan_GlowCare_' . $bln_ind[$lap_bulan] . '_' . $lap_tahun . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $out = fopen('php://output', 'w');
    // BOM untuk Excel agar bisa baca UTF-8
    fwrite($out, "\xEF\xBB\xBF");

    // Header laporan
    fputcsv($out, ['LAPORAN BULANAN - GLOWCARE CLINIC']);
    fputcsv($out, ['Periode', $periode]);
    fputcsv($out, ['Dibuat pada', date('d/m/Y H:i')]);
    fputcsv($out, []);

    // ── Bagian A: Rekap Appointment ──
    fputcsv($out, ['=== A. REKAP APPOINTMENT PER DOKTER ===']);
    fputcsv($out, ['Dokter','Spesialisasi','Total Appt','Selesai','Dibatalkan','Pendapatan (Rp)','Rating']);

    foreach ($rows as $r) {
        fputcsv($out, [
            $r['nama_dokter'],
            $r['spesialisasi'] ?? '-',
            $r['total_appt'],
            $r['selesai'],
            $r['batal'],
            number_format($r['pendapatan'], 0, ',', '.'),
            number_format($r['rating'], 1),
        ]);
    }
    fputcsv($out, ['TOTAL','', $tot_a, $tot_s, $tot_b, number_format($tot_p, 0, ',', '.'), '']);
    fputcsv($out, []);

    // ── Bagian B: Keuangan ──
    if ($keu_ok) {
        fputcsv($out, ['=== B. RINGKASAN KEUANGAN ===']);
        fputcsv($out, ['Total Pemasukan', rupiah_fmt($tot_masuk)]);
        fputcsv($out, ['Total Pengeluaran', rupiah_fmt($tot_keluar)]);
        fputcsv($out, ['Saldo Bersih', rupiah_fmt($tot_masuk - $tot_keluar)]);
        fputcsv($out, []);

        if (!empty($keu_rows)) {
            fputcsv($out, ['=== C. DETAIL TRANSAKSI KEUANGAN ===']);
            fputcsv($out, ['Tanggal','Jenis','Kategori','Keterangan','Metode','Jumlah (Rp)','Referensi']);
            foreach ($keu_rows as $kr) {
                fputcsv($out, [
                    $kr['tanggal'],
                    $kr['jenis'],
                    $kr['kategori'],
                    $kr['keterangan'],
                    $kr['metode'],
                    number_format($kr['jumlah'], 0, ',', '.'),
                    $kr['referensi'] ?: '-',
                ]);
            }
        }
    }

    fclose($out);
    exit;
}

// ══════════════════════════════════════════════════════════════════════════
// FORMAT: PRINT (HTML untuk dicetak / simpan PDF via browser)
// ══════════════════════════════════════════════════════════════════════════
$pct = $tot_a > 0 ? round($tot_s / $tot_a * 100) : 0;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan <?= $periode ?> — GlowCare Clinic</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Times New Roman', Times, serif;
        color: #000;
        background: #fff;
        font-size: 13px;
        line-height: 1.5;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .page {
        max-width: 800px;
        margin: 0 auto;
        padding: 30px 40px;
    }

    /* Kop Surat */
    .kop-surat {
        text-align: center;
        border-bottom: 4px double #000000;
        padding-bottom: 12px;
        margin-bottom: 25px;
    }

    .kop-surat h1 {
        font-size: 24px;
        font-weight: bold;
        text-transform: uppercase;
        margin-bottom: 4px;
        letter-spacing: 1px;
    }

    .kop-surat p {
        font-size: 11px;
        margin-bottom: 2px;
    }

    /* Title */
    .doc-title {
        text-align: center;
        margin-bottom: 25px;
    }

    .doc-title h2 {
        font-size: 16px;
        font-weight: bold;
        text-decoration: underline;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .doc-title p {
        font-size: 12px;
    }

    /* Sections */
    .sec-title {
        font-size: 13px;
        font-weight: bold;
        text-transform: uppercase;
        margin: 25px 0 10px;
        border-bottom: 1px solid #000;
        padding-bottom: 4px;
    }

    /* Tables */
    table.data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
        margin-bottom: 20px;
    }

    table.data-table th {
        padding: 8px 10px;
        font-weight: bold;
        text-transform: uppercase;
        background: #f2f2f2;
        border: 1px solid #000;
        font-size: 11px;
    }

    table.data-table td {
        padding: 8px 10px;
        border: 1px solid #000;
        vertical-align: middle;
    }

    table.data-table tfoot tr {
        font-weight: bold;
        background: #f9f9f9;
    }

    .right {
        text-align: right;
    }

    .center {
        text-align: center;
    }

    /* Sign block */
    .signature-container {
        margin-top: 50px;
        display: flex;
        justify-content: flex-end;
        page-break-inside: avoid;
    }

    .signature-box {
        text-align: center;
        width: 230px;
        font-size: 12px;
    }

    .signature-box .name {
        font-weight: bold;
        text-decoration: underline;
        margin-top: 70px;
    }

    /* Footer */
    .lap-footer {
        margin-top: 40px;
        padding-top: 10px;
        border-top: 1px solid #ccc;
        display: flex;
        justify-content: space-between;
        font-size: 10px;
        color: #555;
    }

    /* Print Bar */
    .print-bar {
        background: #735a39;
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 36px;
        position: sticky;
        top: 0;
        z-index: 100;
        font-family: sans-serif;
    }

    .print-bar .info {
        font-size: 13px;
        font-weight: 500;
        flex: 1;
    }

    .print-bar .btn-group {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .print-btn {
        background: #fff;
        color: #735a39;
        border: none;
        padding: 8px 20px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 1px;
        text-transform: uppercase;
        cursor: pointer;
    }

    .print-btn:hover {
        background: #f9f6f2;
    }

    .back-btn {
        background: transparent;
        color: #fff;
        border: 1.5px solid rgba(255, 255, 255, 0.6);
        padding: 8px 20px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 1px;
        text-transform: uppercase;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .back-btn:hover {
        background: rgba(255, 255, 255, 0.15);
        border-color: #fff;
    }

    @media print {
        .no-print {
            display: none !important;
        }

        .page {
            padding: 10px 0;
        }

        body {
            font-size: 11px;
        }
    }
    </style>
</head>

<body>

    <!-- Print Bar (tidak ikut tercetak) -->
    <div class="print-bar no-print">
        <div class="info">Laporan Bulanan — <?= $periode ?> · GlowCare Clinic</div>
        <div class="btn-group">
            <a href="../../pages/admin/dashboard.php?panel=laporan" class="back-btn">&#8592; Kembali</a>
            <button class="print-btn" onclick="window.print()">Cetak PDF</button>
        </div>
    </div>

    <div class="page">

        <!-- Kop Surat -->
        <div class="kop-surat">
            <h1>Klinik Kecantikan GlowCare</h1>
            <p>Jl. Raya Pejanggik No. 45, Mataram, Nusa Tenggara Barat</p>
            <p>Telp: (0370) 621435 | Email: info@glowcareclinic.co.id | Web: www.glowcareclinic.co.id</p>
        </div>

        <!-- Judul Laporan -->
        <div class="doc-title">
            <h2>LAPORAN BULANAN OPERASIONAL & KEUANGAN</h2>
            <p>Periode Laporan: <strong><?= $periode ?></strong></p>
            <p style="font-size: 11px; color: #444; margin-top: 3px;">No. Dokumen:
                045/DIR-GC/<?= date('m/Y', strtotime($lap_tahun . '-' . $lap_bulan . '-01')) ?></p>
        </div>

        <!-- Ringkasan Eksekutif -->
        <div class="sec-title">I. Ringkasan Eksekutif (Executive Summary)</div>
        <table class="data-table">
            <tbody>
                <tr>
                    <td style="width: 40%; font-weight: bold; background: #f9f9f9;">Total Appointment (Janji Temu)</td>
                    <td><strong><?= $tot_a ?></strong> Janji Temu</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; background: #f9f9f9;">Janji Temu Terealisasi (Selesai)</td>
                    <td><strong><?= $tot_s ?></strong> Selesai (<?= $pct ?>% dari total)</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; background: #f9f9f9;">Janji Temu Dibatalkan</td>
                    <td><strong><?= $tot_b ?></strong> Batal</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; background: #f9f9f9;">Total Pendapatan Terrealisasi</td>
                    <td><strong><?= rupiah_fmt((float)$tot_p) ?></strong> (Pembayaran Lunas)</td>
                </tr>
            </tbody>
        </table>

        <!-- A: Tabel Appointment -->
        <div class="sec-title">II. Rekapitulasi Kinerja dan Pendapatan Dokter</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;" class="center">No</th>
                    <th>Nama Dokter</th>
                    <th>Spesialisasi</th>
                    <th class="center" style="width: 15%;">Total Appt</th>
                    <th class="center" style="width: 15%;">Selesai</th>
                    <th class="center" style="width: 15%;">Dibatalkan</th>
                    <th class="right" style="width: 20%;">Pendapatan</th>
                    <th class="center" style="width: 12%;">Rating</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($rows as $r): ?>
                <tr>
                    <td class="center"><?= $no++ ?></td>
                    <td style="font-weight: bold;"><?= htmlspecialchars($r['nama_dokter']) ?></td>
                    <td><?= htmlspecialchars($r['spesialisasi'] ?? '-') ?></td>
                    <td class="center"><?= $r['total_appt'] ?></td>
                    <td class="center"><?= $r['selesai'] ?></td>
                    <td class="center"><?= $r['batal'] > 0 ? $r['batal'] : '—' ?></td>
                    <td class="right" style="font-weight: bold;"><?= rupiah_fmt((float)$r['pendapatan']) ?></td>
                    <td class="center"><?= number_format($r['rating'], 1) ?> / 5.0</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="center"><strong>TOTAL / RATA-RATA</strong></td>
                    <td class="center"><strong><?= $tot_a ?></strong></td>
                    <td class="center"><strong><?= $tot_s ?></strong></td>
                    <td class="center" style="color: #c62828;"><strong><?= $tot_b ?></strong></td>
                    <td class="right"><strong><?= rupiah_fmt((float)$tot_p) ?></strong></td>
                    <td class="center">
                        <?php
            $avg_rating = 0;
            if (count($rows) > 0) {
                $sum_r = 0;
                foreach ($rows as $r) { $sum_r += $r['rating']; }
                $avg_rating = $sum_r / count($rows);
            }
            echo number_format($avg_rating, 1) . ' / 5.0';
          ?>
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- B: Keuangan -->
        <?php if ($keu_ok): ?>
        <div class="sec-title">III. Ringkasan Keuangan Bulanan</div>
        <table class="data-table">
            <tbody>
                <tr>
                    <td style="width: 40%; font-weight: bold; background: #f9f9f9;">Total Arus Kas Masuk (Pemasukan)
                    </td>
                    <td style="font-weight: bold; color: #2e7d32;"><?= rupiah_fmt($tot_masuk) ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold; background: #f9f9f9;">Total Arus Kas Keluar (Pengeluaran)</td>
                    <td style="font-weight: bold; color: #c62828;"><?= rupiah_fmt($tot_keluar) ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold; background: #f9f9f9;">Saldo Bersih (Net Margin)</td>
                    <td
                        style="font-weight: bold; color: <?= ($tot_masuk - $tot_keluar) >= 0 ? '#2e7d32' : '#c62828' ?>;">
                        <?= rupiah_fmt($tot_masuk - $tot_keluar) ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php if (!empty($keu_rows)): ?>
        <div class="sec-title">IV. Rincian Detail Transaksi Keuangan</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;" class="center">No</th>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Kategori</th>
                    <th>Keterangan</th>
                    <th>Metode</th>
                    <th class="right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php $no_k = 1; foreach ($keu_rows as $kr): ?>
                <tr>
                    <td class="center"><?= $no_k++ ?></td>
                    <td style="white-space:nowrap"><?= date('d M Y', strtotime($kr['tanggal'])) ?></td>
                    <td>
                        <span
                            style="font-weight: bold; color: <?= $kr['jenis'] === 'Pemasukan' ? '#2e7d32' : '#c62828' ?>;">
                            <?= htmlspecialchars($kr['jenis']) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($kr['kategori']) ?></td>
                    <td><?= htmlspecialchars($kr['keterangan']) ?></td>
                    <td><?= htmlspecialchars($kr['metode']) ?></td>
                    <td class="right"
                        style="font-weight: bold; color: <?= $kr['jenis'] === 'Pemasukan' ? '#2e7d32' : '#c62828' ?>;">
                        <?= $kr['jenis'] === 'Pemasukan' ? '+' : '-' ?><?= rupiah_fmt((float)$kr['jumlah']) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
        <?php endif; ?>

        <!-- Tanda Tangan Persetujuan -->
        <div class="signature-container">
            <div class="signature-box">
                <p>Mataram, <?= date('d') . ' ' . $bln_ind[$lap_bulan] . ' ' . $lap_tahun ?></p>
                <p style="margin-top: 5px;">Pimpinan Klinik GlowCare,</p>
                <p class="name">Dr. dr. Ayu Larasati, M.Biomed</p>
                <p style="font-size: 11px; margin-top: 2px;">NIP. 198804122015032001</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="lap-footer">
            <span>GlowCare Clinic — Dokumen Internal Rahasia</span>
            <span>Laporan <?= $periode ?> · Dicetak <?= date('d/m/Y H:i') ?></span>
        </div>

    </div>
</body>

</html>