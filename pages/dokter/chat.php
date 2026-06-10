<?php
require '../../backend/guard_dokter.php';
require '../../backend/koneksi.php';

$user_id = (int)$_SESSION['user_id'];
$qProfil = mysqli_query($conn, "SELECT id, nama, spesialisasi FROM dokter WHERE user_id = $user_id LIMIT 1");
$profil  = $qProfil ? mysqli_fetch_assoc($qProfil) : [];
$dokter_id = $profil['id'] ?? 0;

$appt_id = (int)($_GET['appt_id'] ?? 0);
$consultation_id = 0;
$pasien = ['nama' => 'Pasien', 'usia' => '-'];

// Get doctor's active consultations
$qConsults = mysqli_query($conn, "
    SELECT c.id as consultation_id, c.appointment_id, p.nama as nama_pasien, t.nama as nama_treatment, a.tanggal, a.jam 
    FROM consultations c
    JOIN appointment a ON c.appointment_id = a.id
    JOIN pasien p ON c.pasien_id = p.id
    LEFT JOIN treatment t ON a.treatment_id = t.id
    WHERE c.dokter_id = $dokter_id
    ORDER BY a.tanggal DESC, a.jam DESC
");
$consultations = [];
if ($qConsults) {
    while ($row = mysqli_fetch_assoc($qConsults)) {
        $consultations[] = $row;
    }
}

if ($appt_id > 0) {
    $qConsult = mysqli_query($conn, "SELECT id, pasien_id FROM consultations WHERE appointment_id = $appt_id");
    if (mysqli_num_rows($qConsult) > 0) {
        $consult = mysqli_fetch_assoc($qConsult);
        $consultation_id = $consult['id'];
        $pasien_id = $consult['pasien_id'];
    } else {
        $qAppt = mysqli_query($conn, "SELECT pasien_id FROM appointment WHERE id = $appt_id");
        if ($appt = mysqli_fetch_assoc($qAppt)) {
            $pasien_id = $appt['pasien_id'];
            mysqli_query($conn, "INSERT INTO consultations (appointment_id, pasien_id, dokter_id, status, created_at) VALUES ($appt_id, $pasien_id, $dokter_id, 'Aktif', NOW())");
            $consultation_id = mysqli_insert_id($conn);
        }
    }
    
    if (isset($pasien_id)) {
        $qPasien = mysqli_query($conn, "SELECT nama, usia, jenis_kelamin FROM pasien WHERE id = $pasien_id");
        if ($qPasien && mysqli_num_rows($qPasien) > 0) {
            $pasien = mysqli_fetch_assoc($qPasien);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konsultasi Chat (Dokter) - GlowCare Clinic</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@300;400;500&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../asset/css/dokter.css?v=5">
    <style>
        .chat-container {
            display: flex;
            height: calc(100vh - 100px);
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #d1c4b8;
            box-shadow: 0 10px 30px rgba(115, 90, 57, 0.05);
            overflow: hidden;
            margin: 20px 40px;
        }
        .chat-sidebar {
            width: 300px;
            background: #fbf9f4;
            border-right: 1px solid #d1c4b8;
            display: flex;
            flex-direction: column;
        }
        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #ffffff;
        }
        .chat-header {
            padding: 20px 24px;
            border-bottom: 1px solid #d1c4b8;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .chat-pasien-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .chat-messages {
            flex: 1;
            padding: 24px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .msg {
            max-width: 60%;
            padding: 12px 16px;
            border-radius: 16px;
            font-size: 13px;
            line-height: 1.5;
        }
        .msg.received {
            background: #f5f3ee;
            color: #2D3436;
            align-self: flex-start;
            border-bottom-left-radius: 4px;
        }
        .msg.sent {
            background: linear-gradient(135deg, #735a39 0%, #594323 100%);
            color: #ffffff;
            align-self: flex-end;
            border-bottom-right-radius: 4px;
        }
        .msg-time {
            font-size: 10px;
            margin-top: 4px;
            opacity: 0.7;
        }
        .chat-input-area {
            padding: 16px 24px;
            border-top: 1px solid #d1c4b8;
            background: #fbf9f4;
            display: flex;
            gap: 12px;
            align-items: flex-end;
        }
        .chat-input {
            flex: 1;
            border: 1px solid #d1c4b8;
            border-radius: 20px;
            padding: 12px 16px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            outline: none;
            resize: none;
            max-height: 100px;
            background: #ffffff;
        }
        .chat-input:focus {
            border-color: #735a39;
        }
        .chat-btn {
            background: linear-gradient(135deg, #735a39 0%, #594323 100%);
            color: white;
            border: none;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .chat-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(115, 90, 57, 0.3);
        }
        .chat-sidebar-header {
            padding: 24px;
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            color: #2D3436;
            border-bottom: 1px solid #d1c4b8;
        }
        .chat-session-item {
            padding: 16px 24px;
            border-bottom: 1px solid #eae8e3;
            cursor: pointer;
            transition: background 0.2s;
        }
        .chat-session-item:hover, .chat-session-item.active {
            background: #f5f3ee;
        }
        .avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: #d1c4b8;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 12px;
            font-family: 'Playfair Display', serif;
            color: #735a39;
            margin-right: 8px;
            vertical-align: middle;
        }
    </style>
</head>
<body style="display:block; background:#fbf9f4;">

<!-- TOPBAR -->
<div class="topbar" style="position:static; margin-bottom: 0;">
    <div style="display:flex; align-items:center; gap:16px;">
        <a href="dashboardDokter.php" style="color:#735a39; text-decoration:none; font-size:18px; font-weight:bold; background:#f5f3ee; padding:6px 12px; border-radius:8px;" title="Kembali ke Dashboard">←</a>
    </div>
    <div></div>
</div>

<div class="chat-container">
    <!-- Sidebar -->
    <div class="chat-sidebar">
        <div class="chat-sidebar-header">Riwayat Chat Pasien</div>
        <?php if (count($consultations) === 0): ?>
            <div style="padding: 24px; font-size: 13px; color: #64748b;">Belum ada sesi chat aktif.</div>
        <?php else: ?>
            <?php foreach($consultations as $c): ?>
            <div class="chat-session-item <?= ($c['appointment_id'] == $appt_id) ? 'active' : '' ?>" onclick="window.location.href='chat.php?appt_id=<?= $c['appointment_id'] ?>'">
                <div style="font-weight:500; font-size:14px; color:#2D3436"><?= htmlspecialchars($c['nama_pasien']) ?></div>
                <div style="font-size:12px; color:#735a39; margin-top:4px"><?= htmlspecialchars($c['nama_treatment'] ?? 'Konsultasi') ?></div>
                <div style="font-size:11px; color:#64748b; margin-top:6px"><?= date('d M Y, H:i', strtotime($c['tanggal'] . ' ' . $c['jam'])) ?></div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Main Chat Area -->
    <div class="chat-main">
        <?php if ($consultation_id === 0): ?>
            <div style="flex:1; display:flex; align-items:center; justify-content:center; flex-direction:column; color:#7a7571;">
                <span class="material-symbols-outlined" style="font-size: 48px; margin-bottom:12px;">chat</span>
                <div>Pilih salah satu sesi chat di sebelah kiri untuk memulai konsultasi.</div>
            </div>
        <?php else: ?>
            <div class="chat-header">
                <div class="chat-pasien-info">
                    <div class="avatar" style="width:40px; height:40px; font-size:16px; margin-right:0;"><?= substr(htmlspecialchars($pasien['nama']), 0, 1) ?></div>
                    <div>
                        <div style="font-family:'Playfair Display', serif; font-size:18px; color:#2D3436"><?= htmlspecialchars($pasien['nama']) ?></div>
                        <div style="font-size:12px; color:#735a39"><?= htmlspecialchars($pasien['usia'] ?? '-') ?> Tahun <?= !empty($pasien['jenis_kelamin']) ? '· ' . htmlspecialchars($pasien['jenis_kelamin']) : '' ?></div>
                    </div>
                </div>
                <span class="badge badge-green">Berlangsung</span>
            </div>

            <div class="chat-messages" id="chatMessages">
                <div style="text-align:center; font-size:11px; color:#7f8c8d; margin:10px 0;">Sesi Konsultasi Dimulai</div>
            </div>

            <div class="chat-input-area">
                <input type="file" id="imageUpload" accept="image/*" style="display:none" onchange="sendImage(this)">
                <button type="button" class="chat-btn" style="background: #eae8e3; color: #735a39;" title="Upload Gambar" onclick="document.getElementById('imageUpload').click()">
                    📷
                </button>
                <textarea class="chat-input" placeholder="Ketik pesan Anda di sini..." rows="1" id="chatInput"></textarea>
                <button type="button" class="chat-btn" title="Kirim Pesan" onclick="sendMessage()">
                    ➤
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
const consultationId = <?= $consultation_id ?>;
let lastMessageCount = 0;

function fetchMessages() {
    if (consultationId === 0) return;
    fetch(`../../backend/chat_get.php?consultation_id=${consultationId}`)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success' && data.data.length !== lastMessageCount) {
                renderMessages(data.data);
                lastMessageCount = data.data.length;
            }
        })
        .catch(() => {});
}

function renderMessages(messages) {
    const box = document.getElementById('chatMessages');
    if (!box) return;
    box.innerHTML = '<div style="text-align:center;font-size:11px;color:#7f8c8d;background:#f0ede8;padding:4px 12px;border-radius:20px;margin:4px auto;display:inline-block;">Sesi Konsultasi Dimulai</div>';

    messages.forEach(msg => {
        const isSent = msg.sender_type === 'Dokter';

        const group = document.createElement('div');
        group.style.cssText = 'display:flex;flex-direction:column;align-items:' + (isSent ? 'flex-end' : 'flex-start') + ';margin-bottom:4px;';

        const senderName = document.createElement('div');
        senderName.style.cssText = 'font-size:10px;color:#9a8f87;margin-bottom:3px;padding:0 4px;';
        senderName.textContent = isSent ? 'Saya' : (msg.sender_name || 'Pasien');
        group.appendChild(senderName);

        const bubble = document.createElement('div');
        bubble.className = 'msg ' + (isSent ? 'sent' : 'received');

        let content = '';
        if (msg.image_url) {
            content += `<img src="../../${msg.image_url}" style="max-width:100%;border-radius:10px;margin-bottom:4px;display:block;" alt="Gambar">`;
        }
        if (msg.message) content += `<span>${msg.message}</span>`;
        content += `<div class="msg-time">${msg.time}</div>`;
        bubble.innerHTML = content;

        group.appendChild(bubble);
        box.appendChild(group);
    });

    box.scrollTop = box.scrollHeight;
}

function sendMessage() {
    if (consultationId === 0) return;
    const input = document.getElementById('chatInput');
    const msgText = input.value.trim();
    if (!msgText) return;

    const formData = new FormData();
    formData.append('consultation_id', consultationId);
    formData.append('message', msgText);

    fetch('../../backend/chat_send.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                input.value = '';
                input.style.height = 'auto';
                fetchMessages();
            } else {
                alert('Gagal mengirim: ' + data.message);
            }
        })
        .catch(() => {});
}

function sendImage(input) {
    if (consultationId === 0) return;
    if (input.files && input.files[0]) {
        const formData = new FormData();
        formData.append('consultation_id', consultationId);
        formData.append('image', input.files[0]);
        formData.append('message', '');
        fetch('../../backend/chat_send.php', { method: 'POST', body: formData })
            .then(r => r.json()).then(d => { if (d.status === 'success') { input.value=''; fetchMessages(); } });
    }
}

if (consultationId > 0) {
    fetchMessages();
    setInterval(fetchMessages, 3000);

    const inp = document.getElementById('chatInput');
    if (inp) {
        inp.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
        });
        inp.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });
    }
}
</script>
</body>
</html>
