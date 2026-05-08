    function showPage(id, el) {
        document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
        document.getElementById('page-' + id).classList.add('active');
        document.querySelectorAll('.topnav-item').forEach(n => n.classList.remove('active'));
        if (el) el.classList.add('active');
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

    function showToast(msg) {
        const t = document.getElementById('toast');
        document.getElementById('toast-msg').textContent = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3000);
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