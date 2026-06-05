<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pages/auth/Signin.php'); exit;
}
$conn = require '../koneksi.php';

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
?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan <?= $periode ?> — GlowCare Clinic</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap">
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family:'DM Sans',sans-serif; color:#2D3436; background:#fff; font-size:13px; }
  .page { max-width:820px; margin:0 auto; padding:40px 36px; }

  /* Header */
  .lap-header { display:flex; justify-content:space-between; align-items:flex-start; padding-bottom:24px; border-bottom:2px solid #d1c4b8; margin-bottom:28px; }
  .lap-brand { font-family:'Playfair Display',serif; font-size:22px; color:#735a39; letter-spacing:1px; }
  .lap-brand small { display:block; font-size:10px; letter-spacing:3px; text-transform:uppercase; color:#64748b; font-family:'DM Sans',sans-serif; font-style:normal; margin-top:2px; }
  .lap-periode { text-align:right; }
  .lap-periode .big { font-family:'Playfair Display',serif; font-size:20px; color:#2D3436; }
  .lap-periode .sub { font-size:11px; color:#64748b; margin-top:4px; }

  /* Section title */
  .sec-title { font-family:'Playfair Display',serif; font-size:14px; color:#735a39; letter-spacing:1px; text-transform:uppercase; margin:28px 0 12px; padding-bottom:6px; border-bottom:1px solid #efe8e0; }

  /* Stat pills */
  .stat-row { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:28px; }
  .stat-pill { background:#f9f6f2; border-radius:10px; padding:14px 16px; border-left:4px solid #d1c4b8; }
  .stat-pill.green { border-color:#3dab74; }
  .stat-pill.red   { border-color:#e05050; }
  .stat-pill.gold  { border-color:#c9970e; }
  .stat-pill .lbl { font-size:9px; letter-spacing:2px; text-transform:uppercase; color:#64748b; margin-bottom:5px; }
  .stat-pill .val { font-family:'Playfair Display',serif; font-size:20px; color:#2D3436; }
  .stat-pill .sub { font-size:10px; color:#64748b; margin-top:3px; }

  /* Table */
  table { width:100%; border-collapse:collapse; font-size:12px; }
  thead tr { background:#f0fdf4; }
  th { padding:9px 12px; text-align:left; font-size:9px; letter-spacing:1.5px; text-transform:uppercase; color:#64748b; border-bottom:1px solid #d1c4b8; }
  td { padding:10px 12px; border-bottom:1px solid #f5f3ee; vertical-align:middle; }
  tfoot tr { background:#f9f6f2; font-weight:600; }
  .right { text-align:right; }
  .center { text-align:center; }
  .badge-green { background:#e8f9f1; color:#3dab74; padding:2px 8px; border-radius:20px; font-size:10px; }
  .badge-red   { background:#fef0f0; color:#e05050; padding:2px 8px; border-radius:20px; font-size:10px; }
  .badge-gray  { background:#f5f5f5; color:#888; padding:2px 8px; border-radius:20px; font-size:10px; }
  .green-text  { color:#3dab74; }
  .red-text    { color:#e05050; }

  /* Keuangan section */
  .keu-sum { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:20px; }
  .keu-card { padding:14px 16px; border-radius:10px; border:1px solid #d1c4b8; }
  .keu-card .lbl { font-size:9px; letter-spacing:2px; text-transform:uppercase; color:#64748b; margin-bottom:4px; }
  .keu-card .val { font-size:18px; font-family:'Playfair Display',serif; }
  .keu-card.masuk  { border-top:3px solid #3dab74; }
  .keu-card.keluar { border-top:3px solid #e05050; }
  .keu-card.saldo  { border-top:3px solid #735a39; }

  /* Footer */
  .lap-footer { margin-top:48px; padding-top:16px; border-top:1px solid #d1c4b8; display:flex; justify-content:space-between; font-size:11px; color:#64748b; }

  /* Print */
  @media print {
    body { font-size:11px; }
    .no-print { display:none !important; }
    .page { padding:20px; }
    @page { margin:1.5cm; }
  }
  /* Tombol print */
  .print-bar { background:#735a39; color:#fff; display:flex; justify-content:space-between; align-items:center; padding:12px 36px; position:sticky; top:0; z-index:100; }
  .print-bar .info { font-size:13px; font-weight:500; }
  .print-btn { background:#fff; color:#735a39; border:none; padding:8px 20px; border-radius:50px; font-size:11px; font-weight:600; letter-spacing:1px; text-transform:uppercase; cursor:pointer; font-family:'DM Sans',sans-serif; }
  .print-btn:hover { background:#f9f6f2; }
</style>
</head>
<body>

<!-- Print Bar (tidak ikut tercetak) -->
<div class="print-bar no-print">
  <div class="info">Laporan Bulanan — <?= $periode ?> · GlowCare Clinic</div>
  <button class="print-btn" onclick="window.print()">Cetak / Simpan PDF</button>
</div>

<div class="page">

  <!-- Header -->
  <div class="lap-header">
    <div>
      <div class="lap-brand"><em>GlowCare Clinic</em><small>Admin Panel</small></div>
    </div>
    <div class="lap-periode">
      <div class="big">Laporan Bulanan</div>
      <div class="sub">Periode: <strong><?= $periode ?></strong></div>
      <div class="sub">Dicetak: <?= date('d/m/Y H:i') ?></div>
    </div>
  </div>

  <!-- Stat Pills -->
  <div class="stat-row">
    <div class="stat-pill">
      <div class="lbl">Total Appointment</div>
      <div class="val"><?= $tot_a ?></div>
      <div class="sub"><?= count($rows) ?> dokter</div>
    </div>
    <div class="stat-pill green">
      <div class="lbl">Selesai</div>
      <div class="val"><?= $tot_s ?></div>
      <div class="sub"><?= $pct ?>% dari total</div>
    </div>
    <div class="stat-pill red">
      <div class="lbl">Dibatalkan</div>
      <div class="val"><?= $tot_b ?></div>
      <div class="sub">&nbsp;</div>
    </div>
    <div class="stat-pill gold">
      <div class="lbl">Total Pendapatan</div>
      <div class="val" style="font-size:16px"><?= rupiah_fmt((float)$tot_p) ?></div>
      <div class="sub">pembayaran lunas</div>
    </div>
  </div>

  <!-- A: Tabel Appointment -->
  <div class="sec-title">A. Rekap Appointment Per Dokter</div>
  <table>
    <thead>
      <tr>
        <th>Dokter</th>
        <th>Spesialisasi</th>
        <th class="center">Total Appt</th>
        <th class="center">Selesai</th>
        <th class="center">Dibatalkan</th>
        <th class="right">Pendapatan</th>
        <th class="center">Rating</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><?= htmlspecialchars($r['nama_dokter']) ?></td>
        <td><?= htmlspecialchars($r['spesialisasi'] ?? '-') ?></td>
        <td class="center"><strong><?= $r['total_appt'] ?></strong></td>
        <td class="center"><span class="badge-green"><?= $r['selesai'] ?></span></td>
        <td class="center">
          <?php if ($r['batal'] > 0): ?>
            <span class="badge-red"><?= $r['batal'] ?></span>
          <?php else: ?>
            <span style="color:#b0bec5">—</span>
          <?php endif; ?>
        </td>
        <td class="right green-text"><strong><?= rupiah_fmt((float)$r['pendapatan']) ?></strong></td>
        <td class="center"><?= number_format($r['rating'],1) ?> / 5.0</td>
      </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="2"><strong>Total</strong></td>
        <td class="center"><?= $tot_a ?></td>
        <td class="center"><?= $tot_s ?></td>
        <td class="center red-text"><?= $tot_b ?></td>
        <td class="right green-text"><?= rupiah_fmt((float)$tot_p) ?></td>
        <td></td>
      </tr>
    </tfoot>
  </table>

  <!-- B: Keuangan -->
  <?php if ($keu_ok): ?>
  <div class="sec-title">B. Ringkasan Keuangan</div>
  <div class="keu-sum">
    <div class="keu-card masuk">
      <div class="lbl">Total Pemasukan</div>
      <div class="val green-text"><?= rupiah_fmt($tot_masuk) ?></div>
    </div>
    <div class="keu-card keluar">
      <div class="lbl">Total Pengeluaran</div>
      <div class="val red-text"><?= rupiah_fmt($tot_keluar) ?></div>
    </div>
    <div class="keu-card saldo">
      <div class="lbl">Saldo Bersih</div>
      <div class="val" style="color:<?= ($tot_masuk-$tot_keluar)>=0?'#3dab74':'#e05050' ?>"><?= rupiah_fmt($tot_masuk - $tot_keluar) ?></div>
    </div>
  </div>

  <?php if (!empty($keu_rows)): ?>
  <div class="sec-title">C. Detail Transaksi Keuangan</div>
  <table>
    <thead>
      <tr>
        <th>Tanggal</th>
        <th>Jenis</th>
        <th>Kategori</th>
        <th>Keterangan</th>
        <th>Metode</th>
        <th class="right">Jumlah</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($keu_rows as $kr): ?>
      <tr>
        <td style="white-space:nowrap"><?= date('d M Y', strtotime($kr['tanggal'])) ?></td>
        <td>
          <?php if ($kr['jenis']==='Pemasukan'): ?>
            <span class="badge-green">Pemasukan</span>
          <?php else: ?>
            <span class="badge-red">Pengeluaran</span>
          <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($kr['kategori']) ?></td>
        <td><?= htmlspecialchars($kr['keterangan']) ?></td>
        <td><?= htmlspecialchars($kr['metode']) ?></td>
        <td class="right" style="font-weight:600;color:<?= $kr['jenis']==='Pemasukan'?'#3dab74':'#e05050' ?>">
          <?= $kr['jenis']==='Pemasukan'?'+':'-' ?><?= rupiah_fmt((float)$kr['jumlah']) ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
  <?php endif; ?>

  <!-- Footer -->
  <div class="lap-footer">
    <span>GlowCare Clinic — Dokumen Rahasia</span>
    <span>Laporan <?= $periode ?> · Dicetak <?= date('d/m/Y H:i') ?></span>
  </div>

</div>
</body>
</html>
