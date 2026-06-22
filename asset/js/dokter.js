const dokterTitles = {
    overview: 'Overview',
    jadwal: 'Jadwal Praktik',
    'daftar-pasien': 'Daftar Pasien',
    'rekam-medis': 'Rekam Medis',
    ulasan: 'Ulasan Pasien',
    profil: 'Profil Saya',
    notifikasi: 'Notifikasi'
};

function showPanel(id, el) {
    document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
    const panel = document.getElementById('panel-' + id);
    if (panel) panel.classList.add('active');
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    if (el) el.classList.add('active');
    const title = dokterTitles[id] || id;
    const topEl = document.getElementById('topbar-title');
    const bcEl  = document.getElementById('topbar-bc');
    if (topEl) topEl.textContent = title;
    if (bcEl)  bcEl.textContent = 'GlowCare Dokter';
}

// ── Filter tabel pasien ──────────────────────
function filterPasien() {
    const cari      = document.getElementById('cari-pasien').value.toLowerCase();
    const status    = document.getElementById('filter-status').value;
    const treatment = document.getElementById('filter-treatment').value;
    document.querySelectorAll('#tabel-pasien tbody tr').forEach(tr => {
        const nama = tr.dataset.nama || '';
        const st   = tr.dataset.status || '';
        const tr2  = tr.dataset.treatment || '';
        const ok   = nama.includes(cari)
                  && (status    === '' || st.includes(status))
                  && (treatment === '' || tr2.includes(treatment));
        tr.style.display = ok ? '' : 'none';
    });
}

// ── Filter rekam medis ───────────────────────
function filterRM() {
    const cari      = document.getElementById('cari-rm').value.toLowerCase();
    const treatment = document.getElementById('filter-rm-treatment').value;
    const bulan     = document.getElementById('filter-rm-bulan').value;
    document.querySelectorAll('#tab-rm-list .rm-card').forEach(card => {
        const nama = card.dataset.nama || '';
        const tr   = card.dataset.treatment || '';
        const bln  = card.dataset.bulan || '';
        const ok   = nama.includes(cari)
                  && (treatment === '' || tr.includes(treatment))
                  && (bulan     === '' || bln === bulan);
        card.style.display = ok ? '' : 'none';
    });
}

// ── Buka modal edit RM (isi data dari PHP) ───
function openModalEdit(id, anamnesis, pemeriksaan, tindakLanjut, status, followup) {
    document.getElementById('edit-rm-id').value        = id;
    document.getElementById('edit-anamnesis').value    = anamnesis;
    document.getElementById('edit-pemeriksaan').value  = pemeriksaan;
    document.getElementById('edit-tindak-lanjut').value= tindakLanjut;
    document.getElementById('edit-followup').value     = followup;
    const sel = document.getElementById('edit-status');
    for (let opt of sel.options) {
        opt.selected = opt.value === status;
    }
    openModal('modal-rm-edit');
}

// ── Buka modal tambah RM, otomatis pilih pasien ─
function openModal(id, pasienId = null) {
    document.getElementById(id).classList.add('open');
    if (id === 'modal-rm-baru' && pasienId) {
        const sel = document.getElementById('modal-pasien-select');
        if (sel) sel.value = pasienId;
    }
}

// ── Lihat detail pasien via AJAX ─────────────
function showPasienDetail(pasienId) {
    const box = document.getElementById('pasien-detail-box');
    box.style.display = 'block';
    box.scrollIntoView({ behavior: 'smooth', block: 'start' });

    // Set tombol rekam medis ke pasien ini
    document.getElementById('btn-rm-dari-detail')
        .setAttribute('onclick', `openModal('modal-rm-baru', ${pasienId})`);

    // Ambil data pasien via AJAX
    fetch(`../../backend/dokter/get_pasien_detail.php?id=${pasienId}`)
        .then(r => r.json())
        .then(p => {
            document.getElementById('detail-avatar').textContent =
                p.nama ? p.nama.charAt(0).toUpperCase() : '?';
            document.getElementById('detail-name').textContent  = p.nama || '-';
            document.getElementById('detail-meta').textContent  =
                `${p.usia || '-'} Tahun · ${p.jenis_kelamin || '-'} · ${p.no_rekam || ''}`;

            document.getElementById('detail-info-grid').innerHTML = `
                <div class="info-item"><div class="info-label">Telepon</div><div class="info-value">${p.no_telp || '-'}</div></div>
                <div class="info-item"><div class="info-label">Email</div><div class="info-value">${p.email || '-'}</div></div>
                <div class="info-item"><div class="info-label">Golongan Darah</div><div class="info-value">${p.gol_darah || '-'}</div></div>
                <div class="info-item"><div class="info-label">Alergi</div><div class="info-value">${p.alergi || 'Tidak ada'}</div></div>
                <div class="info-item"><div class="info-label">Kondisi Khusus</div><div class="info-value">${p.kondisi_khusus || '-'}</div></div>
                <div class="info-item"><div class="info-label">Total Kunjungan</div><div class="info-value">${p.total_kunjungan || 0} kali</div></div>
            `;
        })
        .catch(() => {
            document.getElementById('detail-name').textContent = 'Gagal memuat data';
        });
}

// ── Load timeline pasien via AJAX ─────────────
function loadTimeline(pasienId) {
    if (!pasienId) return;
    const container = document.getElementById('timeline-container');
    container.innerHTML = '<div style="color:#b89098; font-size:13px;">Memuat...</div>';

    fetch(`../../backend/dokter/get_timeline.php?pasien_id=${pasienId}`)
        .then(r => r.json())
        .then(data => {
            if (!data.length) {
                container.innerHTML = '<div style="color:#b89098; font-size:13px;">Belum ada riwayat.</div>';
                return;
            }
            const icons = { 'Facelift':'','Rhinoplasty':'','Blepharoplasty':'',
                            'Follow-up':'','Konsultasi':'' };
            container.innerHTML = data.map(rm => `
                <div class="tl-item">
                    <div class="tl-dot">${icons[rm.treatment] || ''}</div>
                    <div class="tl-body">
                        <div class="tl-title">${rm.treatment || '-'}</div>
                        <div class="tl-desc">${rm.anamnesis || rm.pemeriksaan || '-'}</div>
                        <div class="tl-date">${rm.tanggal_label}</div>
                    </div>
                </div>
            `).join('');
        })
        .catch(() => {
            container.innerHTML = '<div style="color:#b89098;">Gagal memuat timeline.</div>';
        });
}

function closeModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('open');
}

function closeModalOutside(e, id) {
    if (e.target.classList.contains('modal-overlay')) closeModal(id);
}

// ── Switch tab rekam medis ────────────────────
function switchTab(tabId, el) {
    document.querySelectorAll('.rm-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.rm-tab').forEach(t => t.classList.remove('active'));
    const target = document.getElementById('tab-' + tabId);
    if (target) target.classList.add('active');
    if (el) el.classList.add('active');
}

// ── Konfirmasi hapus rekam medis ─────────────
function konfirmasiHapusRM(rmId, namaPasien, treatment) {
    document.getElementById('hapus-rm-id').value = rmId;
    const desc = document.getElementById('hapus-rm-desc');
    if (desc) desc.textContent = `Yakin hapus rekam medis ${namaPasien} (${treatment})? Data ini akan dihapus permanen.`;
    openModal('modal-hapus-rm');
}