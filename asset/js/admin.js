function showPanel(id, el) {
    document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
    document.getElementById('panel-' + id).classList.add('active');
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    if (el) el.classList.add('active');

    const titles = {
        dashboard: 'Dashboard',
        pasien: 'Data Pasien',
        dokter: 'Data Dokter',
        jadwal: 'Jadwal Dokter',
        aktivitas: 'Aktivitas',
        laporan: 'Laporan',
        treatment: 'Kelola Treatment',
        profil: 'Profil & Keamanan'
    };
    document.getElementById('topbar-title').textContent = titles[id] || id;
    document.getElementById('topbar-bc').textContent = 'GlowCare Admin → ' + (titles[id] || id);
}

function openModal(id) {
    document.getElementById(id).classList.add('open');
}

function closeModal(id) {
    document.getElementById(id).classList.remove('open');
}

function closeModalOutside(e, id) {
    if (e.target.classList.contains('modal-overlay')) closeModal(id);
}

function showToast(msg) {
    const t = document.getElementById('toast');
    document.getElementById('toast-msg').textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
}