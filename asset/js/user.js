    function showPage(id, el) {
        // Sembunyikan semua page
        document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
        const targetPage = document.getElementById('page-' + id);
        if (targetPage) targetPage.classList.add('active');

        // Hapus active dari SEMUA nav-item
        document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));

        // Aktifkan nav-item yang sesuai dengan id (cari via onclick attribute)
        const navItem = document.querySelector('.nav-item[onclick*="' + id + '"]');
        if (navItem) navItem.classList.add('active');

        // Perbarui judul topbar & breadcrumb
        const patTitles = {
            beranda: 'Beranda',
            'jadwal-dokter': 'Jadwal Dokter',
            'daftar-konsul': 'Daftar Pendaftaran',
            riwayat: 'Riwayat Medis',
            notifikasi: 'Notifikasi',
            akun: 'Profil Saya'
        };
        const title = patTitles[id] || id;
        const topEl = document.getElementById('topbar-title');
        const bcEl  = document.getElementById('topbar-bc');
        if (topEl) topEl.textContent = title;
        if (bcEl)  bcEl.textContent = 'GlowCare Pasien → ' + title;

        window.scrollTo({ top: 0, behavior: 'smooth' });
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

    function showToast(msg, isSuccess) {
        const t = document.getElementById('toast');
        document.getElementById('toast-msg').textContent = msg;
        t.className = 'toast'; // reset
        if (isSuccess === true)  t.classList.add('toast-success');
        if (isSuccess === false) t.classList.add('toast-error');
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3500);
    }

    // Step wizard
    let currentStep = 1;

    function nextStep(step) {
        document.getElementById('step-panel-' + currentStep).style.display = 'none';
        currentStep = step;
        document.getElementById('step-panel-' + step).style.display = 'block';

        // Update step indicator
        for (let i = 1; i <= 4; i++) {
            const el = document.getElementById('step' + i);
            el.className = 'step';
            if (i < step) el.classList.add('done');
            if (i === step) el.classList.add('active');
        }
    }

    function submitPendaftaran() {
        openModal('modal-sukses');
        // reset step
        nextStep(1);
    }

    // Slot selection
    function selectSlot(el) {
        if (el.classList.contains('full')) return;
        // Only deselect in same parent grid
        const grid = el.parentElement;
        grid.querySelectorAll('.slot-btn').forEach(s => s.classList.remove('selected'));
        el.classList.add('selected');
    }

    // Dokter selection
    function selectDokter(n) {
        for (let i = 1; i <= 3; i++) {
            const card = document.getElementById('dokter-option-' + i);
            card.style.border = i === n ? '2px solid #c55085' : '1px solid #f2c4ce';
            const badge = card.querySelector('.badge');
            badge.className = 'badge ' + (i === n ? 'badge-green' : 'badge-gray');
            badge.textContent = i === n ? 'Terpilih ✓' : 'Pilih';
        }
    }

    // Calendar date pick
    function pickDate(el) {
        document.querySelectorAll('.cal-day').forEach(d => {
            if (!d.classList.contains('today')) d.classList.remove('selected');
        });
        el.classList.add('selected');
    }