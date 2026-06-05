<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/Signin.php');
    exit;
}
require '../../backend/koneksi.php';
$user_id = (int)$_SESSION['user_id'];
$qProfil = mysqli_query($conn, "SELECT p.*, u.username FROM pasien p JOIN users u ON u.id = p.user_id WHERE p.user_id = $user_id LIMIT 1");
$profil  = $qProfil ? mysqli_fetch_assoc($qProfil) : [];
$pasien_id = $profil['id'] ?? 0;

$appt_id = (int)($_GET['appt_id'] ?? 0);
$consultation_id = 0;
$dokter = ['nama' => 'Dokter', 'spesialisasi' => 'Spesialis'];

// Get patient's appointments
$qAppts = mysqli_query($conn, "
    SELECT a.id as appt_id, d.nama as nama_dokter, t.nama as nama_treatment, a.tanggal, a.jam 
    FROM appointment a 
    JOIN dokter d ON a.dokter_id = d.id
    LEFT JOIN treatment t ON a.treatment_id = t.id
    WHERE a.pasien_id = $pasien_id
    ORDER BY a.tanggal DESC, a.jam DESC
");
$appointments = [];
if ($qAppts) {
    while ($row = mysqli_fetch_assoc($qAppts)) {
        $appointments[] = $row;
    }
}
$dokter = ['nama' => 'Dokter', 'spesialisasi' => 'Spesialis'];

if ($appt_id > 0) {
    $qConsult = mysqli_query($conn, "SELECT id, dokter_id FROM consultations WHERE appointment_id = $appt_id");
    if (mysqli_num_rows($qConsult) > 0) {
        $consult = mysqli_fetch_assoc($qConsult);
        $consultation_id = $consult['id'];
        $dokter_id = $consult['dokter_id'];
    } else {
        $qAppt = mysqli_query($conn, "SELECT dokter_id FROM appointment WHERE id = $appt_id");
        if ($appt = mysqli_fetch_assoc($qAppt)) {
            $dokter_id = $appt['dokter_id'];
            mysqli_query($conn, "INSERT INTO consultations (appointment_id, pasien_id, dokter_id, status, created_at) VALUES ($appt_id, $pasien_id, $dokter_id, 'Aktif', NOW())");
            $consultation_id = mysqli_insert_id($conn);
        }
    }
    
    if (isset($dokter_id)) {
        $qDokter = mysqli_query($conn, "SELECT nama, spesialisasi FROM dokter WHERE id = $dokter_id");
        if ($qDokter && mysqli_num_rows($qDokter) > 0) {
            $dokter = mysqli_fetch_assoc($qDokter);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konsultasi Chat - GlowCare Clinic</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@300;400;500&display=swap">
    <link rel="stylesheet" href="../../asset/css/user.css?v=4">
    <style>
        .chat-container {
            display: flex;
            height: calc(100vh - 100px);
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            border: 1px solid rgba(220, 232, 232, 0.4);
            box-shadow: 0 10px 30px rgba(115, 90, 57, 0.05);
            overflow: hidden;
            margin: 0 48px;
        }
        .chat-sidebar {
            width: 300px;
            background: rgba(242, 248, 248, 0.5);
            border-right: 1px solid rgba(220, 232, 232, 0.4);
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
            border-bottom: 1px solid rgba(220, 232, 232, 0.4);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .chat-doctor-info {
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
            background: #f0f5f5;
            color: #2c3e50;
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
            border-top: 1px solid rgba(220, 232, 232, 0.4);
            background: #fafafa;
            display: flex;
            gap: 12px;
            align-items: flex-end;
        }
        .chat-input {
            flex: 1;
            border: 1px solid #dce8e8;
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
            box-shadow: 0 0 0 3px rgba(115, 90, 57, 0.1);
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
            color: #2c3e50;
            border-bottom: 1px solid rgba(220, 232, 232, 0.4);
        }
        .chat-session-item {
            padding: 16px 24px;
            border-bottom: 1px solid rgba(220, 232, 232, 0.2);
            cursor: pointer;
            transition: background 0.2s;
        }
        .chat-session-item:hover, .chat-session-item.active {
            background: rgba(255, 255, 255, 0.6);
        }
    </style>
</head>
<body>

<!-- TOPNAV -->
<div class="topnav">
    <div style="display:flex; align-items:center; gap:16px;">
        <a href="dashboarduser.php" style="color:#2c3e50; text-decoration:none; font-size:18px; font-weight:bold; background:#f0fdf4; padding:6px 12px; border-radius:8px;" title="Kembali ke Dashboard">←</a>
        <div class="topnav-brand" onclick="window.location='dashboarduser.php'" style="cursor:pointer">GlowCare Clinic</div>
    </div>
    <div style="display:flex; gap:16px; align-items:center">
        <a href="dashboarduser.php" style="color:#fff; text-decoration:none; font-size:12px; letter-spacing:1px; text-transform:uppercase">Dashboard</a>
        <a href="../../backend/logout.php" class="btn-outline" style="color:#fff; border-color:rgba(255,255,255,0.3); padding:6px 16px">Logout</a>
    </div>
</div>

<div style="padding: 24px 0;">
    <div class="chat-container">
        <!-- Sidebar -->
        <div class="chat-sidebar">
            <div class="chat-sidebar-header">Riwayat Konsultasi</div>
            <?php if (count($appointments) === 0): ?>
                <div style="padding: 24px; font-size: 13px; color: #64748b;">Belum ada riwayat konsultasi.</div>
            <?php else: ?>
                <?php foreach($appointments as $app): ?>
                <div class="chat-session-item <?= ($app['appt_id'] == $appt_id) ? 'active' : '' ?>" onclick="window.location.href='chat.php?appt_id=<?= $app['appt_id'] ?>'">
                    <div style="font-weight:500; font-size:14px; color:#2c3e50"><?= htmlspecialchars($app['nama_dokter']) ?></div>
                    <div style="font-size:12px; color:#735a39; margin-top:4px"><?= htmlspecialchars($app['nama_treatment'] ?? 'Konsultasi') ?></div>
                    <div style="font-size:11px; color:#64748b; margin-top:6px"><?= date('d M Y, H:i', strtotime($app['tanggal'] . ' ' . $app['jam'])) ?></div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Main Chat Area -->
        <div class="chat-main">
            <div class="chat-header">
                <div class="chat-doctor-info">
                    <div class="avatar" style="width:40px; height:40px; font-size:16px"><?= substr(htmlspecialchars($dokter['nama']), 0, 1) ?></div>
                    <div>
                        <div style="font-family:'Playfair Display', serif; font-size:18px; color:#2c3e50"><?= htmlspecialchars($dokter['nama']) ?></div>
                        <div style="font-size:12px; color:#735a39"><?= htmlspecialchars($dokter['spesialisasi']) ?></div>
                    </div>
                </div>
                <span class="badge badge-yellow">Berlangsung</span>
            </div>

            <div class="chat-messages" id="chatMessages">
                <div style="text-align:center; font-size:11px; color:#7f8c8d; margin:10px 0;">Sesi Konsultasi Dimulai</div>
            </div>

            <div class="chat-input-area">
                <input type="file" id="imageUpload" accept="image/*" style="display:none" onchange="sendImage(this)">
                <button type="button" class="chat-btn" style="background: #f0f5f5; color: #735a39;" title="Upload Gambar" onclick="document.getElementById('imageUpload').click()">
                    📷
                </button>
                <textarea class="chat-input" placeholder="Ketik pesan Anda di sini..." rows="1" id="chatInput"></textarea>
                <button type="button" class="chat-btn" title="Kirim Pesan" onclick="sendMessage()">
                    ➤
                </button>
            </div>
        </div>
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
            if (data.status === 'success') {
                if (data.data.length > lastMessageCount) {
                    renderMessages(data.data);
                    lastMessageCount = data.data.length;
                }
            }
        })
        .catch(err => console.error(err));
}

function renderMessages(messages) {
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.innerHTML = '<div style="text-align:center; font-size:11px; color:#7f8c8d; margin:10px 0;">Sesi Konsultasi Dimulai</div>';
    
    messages.forEach(msg => {
        const msgDiv = document.createElement('div');
        msgDiv.className = msg.sender_type === 'Pasien' ? 'msg sent' : 'msg received';
        
        let content = '';
        if (msg.image_url) {
            content += `<img src="../../${msg.image_url}" style="max-width: 100%; border-radius: 8px; margin-bottom: 4px; display: block;" alt="Gambar">`;
        }
        if (msg.message) {
            content += msg.message;
        }
        content += `<div class="msg-time">${msg.time}</div>`;
        
        msgDiv.innerHTML = content;
        chatMessages.appendChild(msgDiv);
    });
    
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function sendMessage() {
    if (consultationId === 0) return;
    const input = document.getElementById('chatInput');
    const msgText = input.value.trim();
    if (!msgText) return;

    const formData = new FormData();
    formData.append('consultation_id', consultationId);
    formData.append('message', msgText);

    fetch('../../backend/chat_send.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            input.value = '';
            fetchMessages();
        } else {
            alert('Gagal mengirim pesan: ' + data.message);
        }
    })
    .catch(err => console.error(err));
}

function sendImage(input) {
    if (consultationId === 0) return;
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const formData = new FormData();
        formData.append('consultation_id', consultationId);
        formData.append('image', file);
        formData.append('message', '');

        fetch('../../backend/chat_send.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                input.value = '';
                fetchMessages();
            } else {
                alert('Gagal mengirim gambar: ' + data.message);
            }
        })
        .catch(err => console.error(err));
    }
}

// Fetch periodically
setInterval(fetchMessages, 3000);
fetchMessages();

// Auto-resize textarea
document.getElementById('chatInput').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
});
</script>
</body>
</html>
