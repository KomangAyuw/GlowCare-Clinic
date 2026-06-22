<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/Signin.php');
    exit;
}
require '../../backend/config/koneksi.php';
$user_id = (int)$_SESSION['user_id'];

$stmtProfil = $conn->prepare("SELECT p.*, u.username FROM pasien p JOIN users u ON u.id = p.user_id WHERE p.user_id = :user_id LIMIT 1");
$stmtProfil->execute(['user_id' => $user_id]);
$profil = $stmtProfil->fetch() ?: [];
$pasien_id    = (int)($profil['id'] ?? 0);
$namaPasien   = htmlspecialchars($profil['nama'] ?? $profil['username'] ?? 'Pasien');

// Ambil dokter unik dengan janji temu terbaru untuk sidebar
$stmtUniqueDocs = $conn->prepare("
    SELECT a.id as appt_id, a.status as appt_status, d.id as dokter_id, d.nama as nama_dokter, d.spesialisasi,
           t.nama as nama_treatment, a.tanggal, a.jam
    FROM appointment a
    JOIN dokter d ON a.dokter_id = d.id
    LEFT JOIN treatment t ON a.treatment_id = t.id
    INNER JOIN (
        SELECT dokter_id, MAX(CONCAT(tanggal, ' ', jam)) as max_datetime
        FROM appointment
        WHERE pasien_id = :pasien_id
        GROUP BY dokter_id
    ) latest ON a.dokter_id = latest.dokter_id AND CONCAT(a.tanggal, ' ', a.jam) = latest.max_datetime
    WHERE a.pasien_id = :pasien_id2
    ORDER BY a.tanggal DESC, a.jam DESC
");
$stmtUniqueDocs->execute(['pasien_id' => $pasien_id, 'pasien_id2' => $pasien_id]);
$unique_doctors = $stmtUniqueDocs->fetchAll() ?: [];

// Appointment yang sedang dipilih
$appt_id = (int)($_GET['appt_id'] ?? ($unique_doctors[0]['appt_id'] ?? 0));
$consultation_id = 0;
$dokter    = ['nama' => 'Dokter', 'spesialisasi' => '-'];
$apptInfo  = null;
$selected_dokter_id = 0;

if ($appt_id > 0) {
    // Cari atau buat sesi konsultasi
    $stmtConsult = $conn->prepare("SELECT id, dokter_id FROM consultations WHERE appointment_id = :appt_id LIMIT 1");
    $stmtConsult->execute(['appt_id' => $appt_id]);
    $consult = $stmtConsult->fetch();
    if ($consult) {
        $consultation_id = (int)$consult['id'];
        $dokter_id = (int)$consult['dokter_id'];
    } else {
        $stmtAppt = $conn->prepare("SELECT dokter_id, status FROM appointment WHERE id = :appt_id AND pasien_id = :pasien_id LIMIT 1");
        $stmtAppt->execute(['appt_id' => $appt_id, 'pasien_id' => $pasien_id]);
        if ($appt = $stmtAppt->fetch()) {
            $dokter_id = (int)$appt['dokter_id'];
            $stmtInsertConsult = $conn->prepare("INSERT INTO consultations (appointment_id, pasien_id, dokter_id, status, created_at) VALUES (:appt_id, :pasien_id, :dokter_id, 'Aktif', NOW())");
            $stmtInsertConsult->execute([
                'appt_id' => $appt_id,
                'pasien_id' => $pasien_id,
                'dokter_id' => $dokter_id
            ]);
            $consultation_id = (int)$conn->lastInsertId();
        }
    }

    if (isset($dokter_id) && $dokter_id > 0) {
        $selected_dokter_id = $dokter_id;
        $stmtDokter = $conn->prepare("SELECT nama, spesialisasi FROM dokter WHERE id = :dokter_id LIMIT 1");
        $stmtDokter->execute(['dokter_id' => $dokter_id]);
        if ($resDokter = $stmtDokter->fetch()) {
            $dokter['nama']         = htmlspecialchars($resDokter['nama']);
            $dokter['spesialisasi'] = htmlspecialchars($resDokter['spesialisasi']);
        }
    }

    // Ambil info detail untuk appointment terpilih
    $stmtApptInfo = $conn->prepare("
        SELECT a.id as appt_id, a.status as appt_status, d.nama as nama_dokter, d.spesialisasi,
               t.nama as nama_treatment, a.tanggal, a.jam 
        FROM appointment a 
        JOIN dokter d ON a.dokter_id = d.id
        LEFT JOIN treatment t ON a.treatment_id = t.id
        WHERE a.id = :appt_id AND a.pasien_id = :pasien_id
        LIMIT 1
    ");
    $stmtApptInfo->execute(['appt_id' => $appt_id, 'pasien_id' => $pasien_id]);
    $apptInfo = $stmtApptInfo->fetch() ?: null;
}

// Ambil riwayat sesi untuk dokter terpilih
$session_history = [];
if ($pasien_id > 0 && $selected_dokter_id > 0) {
    $stmtHistory = $conn->prepare("
        SELECT a.id as appt_id, a.status as appt_status, a.tanggal, a.jam, t.nama as nama_treatment
        FROM appointment a
        LEFT JOIN treatment t ON a.treatment_id = t.id
        WHERE a.pasien_id = :pasien_id AND a.dokter_id = :dokter_id
        ORDER BY a.tanggal DESC, a.jam DESC
    ");
    $stmtHistory->execute(['pasien_id' => $pasien_id, 'dokter_id' => $selected_dokter_id]);
    $session_history = $stmtHistory->fetchAll() ?: [];
}

$canBatal = $apptInfo && !in_array($apptInfo['appt_status'], ['Dibatalkan', 'Selesai']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Konsultasi — GlowCare Clinic</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@300;400;500&display=swap">
    <link rel="stylesheet" href="../../asset/css/user.css?v=6">
    <style>
        * { box-sizing: border-box; }
        body { background: #f5f3ee; margin: 0; overflow: hidden; }

        /* ── Topnav ── */
        .topnav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            height: 60px;
            background: #594323;
            box-shadow: 0 2px 12px rgba(115,90,57,0.18);
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 200;
        }
        .topnav-brand {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            color: #ffffff !important;
            background: none !important;
            -webkit-background-clip: initial !important;
            -webkit-text-fill-color: initial !important;
            letter-spacing: 0.5px;
            display: inline-block;
        }
        .topnav-actions { display:flex; gap:10px; align-items:center; }
        .btn-back {
            background: rgba(255,255,255,0.15);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.3);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 0.5px;
            text-decoration: none;
            transition: background 0.2s;
        }
        .btn-back:hover { background: rgba(255,255,255,0.25); }
        .btn-logout-nav {
            background: #e05050;
            color: #fff;
            border: none;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 12px;
            text-decoration: none;
            font-weight: 500;
        }

        /* ── Layout ── */
        .chat-wrapper {
            display: flex;
            height: calc(100vh - 60px);
            margin-top: 60px;
        }

        /* ── Sidebar ── */
        .chat-sidebar {
            width: 290px;
            background: #fff;
            border-right: 1px solid #e2ddd8;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
        }
        .sidebar-header {
            padding: 18px 20px 14px;
            border-bottom: 1px solid #e2ddd8;
        }
        .sidebar-title {
            font-family:'Playfair Display',serif;
            font-size: 16px;
            color: #2D3436;
            margin-bottom: 2px;
        }
        .sidebar-sub { font-size:11px; color:#9a8f87; }
        .session-list { flex: 1; overflow-y: auto; }
        .session-item {
            padding: 14px 20px;
            border-bottom: 1px solid #f0ede8;
            cursor: pointer;
            transition: background 0.15s;
            position: relative;
        }
        .session-item:hover { background: #faf8f5; }
        .session-item.active { background: #faf6ee; border-left: 3px solid #735a39; }
        .session-item .doc-name { font-size:13px; font-weight:500; color:#2D3436; }
        .session-item .doc-spec { font-size:11px; color:#735a39; margin-top:3px; }
        .session-item .doc-date { font-size:10px; color:#9a8f87; margin-top:5px; }
        .session-item .status-dot {
            display: inline-block;
            width: 7px; height: 7px;
            border-radius: 50%;
            margin-right: 4px;
            vertical-align: middle;
        }
        .dot-active { background: #3dab74; }
        .dot-selesai { background: #9a8f87; }
        .dot-batal { background: #e05050; }

        /* ── Main area ── */
        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #fff;
            min-width: 0;
        }

        /* ── Chat header ── */
        .chat-header {
            padding: 16px 24px;
            border-bottom: 1px solid #e2ddd8;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
        }
        .chat-doc-left { display:flex; align-items:center; gap:12px; }
        .doc-avatar {
            width: 42px; height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg,#735a39,#2D3436);
            color: #fff;
            font-family: 'Playfair Display',serif;
            font-size: 18px;
            display: flex; align-items:center; justify-content:center;
            flex-shrink: 0;
        }
        .doc-info-name { font-family:'Playfair Display',serif; font-size:16px; color:#2D3436; }
        .doc-info-spec { font-size:11px; color:#735a39; margin-top:2px; }
        .appt-meta { font-size:11px; color:#7a7571; margin-top:4px; }
        .btn-batal-konsul {
            background: #fef0f0;
            color: #c0392b;
            border: 1px solid #fbc4c4;
            padding: 7px 16px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.5px;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            transition: all 0.2s;
        }
        .btn-batal-konsul:hover { background: #fddede; border-color: #e05050; }

        /* ── Messages ── */
        .chat-messages {
            flex: 1;
            padding: 24px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 16px;
            background: #faf8f5;
        }
        .msg-group { display:flex; flex-direction:column; }
        .msg-group.right { align-items:flex-end; }
        .msg-group.left  { align-items:flex-start; }
        .msg-sender-name {
            font-size: 10px;
            color: #9a8f87;
            margin-bottom: 4px;
            padding: 0 4px;
            letter-spacing: 0.3px;
        }
        .msg {
            max-width: 65%;
            padding: 11px 16px;
            border-radius: 18px;
            font-size: 13px;
            line-height: 1.55;
            word-break: break-word;
        }
        .msg.sent {
            background: #735a39;
            color: #fff;
            border-bottom-right-radius: 4px;
        }
        .msg.received {
            background: #fff;
            color: #2D3436;
            border: 1px solid #e2ddd8;
            border-bottom-left-radius: 4px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }
        .msg-time { font-size:10px; opacity:0.65; margin-top:5px; display:block; }
        .chat-system-msg {
            text-align: center;
            font-size: 11px;
            color: #9a8f87;
            background: #f0ede8;
            padding: 5px 14px;
            border-radius: 20px;
            margin: 4px auto;
        }

        /* ── Empty state ── */
        .chat-empty {
            flex: 1; display:flex; flex-direction:column;
            align-items:center; justify-content:center;
            color: #b0a89e; gap: 12px;
        }
        .chat-empty-icon { font-size:48px; }
        .chat-empty-text { font-size:14px; }

        /* ── Input area ── */
        .chat-input-area {
            padding: 14px 20px;
            border-top: 1px solid #e2ddd8;
            background: #fff;
            display: flex;
            gap: 10px;
            align-items: flex-end;
        }
        .chat-input {
            flex: 1;
            border: 1.5px solid #c4a882;
            border-radius: 22px;
            padding: 11px 18px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            outline: none;
            resize: none;
            max-height: 120px;
            background: #faf8f5;
            transition: border-color 0.2s;
            line-height: 1.5;
        }
        .chat-input:focus { border-color: #735a39; background: #fff; }
        .chat-send-btn {
            background: #735a39;
            color: #fff;
            border: none;
            height: 44px;
            border-radius: 22px;
            padding: 0 18px;
            display: flex; align-items:center; justify-content:center;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            flex-shrink: 0;
            transition: transform 0.15s, box-shadow 0.2s;
        }
        .chat-send-btn:hover { transform: scale(1.07); box-shadow: 0 4px 12px rgba(115,90,57,0.3); }
        .chat-send-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

        /* ── Modal Batal ── */
        .modal-overlay {
            display:none; position:fixed; inset:0; z-index:9000;
            background: rgba(0,0,0,0.45); backdrop-filter:blur(4px);
            align-items:center; justify-content:center;
        }
        .modal-overlay.open { display:flex; }
        .modal-box {
            background:#fff; border-radius:20px; padding:36px 32px;
            width:360px; text-align:center;
            box-shadow:0 24px 64px rgba(0,0,0,0.18);
            animation: modalIn .2s ease;
        }
        @keyframes modalIn { from{transform:scale(.9);opacity:0} to{transform:scale(1);opacity:1} }
        .modal-icon {
            width:52px;height:52px; border-radius:50%;
            background:#fef0f0; display:flex; align-items:center; justify-content:center;
            margin: 0 auto 16px; font-size:22px;
        }
        .modal-title { font-family:'Playfair Display',serif; font-size:18px; color:#2D3436; margin-bottom:8px; }
        .modal-desc { font-size:12px; color:#7a7571; line-height:1.7; margin-bottom:22px; }
        .modal-alasan {
            width:100%; border:1.5px solid #c4a882; border-radius:10px;
            padding:10px 14px; font-size:12px; font-family:'DM Sans',sans-serif;
            margin-bottom:20px; outline:none; resize:none;
        }
        .modal-alasan:focus { border-color:#735a39; }
        .modal-btns { display:flex; gap:10px; }
        .modal-btn-cancel {
            flex:1; padding:10px; border:1.5px solid #c4a882; border-radius:50px;
            background:#fff; color:#7a7571; font-size:12px; font-weight:500;
            letter-spacing:0.5px; text-transform:uppercase; cursor:pointer;
            font-family:'DM Sans',sans-serif;
        }
        .modal-btn-confirm {
            flex:1; padding:10px; border:none; border-radius:50px;
            background:#e05050; color:#fff; font-size:12px; font-weight:500;
            letter-spacing:0.5px; text-transform:uppercase; cursor:pointer;
            font-family:'DM Sans',sans-serif;
        }
        .modal-btn-confirm:hover { background:#c0392b; }
    </style>
</head>
<body>

<!-- TOPNAV -->
<div class="topnav">
    <div class="topnav-brand">GlowCare Clinic · Chat Konsultasi</div>
    <div class="topnav-actions">
        <a href="dashboarduser.php" class="btn-back">&#8592; Dashboard</a>
        <a href="../../backend/auth/logout.php" class="btn-logout-nav">Logout</a>
    </div>
</div>

<div class="chat-wrapper">
    <!-- SIDEBAR -->
    <div class="chat-sidebar">
        <div class="sidebar-header">
            <div class="sidebar-title">Konsultasi Saya</div>
            <div class="sidebar-sub"><?= count($unique_doctors) ?> Dokter</div>
        </div>
        <div class="session-list">
            <?php if (empty($unique_doctors)): ?>
                <div style="padding:24px; font-size:13px; color:#9a8f87; text-align:center;">
                    Belum ada janji temu.<br>
                    <a href="dashboarduser.php" style="color:#735a39; font-weight:500; text-decoration:none;">Buat Booking</a>
                </div>
            <?php else: ?>
                <?php foreach ($unique_doctors as $a):
                    $isActive = ($apptInfo && $a['dokter_id'] == $selected_dokter_id);
                    $dotClass = match($a['appt_status']) {
                        'Selesai'    => 'dot-selesai',
                        'Dibatalkan' => 'dot-batal',
                        default      => 'dot-active',
                    };
                ?>
                <div class="session-item <?= $isActive ? 'active' : '' ?>" onclick="window.location.href='chat.php?appt_id=<?= $a['appt_id'] ?>'">
                    <div class="doc-name">
                        <span class="status-dot <?= $dotClass ?>"></span>
                        <?= htmlspecialchars($a['nama_dokter']) ?>
                    </div>
                    <div class="doc-spec"><?= htmlspecialchars($a['nama_treatment'] ?? 'Konsultasi Umum') ?></div>
                    <div class="doc-date">Terakhir: <?= date('d M Y, H:i', strtotime($a['tanggal'] . ' ' . $a['jam'])) ?> WIB</div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- MAIN CHAT -->
    <div class="chat-main">
        <?php if ($consultation_id === 0): ?>
            <div class="chat-empty">
                <div style="font-family:'Playfair Display',serif; font-size: 20px; color: #735a39; margin-bottom: 8px;">Konsultasi Chat</div>
                <div class="chat-empty-text">Pilih konsultasi di sebelah kiri untuk memulai chat</div>
                <?php if (empty($unique_doctors)): ?>
                    <a href="dashboarduser.php" style="margin-top:8px; background:#735a39; color:#fff; padding:8px 20px; border-radius:50px; text-decoration:none; font-size:12px; font-weight:500;">Buat Janji Sekarang</a>
                <?php endif; ?>
            </div>
        <?php else: ?>

            <!-- Chat Header -->
            <div class="chat-header" style="flex-wrap: wrap; gap: 12px; padding: 12px 24px;">
                <div class="chat-doc-left" style="flex: 1; min-width: 200px;">
                    <div class="doc-avatar"><?= substr($dokter['nama'], 0, 1) ?></div>
                    <div>
                        <div class="doc-info-name"><?= $dokter['nama'] ?></div>
                        <div class="doc-info-spec" style="margin-bottom: 2px;"><?= $dokter['spesialisasi'] ?></div>
                        
                        <?php if ($apptInfo): ?>
                        <div class="appt-meta" style="margin-top: 4px;">
                            Sesi Terpilih: <strong><?= date('d M Y', strtotime($apptInfo['tanggal'])) ?> pukul <?= substr($apptInfo['jam'], 0, 5) ?> WIB</strong>
                            · <span style="color:<?= $apptInfo['appt_status']==='Dibatalkan'?'#e05050':($apptInfo['appt_status']==='Selesai'?'#3dab74':'#735a39') ?>; font-weight:500;"><?= htmlspecialchars($apptInfo['appt_status']) ?></span>
                        </div>
                        <?php endif; ?>

                        <?php if (count($session_history) > 1): ?>
                            <div style="margin-top: 6px; display: flex; align-items: center; gap: 8px; font-size: 11px; color: #4a3e36;">
                                <span style="font-weight: 500;">Riwayat Janji Temu:</span>
                                <select onchange="window.location.href='chat.php?appt_id=' + this.value" style="padding: 2px 6px; border-radius: 4px; border: 1px solid #d1c4b8; font-family: 'DM Sans', sans-serif; font-size: 11px; background: #faf8f5; color: #2b2621; outline: none; cursor: pointer;">
                                    <?php foreach ($session_history as $history):
                                        $selectedStr = ($history['appt_id'] == $appt_id) ? 'selected' : '';
                                        $statusStr = $history['appt_status'];
                                        $dateStr = date('d M Y, H:i', strtotime($history['tanggal'] . ' ' . $history['jam'])) . ' WIB';
                                    ?>
                                        <option value="<?= $history['appt_id'] ?>" <?= $selectedStr ?>>
                                            <?= $dateStr ?> (<?= htmlspecialchars($history['nama_treatment'] ?? 'Konsultasi Umum') ?> - <?= $statusStr ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($canBatal): ?>
                <button class="btn-batal-konsul" onclick="openBatalModal()">Batalkan Konsultasi</button>
                <?php endif; ?>
            </div>

            <!-- Messages -->
            <div class="chat-messages" id="chatMessages">
                <div class="chat-system-msg">Sesi Konsultasi Dimulai — <?= $dokter['nama'] ?></div>
            </div>

            <!-- Input -->
            <div class="chat-input-area">
                <input type="file" id="imageUpload" accept="image/*" style="display:none" onchange="sendImage(this)">
                <button type="button" class="chat-send-btn" style="background: #eae8e3; color: #735a39; display: flex; align-items: center; justify-content: center;" title="Upload Gambar" onclick="document.getElementById('imageUpload').click()">
                    Upload Gambar
                </button>
                <textarea class="chat-input" id="chatInput" rows="1" placeholder="Ketik pesan Anda..."></textarea>
                <button class="chat-send-btn" id="sendBtn" onclick="sendMessage()" title="Kirim (Enter)">Kirim</button>
            </div>

        <?php endif; ?>
    </div>
</div>

<!-- MODAL BATAL KONSULTASI -->
<div class="modal-overlay" id="batalModal">
    <div class="modal-box">
        <div class="modal-icon" style="color: #e05050; font-weight: bold;">!</div>
        <div class="modal-title">Batalkan Konsultasi?</div>
        <div class="modal-desc">
            Anda akan membatalkan janji temu dengan <strong><?= $dokter['nama'] ?></strong>.<br>
            Tindakan ini tidak dapat dibatalkan.
        </div>
        <form method="POST" action="../../backend/user/batal_booking.php">
            <input type="hidden" name="appointment_id" value="<?= $appt_id ?>">
            <textarea class="modal-alasan" name="alasan" rows="2" placeholder="Alasan pembatalan (opsional)..."></textarea>
            <div class="modal-btns">
                <button type="button" class="modal-btn-cancel" onclick="closeBatalModal()">Tidak, Kembali</button>
                <button type="submit" class="modal-btn-confirm">Ya, Batalkan</button>
            </div>
        </form>
    </div>
</div>

<script>
const consultationId = <?= $consultation_id ?>;
const namaPasien = "<?= $namaPasien ?>";
let lastCount = 0;

// ── Fetch & render pesan ──
function fetchMessages() {
    if (!consultationId) return;
    fetch(`../../backend/chat/chat_get.php?consultation_id=${consultationId}`)
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success' && data.data.length !== lastCount) {
                lastCount = data.data.length;
                renderMessages(data.data);
            }
        })
        .catch(() => {});
}

function renderMessages(messages) {
    const box = document.getElementById('chatMessages');
    if (!box) return;
    box.innerHTML = `<div class="chat-system-msg">Sesi Konsultasi Dimulai</div>`;

    messages.forEach(msg => {
        const isSent = msg.sender_type === 'Pasien';
        const group  = document.createElement('div');
        group.className = 'msg-group ' + (isSent ? 'right' : 'left');

        const senderLabel = document.createElement('div');
        senderLabel.className = 'msg-sender-name';
        senderLabel.textContent = isSent ? 'Saya' : msg.sender_name || 'Dokter';
        group.appendChild(senderLabel);

        const bubble = document.createElement('div');
        bubble.className = 'msg ' + (isSent ? 'sent' : 'received');

        let content = '';
        if (msg.image_url) {
            content += `<img src="../../${msg.image_url}" style="max-width:100%;border-radius:10px;margin-bottom:6px;display:block;" alt="Gambar">`;
        }
        if (msg.message) content += `<span>${escapeHtml(msg.message)}</span>`;
        content += `<span class="msg-time">${msg.time}</span>`;
        bubble.innerHTML = content;

        group.appendChild(bubble);
        box.appendChild(group);
    });

    box.scrollTop = box.scrollHeight;
}

function escapeHtml(text) {
    return text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// ── Kirim pesan ──
function sendMessage() {
    if (!consultationId) return;
    const input = document.getElementById('chatInput');
    const msg   = input.value.trim();
    if (!msg) return;

    const btn = document.getElementById('sendBtn');
    btn.disabled = true;

    const fd = new FormData();
    fd.append('consultation_id', consultationId);
    fd.append('message', msg);

    fetch('../../backend/chat/chat_send.php', { method:'POST', body:fd })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                input.value = '';
                input.style.height = 'auto';
                fetchMessages();
            } else {
                alert('Gagal mengirim: ' + data.message);
            }
        })
        .catch(() => alert('Terjadi kesalahan.'))
        .finally(() => { btn.disabled = false; input.focus(); });
}

function sendImage(input) {
    if (!consultationId) return;
    if (input.files && input.files[0]) {
        const fd = new FormData();
        fd.append('consultation_id', consultationId);
        fd.append('image', input.files[0]);
        fd.append('message', '');

        fetch('../../backend/chat/chat_send.php', { method:'POST', body:fd })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    input.value = '';
                    fetchMessages();
                } else {
                    alert('Gagal mengirim gambar: ' + data.message);
                }
            })
            .catch(() => alert('Terjadi kesalahan.'));
    }
}

// ── Kirim dengan Enter (Shift+Enter = baris baru) ──
if (document.getElementById('chatInput')) {
    document.getElementById('chatInput').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
    document.getElementById('chatInput').addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });
}

// ── Modal Batal ──
function openBatalModal() {
    document.getElementById('batalModal').classList.add('open');
}
function closeBatalModal() {
    document.getElementById('batalModal').classList.remove('open');
}
document.getElementById('batalModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeBatalModal();
});

// ── Polling real-time setiap 3 detik ──
if (consultationId > 0) {
    fetchMessages();
    setInterval(fetchMessages, 3000);
}
</script>
</body>
</html>
